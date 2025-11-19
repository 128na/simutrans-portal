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
  const shouldNotify = useArticleEditor((s) => s.shouldNotify);
  const updateShouldNotify = useArticleEditor((s) => s.updateShouldNotify);
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
      <div className="mt-2">
        <Label>
          <div className="font-medium">保存時の更新日時</div>

          <Checkbox
            checked={withoutUpdateModifiedAt}
            onChange={() => {
              useArticleEditor.setState((state) => {
                // 更新日時を変えないときはSNS通知も不要なはずなのでOFFにする
                if (!withoutUpdateModifiedAt) {
                  state.shouldNotify = false;
                }
                state.withoutUpdateModifiedAt = !withoutUpdateModifiedAt;
              });
            }}
          >
            更新しない
          </Checkbox>
        </Label>
      </div>
      {article.status === "publish" && (
        <div className="mt-2">
          <Label>
            <div className="font-medium">公開時のSNS通知</div>

            <Checkbox
              checked={shouldNotify}
              onChange={() => updateShouldNotify(!shouldNotify)}
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
