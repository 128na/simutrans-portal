import { Upload } from "@/components/form/Upload";
import Input from "@/components/ui/Input";
import TextBadge from "@/components/ui/TextBadge";
import { useArticleEditor, useIsSlugUpdated } from "@/hooks/useArticleEditor";
import TextSub from "@/components/ui/TextSub";
import TextError from "@/components/ui/TextError";
import { useAxiosError } from "@/hooks/useAxiosError";
import { ModalFull } from "@/components/ui/ModalFull";
import { AttachmentEdit } from "@/features/attachments/AttachmentEdit";
import Checkbox from "@/components/ui/Checkbox";
import ButtonOutline from "@/components/ui/ButtonOutline";
import { FormCaption } from "@/components/ui/FormCaption";

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
          <TextBadge className="bg-c-danger">必須</TextBadge>
          タイトル
        </FormCaption>
        <TextError>{getError("article.title")}</TextError>
        <Input
          labelClassName="font-medium"
          className="font-normal"
          value={article.title || ""}
          onChange={(e) => update((draft) => (draft.title = e.target.value))}
        />
      </div>

      <div>
        <FormCaption>
          <TextBadge className="bg-c-danger">必須</TextBadge>
          記事URL
        </FormCaption>
        <TextError>{getError("article.slug")}</TextError>
        <Input
          labelClassName="font-medium"
          className="font-normal"
          value={decodeURI(article.slug || "")}
          onChange={(e) =>
            update((draft) => (draft.slug = escape(e.target.value)))
          }
        />
        <TextSub className="my-1">
          URLプレビュー: /users/{user.nickname ?? user.id}/{article.slug || ""}
        </TextSub>
        <ButtonOutline
          disabled={!article.title}
          onClick={() => {
            update((draft) => (draft.slug = escape(draft.title || "")));
          }}
        >
          タイトルから入力
        </ButtonOutline>
      </div>
      {isSlugUpdated && (
        <div>
          <FormCaption>リダイレクト設定</FormCaption>
          <Checkbox
            checked={followRedirect}
            onChange={() => {
              updateFollowRedirect(!followRedirect);
            }}
          >
            追加する
          </Checkbox>
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
        <TextSub className="mb-1">
          {(article.contents.thumbnail &&
            attachments.find((a) => a.id === article.contents.thumbnail)
              ?.original_name) ??
            "未選択"}
        </TextSub>
        <div className="space-x-2 mb-2">
          <Upload
            accept="image/*"
            onUploaded={(a) => {
              useArticleEditor.setState((state) => {
                // アップロードした画像を同時にセットする
                state.attachments.unshift(a);
                state.article.contents.thumbnail = a.id;
              });
            }}
          >
            画像をアップロード
          </Upload>
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
      </div>
    </>
  );
};
