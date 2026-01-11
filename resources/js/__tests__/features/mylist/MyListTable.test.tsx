import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import userEvent from "@testing-library/user-event";
import { MyListTable } from "@/features/mylist/MyListTable";
import { ToastProvider } from "@/providers/ToastProvider";
import type { MyListShow } from "@/types/models/MyList";

describe("MyListTable コンポーネント", () => {
  const mockLists: MyListShow[] = [
    {
      id: 1,
      user_id: 1,
      title: "お気に入りアドオン",
      note: "よく使うアドオンリスト",
      is_public: true,
      slug: "abc-123",
      items_count: 5,
      created_at: "2025-01-01 10:00:00",
      updated_at: "2025-01-02 12:00:00",
    },
    {
      id: 2,
      user_id: 1,
      title: "保存用リスト",
      note: null,
      is_public: false,
      slug: null,
      items_count: 2,
      created_at: "2025-01-03 14:00:00",
      updated_at: "2025-01-03 14:00:00",
    },
  ];

  it("マイリスト一覧が表示される", () => {
    const onEdit = vi.fn();
    const onDelete = vi.fn();

    render(
      <ToastProvider>
        <MyListTable lists={mockLists} onEdit={onEdit} onDelete={onDelete} />
      </ToastProvider>
    );

    expect(screen.getByText("お気に入りアドオン")).toBeInTheDocument();
    expect(screen.getByText("保存用リスト")).toBeInTheDocument();
  });

  it("公開リストに公開バッジが表示される", () => {
    const onEdit = vi.fn();
    const onDelete = vi.fn();

    render(
      <ToastProvider>
        <MyListTable lists={mockLists} onEdit={onEdit} onDelete={onDelete} />
      </ToastProvider>
    );

    expect(screen.getByText("公開")).toBeInTheDocument();
  });

  it("アイテム数が表示される", () => {
    const onEdit = vi.fn();
    const onDelete = vi.fn();

    render(
      <ToastProvider>
        <MyListTable lists={mockLists} onEdit={onEdit} onDelete={onDelete} />
      </ToastProvider>
    );

    expect(screen.getByText("5件")).toBeInTheDocument();
    expect(screen.getByText("2件")).toBeInTheDocument();
  });

  it("編集ボタンクリックでコールバックが呼ばれる", async () => {
    const user = userEvent.setup();
    const onEdit = vi.fn();
    const onDelete = vi.fn();

    render(
      <ToastProvider>
        <MyListTable lists={mockLists} onEdit={onEdit} onDelete={onDelete} />
      </ToastProvider>
    );

    const editButtons = screen.getAllByText("編集");
    await user.click(editButtons[0]);

    expect(onEdit).toHaveBeenCalledWith(mockLists[0]);
  });

  it("削除ボタンクリックでコールバックが呼ばれる", async () => {
    const user = userEvent.setup();
    const onEdit = vi.fn();
    const onDelete = vi.fn();

    render(
      <ToastProvider>
        <MyListTable lists={mockLists} onEdit={onEdit} onDelete={onDelete} />
      </ToastProvider>
    );

    const deleteButtons = screen.getAllByText("削除");
    await user.click(deleteButtons[0]);

    expect(onDelete).toHaveBeenCalledWith(mockLists[0]);
  });

  it("空のリストでメッセージが表示される", () => {
    const onEdit = vi.fn();
    const onDelete = vi.fn();

    render(
      <ToastProvider>
        <MyListTable lists={[]} onEdit={onEdit} onDelete={onDelete} />
      </ToastProvider>
    );

    expect(
      screen.getByText("マイリストがありません。新しく作成してください。")
    ).toBeInTheDocument();
  });
});
