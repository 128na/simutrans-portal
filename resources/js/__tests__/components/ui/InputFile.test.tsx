import InputFile from "@/components/ui/InputFile";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";

describe("InputFile コンポーネント", () => {
  it("アップロードボタンが表示される", () => {
    render(<InputFile />);
    expect(
      screen.getByRole("button", { name: "アップロード" })
    ).toBeInTheDocument();
  });

  it("カスタムボタンテキストが表示される", () => {
    render(<InputFile>ファイルを選択</InputFile>);
    expect(
      screen.getByRole("button", { name: "ファイルを選択" })
    ).toBeInTheDocument();
  });

  it("ファイル入力要素が非表示で存在する", () => {
    const { container } = render(<InputFile />);
    const fileInput = container.querySelector('input[type="file"]');
    expect(fileInput).toBeInTheDocument();
    expect(fileInput).toHaveClass("hidden");
  });

  it("ボタンをクリックするとファイル選択ダイアログが開く", async () => {
    const user = userEvent.setup();
    const { container } = render(<InputFile />);
    const fileInput = container.querySelector('input[type="file"]');
    const clickSpy = vi.spyOn(fileInput as HTMLElement, "click");

    const button = screen.getByRole("button");
    await user.click(button);

    expect(clickSpy).toHaveBeenCalled();
  });

  it("accept 属性が設定される", () => {
    const { container } = render(<InputFile accept="image/*" />);
    const fileInput = container.querySelector('input[type="file"]');
    expect(fileInput).toHaveAttribute("accept", "image/*");
  });

  it("multiple 属性が設定される", () => {
    const { container } = render(<InputFile multiple />);
    const fileInput = container.querySelector('input[type="file"]');
    expect(fileInput).toHaveAttribute("multiple");
  });

  it("onChange ハンドラーが動作する", async () => {
    const user = userEvent.setup();
    let files: FileList | null = null;
    const { container } = render(
      <InputFile onChange={(e) => (files = e.target.files)} />
    );
    const fileInput = container.querySelector(
      'input[type="file"]'
    ) as HTMLInputElement;

    const file = new File(["test"], "test.txt", { type: "text/plain" });
    await user.upload(fileInput, file);

    expect(files).not.toBeNull();
    expect(files?.[0]).toEqual(file);
  });

  it("disabled 状態がファイル入力に適用される", () => {
    const { container } = render(<InputFile disabled />);
    const fileInput = container.querySelector('input[type="file"]');
    expect(fileInput).toBeDisabled();
  });
});
