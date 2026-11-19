import { format } from "date-fns";
import { Upload } from "@/components/form/Upload";
import { ModalFull } from "@/components/ui/ModalFull";
import { AttachmentEdit } from "./AttachmentEdit";
import { displaySize } from "./attachmentUtil";

type Kind = "file" | "image";

const labels = {
  file: {
    current: "現在のファイル",
    empty: "ファイルが未選択です",
    noun: "ファイル",
    upload: "新しいファイルをアップロード",
    modalTitle: "ファイルを選択",
  },
  image: {
    current: "現在の画像",
    empty: "未設定",
    noun: "画像",
    upload: "新しい画像をアップロード",
    modalTitle: "画像を選択",
  },
} as const satisfies Record<Kind, Record<string, string>>;

type Props = {
  kind: Kind;
  attachments: Attachment.MypageEdit[];
  selected: number | null;
  attachmentableId: number | null;
  attachmentableType?: AttachmentableType;
  onUploaded: (attachment: Attachment.MypageEdit) => void;
  onSelectAttachment: (attachmentId: number | null) => void;
  onChangeAttachments: (attachments: Attachment.MypageEdit[]) => void;
  onUploadingChange?: (uploading: boolean) => void;
  onUploadError?: (error: unknown) => void;
};

/**
 * 添付ファイル・画像の選択欄。設定済みの内容を枠で示し、新しいアップロードとアップロード済みからの変更を並べる。
 */
export const AttachmentField = ({
  kind,
  attachments,
  selected,
  attachmentableId,
  attachmentableType,
  onUploaded,
  onSelectAttachment,
  onChangeAttachments,
  onUploadingChange,
  onUploadError,
}: Props) => {
  const label = labels[kind];

  return (
    <div className="grid gap-2">
      <CurrentAttachment
        kind={kind}
        attachment={attachments.find((a) => a.id === selected)}
        selected={selected}
      />
      <div className="flex flex-wrap gap-2">
        <Upload
          label={label.upload}
          accept={kind === "image" ? "image/*" : undefined}
          onUploaded={onUploaded}
          onUploadingChange={onUploadingChange}
          onUploadError={onUploadError}
        />
        <ModalFull
          buttonTitle="アップロード済みから変更"
          title={label.modalTitle}
        >
          {({ close }) => (
            <AttachmentEdit
              attachments={attachments}
              attachmentableId={attachmentableId}
              attachmentableType={attachmentableType}
              selected={selected}
              types={[kind]}
              onSelectAttachment={(attachmentId) => {
                onSelectAttachment(attachmentId);
                close();
              }}
              onChangeAttachments={onChangeAttachments}
            />
          )}
        </ModalFull>
      </div>
    </div>
  );
};

const fileDetail = (attachment: Attachment.MypageEdit): string => {
  const details: string[] = [];
  if (typeof attachment.size === "number") {
    details.push(displaySize(attachment.size));
  }
  const uploadedAt = attachment.created_at
    ? new Date(attachment.created_at)
    : null;
  if (uploadedAt && !Number.isNaN(uploadedAt.getTime())) {
    details.push(`アップロード日 ${format(uploadedAt, "yyyy/MM/dd H:mm")}`);
  }
  return details.join(" / ");
};

type CurrentProps = {
  kind: Kind;
  attachment: Attachment.MypageEdit | undefined;
  selected: number | null;
};

const CurrentAttachment = ({ kind, attachment, selected }: CurrentProps) => {
  const label = labels[kind];

  return (
    <div
      role="group"
      aria-label={label.current}
      aria-live="polite"
      className="rounded-lg border border-c-sub/30 bg-c-sub/3 p-3 text-sm"
    >
      <div className="v2-text-sub text-xs mb-1">{label.current}</div>
      {attachment ? (
        kind === "image" ? (
          <div className="flex items-center gap-3 min-w-0">
            <img
              src={attachment.thumbnail || "/storage/default/image.png"}
              alt=""
              className="h-16 w-24 shrink-0 object-cover rounded"
            />
            <span className="break-all">{attachment.original_name}</span>
          </div>
        ) : (
          <div className="min-w-0">
            <div className="break-all font-bold">
              {attachment.original_name}
            </div>
            <div className="v2-text-sub">{fileDetail(attachment)}</div>
          </div>
        )
      ) : selected !== null ? (
        <div className="v2-text-sub">
          設定済みの{label.noun}（ID: {selected}）の詳細を表示できません
        </div>
      ) : (
        <div className="v2-text-sub">{label.empty}</div>
      )}
    </div>
  );
};
