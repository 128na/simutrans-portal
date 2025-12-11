import { Pagination } from "@/components/layout/Pagination";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";

describe("Pagination コンポーネント", () => {
  it("ページネーションが表示される", () => {
    render(<Pagination total={5} current={1} onChange={() => {}} />);
    expect(screen.getByRole("navigation")).toBeInTheDocument();
  });

  it("現在のページと周辺ページが表示される", () => {
    render(<Pagination total={10} current={5} onChange={() => {}} />);
    expect(screen.getByText("4")).toBeInTheDocument();
    expect(screen.getByText("5")).toBeInTheDocument();
    expect(screen.getByText("6")).toBeInTheDocument();
  });

  it("前へボタンが表示される", () => {
    render(<Pagination total={5} current={2} onChange={() => {}} />);
    expect(screen.getByRole("button", { name: "前" })).toBeInTheDocument();
  });

  it("次へボタンが表示される", () => {
    render(<Pagination total={5} current={2} onChange={() => {}} />);
    expect(screen.getByRole("button", { name: "次" })).toBeInTheDocument();
  });

  it("最初のページでは前へボタンが無効", () => {
    render(<Pagination total={5} current={1} onChange={() => {}} />);
    const prevButton = screen.getByRole("button", { name: "前" });
    expect(prevButton).toBeDisabled();
  });

  it("最後のページでは次へボタンが無効", () => {
    render(<Pagination total={5} current={5} onChange={() => {}} />);
    const nextButton = screen.getByRole("button", { name: "次" });
    expect(nextButton).toBeDisabled();
  });

  it("ページ番号をクリックすると onChange が呼ばれる", async () => {
    const user = userEvent.setup();
    const onChange = vi.fn();
    render(<Pagination total={5} current={2} onChange={onChange} />);
    const page3Button = screen.getByRole("button", { name: "3" });
    await user.click(page3Button);
    expect(onChange).toHaveBeenCalledWith(3);
  });

  it("前へボタンをクリックすると前のページに移動", async () => {
    const user = userEvent.setup();
    const onChange = vi.fn();
    render(<Pagination total={5} current={3} onChange={onChange} />);
    const prevButton = screen.getByRole("button", { name: "前" });
    await user.click(prevButton);
    expect(onChange).toHaveBeenCalledWith(2);
  });

  it("次へボタンをクリックすると次のページに移動", async () => {
    const user = userEvent.setup();
    const onChange = vi.fn();
    render(<Pagination total={5} current={3} onChange={onChange} />);
    const nextButton = screen.getByRole("button", { name: "次" });
    await user.click(nextButton);
    expect(onChange).toHaveBeenCalledWith(4);
  });

  it("現在のページがハイライトされる", () => {
    render(<Pagination total={5} current={3} onChange={() => {}} />);
    const currentButton = screen.getByRole("button", { name: "3" });
    expect(currentButton).toHaveClass("v2-pagination-item-active");
  });

  it("総ページ数が多い場合は省略記号が表示される", () => {
    render(<Pagination total={20} current={10} onChange={() => {}} />);
    const ellipsis = screen.getAllByText("...");
    expect(ellipsis.length).toBeGreaterThan(0);
  });

  it("最初のページから離れている場合は前方に省略記号が表示される", () => {
    render(<Pagination total={10} current={5} onChange={() => {}} />);
    const ellipsis = screen.getAllByText("...");
    expect(ellipsis.length).toBeGreaterThan(0);
  });
});
