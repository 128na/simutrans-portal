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
  | { type: "waiting"; seconds: number; item: BulkItem };

export type BulkResult = {
  succeeded: BulkItem[];
  failed: BulkItem[];
  /** 中止したときに取得しなかったもの */
  skipped: BulkItem[];
};

type Options = {
  fetchImpl?: typeof fetch;
  saveBlob?: (blob: Blob, fileName: string) => void;
  /** ミリ秒待つ。テストで差し替える */
  sleep?: (ms: number) => Promise<void>;
  onProgress?: (progress: BulkProgress) => void;
  /** true を返したら次の件から取得をやめる */
  isCancelled?: () => boolean;
};

/** Retry-After が読めなかったときの待ち時間（秒） */
export const DEFAULT_RETRY_AFTER_SECONDS = 60;

const defaultSleep = (ms: number) =>
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
  setTimeout(() => URL.revokeObjectURL(objectUrl), 60_000);
};

/**
 * アドオン投稿（download_url があるもの）だけを順番に 1 件ずつ取得する
 */
export const downloadSequentially = async (
  items: BulkItem[],
  {
    fetchImpl = fetch,
    saveBlob = saveBlobAsFile,
    sleep = defaultSleep,
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
    // 429 の間だけ待って同じ件を取り直す
    for (;;) {
      let response: Response;
      try {
        response = await fetchImpl(item.download_url as string, {
          method: "GET",
          credentials: "same-origin",
        });
      } catch {
        break;
      }

      if (response.status === 429) {
        let seconds = parseRetryAfter(response.headers.get("Retry-After"));
        while (seconds > 0) {
          if (isCancelled()) {
            cancelledWhileWaiting = true;
            break;
          }
          onProgress?.({ type: "waiting", seconds, item });
          await sleep(1000);
          seconds--;
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
