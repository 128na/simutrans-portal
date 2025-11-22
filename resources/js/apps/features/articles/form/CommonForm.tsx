import { Upload } from "@/apps/components/form/Upload";
import Input from "@/apps/components/ui/Input";
import Label from "@/apps/components/ui/Label";
import TextBadge from "@/apps/components/ui/TextBadge";
import {
  useArticleEditor,
  useIsSlugUpdated,
} from "@/apps/state/useArticleEditor";
import TextSub from "@/apps/components/ui/TextSub";
import TextError from "@/apps/components/ui/TextError";
import { useAxiosError } from "@/apps/state/useAxiosError";
import { ModalFull } from "@/apps/components/ui/ModalFull";
import { AttachmentEdit } from "../../attachments/AttachmentEdit";
import Checkbox from "@/apps/components/ui/Checkbox";

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

  return (
    <>
      <Input
        labelClassName="font-medium"
        className="font-normal"
        value={article.title || ""}
        onChange={(e) => update((draft) => (draft.title = e.target.value))}
      >
        <TextBadge color="red">必須</TextBadge>
        タイトル
        <TextError className="mb-2">{getError("article.title")}</TextError>
      </Input>

      <div>
        <Input
          labelClassName="font-medium"
          className="font-normal"
          value={decodeURI(article.slug || "")}
          onChange={(e) =>
            update((draft) => {
              const replaced = e.target.value
                .toLowerCase()
                .replace(regReplace, "-");
              draft.slug = encodeURI(replaced);
            })
          }
        >
          <TextBadge color="red">必須</TextBadge>
          記事URL
          <TextError className="mb-2">{getError("article.slug")}</TextError>
        </Input>
        <TextSub>
          URLプレビュー: /users/{user.nickname ?? user.id}/{article.slug || ""}
        </TextSub>
      </div>
      {isSlugUpdated && (
        <div>
          <Label className="font-medium mb-1">リダイレクト設定</Label>
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
        <Label className="font-medium">
          サムネイル
          <TextError className="mb-2">
            {getError("article.contents.thumbnail")}
          </TextError>
        </Label>
        <TextSub>
          {(article.contents.thumbnail &&
            attachments.find((a) => a.id === article.contents.thumbnail)
              ?.original_name) ??
            "未選択"}
        </TextSub>
        <div className="space-x-2">
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
