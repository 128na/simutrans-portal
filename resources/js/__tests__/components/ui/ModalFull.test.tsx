import { ModalFull } from "@/components/ui/ModalFull";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it } from "vitest";

describe("ModalFull コンポーネント", () => {
  it("初期状態ではモーダルが閉じている", () => {
    render(
      <ModalFull buttonTitle="開く" title="テストモーダル">
        モーダルコンテンツ
      </ModalFull>
    );
    expect(screen.queryByRole("dialog")).not.toBeInTheDocument();
    expect(screen.getByRole("button", { name: "開く" })).toBeInTheDocument();
  });

  it("ボタンをクリックするとモーダルが開く", async () => {
    const user = userEvent.setup();
    render(
      <ModalFull buttonTitle="開く" title="テストモーダル">
        モーダルコンテンツ
      </ModalFull>
    );
    const openButton = screen.getByRole("button", { name: "開く" });
    await user.click(openButton);
    expect(screen.getByRole("dialog")).toBeInTheDocument();
    expect(screen.getByText("テストモーダル")).toBeInTheDocument();
    expect(screen.getByText("モーダルコンテンツ")).toBeInTheDocument();
  });

  it("閉じるボタンをクリックするとモーダルが閉じる", async () => {
    const user = userEvent.setup();
    render(
      <ModalFull buttonTitle="開く" title="テストモーダル">
        モーダルコンテンツ
      </ModalFull>
    );
    const openButton = screen.getByRole("button", { name: "開く" });
    await user.click(openButton);
    expect(screen.getByRole("dialog")).toBeInTheDocument();

    const closeButtons = screen.getAllByRole("button");
    const closeButton = closeButtons.find((btn) => btn.querySelector("svg"));
    expect(closeButton).toBeDefined();
    await user.click(closeButton!);
    expect(screen.queryByRole("dialog")).not.toBeInTheDocument();
  });

  it("再度ボタンをクリックするとモーダルを開閉できる", async () => {
    const user = userEvent.setup();
    render(
      <ModalFull buttonTitle="開く" title="テストモーダル">
        モーダルコンテンツ
      </ModalFull>
    );
    const openButton = screen.getByRole("button", { name: "開く" });

    // 開く
    await user.click(openButton);
    expect(screen.getByRole("dialog")).toBeInTheDocument();

    // 閉じる
    const closeButtons = screen.getAllByRole("button");
    const closeButton = closeButtons.find((btn) => btn.querySelector("svg"));
    expect(closeButton).toBeDefined();
    await user.click(closeButton!);
    expect(screen.queryByRole("dialog")).not.toBeInTheDocument();
  });

  it("カスタムボタンクラスが適用される", () => {
    render(
      <ModalFull
        buttonTitle="開く"
        title="テストモーダル"
        buttonClass="custom-button-class"
      >
        内容
      </ModalFull>
    );
    const button = screen.getByRole("button");
    expect(button).toHaveClass("custom-button-class");
  });

  it("カスタムモーダルクラスが適用される", async () => {
    const user = userEvent.setup();
    const { container } = render(
      <ModalFull
        buttonTitle="開く"
        title="テストモーダル"
        modalClass="custom-modal-class"
      >
        内容
      </ModalFull>
    );
    const openButton = screen.getByRole("button", { name: "開く" });
    await user.click(openButton);
    const modalDiv = container.querySelector(".custom-modal-class");
    expect(modalDiv).toBeInTheDocument();
  });

  it("関数として children を渡すと close が使える", async () => {
    const user = userEvent.setup();
    render(
      <ModalFull buttonTitle="開く" title="テストモーダル">
        {({ close }) => <button onClick={close}>カスタム閉じる</button>}
      </ModalFull>
    );
    const openButton = screen.getByRole("button", { name: "開く" });
    await user.click(openButton);
    expect(screen.getByRole("dialog")).toBeInTheDocument();

    const customCloseButton = screen.getByRole("button", {
      name: "カスタム閉じる",
    });
    await user.click(customCloseButton);
    expect(screen.queryByRole("dialog")).not.toBeInTheDocument();
  });
});
