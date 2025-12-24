import { Accordion } from "@/components/ui/Accordion";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it } from "vitest";

describe("Accordion コンポーネント", () => {
  it("タイトルが表示される", () => {
    render(<Accordion title="アコーディオンタイトル">内容</Accordion>);
    expect(screen.getByText("アコーディオンタイトル")).toBeInTheDocument();
  });

  it("初期状態では閉じている", () => {
    render(<Accordion title="タイトル">内容</Accordion>);
    expect(screen.queryByText("内容")).not.toBeInTheDocument();
  });

  it("defaultOpen が true の場合は開いている", () => {
    render(
      <Accordion title="タイトル" defaultOpen={true}>
        内容
      </Accordion>
    );
    expect(screen.getByText("内容")).toBeInTheDocument();
  });

  it("クリックすると開く", async () => {
    const user = userEvent.setup();
    render(<Accordion title="タイトル">内容</Accordion>);
    expect(screen.queryByText("内容")).not.toBeInTheDocument();

    const button = screen.getByRole("button");
    await user.click(button);

    expect(screen.getByText("内容")).toBeInTheDocument();
  });

  it("開いた状態でクリックすると閉じる", async () => {
    const user = userEvent.setup();
    render(
      <Accordion title="タイトル" defaultOpen={true}>
        内容
      </Accordion>
    );
    expect(screen.getByText("内容")).toBeInTheDocument();

    const button = screen.getByRole("button");
    await user.click(button);

    expect(screen.queryByText("内容")).not.toBeInTheDocument();
  });

  it("aria-expanded 属性が正しく設定される", async () => {
    const user = userEvent.setup();
    render(<Accordion title="タイトル">内容</Accordion>);
    const button = screen.getByRole("button");

    expect(button).toHaveAttribute("aria-expanded", "false");

    await user.click(button);
    expect(button).toHaveAttribute("aria-expanded", "true");
  });
});
