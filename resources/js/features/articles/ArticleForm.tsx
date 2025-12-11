import { match } from "ts-pattern";
import { JSX } from "react";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import { Page } from "./postType/Page";
import { Markdown } from "./postType/Markdown";
import { AddonPost } from "./postType/AddonPost";
import { AddonIntroduction } from "./postType/AddonIntroduction";
import { FormCaption } from "@/components/ui/FormCaption";
import V2Checkbox from "@/components/ui/v2/V2Checkbox";
import { StatusForm } from "./forms/StatusForm";
import { CommonForm } from "./forms/CommonForm";
export const ArticleForm = () => {
  const article = useArticleEditor((s) => s.article);
  const shouldNotify = useArticleEditor((s) => s.shouldNotify);
  const updateShouldNotify = useArticleEditor((s) => s.updateShouldNotify);
  const withoutUpdateModifiedAt = useArticleEditor(
    (s) => s.withoutUpdateModifiedAt
  );

  if (!article || !article.post_type) return null;

  return (
    <div className="grid gap-6">
      <CommonForm />
      {match<ArticlePostType>(article.post_type)
        .returnType<JSX.Element>()
        .with("page", () => <Page />)
        .with("markdown", () => <Markdown />)
        .with("addon-post", () => <AddonPost />)
        .with("addon-introduction", () => <AddonIntroduction />)
        .exhaustive()}
      <StatusForm />
      <div>
        <FormCaption>保存時の更新日時</FormCaption>
        <V2Checkbox
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
        </V2Checkbox>
      </div>
      {article.status === "publish" && (
        <div>
          <FormCaption>公開時のSNS通知</FormCaption>
          <V2Checkbox
            checked={shouldNotify}
            onChange={() => updateShouldNotify(!shouldNotify)}
          >
            通知する
          </V2Checkbox>
        </div>
      )}
    </div>
  );
};
