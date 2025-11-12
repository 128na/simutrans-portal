import { match } from "ts-pattern";
import { JSX } from "react";
import { useArticleEditor } from "@/apps/state/useArticleEditor";
import { Page } from "./postType/Page";
import { Markdown } from "./postType/Markdown";
import { AddonPost } from "./postType/AddonPost";
import { AddonIntroduction } from "./postType/AddonIntroduction";
import Label from "@/apps/components/ui/Label";
import Checkbox from "@/apps/components/ui/Checkbox";
import Button from "@/apps/components/ui/Button";
import axios, { AxiosError } from "axios";
import { useAxiosError } from "@/apps/state/useAxiosError";
export const ArticleEditor = () => {
  const article = useArticleEditor((s) => s.article);
  const shouldNotfy = useArticleEditor((s) => s.shouldNotfy);
  const updateShouldNotify = useArticleEditor((s) => s.updateShouldNotify);

  const { setError } = useAxiosError();
  const url = article.id
    ? `/api/v2/articles/${article.id}`
    : "/api/v2/articles";
  const save = async () => {
    try {
      const res = await axios.post(url, {
        article,
        should_notfy: shouldNotfy,
      });
      window.location.href = `/mypage/articles/edit/${res.data.article_id}?updated=1`;
    } catch (error) {
      if (error instanceof AxiosError) {
        setError(error.response?.data);
      }
    }
  };
  if (!article || !article.post_type) return null;

  return (
    <>
      {match<Article.PostType>(article.post_type)
        .returnType<JSX.Element>()
        .with("page", () => <Page />)
        .with("markdown", () => <Markdown />)
        .with("addon-post", () => <AddonPost />)
        .with("addon-introduction", () => <AddonIntroduction />)
        .exhaustive()}
      {article.status === "publish" && (
        <div className="mt-2">
          <Label>
            <div className="font-medium">公開時のSNS通知</div>

            <Checkbox
              checked={shouldNotfy}
              onChange={() => updateShouldNotify(!shouldNotfy)}
            >
              通知する
            </Checkbox>
          </Label>
        </div>
      )}
      <div className="mt-2">
        <Button onClick={save}>保存</Button>
      </div>
    </>
  );
};
