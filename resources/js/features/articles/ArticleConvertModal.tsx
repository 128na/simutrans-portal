import { useCallback, useEffect, useRef, useState } from "react";
import axios from "axios";
import { Modal } from "@/components/ui/Modal";
import { ModalFull } from "@/components/ui/ModalFull";
import Button from "@/components/ui/Button";
import Checkbox from "@/components/ui/Checkbox";
import { Upload } from "@/components/form/Upload";
import { AttachmentEdit } from "@/features/attachments/AttachmentEdit";
import { useApiCall } from "@/hooks/useApiCall";
import { extractErrorMessage } from "@/lib/errorHandler";

type Props = {
  article: Article.MypageShow;
  onClose: () => void;
  onSuccess: (id: number) => void;
};

export const ArticleConvertModal = ({ article, onClose, onSuccess }: Props) => {
  const [attachments, setAttachments] = useState<Attachment.MypageEdit[]>([]);
  const [file, setFile] = useState<number | null>(null);
  const [withoutUpdateModifiedAt, setWithoutUpdateModifiedAt] = useState(false);
  const [loading, setLoading] = useState(true);
  const [uploading, setUploading] = useState(false);
  const [loaded, setLoaded] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const busyRef = useRef(false);
  const { call, isLoading } = useApiCall();
  const busy = loading || uploading || isLoading;

  const load = useCallback(() => {
    return axios
      .get<{
        attachments: Attachment.MypageEdit[];
      }>(`/api/v2/articles/${article.id}/convert-to-post`)
      .then((response) => {
        setAttachments(response.data.attachments);
        setLoaded(true);
      })
      .catch((error: unknown) => {
        setError(extractErrorMessage(error));
      })
      .finally(() => {
        setLoading(false);
      });
  }, [article.id]);

  useEffect(() => {
    void load();
  }, [load]);

  const convert = async () => {
    if (busyRef.current || busy || !loaded || file === null) return;
    busyRef.current = true;
    setError(null);
    const result = await call(
      () =>
        axios.post<{ article_id: number }>(
          `/api/v2/articles/${article.id}/convert-to-post`,
          { file, without_update_modified_at: withoutUpdateModifiedAt }
        ),
      { silent: true, onError: (error) => setError(extractErrorMessage(error)) }
    );
    if (result.validationErrors) {
      setError(Object.values(result.validationErrors).flat().join(" "));
    }
    if (result.data) {
      onSuccess(result.data.data.article_id);
    } else {
      busyRef.current = false;
    }
  };

  return (
    <Modal
      title="投稿記事へ変換"
      onClose={() => {
        if (!busy && !busyRef.current) onClose();
      }}
    >
      <p className="mb-4">
        「{article.title}
        」を、当サイトでファイルを配布する投稿記事へ変換します。
      </p>
      <p className="mb-4">
        記事URL、本文、画像、タグ、公開状態は引き継ぎます。外部掲載URLと紹介記事専用の設定は削除されます。公開中の記事は変換直後からファイルが配布されます。
      </p>
      <p className="mb-4">
        既存の投稿ルールに従い、配布するファイルの権利・許可を確認してください。紹介の掲載許可だけでは再配布できません。
      </p>
      {error && (
        <p role="alert" className="text-red-600 mb-4">
          {error}
        </p>
      )}
      {loading && <p role="status">添付ファイルを取得中...</p>}
      {!loading && !loaded && (
        <Button
          onClick={() => {
            setLoading(true);
            setError(null);
            void load();
          }}
        >
          再取得
        </Button>
      )}
      {loaded && (
        <fieldset disabled={busy}>
          <legend>配布ファイル（必須）</legend>
          <Upload
            onUploadingChange={setUploading}
            onUploadError={(error) => setError(extractErrorMessage(error))}
            onUploaded={(attachment) => {
              setAttachments((prev) => [attachment, ...prev]);
              if (attachment.type === "file") {
                setFile(attachment.id);
                setError(null);
              } else {
                setError("配布用のファイルを選択してください。");
              }
            }}
          />
          {uploading && <p role="status">アップロード中...</p>}
          <ModalFull
            buttonTitle="アップロード済みのファイルから選択"
            title="ファイルを選択"
          >
            {({ close }) => (
              <AttachmentEdit
                attachments={attachments}
                attachmentableId={article.id}
                selected={file}
                types={["file"]}
                onChangeAttachments={setAttachments}
                onSelectAttachment={(id) => {
                  setFile(id);
                  close();
                }}
              />
            )}
          </ModalFull>
          <p className="my-4">
            選択中:{" "}
            {attachments.find((a) => a.id === file)?.original_name ?? "未選択"}
          </p>
        </fieldset>
      )}
      <div className="mt-4">
        <Checkbox
          checked={withoutUpdateModifiedAt}
          disabled={busy}
          onChange={(event) => setWithoutUpdateModifiedAt(event.target.checked)}
        >
          更新日時を更新しない
        </Checkbox>
        <p className="text-sm v2-text-sub">
          チェックすると現在の更新日時を維持します。チェックしない場合は変換日時に更新します。
        </p>
      </div>
      <div className="flex gap-2 justify-end mt-4">
        <Button variant="subOutline" disabled={busy} onClick={onClose}>
          キャンセル
        </Button>
        <Button
          disabled={busy || !loaded || file === null}
          onClick={() => void convert()}
        >
          {isLoading ? "変換中..." : "変換する"}
        </Button>
      </div>
    </Modal>
  );
};
