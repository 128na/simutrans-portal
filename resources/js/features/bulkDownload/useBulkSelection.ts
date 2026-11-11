import { useCallback, useState } from "react";
import type { BulkItem } from "./bulkDownload";

/**
 * 選択状態はページ送り・別の一覧への移動をまたいで残す（タブを閉じれば消える sessionStorage）
 */
export const SELECTION_STORAGE_KEY = "simutrans-portal:bulk-selection";

const isBulkItem = (value: unknown): value is BulkItem => {
  if (!value || typeof value !== "object") {
    return false;
  }
  const item = value as Record<string, unknown>;
  return (
    typeof item.id === "number" &&
    typeof item.title === "string" &&
    typeof item.url === "string"
  );
};

export const loadSelection = (): BulkItem[] => {
  try {
    const raw = window.sessionStorage.getItem(SELECTION_STORAGE_KEY);
    if (!raw) {
      return [];
    }
    const parsed: unknown = JSON.parse(raw);
    return Array.isArray(parsed) ? parsed.filter(isBulkItem) : [];
  } catch {
    return [];
  }
};

const saveSelection = (items: BulkItem[]): void => {
  try {
    if (items.length === 0) {
      window.sessionStorage.removeItem(SELECTION_STORAGE_KEY);
    } else {
      window.sessionStorage.setItem(
        SELECTION_STORAGE_KEY,
        JSON.stringify(items)
      );
    }
  } catch {
    // 保存できなくても、このページの中では選択を使える
  }
};

/**
 * 選択対象になるのはアドオン投稿（download_url）とアドオン紹介（addon_page_url）だけ
 */
export const isSelectable = (article: Article.Card): boolean =>
  Boolean(article.download_url || article.addon_page_url);

export const toBulkItem = (article: Article.Card): BulkItem => ({
  id: article.id,
  title: article.title,
  url: article.url,
  ...(article.download_url ? { download_url: article.download_url } : {}),
  ...(article.addon_page_url ? { addon_page_url: article.addon_page_url } : {}),
});

export const useBulkSelection = () => {
  const [items, setItems] = useState<BulkItem[]>(loadSelection);

  const update = useCallback((next: BulkItem[]) => {
    setItems(next);
    saveSelection(next);
  }, []);

  const isSelected = useCallback(
    (id: number) => items.some((item) => item.id === id),
    [items]
  );

  const toggle = useCallback(
    (article: Article.Card) => {
      if (!isSelectable(article)) {
        return;
      }
      update(
        items.some((item) => item.id === article.id)
          ? items.filter((item) => item.id !== article.id)
          : [...items, toBulkItem(article)]
      );
    },
    [items, update]
  );

  const clear = useCallback(() => update([]), [update]);

  return { items, isSelected, toggle, clear, replace: update };
};
