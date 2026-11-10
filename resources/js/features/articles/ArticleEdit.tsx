import { useEffect, useRef, useState } from "react";
import {
  useArticleEditor,
  useIsArticleUpdated,
} from "@/hooks/useArticleEditor";
import axios from "axios";
import { useAxiosError } from "@/hooks/useAxiosError";
import { ArticlePreview } from "./ArticlePreview";
import { ArticleForm } from "./ArticleForm";
import { handleError } from "@/lib/errorHandler";
import Button from "@/components/ui/Button";
import { StatusText } from "./utils/articleUtil";

const actions = {
  draft: { label: "下書きを保存", result: "下書きを保存しました。" },
  publish: { label: "公開する", result: "記事を公開しました。" },
  update: { label: "公開内容を更新", result: "公開中の記事を更新しました。" },
  reservation: { label: "公開を予約する", result: "公開を予約しました。" },
  private: { label: "非公開で保存", result: "非公開で保存しました。" },
  trash: { label: "ゴミ箱に移す", result: "記事をゴミ箱に移しました。" },
} as const;

const savedMessage = () => {
  const params = new URLSearchParams(window.location.search);
  const operation = params.get("saved");
  if (
    params.get("updated") === "1" &&
    operation &&
    Object.hasOwn(actions, operation)
  ) {
    return actions[operation as keyof typeof actions].result;
  }
};

export const ArticleEdit = () => {
  const contentRef = useRef<HTMLDivElement>(null);
  const saveInProgress = useRef(false);
  const saved = useRef(false);
  const article = useArticleEditor((s) => s.article);
  const initialStatus = useArticleEditor((s) => s.initialArticle?.status);
  const shouldNotify = useArticleEditor((s) => s.shouldNotify);
  const withoutUpdateModifiedAt = useArticleEditor(
    (s) => s.withoutUpdateModifiedAt
  );
  const followRedirect = useArticleEditor((s) => s.followRedirect);
  const uploading = useArticleEditor((s) => s.uploadsInProgress > 0);
  const isDirty =
    useIsArticleUpdated() ||
    shouldNotify ||
    withoutUpdateModifiedAt ||
    followRedirect ||
    uploading;
  const [panel, setPanel] = useState<"edit" | "preview">("edit");
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string>();
  const [success, setSuccess] = useState(savedMessage);

  const { setValidationErrorFrom } = useAxiosError();
  const operation =
    article?.id && article.status === "publish" && initialStatus === "publish"
      ? "update"
      : (article?.status ?? "draft");

  useEffect(() => {
    if (!isDirty) return;
    const warn = (event: BeforeUnloadEvent) => {
      if (saved.current) return;
      event.preventDefault();
      event.returnValue = true;
    };
    window.addEventListener("beforeunload", warn);
    return () => window.removeEventListener("beforeunload", warn);
  }, [isDirty]);

  const save = async () => {
    if (saveInProgress.current || saved.current || uploading) return;
    saveInProgress.current = true;
    setSaving(true);
    setError(undefined);
    setSuccess(undefined);

    try {
      const url = article.id
        ? "/api/v2/articles/" + article.id
        : "/api/v2/articles";
      const res = await axios.post(url, {
        article,
        should_notify: shouldNotify,
        without_update_modified_at: withoutUpdateModifiedAt,
        follow_redirect: followRedirect,
      });
      saved.current = true;
      window.location.replace(
        "/mypage/articles/edit/" +
          res.data.article_id +
          "?updated=1&saved=" +
          operation
      );
    } catch (error) {
      if (setValidationErrorFrom(error)) {
        setError("入力内容を確認してください。");
        setPanel("edit");
        requestAnimationFrame(() =>
          contentRef.current?.scrollTo({ top: 0, behavior: "smooth" })
        );
      } else {
        setError(
          "保存できませんでした。入力内容を保持しています。もう一度お試しください。"
        );
        handleError(error, {
          component: "ArticleEdit",
          action: "save",
          silent: true,
        });
      }
    } finally {
      saveInProgress.current = false;
      setSaving(false);
    }
  };
  if (!article || !article.post_type) return null;

  return (
    <>
      {success && (
        <p role="status" className="my-3 text-c-primary">
          {success}
        </p>
      )}
      <div
        className="flex gap-2 mb-3 lg:hidden"
        role="group"
        aria-label="投稿画面の表示"
      >
        <Button
          variant={panel === "edit" ? "primary" : "subOutline"}
          aria-pressed={panel === "edit"}
          aria-controls="article-edit-panel"
          onClick={() => setPanel("edit")}
        >
          編集
        </Button>
        <Button
          variant={panel === "preview" ? "primary" : "subOutline"}
          aria-pressed={panel === "preview"}
          aria-controls="article-preview-panel"
          onClick={() => setPanel("preview")}
        >
          プレビュー
        </Button>
      </div>
      <div className="flex flex-col gap-y-0 lg:grid lg:grid-cols-2 h-[calc(100vh-240px)]">
        <div
          ref={contentRef}
          id="article-edit-panel"
          aria-label="編集欄"
          className={
            "min-w-0 overflow-y-auto pr-4 pb-10 " +
            (panel === "edit" ? "" : "hidden lg:block")
          }
        >
          <fieldset disabled={saving} className="min-w-0">
            <ArticleForm />
          </fieldset>
        </div>
        <div
          id="article-preview-panel"
          aria-label="プレビュー欄"
          className={
            "min-w-0 overflow-y-auto lg:pl-4 pb-10 v2-divider pt-4 lg:border-t-0 " +
            (panel === "preview" ? "" : "hidden lg:block")
          }
        >
          <ArticlePreview />
        </div>
      </div>
      <div className="v2-divider pt-4 space-y-2">
        {error && (
          <p role="alert" className="text-sm">
            {error}
          </p>
        )}
        <div className="flex flex-wrap items-center gap-3">
          <Button onClick={save} size="lg" disabled={saving || uploading}>
            {saving
              ? "保存中…"
              : uploading
                ? "アップロード中…"
                : actions[operation].label}
          </Button>
          <p className="text-sm text-c-sub">
            保存後の状態は「{StatusText[article.status]}」です。
            {isDirty && !saving && " 未保存の変更があります。"}
          </p>
        </div>
      </div>
    </>
  );
};
