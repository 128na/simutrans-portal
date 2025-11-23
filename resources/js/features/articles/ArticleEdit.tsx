import { useRef } from "react";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import Button from "@/components/ui/Button";
import axios, { AxiosError } from "axios";
import { useAxiosError } from "@/hooks/useAxiosError";
import { ArticlePreview } from "./ArticlePreview";
import { ArticleForm } from "./ArticleForm";
export const ArticleEdit = () => {
  const contentRef = useRef<HTMLDivElement>(null);
  const article = useArticleEditor((s) => s.article);
  const shouldNotify = useArticleEditor((s) => s.shouldNotify);
  const withoutUpdateModifiedAt = useArticleEditor(
    (s) => s.withoutUpdateModifiedAt,
  );
  const followRedirect = useArticleEditor((s) => s.followRedirect);

  const { setError } = useAxiosError();
  const url = article.id
    ? `/api/v2/articles/${article.id}`
    : "/api/v2/articles";
  const save = async () => {
    try {
      const res = await axios.post(url, {
        article,
        should_notify: shouldNotify,
        without_update_modified_at: withoutUpdateModifiedAt,
        follow_redirect: followRedirect,
      });
      window.location.href = `/mypage/articles/edit/${res.data.article_id}?updated=1`;
    } catch (error) {
      if (error instanceof AxiosError) {
        setError(error.response?.data);
        contentRef.current?.scrollTo({ top: 0, behavior: "smooth" });
      }
    }
  };
  if (!article || !article.post_type) return null;

  return (
    <>
      <div className="flex flex-col gap-y-0 lg:grid lg:grid-cols-2 h-[calc(100vh-200px)]">
        <div ref={contentRef} className="overflow-y-auto pr-4 pb-10">
          <ArticleForm />
        </div>
        <div className="overflow-y-auto pl-4 pb-10 border-t border-gray-200 pt-4 lg:border-t-0 hidden lg:block">
          <ArticlePreview />
        </div>
      </div>
      <div className="border-t border-gray-200 pt-4">
        <Button onClick={save}>保存</Button>
      </div>
    </>
  );
};
