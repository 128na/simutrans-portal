import { useRef, useState } from "react";
import Button from "@/components/ui/Button";
import { ArticleCardList } from "../articles/components/ArticleCardList";
import type { ArticleCardSort } from "../articles/components/ArticleCard";
import { AddToMyListModal } from "../mylist/AddToMyList";
import {
  downloadSequentially,
  type BulkItem,
  type BulkProgress,
  type BulkResult,
} from "./bulkDownload";
import { isSelectable, useBulkSelection } from "./useBulkSelection";

type Props = {
  articles: Article.Card[];
  sort?: ArticleCardSort;
  isAuthenticated?: boolean;
  /** テストで差し替える */
  download?: typeof downloadSequentially;
};

type Finished = BulkResult & { external: BulkItem[] };

const SelectToggle = ({
  article,
  selected,
  disabled,
  onToggle,
}: {
  article: Article.Card;
  selected: boolean;
  disabled: boolean;
  onToggle: () => void;
}) => (
  <Button
    variant={selected ? "primaryOutline" : "subOutline"}
    aria-pressed={selected}
    aria-label={`${article.title} を${selected ? "選択から外す" : "選ぶ"}`}
    disabled={disabled}
    onClick={onToggle}
  >
    {selected ? "選択中" : "選ぶ"}
  </Button>
);

const ProgressMessage = ({ progress }: { progress: BulkProgress }) => {
  if (progress.type === "waiting") {
    return (
      <>
        ダウンロードの上限に達したため待機中です。あと {progress.seconds} 秒で「
        {progress.item.title}」から再開します。
      </>
    );
  }
  return (
    <>
      {progress.index + 1} / {progress.total} 件目「{progress.item.title}
      」をダウンロード中
    </>
  );
};

/**
 * 一覧のカードを選んでまとめてダウンロード・マイリストに保存する
 */
export const BulkSelectableArticleList = ({
  articles,
  sort = "modified",
  isAuthenticated = false,
  download = downloadSequentially,
}: Props) => {
  const { items, isSelected, toggle, clear, replace } = useBulkSelection();
  const [progress, setProgress] = useState<BulkProgress | null>(null);
  const [isRunning, setIsRunning] = useState(false);
  const [finished, setFinished] = useState<Finished | null>(null);
  const [isMyListOpen, setIsMyListOpen] = useState(false);
  const runningRef = useRef(false);
  const cancelRef = useRef(false);

  const downloadable = items.filter((item) => item.download_url);
  const external = items.filter((item) => !item.download_url);

  // クリックからだけ起動する（effect から起動すると StrictMode で 2 回走り CV が二重になる）
  const handleBulkDownload = async () => {
    if (runningRef.current) {
      return;
    }
    runningRef.current = true;
    cancelRef.current = false;
    setIsRunning(true);
    setFinished(null);

    const result = await download(items, {
      onProgress: setProgress,
      isCancelled: () => cancelRef.current,
    });

    // 取得できたアドオン投稿だけ選択から外す。失敗・中止したものはもう一度試せるように、
    // アドオン紹介は掲載先を開くまで・マイリストに保存できるように残す
    const succeeded = new Set(result.succeeded.map((item) => item.id));
    replace(items.filter((item) => !succeeded.has(item.id)));
    setFinished({ ...result, external });
    setProgress(null);
    setIsRunning(false);
    runningRef.current = false;
  };

  return (
    <div className="flex flex-col gap-4">
      {finished && (
        <section
          role="status"
          aria-label="まとめてダウンロードの結果"
          className="flex flex-col gap-2 rounded-lg border border-c-border bg-white p-4"
        >
          <div className="flex flex-wrap items-center gap-3">
            <span className="font-semibold">
              {finished.succeeded.length} 件をダウンロードしました
            </span>
            <Button
              variant="subOutline"
              className="ml-auto"
              onClick={() => setFinished(null)}
            >
              閉じる
            </Button>
          </div>
          {finished.failed.length > 0 && (
            <p className="text-body text-c-danger">
              {finished.failed.length}{" "}
              件はダウンロードできませんでした。選択に残しているので、もう一度お試しください。
            </p>
          )}
          {finished.skipped.length > 0 && (
            <p className="text-body text-c-sub">
              中止したため {finished.skipped.length}{" "}
              件はダウンロードしていません（選択に残しています）。
            </p>
          )}
          {finished.external.length > 0 && (
            <div className="flex flex-col gap-1 border-t border-c-border pt-2">
              <span className="text-body text-c-sub">
                掲載先で入手するもの {finished.external.length}{" "}
                件（リンクを開いて入手してください）
              </span>
              <ul className="flex flex-col">
                {finished.external.map((item) => (
                  <li key={item.id}>
                    <a
                      href={item.addon_page_url ?? item.url}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="inline-block py-2 font-semibold text-c-link"
                    >
                      {item.title} ↗
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          )}
        </section>
      )}

      <ArticleCardList
        articles={articles}
        sort={sort}
        isAuthenticated={isAuthenticated}
        renderActions={(article) =>
          isSelectable(article) ? (
            <SelectToggle
              article={article}
              selected={isSelected(article.id)}
              disabled={isRunning}
              onToggle={() => toggle(article)}
            />
          ) : null
        }
      />

      {/* 選択の帯は一覧の後ろに置いて画面の下端に貼り付け、出しても一覧を押し下げない */}
      {(items.length > 0 || isRunning) && (
        <section
          aria-label="選択中のアドオン"
          className="sticky bottom-0 z-10 flex flex-col gap-2 rounded-lg border border-c-primary bg-c-surface p-3 shadow-lg"
        >
          <div className="flex flex-wrap items-center gap-3">
            <span className="font-semibold">{items.length} 件を選択中</span>
            <span className="text-meta text-c-sub">
              ダウンロード {downloadable.length} 件 ・ 掲載先で入手{" "}
              {external.length} 件
            </span>
            <div className="ml-auto flex flex-wrap gap-2">
              {isRunning ? (
                <Button
                  variant="subOutline"
                  onClick={() => {
                    cancelRef.current = true;
                  }}
                >
                  中止
                </Button>
              ) : (
                <>
                  <Button variant="subOutline" onClick={clear}>
                    選択を解除
                  </Button>
                  {isAuthenticated ? (
                    <Button
                      variant="primaryOutline"
                      onClick={() => setIsMyListOpen(true)}
                    >
                      マイリストに保存
                    </Button>
                  ) : (
                    <a
                      href="/login"
                      className="v2-button-outline v2-button-md v2-button-primary-outline inline-block"
                    >
                      ログインしてマイリストに保存
                    </a>
                  )}
                  <Button
                    variant="primary"
                    onClick={handleBulkDownload}
                    disabled={downloadable.length === 0}
                  >
                    まとめてダウンロード
                  </Button>
                </>
              )}
            </div>
          </div>
          {progress && (
            <p className="text-body" role="status">
              <ProgressMessage progress={progress} />
            </p>
          )}
          {downloadable.length > 1 && (
            <p className="text-meta text-c-sub">
              1
              件ずつ順番に保存します。ブラウザが「複数ファイルのダウンロード」の許可を求めたら「許可」を選んでください。ブロックすると
              2
              件目以降が保存されません（アドレスバーのアイコンから許可し直せます）。
            </p>
          )}
        </section>
      )}

      {isMyListOpen && (
        <AddToMyListModal
          articleIds={items.map((item) => item.id)}
          onClose={() => setIsMyListOpen(false)}
          onSuccess={() => setIsMyListOpen(false)}
        />
      )}
    </div>
  );
};
