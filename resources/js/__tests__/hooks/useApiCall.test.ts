import { renderHook, act, waitFor } from "@testing-library/react";
import { describe, it, expect, vi, beforeEach } from "vitest";
import { useApiCall } from "@/hooks/useApiCall";
import { useToast } from "@/hooks/useToast";
import type { AxiosError } from "axios";

// useToast のモック
vi.mock("@/hooks/useToast", () => ({
  useToast: vi.fn(),
}));

describe("useApiCall", () => {
  const mockShowSuccess = vi.fn();
  const mockShowError = vi.fn();

  beforeEach(() => {
    vi.clearAllMocks();
    (useToast as ReturnType<typeof vi.fn>).mockReturnValue({
      showSuccess: mockShowSuccess,
      showError: mockShowError,
    });
  });

  it("API呼び出しが成功した場合、データを返す", async () => {
    const { result } = renderHook(() => useApiCall());
    const mockData = { id: 1, name: "Test" };
    const mockApiCall = vi.fn().mockResolvedValue(mockData);

    let apiResult;
    await act(async () => {
      apiResult = await result.current.call(mockApiCall);
    });

    expect(apiResult).toEqual({
      data: mockData,
      hasError: false,
    });
    expect(mockApiCall).toHaveBeenCalledTimes(1);
  });

  it("成功メッセージが指定された場合、トーストを表示する", async () => {
    const { result } = renderHook(() => useApiCall());
    const mockApiCall = vi.fn().mockResolvedValue({ id: 1 });

    await act(async () => {
      await result.current.call(mockApiCall, {
        successMessage: "保存しました",
      });
    });

    expect(mockShowSuccess).toHaveBeenCalledWith("保存しました");
  });

  it("成功時のコールバックが実行される", async () => {
    const { result } = renderHook(() => useApiCall());
    const mockData = { id: 1 };
    const mockApiCall = vi.fn().mockResolvedValue(mockData);
    const mockOnSuccess = vi.fn();

    await act(async () => {
      await result.current.call(mockApiCall, {
        onSuccess: mockOnSuccess,
      });
    });

    expect(mockOnSuccess).toHaveBeenCalledWith(mockData);
  });

  it("バリデーションエラー（422）の場合、validationErrorsを返す", async () => {
    const { result } = renderHook(() => useApiCall());
    const validationErrors = {
      title: ["タイトルは必須です"],
      email: ["正しいメールアドレスを入力してください"],
    };
    const mockError: Partial<AxiosError> = {
      isAxiosError: true,
      response: {
        status: 422,
        data: { errors: validationErrors },
        statusText: "Unprocessable Entity",
        headers: {},
        config: {} as never,
      },
    };
    const mockApiCall = vi.fn().mockRejectedValue(mockError);

    let apiResult;
    await act(async () => {
      apiResult = await result.current.call(mockApiCall);
    });

    expect(apiResult).toEqual({
      validationErrors,
      hasError: true,
    });
    // バリデーションエラーではトースト通知は表示しない
    expect(mockShowError).not.toHaveBeenCalled();
  });

  it("一般的なエラーの場合、エラートーストを表示する", async () => {
    const { result } = renderHook(() => useApiCall());
    const mockError: Partial<AxiosError> = {
      isAxiosError: true,
      response: {
        status: 500,
        data: { message: "サーバーエラー" },
        statusText: "Internal Server Error",
        headers: {},
        config: {} as never,
      },
    };
    const mockApiCall = vi.fn().mockRejectedValue(mockError);

    await act(async () => {
      await result.current.call(mockApiCall);
    });

    expect(mockShowError).toHaveBeenCalledWith("サーバーエラー");
  });

  it("エラー時のコールバックが実行される", async () => {
    const { result } = renderHook(() => useApiCall());
    const mockError = new Error("エラー");
    const mockApiCall = vi.fn().mockRejectedValue(mockError);
    const mockOnError = vi.fn();

    await act(async () => {
      await result.current.call(mockApiCall, {
        onError: mockOnError,
      });
    });

    expect(mockOnError).toHaveBeenCalledWith(mockError);
  });

  it("silent=trueの場合、トースト通知を表示しない", async () => {
    const { result } = renderHook(() => useApiCall());
    const mockApiCall = vi.fn().mockResolvedValue({ id: 1 });

    await act(async () => {
      await result.current.call(mockApiCall, {
        successMessage: "成功",
        silent: true,
      });
    });

    expect(mockShowSuccess).not.toHaveBeenCalled();
  });

  it("エラー時にsilent=trueの場合、エラートーストを表示しない", async () => {
    const { result } = renderHook(() => useApiCall());
    const mockError = new Error("エラー");
    const mockApiCall = vi.fn().mockRejectedValue(mockError);

    await act(async () => {
      await result.current.call(mockApiCall, {
        silent: true,
      });
    });

    expect(mockShowError).not.toHaveBeenCalled();
  });

  it("API呼び出し中はisLoadingがtrueになる", async () => {
    const { result } = renderHook(() => useApiCall());
    let resolvePromise: (value: { id: number }) => void;
    const mockApiCall = vi.fn(
      () =>
        new Promise<{ id: number }>((resolve) => {
          resolvePromise = resolve;
        })
    );

    expect(result.current.isLoading).toBe(false);

    // API呼び出し開始（awaitせずに実行）
    let callPromise: Promise<unknown>;
    act(() => {
      callPromise = result.current.call(mockApiCall);
    });

    // isLoadingがtrueになるまで待つ
    await waitFor(() => {
      expect(result.current.isLoading).toBe(true);
    });

    // API呼び出し完了
    act(() => {
      resolvePromise!({ id: 1 });
    });

    // 完了後
    await callPromise!;
    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });
  });
});
