import { renderHook } from "@testing-library/react";
import { describe, expect, it, vi, beforeEach } from "vitest";
import { useErrorHandler } from "@/hooks/useErrorHandler";
import {
  AxiosError,
  type AxiosResponse,
  type InternalAxiosRequestConfig,
} from "axios";

// logger モジュールをモック
vi.mock("@/utils/logger", () => ({
  logger: {
    debug: vi.fn(),
    error: vi.fn(),
    warn: vi.fn(),
  },
}));

describe("useErrorHandler", () => {
  let alertMock: ReturnType<typeof vi.fn>;

  beforeEach(() => {
    vi.clearAllMocks();
    alertMock = vi.fn();
    vi.stubGlobal("alert", alertMock);
  });

  describe("handleErrorWithContext", () => {
    it("エラーを処理してalertを表示する", () => {
      const { result } = renderHook(() => useErrorHandler());
      const error = new Error("テストエラー");

      result.current.handleErrorWithContext(error);

      expect(alertMock).toHaveBeenCalledWith("テストエラー");
    });

    it("デフォルトコンテキストとマージされる", () => {
      const { result } = renderHook(() =>
        useErrorHandler({ component: "TestComponent" })
      );
      const error = new Error("テストエラー");

      result.current.handleErrorWithContext(error, { action: "save" });

      expect(alertMock).toHaveBeenCalledWith("テストエラー");
    });
  });

  describe("handleSilent", () => {
    it("alertを表示しない", () => {
      const { result } = renderHook(() => useErrorHandler());
      const error = new Error("テストエラー");

      result.current.handleSilent(error);

      expect(alertMock).not.toHaveBeenCalled();
    });
  });

  describe("getMessage", () => {
    it("Errorからメッセージを取得する", () => {
      const { result } = renderHook(() => useErrorHandler());
      const error = new Error("テストエラー");

      expect(result.current.getMessage(error)).toBe("テストエラー");
    });

    it("AxiosErrorからメッセージを取得する", () => {
      const { result } = renderHook(() => useErrorHandler());
      const error = createAxiosError(422, { message: "バリデーションエラー" });

      expect(result.current.getMessage(error)).toBe("バリデーションエラー");
    });
  });

  describe("isValidation", () => {
    it("422エラーをtrueで判定する", () => {
      const { result } = renderHook(() => useErrorHandler());
      const error = createAxiosError(422, {
        errors: { field: ["error"] },
        message: "Validation failed",
      });

      expect(result.current.isValidation(error)).toBe(true);
    });

    it("400エラーをfalseで判定する", () => {
      const { result } = renderHook(() => useErrorHandler());
      const error = createAxiosError(400, {});

      expect(result.current.isValidation(error)).toBe(false);
    });

    it("通常のErrorをfalseで判定する", () => {
      const { result } = renderHook(() => useErrorHandler());
      const error = new Error("test");

      expect(result.current.isValidation(error)).toBe(false);
    });
  });
});

/**
 * テスト用のAxiosErrorを作成するヘルパー関数
 */
function createAxiosError(
  status: number | undefined,
  data: unknown
): AxiosError {
  const config: InternalAxiosRequestConfig = {
    url: "/test",
    headers: {} as InternalAxiosRequestConfig["headers"],
  };

  const response: AxiosResponse | undefined = status
    ? {
        data,
        status,
        statusText: "Error",
        headers: {},
        config,
      }
    : undefined;

  const error = new AxiosError(
    "Request failed",
    "ERR_BAD_REQUEST",
    config,
    null,
    response
  );
  return error;
}
