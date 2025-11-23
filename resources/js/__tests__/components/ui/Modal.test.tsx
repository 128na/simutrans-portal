import { Modal } from "@/components/ui/Modal";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";

describe("Modal コンポーネント", () => {
  it("モーダルが表示される", () => {
    render(<Modal title="テストモーダル">モーダルコンテンツ</Modal>);
    expect(screen.getByRole("dialog")).toBeInTheDocument();
    expect(screen.getByText("テストモーダル")).toBeInTheDocument();
    expect(screen.getByText("モーダルコンテンツ")).toBeInTheDocument();
  });

  it("閉じるボタンが表示される", () => {
    render(<Modal title="テスト">内容</Modal>);
    const closeButton = screen.getByRole("button");
    expect(closeButton).toBeInTheDocument();
  });

  it("閉じるボタンをクリックすると onClose が呼ばれる", async () => {
    const user = userEvent.setup();
    const onClose = vi.fn();
    render(
      <Modal title="テスト" onClose={onClose}>
        内容
      </Modal>
    );
    const closeButton = screen.getByRole("button");
    await user.click(closeButton);
    expect(onClose).toHaveBeenCalledTimes(1);
  });

  it("背景をクリックすると onClose が呼ばれる", async () => {
    const user = userEvent.setup();
    const onClose = vi.fn();
    render(
      <Modal title="テスト" onClose={onClose}>
        内容
      </Modal>
    );
    const backdrop = screen.getByRole("dialog");
    await user.click(backdrop);
    expect(onClose).toHaveBeenCalledTimes(1);
  });

  it("モーダル内部をクリックしても onClose が呼ばれない", async () => {
    const user = userEvent.setup();
    const onClose = vi.fn();
    render(
      <Modal title="テスト" onClose={onClose}>
        内容
      </Modal>
    );
    const content = screen.getByText("内容");
    await user.click(content);
    expect(onClose).not.toHaveBeenCalled();
  });

  it("カスタムクラス名が適用される", () => {
    const { container } = render(
      <Modal title="テスト" modalClass="custom-modal-class">
        内容
      </Modal>
    );
    const modalDiv = container.querySelector(".custom-modal-class");
    expect(modalDiv).toBeInTheDocument();
  });

  it("関数として children を渡すと close が使える", async () => {
    const user = userEvent.setup();
    const onClose = vi.fn();
    render(
      <Modal title="テスト" onClose={onClose}>
        {() => <button onClick={onClose}>カスタム閉じる</button>}
      </Modal>
    );
    const customCloseButton = screen.getByRole("button", {
      name: "カスタム閉じる",
    });
    await user.click(customCloseButton);
    expect(onClose).toHaveBeenCalledTimes(1);
  });
});
