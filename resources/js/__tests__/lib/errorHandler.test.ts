import { beforeEach, describe, expect, it, vi } from "vitest";
import {
  AppError,
  extractErrorMessage,
  handleError,
  handleErrorSilent,
  isAxiosError,
  isValidationError,
} from "@/lib/errorHandler";
import { AxiosError, type AxiosResponse, type InternalAxiosRequestConfig } from "axios";

// logger モジュールをモック
vi.mock("@/utils/logger", () => ({
  logger: {
    debug: vi.fn(),
    error: vi.fn(),
    warn: vi.fn(),
  },
}));

describe("errorHandler", () => {
  let alertMock: ReturnType<typeof vi.fn>;

  beforeEach(() => {
    vi.clearAllMocks();
    alertMock = vi.fn();
    vi.stubGlobal("alert", alertMock);
  });

  describe("AppError", () => {
    it("メッセージを持つエラーを作成できる", () => {
      const error = new AppError("テストエラー");
      expect(error.message).toBe("テストエラー");
      expect(error.name).toBe("AppError");
    });

    it("コードとステータスコードを持つエラーを作成できる", () => {
      const error = new AppError("テストエラー", "ERR_001", 400);
      expect(error.message).toBe("テストエラー");
      expect(error.code).toBe("ERR_001");
      expect(error.statusCode).toBe(400);
    });

    it("Errorを継承している", () => {
      const error = new AppError("テストエラー");
      expect(error).toBeInstanceOf(Error);
    });
  });

  describe("extractErrorMessage", () => {
    it("AppErrorからメッセージを抽出できる", () => {
      const error = new AppError("アプリエラー");
      expect(extractErrorMessage(error)).toBe("アプリエラー");
    });

    it("通常のErrorからメッセージを抽出できる", () => {
      const error = new Error("通常エラー");
      expect(extractErrorMessage(error)).toBe("通常エラー");
    });

    it("AxiosErrorのネットワークエラーを適切に処理する", () => {
      const error = createAxiosError(undefined, undefined);
      expect(extractErrorMessage(error)).toBe(
        "ネットワークエラーが発生しました。接続を確認してください"
      );
    });

    it("AxiosError 401を適切に処理する", () => {
      const error = createAxiosError(401, {});
      expect(extractErrorMessage(error)).toBe(
        "認証が必要です。ログインしてください"
      );
    });

    it("AxiosError 403を適切に処理する", () => {
      const error = createAxiosError(403, {});
      expect(extractErrorMessage(error)).toBe(
        "この操作を行う権限がありません"
      );
    });

    it("AxiosError 404を適切に処理する", () => {
      const error = createAxiosError(404, {});
      expect(extractErrorMessage(error)).toBe(
        "リソースが見つかりませんでした"
      );
    });

    it("AxiosError 422（バリデーションエラー）を適切に処理する", () => {
      const error = createAxiosError(422, {
        errors: { name: ["必須です"] },
        message: "入力エラー",
      });
      expect(extractErrorMessage(error)).toBe("入力エラー");
    });

    it("AxiosError 500を適切に処理する", () => {
      const error = createAxiosError(500, {});
      expect(extractErrorMessage(error)).toBe("サーバーエラーが発生しました");
    });

    it("サーバーからのメッセージを抽出する", () => {
      const error = createAxiosError(400, { message: "カスタムエラー" });
      expect(extractErrorMessage(error)).toBe("カスタムエラー");
    });

    it("不明なエラーにデフォルトメッセージを返す", () => {
      expect(extractErrorMessage("文字列エラー")).toBe(
        "予期しないエラーが発生しました"
      );
      expect(extractErrorMessage(null)).toBe(
        "予期しないエラーが発生しました"
      );
      expect(extractErrorMessage(undefined)).toBe(
        "予期しないエラーが発生しました"
      );
    });
  });

  describe("isAxiosError", () => {
    it("AxiosErrorをtrueで判定する", () => {
      const error = createAxiosError(400, {});
      expect(isAxiosError(error)).toBe(true);
    });

    it("通常のErrorをfalseで判定する", () => {
      const error = new Error("test");
      expect(isAxiosError(error)).toBe(false);
    });

    it("nullをfalseで判定する", () => {
      expect(isAxiosError(null)).toBe(false);
    });

    it("undefinedをfalseで判定する", () => {
      expect(isAxiosError(undefined)).toBe(false);
    });
  });

  describe("isValidationError", () => {
    it("422エラーをtrueで判定する", () => {
      const error = createAxiosError(422, {
        errors: { field: ["error"] },
        message: "Validation failed",
      });
      expect(isValidationError(error)).toBe(true);
    });

    it("400エラーをfalseで判定する", () => {
      const error = createAxiosError(400, {});
      expect(isValidationError(error)).toBe(false);
    });

    it("通常のErrorをfalseで判定する", () => {
      const error = new Error("test");
      expect(isValidationError(error)).toBe(false);
    });
  });

  describe("handleError", () => {
    it("エラーをログに記録し、alertを表示する", () => {
      const error = new Error("テストエラー");
      handleError(error);

      expect(alertMock).toHaveBeenCalledWith("テストエラー");
    });

    it("コンテキストを含めてエラーを処理する", () => {
      const error = new Error("テストエラー");
      handleError(error, { component: "TestComponent", action: "save" });

      expect(alertMock).toHaveBeenCalledWith("テストエラー");
    });

    it("サイレントモードではalertを表示しない", () => {
      const error = new Error("テストエラー");
      handleError(error, { silent: true });

      expect(alertMock).not.toHaveBeenCalled();
    });
  });

  describe("handleErrorSilent", () => {
    it("alertを表示しない", () => {
      const error = new Error("テストエラー");
      handleErrorSilent(error);

      expect(alertMock).not.toHaveBeenCalled();
    });

    it("コンテキストを含めて処理できる", () => {
      const error = new Error("テストエラー");
      handleErrorSilent(error, { component: "TestComponent" });

      expect(alertMock).not.toHaveBeenCalled();
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

  const error = new AxiosError("Request failed", "ERR_BAD_REQUEST", config, null, response);
  return error;
}
