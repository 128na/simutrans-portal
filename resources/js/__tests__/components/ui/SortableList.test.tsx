import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import { SortableList } from "@/components/ui/SortableList";

type TestItem = {
  id: string;
  name: string;
};

describe("SortableList", () => {
  const mockItems: TestItem[] = [
    { id: "1", name: "アイテム1" },
    { id: "2", name: "アイテム2" },
    { id: "3", name: "アイテム3" },
  ];

  it("すべてのアイテムが表示される", () => {
    const mockOnReorder = vi.fn();
    render(
      <SortableList
        items={mockItems}
        onReorder={mockOnReorder}
        renderItem={(item) => <div>{item.name}</div>}
        getItemId={(item) => item.id}
      />
    );

    expect(screen.getByText("アイテム1")).toBeInTheDocument();
    expect(screen.getByText("アイテム2")).toBeInTheDocument();
    expect(screen.getByText("アイテム3")).toBeInTheDocument();
  });

  it("ドラッグハンドルが各アイテムに表示される", () => {
    const mockOnReorder = vi.fn();
    render(
      <SortableList
        items={mockItems}
        onReorder={mockOnReorder}
        renderItem={(item) => <div>{item.name}</div>}
        getItemId={(item) => item.id}
      />
    );

    const dragHandles = screen.getAllByTitle("ドラッグして並び替え");
    expect(dragHandles).toHaveLength(3);
  });

  it("カスタムrenderItem関数が正しく動作する", () => {
    const mockOnReorder = vi.fn();
    render(
      <SortableList
        items={mockItems}
        onReorder={mockOnReorder}
        renderItem={(item, index) => (
          <div>
            {index + 1}. {item.name}
          </div>
        )}
        getItemId={(item) => item.id}
      />
    );

    expect(screen.getByText("1. アイテム1")).toBeInTheDocument();
    expect(screen.getByText("2. アイテム2")).toBeInTheDocument();
    expect(screen.getByText("3. アイテム3")).toBeInTheDocument();
  });

  it("空の配列でもエラーが出ない", () => {
    const mockOnReorder = vi.fn();
    render(
      <SortableList
        items={[]}
        onReorder={mockOnReorder}
        renderItem={(item: TestItem) => <div>{item.name}</div>}
        getItemId={(item: TestItem) => item.id}
      />
    );

    expect(screen.queryByText("アイテム1")).not.toBeInTheDocument();
  });

  it("getItemIdが正しくIDを生成する", () => {
    const mockOnReorder = vi.fn();
    const customGetItemId = vi.fn((item: TestItem, index: number) => {
      return `custom-${item.id}-${index}`;
    });

    render(
      <SortableList
        items={mockItems}
        onReorder={mockOnReorder}
        renderItem={(item) => <div>{item.name}</div>}
        getItemId={customGetItemId}
      />
    );

    expect(customGetItemId).toHaveBeenCalledTimes(mockItems.length * 3); // 初回レンダー + map + SortableContext
  });

  it("ドラッグハンドルにカーソルスタイルが適用される", () => {
    const mockOnReorder = vi.fn();
    render(
      <SortableList
        items={mockItems}
        onReorder={mockOnReorder}
        renderItem={(item) => <div>{item.name}</div>}
        getItemId={(item) => item.id}
      />
    );

    const dragHandles = screen.getAllByTitle("ドラッグして並び替え");
    dragHandles.forEach((handle) => {
      expect(handle).toHaveClass("cursor-grab", "active:cursor-grabbing");
    });
  });

  it("複雑なアイテムをレンダリングできる", () => {
    const complexItems = [
      { id: "1", name: "アイテム1", description: "説明1" },
      { id: "2", name: "アイテム2", description: "説明2" },
    ];

    const mockOnReorder = vi.fn();
    render(
      <SortableList
        items={complexItems}
        onReorder={mockOnReorder}
        renderItem={(item) => (
          <div>
            <h3>{item.name}</h3>
            <p>{item.description}</p>
          </div>
        )}
        getItemId={(item) => item.id}
      />
    );

    expect(screen.getByText("アイテム1")).toBeInTheDocument();
    expect(screen.getByText("説明1")).toBeInTheDocument();
    expect(screen.getByText("アイテム2")).toBeInTheDocument();
    expect(screen.getByText("説明2")).toBeInTheDocument();
  });
});
