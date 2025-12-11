import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";
import { SelectableSearch } from "@/components/form/SelectableSearch";

type TestOption = {
  id: number;
  name: string;
};

describe("SelectableSearch", () => {
  const mockOptions: TestOption[] = [
    { id: 1, name: "オプション1" },
    { id: 2, name: "オプション2" },
    { id: 3, name: "オプション3" },
  ];

  it("選択済みアイテムが表示される", () => {
    render(
      <SelectableSearch
        options={mockOptions}
        selectedIds={[1, 2]}
        onChange={vi.fn()}
      />
    );

    expect(screen.getByText("オプション1")).toBeInTheDocument();
    expect(screen.getByText("オプション2")).toBeInTheDocument();
  });

  it("未選択時に「（未選択）」が表示される", () => {
    render(
      <SelectableSearch
        options={mockOptions}
        selectedIds={[]}
        onChange={vi.fn()}
      />
    );

    expect(screen.getByText("（未選択）")).toBeInTheDocument();
  });

  it("検索フィールドが表示される", () => {
    render(
      <SelectableSearch
        options={mockOptions}
        selectedIds={[]}
        onChange={vi.fn()}
      />
    );

    expect(screen.getByPlaceholderText("検索...")).toBeInTheDocument();
  });

  it("カスタムプレースホルダーが設定できる", () => {
    render(
      <SelectableSearch
        options={mockOptions}
        selectedIds={[]}
        placeholder="カスタム検索"
        onChange={vi.fn()}
      />
    );

    expect(screen.getByPlaceholderText("カスタム検索")).toBeInTheDocument();
  });

  it("検索条件で絞り込みができる", async () => {
    const user = userEvent.setup();
    render(
      <SelectableSearch
        options={mockOptions}
        selectedIds={[]}
        onChange={vi.fn()}
      />
    );

    const input = screen.getByPlaceholderText("検索...");
    await user.type(input, "オプション1");

    expect(screen.getByText("オプション1")).toBeInTheDocument();
    expect(screen.queryByText("オプション2")).not.toBeInTheDocument();
    expect(screen.queryByText("オプション3")).not.toBeInTheDocument();
  });

  it("大文字小文字を区別せず検索できる", async () => {
    const user = userEvent.setup();
    render(
      <SelectableSearch
        options={mockOptions}
        selectedIds={[]}
        onChange={vi.fn()}
      />
    );

    const input = screen.getByPlaceholderText("検索...");
    await user.type(input, "オプション");

    expect(screen.getByText("オプション1")).toBeInTheDocument();
    expect(screen.getByText("オプション2")).toBeInTheDocument();
    expect(screen.getByText("オプション3")).toBeInTheDocument();
  });

  it("アイテムをクリックして選択できる", async () => {
    const user = userEvent.setup();
    const mockOnChange = vi.fn();

    render(
      <SelectableSearch
        options={mockOptions}
        selectedIds={[]}
        onChange={mockOnChange}
      />
    );

    await user.click(screen.getByText("オプション1"));

    expect(mockOnChange).toHaveBeenCalledWith([1]);
  });

  it("選択済みアイテムをクリックして解除できる", async () => {
    const user = userEvent.setup();
    const mockOnChange = vi.fn();

    render(
      <SelectableSearch
        options={mockOptions}
        selectedIds={[1]}
        onChange={mockOnChange}
      />
    );

    // ✕ボタンをクリック
    await user.click(screen.getByText("✕"));

    expect(mockOnChange).toHaveBeenCalledWith([]);
  });

  it("選択済みアイテムは候補から除外される", () => {
    render(
      <SelectableSearch
        options={mockOptions}
        selectedIds={[1]}
        onChange={vi.fn()}
      />
    );

    // 選択済みエリアには表示される
    const selectedArea = screen.getAllByText("オプション1");
    expect(selectedArea).toHaveLength(1);

    // 候補リストには表示されない（検索結果に含まれない）
    expect(screen.getByText("オプション2")).toBeInTheDocument();
    expect(screen.getByText("オプション3")).toBeInTheDocument();
  });

  it("該当なし時のメッセージが表示される", async () => {
    const user = userEvent.setup();
    render(
      <SelectableSearch
        options={mockOptions}
        selectedIds={[]}
        onChange={vi.fn()}
      />
    );

    const input = screen.getByPlaceholderText("検索...");
    await user.type(input, "存在しない");

    expect(screen.getByText("該当なし")).toBeInTheDocument();
  });

  it("カスタムrenderが使用できる", () => {
    render(
      <SelectableSearch
        options={mockOptions}
        selectedIds={[1]}
        render={(option) => `カスタム: ${option.name}`}
        onChange={vi.fn()}
      />
    );

    expect(screen.getByText("カスタム: オプション1")).toBeInTheDocument();
  });

  it("カスタムlabelKeyが使用できる", () => {
    type CustomOption = {
      id: number;
      title: string;
    };

    const customOptions: CustomOption[] = [
      { id: 1, title: "タイトル1" },
      { id: 2, title: "タイトル2" },
    ];

    render(
      <SelectableSearch
        options={customOptions}
        selectedIds={[1]}
        labelKey="title"
        onChange={vi.fn()}
      />
    );

    expect(screen.getByText("タイトル1")).toBeInTheDocument();
  });

  it("複数選択ができる", async () => {
    const user = userEvent.setup();
    const mockOnChange = vi.fn();

    render(
      <SelectableSearch
        options={mockOptions}
        selectedIds={[1]}
        onChange={mockOnChange}
      />
    );

    await user.click(screen.getByText("オプション2"));

    expect(mockOnChange).toHaveBeenCalledWith([1, 2]);
  });

  it("カスタムclassNameが適用される", () => {
    const { container } = render(
      <SelectableSearch
        options={mockOptions}
        selectedIds={[]}
        className="custom-class"
        onChange={vi.fn()}
      />
    );

    const element = container.querySelector(".custom-class");
    expect(element).toBeInTheDocument();
  });
});
