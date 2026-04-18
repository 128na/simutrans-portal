import axios from "axios";
import { useState, useEffect, useCallback } from "react";
import { extractErrorMessage } from "@/lib/errorHandler";
import type { MyListShow } from "@/types/models";

/**
 * 自分のマイリスト一覧を取得・管理するフック。
 * AddToMyListModal と MyListIndexPage で共有される。
 */
export const useMyLists = () => {
  const [lists, setLists] = useState<MyListShow[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchLists = useCallback(async () => {
    try {
      setIsLoading(true);
      setError(null);
      const { data } = await axios.get("/api/v1/mylist");
      if (Array.isArray(data.data)) {
        setLists(data.data);
      } else {
        throw new Error("リストの取得に失敗しました");
      }
    } catch (err) {
      setError(extractErrorMessage(err));
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchLists();
  }, [fetchLists]);

  return { lists, setLists, isLoading, error, setError, refetch: fetchLists };
};
