/**
 * UI コンポーネント関連型
 * UI component-related types
 */

export declare namespace Ui {
  type ImageProps = {
    attachmentId: number | null;
    // Uses global Attachment namespace for backward compatibility
    // To avoid circular dependencies, we rely on the global type
    attachments: Attachment.MypageEdit[] | Attachment.Show[];
    defaultUrl?: string;
    openFullSize?: boolean;
    className?: string;
  };
}
