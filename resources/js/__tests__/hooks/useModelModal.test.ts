import { renderHook, act, waitFor } from "@testing-library/react";
import { describe, it, expect, vi, beforeEach } from "vitest";
import { useModelModal } from "@/hooks/useModelModal";
import { useToast } from "@/hooks/useToast";
import type { AxiosError } from "axios";

// useToast のモック
vi.mock("@/hooks/useToast", () => ({
  useToast: vi.fn(),
}));

describe("useModelModal", () => {
  const mockShowSuccess = vi.fn();
  const mockShowError = vi.fn();

  beforeEach(() => {
    vi.clearAllMocks();
    (useToast as ReturnType<typeof vi.fn>).mockReturnValue({
      showSuccess: mockShowSuccess,
      showError: mockShowError,
    });
  });

  it("初期状態でローディングがfalse", () => {
    const { result } = renderHook(() => useModelModal());

    expect(result.current.isLoading).toBe(false);
    expect(result.current.error).toBeNull();
    expect(result.current.errors).toBeNull();
  });

  it("API呼び出しが成功した場合、成功コールバックが実行される", async () => {
    const { result } = renderHook(() => useModelModal());
    const mockData = { id: 1, name: "Test" };
    const mockApiCall = vi.fn().mockResolvedValue(mockData);
    const mockOnSuccess = vi.fn();

    await act(async () => {
      await result.current.handleSave(mockApiCall, {
        onSuccess: mockOnSuccess,
      });
    });

    expect(mockOnSuccess).toHaveBeenCalledWith(mockData);
    expect(result.current.isLoading).toBe(false);
  });

  it("成功メッセージが指定された場合、トーストを表示する", async () => {
    const { result } = renderHook(() => useModelModal());
    const mockApiCall = vi.fn().mockResolvedValue({ id: 1 });

    await act(async () => {
      await result.current.handleSave(mockApiCall, {
        successMessage: "保存しました",
      });
    });

    expect(mockShowSuccess).toHaveBeenCalledWith("保存しました");
  });

  it("バリデーションエラー（422）の場合、validationErrorsが設定される", async () => {
    const { result } = renderHook(() => useModelModal());
    const validationErrors = {
      name: ["名前は必須です"],
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

    await act(async () => {
      await result.current.handleSave(mockApiCall);
    });

    expect(result.current.errors).toEqual(validationErrors);
    expect(result.current.error).toBeNull();
  });

  it("getError でバリデーションエラーを取得できる", async () => {
    const { result } = renderHook(() => useModelModal());
    const mockError: Partial<AxiosError> = {
      isAxiosError: true,
      response: {
        status: 422,
        data: { errors: { name: ["名前は必須です"] } },
        statusText: "Unprocessable Entity",
        headers: {},
        config: {} as never,
      },
    };
    const mockApiCall = vi.fn().mockRejectedValue(mockError);

    await act(async () => {
      await result.current.handleSave(mockApiCall);
    });

    expect(result.current.getError("name")).toEqual(["名前は必須です"]);
  });

  it("一般的なエラーの場合、errorが設定される", async () => {
    const { result } = renderHook(() => useModelModal());
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
      await result.current.handleSave(mockApiCall);
    });

    expect(result.current.error).toBe("サーバーエラー");
    expect(result.current.errors).toBeNull();
    expect(mockShowError).toHaveBeenCalledWith("サーバーエラー");
  });

  it("setError で汎用エラーを設定できる", () => {
    const { result } = renderHook(() => useModelModal());

    act(() => {
      result.current.setError("カスタムエラー");
    });

    expect(result.current.error).toBe("カスタムエラー");
    expect(result.current.getError()).toBe("カスタムエラー");
  });

  it("silent=trueの場合、エラートーストを表示しない", async () => {
    const { result } = renderHook(() => useModelModal());
    const mockError = new Error("エラー");
    const mockApiCall = vi.fn().mockRejectedValue(mockError);

    await act(async () => {
      await result.current.handleSave(mockApiCall, {
        silent: true,
      });
    });

    expect(mockShowError).not.toHaveBeenCalled();
    expect(result.current.error).not.toBeNull();
  });

  it("API呼び出し中はisLoadingがtrueになる", async () => {
    const { result } = renderHook(() => useModelModal());
    let resolvePromise: (value: { id: number }) => void;
    const mockApiCall = vi.fn(
      () =>
        new Promise<{ id: number }>((resolve) => {
          resolvePromise = resolve;
        })
    );

    expect(result.current.isLoading).toBe(false);

    // API呼び出し開始
    let callPromise: Promise<void>;
    act(() => {
      callPromise = result.current.handleSave(mockApiCall);
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

  it("エラー時のコールバックが実行される", async () => {
    const { result } = renderHook(() => useModelModal());
    const mockError = new Error("エラー");
    const mockApiCall = vi.fn().mockRejectedValue(mockError);
    const mockOnError = vi.fn();

    await act(async () => {
      await result.current.handleSave(mockApiCall, {
        onError: mockOnError,
      });
    });

    expect(mockOnError).toHaveBeenCalledWith(mockError);
  });

  it("setValidationErrors でバリデーションエラーを設定できる", () => {
    const { result } = renderHook(() => useModelModal());
    const errors = { name: ["名前は必須です"] };

    act(() => {
      result.current.setValidationErrors(errors);
    });

    expect(result.current.errors).toEqual(errors);
    expect(result.current.getError("name")).toEqual(["名前は必須です"]);
  });
});
