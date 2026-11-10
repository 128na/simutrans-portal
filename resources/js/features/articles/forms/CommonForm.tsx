import { useRef } from "react";
import { ArticleUpload } from "@/features/articles/forms/ArticleUpload";
import { useArticleEditor, useIsSlugUpdated } from "@/hooks/useArticleEditor";
import TextSub from "@/components/ui/TextSub";
import TextError from "@/components/ui/TextError";
import { useAxiosError } from "@/hooks/useAxiosError";
import { ModalFull } from "@/components/ui/ModalFull";
import { AttachmentEdit } from "@/features/attachments/AttachmentEdit";
import { FormCaption } from "@/components/ui/FormCaption";
import Input from "@/components/ui/Input";
import TextBadge from "@/components/ui/TextBadge";
import Button from "@/components/ui/Button";
import Checkbox from "@/components/ui/Checkbox";

const regReplace =
  /(!|"|#|\$|%|&|'|\(|\)|\*|\+|,|\/|:|;|<|=|>|\?|@|\[|\\|\]|\^|`|\{|\||\}|\s|\.)+/gi;

const generateSlug = (title: string) => {
  if (!title.trim()) return "";

  const cleaned = title
    .toLowerCase()
    .replace(regReplace, "-")
    .replace(/^-+|-+$/g, "");
  const candidate = cleaned
    ? Number.isNaN(Number(cleaned))
      ? cleaned
      : "article-" + cleaned
    : "article";
  let encoded = "";
  for (const character of candidate) {
    const next = encodeURI(character);
    if (encoded.length + next.length > 255) break;
    encoded += next;
  }
  return encoded;
};

export const CommonForm = () => {
  const article = useArticleEditor((s) => s.article);
  const slugManuallyEdited = useRef(false);
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
          <TextBadge variant="danger">必須</TextBadge>
          タイトル
        </FormCaption>
        <TextError>{getError("article.title")}</TextError>
        <Input
          className="w-full"
          aria-label="タイトル"
          value={article.title || ""}
          required
          maxLength={255}
          onChange={(e) => {
            const title = e.target.value;
            update((draft) => {
              draft.title = title;
              if (!draft.id && !slugManuallyEdited.current) {
                draft.slug = generateSlug(title);
              }
            });
          }}
        />
      </div>

      <div>
        <FormCaption>
          <TextBadge variant="danger">必須</TextBadge>
          記事URL
        </FormCaption>
        <TextError>{getError("article.slug")}</TextError>
        <Input
          className="w-full"
          aria-label="記事URL"
          value={decodeURI(article.slug || "")}
          required
          maxLength={255}
          counter={(value) => [...escape(value)].length}
          onChange={(e) => {
            slugManuallyEdited.current = true;
            update((draft) => (draft.slug = escape(e.target.value)));
          }}
        />
        <TextSub className="my-1">
          URLプレビュー: /users/{user.nickname ?? user.id}/{article.slug || ""}
        </TextSub>
        <Button
          variant="subOutline"
          disabled={!article.title}
          onClick={() => {
            slugManuallyEdited.current = true;
            update((draft) => (draft.slug = generateSlug(draft.title || "")));
          }}
        >
          タイトルから入力
        </Button>
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
        <ArticleUpload
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
