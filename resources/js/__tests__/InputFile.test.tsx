import { describe, it, expect, vi } from "vitest";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import InputFile from "../apps/components/ui/InputFile";

describe("InputFile", () => {
  it("基本的なレンダリング", () => {
    render(<InputFile />);
    expect(screen.getByText("アップロード")).toBeInTheDocument();
  });

  it("カスタムchildrenが表示される", () => {
    render(<InputFile>ファイルを選択</InputFile>);
    expect(screen.getByText("ファイルを選択")).toBeInTheDocument();
  });

  it("ボタンクリックでファイル選択ダイアログが開く", async () => {
    const user = userEvent.setup();
    render(<InputFile />);
    
    const button = screen.getByText("アップロード");
    await user.click(button);
    
    // ファイル入力要素が存在することを確認
    const fileInput = document.querySelector('input[type="file"]');
    expect(fileInput).toBeInTheDocument();
  });

  it("onChangeハンドラーが動作する", async () => {
    const handleChange = vi.fn();
    render(<InputFile onChange={handleChange} />);
    
    const fileInput = document.querySelector('input[type="file"]') as HTMLInputElement;
    const file = new File(["test"], "test.txt", { type: "text/plain" });
    
    await userEvent.upload(fileInput, file);
    expect(handleChange).toHaveBeenCalled();
  });

  it("accept属性が設定される", () => {
    render(<InputFile accept="image/*" />);
    const fileInput = document.querySelector('input[type="file"]');
    expect(fileInput).toHaveAttribute("accept", "image/*");
  });

  it("multiple属性が設定される", () => {
    render(<InputFile multiple />);
    const fileInput = document.querySelector('input[type="file"]');
    expect(fileInput).toHaveAttribute("multiple");
  });

  it("disabled状態が正しく反映される", () => {
    render(<InputFile disabled />);
    const fileInput = document.querySelector('input[type="file"]');
    expect(fileInput).toBeDisabled();
  });

  it("カスタムclassNameが適用される", () => {
    render(<InputFile className="custom-file" />);
    const fileInput = document.querySelector('input[type="file"]');
    expect(fileInput).toHaveClass("custom-file");
  });

  it("ファイル入力要素は非表示である", () => {
    render(<InputFile />);
    const fileInput = document.querySelector('input[type="file"]');
    expect(fileInput).toHaveClass("hidden");
  });
});
