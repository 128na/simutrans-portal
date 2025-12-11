import { Upload } from "@/components/form/Upload";
import { useArticleEditor, useIsSlugUpdated } from "@/hooks/useArticleEditor";
import TextSub from "@/components/ui/TextSub";
import TextError from "@/components/ui/TextError";
import { useAxiosError } from "@/hooks/useAxiosError";
import { ModalFull } from "@/components/ui/ModalFull";
import { AttachmentEdit } from "@/features/attachments/AttachmentEdit";
import { FormCaption } from "@/components/ui/FormCaption";
import V2Input from "@/components/ui/v2/V2Input";
import V2TextBadge from "@/components/ui/v2/V2TextBadge";
import V2Button from "@/components/ui/v2/V2Button";
import V2Checkbox from "@/components/ui/v2/V2Checkbox";

const regReplace =
  /(!|"|#|\$|%|&|'|\(|\)|\*|\+|,|\/|:|;|<|=|>|\?|@|\[|\\|\]|\^|`|\{|\||\}|\s|\.)+/gi;

export const CommonForm = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);

  const user = useArticleEditor((s) => s.user);

  const followRedirect = useArticleEditor((s) => s.followRedirect);
  const updateFollowRedirect = useArticleEditor((s) => s.updateFollowRedirect);
  const isSlugUpdated = useIsSlugUpdated();
  const updateContents = useArticleEditor((s) => s.updateContents);

  const attachments = useArticleEditor((s) => s.attachments);

  const { getError } = useAxiosError();

  const escape = (str: string) =>
    encodeURI(str.toLowerCase().replace(regReplace, "-"));

  return (
    <>
      <div>
        <FormCaption>
          <V2TextBadge variant="danger">必須</V2TextBadge>
          タイトル
        </FormCaption>
        <TextError>{getError("article.title")}</TextError>
        <V2Input
          className="w-full"
          value={article.title || ""}
          required
          maxLength={255}
          onChange={(e) => update((draft) => (draft.title = e.target.value))}
        />
      </div>

      <div>
        <FormCaption>
          <V2TextBadge variant="danger">必須</V2TextBadge>
          記事URL
        </FormCaption>
        <TextError>{getError("article.slug")}</TextError>
        <V2Input
          className="w-full"
          value={decodeURI(article.slug || "")}
          required
          maxLength={255}
          counter={(value) => [...escape(value)].length}
          onChange={(e) =>
            update((draft) => (draft.slug = escape(e.target.value)))
          }
        />
        <TextSub className="my-1">
          URLプレビュー: /users/{user.nickname ?? user.id}/{article.slug || ""}
        </TextSub>
        <V2Button
          variant="subOutline"
          disabled={!article.title}
          onClick={() => {
            update((draft) => (draft.slug = escape(draft.title || "")));
          }}
        >
          タイトルから入力
        </V2Button>
      </div>

      {isSlugUpdated && (
        <div>
          <FormCaption>リダイレクト設定</FormCaption>
          <V2Checkbox
            checked={followRedirect}
            onChange={() => {
              updateFollowRedirect(!followRedirect);
            }}
          >
            追加する
          </V2Checkbox>
          <TextSub>
            記事URLを変更したとき、古い記事URLからのアクセスを新しい記事URLへ転送します。
            <br />
            SNS通知など古いリンクを修正できない場合にリンク切れしなくなります。
          </TextSub>
        </div>
      )}

      <div>
        <FormCaption>サムネイル</FormCaption>
        <TextError>{getError("article.contents.thumbnail")}</TextError>
        <Upload
          className="w-full mb-4"
          accept="image/*"
          onUploaded={(a) => {
            useArticleEditor.setState((state) => {
              // アップロードした画像を同時にセットする
              state.attachments.unshift(a);
              state.article.contents.thumbnail = a.id;
            });
          }}
        />
        <ModalFull
          buttonTitle="アップロード済みの画像から選択"
          title="画像を選択"
        >
          {({ close }) => (
            <AttachmentEdit
              attachments={attachments}
              attachmentableId={article.id}
              selected={article.contents.thumbnail}
              types={["image"]}
              onSelectAttachment={(attachmentId) => {
                updateContents((draft) => (draft.thumbnail = attachmentId));
                close();
              }}
              onChangeAttachments={(attachments) => {
                useArticleEditor.setState((state) => {
                  state.attachments = attachments;
                });
              }}
            />
          )}
        </ModalFull>
      </div>
    </>
  );
};
