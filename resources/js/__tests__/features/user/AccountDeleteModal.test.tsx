import { render, screen, waitFor } from "@testing-library/react";
import { describe, expect, it, vi, beforeEach } from "vitest";
import userEvent from "@testing-library/user-event";
import axios from "axios";
import { AccountDeleteModal } from "@/features/user/AccountDeleteModal";
import { ToastProvider } from "@/providers/ToastProvider";

vi.mock("axios");
const mockAxios = axios;

describe("AccountDeleteModal コンポーネント", () => {
  const originalLocation = window.location;

  beforeEach(() => {
    vi.clearAllMocks();
    mockAxios.delete = vi.fn().mockResolvedValue({ data: {} });
    Object.defineProperty(window, "location", {
      configurable: true,
      value: { ...originalLocation, href: "" },
    });
  });

  it("パスワード未入力では退会ボタンが無効", () => {
    render(
      <ToastProvider>
        <AccountDeleteModal onClose={vi.fn()} />
      </ToastProvider>
    );

    expect(screen.getByRole("button", { name: "退会する" })).toBeDisabled();
  });

  it("パスワード入力後に退会するとAPIが呼ばれログイン画面へ遷移する", async () => {
    const user = userEvent.setup();

    render(
      <ToastProvider>
        <AccountDeleteModal onClose={vi.fn()} />
      </ToastProvider>
    );

    await user.type(screen.getByLabelText("パスワード"), "password");
    await user.click(screen.getByRole("button", { name: "退会する" }));

    await waitFor(() => {
      expect(mockAxios.delete).toHaveBeenCalledWith("/mypage/account", {
        data: { current_password: "password" },
      });
      expect(window.location.href).toBe("/login");
    });
  });

  it("キャンセルボタンクリックでonCloseが呼ばれる", async () => {
    const user = userEvent.setup();
    const onClose = vi.fn();

    render(
      <ToastProvider>
        <AccountDeleteModal onClose={onClose} />
      </ToastProvider>
    );

    await user.click(screen.getByRole("button", { name: "キャンセル" }));

    expect(onClose).toHaveBeenCalled();
  });

  it("パスワード誤りのバリデーションエラーが表示される", async () => {
    mockAxios.delete = vi.fn().mockRejectedValue({
      isAxiosError: true,
      response: {
        status: 422,
        data: { errors: { current_password: ["パスワードが正しくありません"] } },
      },
    });
    const user = userEvent.setup();

    render(
      <ToastProvider>
        <AccountDeleteModal onClose={vi.fn()} />
      </ToastProvider>
    );

    await user.type(screen.getByLabelText("パスワード"), "wrong-password");
    await user.click(screen.getByRole("button", { name: "退会する" }));

    expect(
      await screen.findByText("パスワードが正しくありません")
    ).toBeInTheDocument();
  });
});
