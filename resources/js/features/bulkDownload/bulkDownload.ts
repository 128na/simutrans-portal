/**
 * まとめてダウンロードの本体。
 *
 * 1 件ごとに articles.download を fetch で 1 回だけ取得し、Blob を保存させる。
 * - CV はサーバーがファイルを返すときに 1 回数える。HEAD やプリフライトはしない（GET ルートは HEAD でも CV が増える）
 * - 自動で取り直すのは 429 のときだけ。429 はレート制限がコントローラより前で返すので CV は増えていない
 * - それ以外の失敗（5xx・通信断）はサーバー側で数え済みの可能性があるので取り直さず「失敗」として返す
 */

export type BulkItem = {
  id: number;
  title: string;
  url: string;
  download_url?: string;
  addon_page_url?: string;
};

export type BulkProgress =
  | { type: "downloading"; index: number; total: number; item: BulkItem }
  | {
      type: "waiting";
      /** 再開までの残り秒数 */
      seconds: number;
      /** この待機の最初の待ち時間（Retry-After の秒数）。長い待ちかどうかはこれで決める */
      totalSeconds: number;
      /** 再開する時刻（エポックミリ秒） */
      resumeAt: number;
      index: number;
      total: number;
      item: BulkItem;
    };

export type BulkResult = {
  succeeded: BulkItem[];
  failed: BulkItem[];
  /** 中止したときに取得しなかったもの */
  skipped: BulkItem[];
  /** 長い待ち（isLongWait）の途中で中止したとき、上限が解ける時刻（エポックミリ秒） */
  resumeAt?: number;
};

type Options = {
  fetchImpl?: typeof fetch;
  saveBlob?: (blob: Blob, fileName: string) => void;
  /** ミリ秒待つ。テストで差し替える */
  sleep?: (ms: number) => Promise<void>;
  /** 今の時刻（エポックミリ秒）。テストで差し替える */
  now?: () => number;
  onProgress?: (progress: BulkProgress) => void;
  /** true を返したら次の件から取得をやめる */
  isCancelled?: () => boolean;
};

/** Retry-After が読めなかったときの待ち時間（秒） */
export const DEFAULT_RETRY_AFTER_SECONDS = 60;

/**
 * この秒数以上待つときは「長い待ち」として分で示し、時間をおいて残りを取得する案内を出す。
 * 1 分の上限なら Retry-After は 60 秒以下なので、これを超えるのは 1 時間の上限に達したとき
 */
export const LONG_WAIT_SECONDS = 120;

export const isLongWait = (totalSeconds: number): boolean =>
  totalSeconds >= LONG_WAIT_SECONDS;

export const sleepMs = (ms: number) =>
  new Promise<void>((resolve) => setTimeout(resolve, ms));

/**
 * Retry-After（秒数または HTTP 日付）を秒にする
 */
export const parseRetryAfter = (
  value: string | null,
  now: number = Date.now()
): number => {
  if (!value) {
    return DEFAULT_RETRY_AFTER_SECONDS;
  }
  const trimmed = value.trim();
  if (/^\d+$/.test(trimmed)) {
    return Math.max(1, Number(trimmed));
  }
  const date = Date.parse(trimmed);
  if (!Number.isNaN(date)) {
    return Math.max(1, Math.ceil((date - now) / 1000));
  }
  return DEFAULT_RETRY_AFTER_SECONDS;
};

/**
 * Content-Disposition からファイル名を取り出す。filename* を優先する
 */
export const fileNameFromDisposition = (
  value: string | null,
  fallback: string
): string => {
  if (!value) {
    return fallback;
  }
  const extended = /filename\*\s*=\s*(?:UTF-8|utf-8)''([^;]+)/.exec(value);
  if (extended?.[1]) {
    try {
      return decodeURIComponent(extended[1].trim());
    } catch {
      // 壊れた値なら filename= を見る
    }
  }
  const plain = /filename\s*=\s*"?([^";]+)"?/.exec(value);
  return plain?.[1]?.trim() || fallback;
};

/**
 * Blob をファイルとして保存させる。URL はすぐ消すとダウンロードが中断されるブラウザがあるので少し待って消す
 */
export const saveBlobAsFile = (blob: Blob, fileName: string): void => {
  const objectUrl = URL.createObjectURL(blob);
  const anchor = document.createElement("a");
  anchor.href = objectUrl;
  anchor.download = fileName;
  anchor.rel = "noopener";
  anchor.style.display = "none";
  document.body.appendChild(anchor);
  anchor.click();
  anchor.remove();
  setTimeout(() => URL.revokeObjectURL(objectUrl), 10_000);
};

/**
 * download_url は APP_URL で組み立てた絶対 URL。開いているホストと違う（www の有無など）と
 * CORS で本文を読めず、サーバーだけが CV を数える。パスだけを今のオリジンで使う
 */
export const toSameOrigin = (
  url: string,
  origin: string = window.location.origin
): string => {
  try {
    const parsed = new URL(url, origin);
    return `${origin}${parsed.pathname}${parsed.search}`;
  } catch {
    return url;
  }
};

/**
 * アドオン投稿（download_url があるもの）だけを順番に 1 件ずつ取得する
 */
export const downloadSequentially = async (
  items: BulkItem[],
  {
    fetchImpl = fetch,
    saveBlob = saveBlobAsFile,
    sleep = sleepMs,
    now = Date.now,
    onProgress,
    isCancelled = () => false,
  }: Options = {}
): Promise<BulkResult> => {
  const targets = items.filter((item) => item.download_url);
  const result: BulkResult = { succeeded: [], failed: [], skipped: [] };

  for (let index = 0; index < targets.length; index++) {
    const item = targets[index];
    if (isCancelled()) {
      result.skipped.push(...targets.slice(index));
      break;
    }
    onProgress?.({ type: "downloading", index, total: targets.length, item });

    let saved = false;
    let cancelledWhileWaiting = false;
    /** 長い待ちの途中で中止したときに返す、上限が解ける時刻 */
    let longWaitResumeAt: number | undefined;
    // 429 の間だけ待って同じ件を取り直す
    for (;;) {
      let response: Response;
      try {
        response = await fetchImpl(toSameOrigin(item.download_url as string), {
          method: "GET",
          credentials: "same-origin",
        });
      } catch {
        break;
      }

      if (response.status === 429) {
        const startedAt = now();
        const totalSeconds = parseRetryAfter(
          response.headers.get("Retry-After"),
          startedAt
        );
        // 残りは締切の時刻から数える。隠れたタブではタイマーが間引かれ、1 秒ずつ数えると長い待ちが終わらない
        const resumeAt = startedAt + totalSeconds * 1000;
        for (;;) {
          const remainingMs = resumeAt - now();
          if (remainingMs <= 0) {
            break;
          }
          if (isCancelled()) {
            cancelledWhileWaiting = true;
            // 短い待ち（1 分の上限）なら再開の時刻は案内しない
            if (isLongWait(totalSeconds)) {
              longWaitResumeAt = resumeAt;
            }
            break;
          }
          onProgress?.({
            type: "waiting",
            seconds: Math.ceil(remainingMs / 1000),
            totalSeconds,
            resumeAt,
            index,
            total: targets.length,
            item,
          });
          // 中止に 1 秒以内で応じられるよう、1 秒より長くは眠らない
          await sleep(Math.min(1000, remainingMs));
        }
        if (cancelledWhileWaiting) {
          break;
        }
        onProgress?.({
          type: "downloading",
          index,
          total: targets.length,
          item,
        });
        continue;
      }

      if (!response.ok) {
        break;
      }

      try {
        const blob = await response.blob();
        saveBlob(
          blob,
          fileNameFromDisposition(
            response.headers.get("Content-Disposition"),
            item.title
          )
        );
        saved = true;
      } catch {
        // 本文の途中で切れた。サーバーは数え済みなので取り直さない
      }
      break;
    }

    if (cancelledWhileWaiting) {
      result.skipped.push(...targets.slice(index));
      if (longWaitResumeAt !== undefined) {
        result.resumeAt = longWaitResumeAt;
      }
      break;
    }
    if (saved) {
      result.succeeded.push(item);
    } else {
      result.failed.push(item);
    }
  }

  return result;
};
