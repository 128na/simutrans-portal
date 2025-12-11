import MultiColumn from "@/components/ui/MultiColumn";
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("MultiColumn コンポーネント", () => {
  it("複数のカラムが表示される", () => {
    render(
      <MultiColumn>
        {[
          <div key="1">カラム1</div>,
          <div key="2">カラム2</div>,
          <div key="3">カラム3</div>,
        ]}
      </MultiColumn>
    );

    expect(screen.getByText("カラム1")).toBeInTheDocument();
    expect(screen.getByText("カラム2")).toBeInTheDocument();
    expect(screen.getByText("カラム3")).toBeInTheDocument();
  });

  it("カスタムクラス名が親要素に適用される", () => {
    const { container } = render(
      <MultiColumn className="custom-grid">
        {[<div key="1">内容</div>]}
      </MultiColumn>
    );

    expect(container.querySelector(".custom-grid")).toBeInTheDocument();
  });

  it("個別のカラムにクラス名を適用できる", () => {
    const { container } = render(
      <MultiColumn classNames={["col-1", "col-2", "col-3"]}>
        {[
          <div key="1">カラム1</div>,
          <div key="2">カラム2</div>,
          <div key="3">カラム3</div>,
        ]}
      </MultiColumn>
    );

    expect(container.querySelector(".col-1")).toBeInTheDocument();
    expect(container.querySelector(".col-2")).toBeInTheDocument();
    expect(container.querySelector(".col-3")).toBeInTheDocument();
  });

  it("デフォルトのスタイルが適用される", () => {
    const { container } = render(
      <MultiColumn>{[<div key="1">内容</div>]}</MultiColumn>
    );

    const parent = container.querySelector(".flex");
    expect(parent).toBeInTheDocument();
    expect(parent).toHaveClass("w-full");
    expect(parent).toHaveClass("gap-4");
  });

  it("1つの子要素でも表示できる", () => {
    render(<MultiColumn>{[<div key="1">単一カラム</div>]}</MultiColumn>);

    expect(screen.getByText("単一カラム")).toBeInTheDocument();
  });

  it("classNames が不足している場合は空文字列が適用される", () => {
    const { container } = render(
      <MultiColumn classNames={["col-1"]}>
        {[<div key="1">カラム1</div>, <div key="2">カラム2</div>]}
      </MultiColumn>
    );

    expect(container.querySelector(".col-1")).toBeInTheDocument();
    const columns = container.querySelectorAll(".flex > div");
    expect(columns).toHaveLength(2);
  });

  it("React要素以外も子要素にできる", () => {
    render(
      <MultiColumn>
        {[
          <p key="1">段落</p>,
          <span key="2">スパン</span>,
          <button key="3">ボタン</button>,
        ]}
      </MultiColumn>
    );

    expect(screen.getByText("段落")).toBeInTheDocument();
    expect(screen.getByText("スパン")).toBeInTheDocument();
    expect(screen.getByRole("button")).toBeInTheDocument();
  });

  it("HTML属性を親要素に渡せる", () => {
    const { container } = render(
      <MultiColumn data-testid="multi-column" id="my-columns">
        {[<div key="1">内容</div>]}
      </MultiColumn>
    );

    const parent = container.querySelector("#my-columns");
    expect(parent).toBeInTheDocument();
    expect(parent).toHaveAttribute("data-testid", "multi-column");
  });

  it("空の配列でもエラーにならない", () => {
    const { container } = render(<MultiColumn>{[]}</MultiColumn>);
    expect(container.querySelector(".flex")).toBeInTheDocument();
  });
});
