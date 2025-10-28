import { compareAsc, parseISO } from "date-fns";
export const compareTagValues = (a: unknown, b: unknown): number => {
  // null を末尾扱いにする
  if (a == null && b == null) return 0;
  if (a == null) return 1;
  if (b == null) return -1;

  // SimpleUser型
  if (typeof a === "object" && typeof b === "object") {
    const aUser = a as SimpleUser;
    const bUser = b as SimpleUser;
    return aUser.name.localeCompare(bUser.name);
  }

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

export const tagFilter = (tags: Tag[], criteria: string): Tag[] => {
  const q = criteria.toLowerCase();
  return tags.filter((t) => {
    return (
      t.name.toLowerCase().includes(q) ||
      t.description?.toLowerCase().includes(q) ||
      t.created_by?.name.toLowerCase().includes(q) ||
      t.last_modified_by?.name.toLowerCase().includes(q)
    );
  });
};
