import { beforeEach, describe, expect, it, vi } from "vitest";
import { logger } from "@/utils/logger";

describe("logger", () => {
  beforeEach(() => {
    // Mock console methods
    vi.spyOn(console, "log").mockImplementation(() => {});
    vi.spyOn(console, "error").mockImplementation(() => {});
    vi.spyOn(console, "warn").mockImplementation(() => {});
  });

  describe("debug", () => {
    it("エラーなく呼び出せる", () => {
      expect(() => {
        logger.debug("test message", { data: "value" });
      }).not.toThrow();
    });
  });

  describe("error", () => {
    it("エラーなく呼び出せる", () => {
      const testError = new Error("test error");
      expect(() => {
        logger.error("test message", testError);
      }).not.toThrow();
    });
  });

  describe("warn", () => {
    it("エラーなく呼び出せる", () => {
      expect(() => {
        logger.warn("test warning", { data: "value" });
      }).not.toThrow();
    });
  });
});
