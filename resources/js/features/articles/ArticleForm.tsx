import { match } from "ts-pattern";
import { JSX } from "react";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import { Page } from "./postType/Page";
import { Markdown } from "./postType/Markdown";
import { AddonPost } from "./postType/AddonPost";
import { AddonIntroduction } from "./postType/AddonIntroduction";
import Label from "@/components/ui/Label";
import Checkbox from "@/components/ui/Checkbox";
export const ArticleForm = () => {
  const article = useArticleEditor((s) => s.article);
  const shouldNotify = useArticleEditor((s) => s.shouldNotify);
  const updateShouldNotify = useArticleEditor((s) => s.updateShouldNotify);
  const withoutUpdateModifiedAt = useArticleEditor(
    (s) => s.withoutUpdateModifiedAt
  );

  if (!article || !article.post_type) return null;

  return (
    <>
      {match<ArticlePostType>(article.post_type)
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
    </>
  );
};
