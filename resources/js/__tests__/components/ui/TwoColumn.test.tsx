import TwoColumn from "@/components/ui/TwoColumn";
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("TwoColumn コンポーネント", () => {
  it("2つの子要素がレンダリングされる", () => {
    render(
      <TwoColumn>
        <div>左側</div>
        <div>右側</div>
      </TwoColumn>
    );
    expect(screen.getByText("左側")).toBeInTheDocument();
    expect(screen.getByText("右側")).toBeInTheDocument();
  });

  it("デフォルトでは grow なし", () => {
    const { container } = render(
      <TwoColumn>
        <div>左</div>
        <div>右</div>
      </TwoColumn>
    );
    const wrapper = container.querySelector(".flex");
    const columns = wrapper?.querySelectorAll(":scope > div");
    expect(columns?.[0]).not.toHaveClass("flex-grow");
    expect(columns?.[1]).not.toHaveClass("flex-grow");
  });

  it("左側が伸びる設定", () => {
    const { container } = render(
      <TwoColumn grow="left">
        <div>左</div>
        <div>右</div>
      </TwoColumn>
    );
    const wrapper = container.querySelector(".flex");
    const columns = wrapper?.querySelectorAll(":scope > div");
    expect(columns?.[0]).toHaveClass("flex-grow");
    expect(columns?.[1]).not.toHaveClass("flex-grow");
  });

  it("右側が伸びる設定", () => {
    const { container } = render(
      <TwoColumn grow="right">
        <div>左</div>
        <div>右</div>
      </TwoColumn>
    );
    const wrapper = container.querySelector(".flex");
    const columns = wrapper?.querySelectorAll(":scope > div");
    expect(columns?.[0]).not.toHaveClass("flex-grow");
    expect(columns?.[1]).toHaveClass("flex-grow");
  });

  it("複雑な子要素がレンダリングされる", () => {
    render(
      <TwoColumn>
        <div>
          <h2>タイトル</h2>
          <p>説明</p>
        </div>
        <button>ボタン</button>
      </TwoColumn>
    );
    expect(screen.getByText("タイトル")).toBeInTheDocument();
    expect(screen.getByText("説明")).toBeInTheDocument();
    expect(screen.getByRole("button", { name: "ボタン" })).toBeInTheDocument();
  });
});
