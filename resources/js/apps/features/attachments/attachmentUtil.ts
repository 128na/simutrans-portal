import { compareAsc, parseISO } from "date-fns";
export const compareAttachmentValues = (a: unknown, b: unknown): number => {
  // 日付文字列
  if (typeof a === "string" && /^\d{4}-\d{2}-\d{2}/.test(a)) {
    try {
      return compareAsc(parseISO(a as string), parseISO(b as string));
    } catch {
      // 日付変換失敗時は単純文字比較
      return (a as string).localeCompare(b as string);
    }
  }

  // 文字列
  if (typeof a === "string" && typeof b === "string") {
    return a.localeCompare(b);
  }

  // 数値
  if (typeof a === "number" && typeof b === "number") {
    return a - b;
  }

  return 0;
};

export const attachmentFilter = <T extends Attachment>(
  attachments: T[],
  criteria: string,
  types: AttachmentType[],
  attachmentableId: number | null,
  attachmentableType: AttachmentableType | null,
): T[] => {
  const q = criteria.toLowerCase();
  return attachments.filter((a) => {
    if (types?.includes(a.type) === false) {
      return false;
    }
    // 添付先が指定されているときは他記事に紐づくものを除外する
    if (
      attachmentableId &&
      a.attachmentable_id &&
      !(
        attachmentableType === a.attachmentable_type &&
        a.attachmentable_id === attachmentableId
      )
    ) {
      return false;
    }
    return a.original_name.toLowerCase().includes(q);
  });
};

export const displaySize = (bytes: number): string => {
  const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
  if (bytes === 0) return "0 Byte";
  const i = Math.floor(Math.log(bytes) / Math.log(1024));
  return Math.round(bytes / Math.pow(1024, i)) + " " + sizes[i];
};
