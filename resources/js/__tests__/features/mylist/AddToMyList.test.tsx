import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import userEvent from "@testing-library/user-event";
import { AddToMyListButton } from "@/features/mylist/AddToMyList";

describe("AddToMyListButton コンポーネント", () => {
  it("ボタンが表示される", () => {
    render(<AddToMyListButton articleId={1} />);

    expect(screen.getByLabelText("マイリストに追加")).toBeInTheDocument();
  });

  it("ボタンクリックでモーダルが開く", async () => {
    const user = userEvent.setup();
    render(<AddToMyListButton articleId={1} />);

    const button = screen.getByLabelText("マイリストに追加");
    await user.click(button);

    expect(screen.getByRole("dialog")).toBeInTheDocument();
    expect(
      screen.getByText("マイリストに追加", { selector: "h3" })
    ).toBeInTheDocument();
  });

  it("モーダルにリスト選択UIが表示される", async () => {
    const user = userEvent.setup();
    render(<AddToMyListButton articleId={1} />);

    const button = screen.getByLabelText("マイリストに追加");
    await user.click(button);

    expect(screen.getByText("新しいリストを作成")).toBeInTheDocument();
  });

  it("キャンセルボタンでモーダルが閉じる", async () => {
    const user = userEvent.setup();
    render(<AddToMyListButton articleId={1} />);

    const button = screen.getByLabelText("マイリストに追加");
    await user.click(button);

    const cancelButton = screen.getByText("キャンセル");
    await user.click(cancelButton);

    expect(screen.queryByRole("dialog")).not.toBeInTheDocument();
  });
});
