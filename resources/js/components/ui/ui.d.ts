namespace Ui {
  type ImageProps = {
    attachmentId: number | null;
    attachments: Attachment.MypageEdit[] | Attachment.Show[];
    defaultUrl?: string;
    openFullSize?: boolean;
    original?: boolean;
    className?: string;
  };
}
