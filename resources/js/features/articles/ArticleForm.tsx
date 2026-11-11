import { match } from "ts-pattern";
import { JSX } from "react";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import { Page } from "./postType/Page";
import { Markdown } from "./postType/Markdown";
import { AddonPost, AddonPostFile } from "./postType/AddonPost";
import { AddonIntroduction } from "./postType/AddonIntroduction";
import { FormCaption } from "@/components/ui/FormCaption";
import Checkbox from "@/components/ui/Checkbox";
import { StatusForm } from "./forms/StatusForm";
import { CommonForm } from "./forms/CommonForm";
import TextSub from "@/components/ui/TextSub";
import { canChoosePublish } from "./utils/articleUtil";
export const ArticleForm = () => {
  const article = useArticleEditor((s) => s.article);
  const initialStatus = useArticleEditor((s) => s.initialArticle?.status);
  const shouldNotify = useArticleEditor((s) => s.shouldNotify);
  const updateShouldNotify = useArticleEditor((s) => s.updateShouldNotify);
  const withoutUpdateModifiedAt = useArticleEditor(
    (s) => s.withoutUpdateModifiedAt
  );

  if (!article || !article.post_type) return null;

  return (
    <div className="grid gap-6">
      {article.post_type === "addon-post" && <AddonPostFile />}
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
      </div>
      {/* 下書きからも「公開する」で公開できるため、そのときも通知の希望を選べるようにする */}
      {(article.status === "publish" ||
        canChoosePublish(article.status, initialStatus)) && (
        <div>
          <FormCaption>公開時のSNS通知</FormCaption>
          <Checkbox
            checked={shouldNotify}
            onChange={() => updateShouldNotify(!shouldNotify)}
          >
            通知する
          </Checkbox>
          {article.status === "draft" && (
            <TextSub>
              「公開する」で保存したときに通知します。下書き保存では通知の希望は残りません。
            </TextSub>
          )}
        </div>
      )}
    </div>
  );
};
