import { useEffect, useRef, useState } from "react";
import { format } from "date-fns";
import { Upload, useUploadFile } from "@/components/form/Upload";
import { ModalFull } from "@/components/ui/ModalFull";
import TextError from "@/components/ui/TextError";
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
    reject: "このファイルはアップロードできません",
  },
  image: {
    current: "現在の画像",
    empty: "未設定",
    noun: "画像",
    upload: "新しい画像をアップロード",
    modalTitle: "画像を選択",
    reject: "画像ファイルのみアップロードできます",
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

type DragState = "none" | "accept" | "reject";

/** ページ内の文字や画像ではなく、ファイルのドラッグかどうか */
const isFileDrag = (dataTransfer: DataTransfer | null): boolean =>
  Array.from(dataTransfer?.types ?? []).includes("Files");

const acceptsType = (kind: Kind, type: string): boolean =>
  kind !== "image" || type.startsWith("image/");

/**
 * 種類が空のファイルを画像として受け付ける拡張子。
 * サーバーが受け付ける拡張子（SafeAttachment::ALLOWED_EXTENSIONS）のうち、画像として保存されるもの（ImageFormat）だけ。
 */
const IMAGE_EXTENSIONS: readonly string[] = ["png", "jpg", "jpeg", "jfif"];

const extensionOf = (name: string): string => {
  const dot = name.lastIndexOf(".");
  return dot < 0 ? "" : name.slice(dot + 1).toLowerCase();
};

/**
 * ドロップされたファイルを受け付けるか。環境によって heic などは種類が空になるので、そのときは拡張子で判定する。
 */
const acceptsFile = (kind: Kind, file: File): boolean => {
  if (kind !== "image") return true;
  if (file.type) return acceptsType(kind, file.type);
  return IMAGE_EXTENSIONS.includes(extensionOf(file.name));
};

/** 拒否したときのメッセージを出しておく時間 */
const REJECT_MESSAGE_MS = 5000;

/**
 * ドラッグ中に分かる範囲で受け付けるかを決める。種類が分からないときは受け付けとし、ドロップ時に確かめる。
 */
const judgeDrag = (kind: Kind, dataTransfer: DataTransfer): DragState => {
  const item = Array.from(dataTransfer.items ?? []).find(
    (i) => i.kind === "file"
  );
  if (item?.type && !acceptsType(kind, item.type)) {
    return "reject";
  }
  return "accept";
};

/**
 * 受け口の外にファイルを落としたとき、ブラウザがそのファイルを開いて編集中の内容を失わないようにする。
 * 受け口が先に処理したドロップ（defaultPrevented）には触れない。
 */
const preventPageFileDrop = (e: DragEvent) => {
  if (e.defaultPrevented || !isFileDrag(e.dataTransfer)) {
    return;
  }
  e.preventDefault();
  if (e.dataTransfer) {
    e.dataTransfer.dropEffect = "none";
  }
};

/**
 * 添付ファイル・画像の選択欄。設定済みの内容を枠で示し、新しいアップロードとアップロード済みからの変更を並べる。
 * 欄全体にファイルをドロップしてもアップロードできる（受け付けるのは先頭の 1 ファイルだけ）。
 * 画像の欄に画像以外を落としたときは理由を出す。アップロード中はボタンもドロップも受け付けない。
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
  const zoneRef = useRef<HTMLDivElement>(null);
  // 子要素をまたぐたびに dragenter / dragleave が起きるので、入った深さで数える
  const dragDepth = useRef(0);
  const [dragState, setDragState] = useState<DragState>("none");
  // 拒否したときのメッセージ。0 は非表示。拒否するたびに増やし、続けて拒否したときは消すまでの時間を数え直す
  const [rejectNotice, setRejectNotice] = useState(0);
  // ボタンとドロップで同じ状態を使い、どちらかがアップロード中なら両方止める
  const uploader = useUploadFile({
    onUploaded,
    onUploadingChange,
    onUploadError,
  });

  useEffect(() => {
    if (rejectNotice === 0) return;
    const timer = window.setTimeout(
      () => setRejectNotice(0),
      REJECT_MESSAGE_MS
    );
    return () => window.clearTimeout(timer);
  }, [rejectNotice]);

  useEffect(() => {
    window.addEventListener("dragover", preventPageFileDrop);
    window.addEventListener("drop", preventPageFileDrop);
    return () => {
      window.removeEventListener("dragover", preventPageFileDrop);
      window.removeEventListener("drop", preventPageFileDrop);
    };
  }, []);

  /**
   * 保存中などで fieldset ごと無効になっているとき、アップロード中のときは、ボタンと同じくドロップも受け付けない
   */
  const isDisabled = (): boolean =>
    uploader.uploading || zoneRef.current?.closest("fieldset:disabled") != null;

  /**
   * 「アップロード済みから変更」の選択画面は欄の中に描かれるので、そこでのドラッグは受け口として扱わない。
   * 欄そのものがモーダルの中にある場合（投稿記事への変換）は、欄より外側のダイアログなので対象外。
   */
  const isFromInnerDialog = (e: React.DragEvent<HTMLDivElement>): boolean => {
    const dialog =
      e.target instanceof Element ? e.target.closest('[role="dialog"]') : null;
    return dialog !== null && (zoneRef.current?.contains(dialog) ?? false);
  };

  /** 受け口として扱うドラッグか */
  const isZoneDrag = (e: React.DragEvent<HTMLDivElement>): boolean =>
    isFileDrag(e.dataTransfer) && !isFromInnerDialog(e);

  const onDragEnter = (e: React.DragEvent<HTMLDivElement>) => {
    if (!isZoneDrag(e)) return;
    e.preventDefault();
    dragDepth.current += 1;
    setDragState(isDisabled() ? "none" : judgeDrag(kind, e.dataTransfer));
  };

  const onDragOver = (e: React.DragEvent<HTMLDivElement>) => {
    if (!isZoneDrag(e)) return;
    e.preventDefault();
    // dragenter の setState が反映される前にも呼ばれるので、state ではなくその場で判定する
    e.dataTransfer.dropEffect =
      !isDisabled() && judgeDrag(kind, e.dataTransfer) === "accept"
        ? "copy"
        : "none";
  };

  const onDragLeave = (e: React.DragEvent<HTMLDivElement>) => {
    if (!isZoneDrag(e)) return;
    dragDepth.current = Math.max(0, dragDepth.current - 1);
    if (dragDepth.current === 0) {
      setDragState("none");
    }
  };

  const onDrop = (e: React.DragEvent<HTMLDivElement>) => {
    if (!isZoneDrag(e)) return;
    e.preventDefault();
    dragDepth.current = 0;
    setDragState("none");
    if (isDisabled()) return;

    const file = e.dataTransfer.files[0];
    if (!file) return;
    if (!acceptsFile(kind, file)) {
      setRejectNotice((n) => n + 1);
      return;
    }
    setRejectNotice(0);
    void uploader.uploadFile(file);
  };

  return (
    <div
      ref={zoneRef}
      className="relative grid gap-2"
      onDragEnter={onDragEnter}
      onDragOver={onDragOver}
      onDragLeave={onDragLeave}
      onDrop={onDrop}
    >
      {dragState !== "none" && (
        <div
          aria-hidden="true"
          data-testid="attachment-drop-overlay"
          className={`pointer-events-none absolute -inset-1 z-10 flex items-center justify-center rounded-lg border-2 border-dashed bg-white/90 p-2 text-center text-sm font-bold ${
            dragState === "accept"
              ? "border-c-primary text-c-primary"
              : "border-c-sub text-c-sub"
          }`}
        >
          {dragState === "accept"
            ? "ここにドロップしてアップロード"
            : label.reject}
        </div>
      )}
      <CurrentAttachment
        kind={kind}
        attachment={attachments.find((a) => a.id === selected)}
        selected={selected}
      />
      {rejectNotice > 0 && <TextError role="alert">{label.reject}</TextError>}
      {/* ボタンを操作したら、前に拒否したときのメッセージは消す */}
      <div
        className="flex flex-wrap gap-2"
        onClickCapture={() => setRejectNotice(0)}
      >
        <Upload
          label={label.upload}
          accept={kind === "image" ? "image/*" : undefined}
          uploader={uploader}
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
