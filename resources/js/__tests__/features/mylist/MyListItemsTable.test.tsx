import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import userEvent from "@testing-library/user-event";
import { MyListItemsTable } from "@/features/mylist/MyListItemsTable";
import { ToastProvider } from "@/providers/ToastProvider";
import type { MyListItemShow } from "@/types/models/MyList";

describe("MyListItemsTable コンポーネント", () => {
  const mockItems: MyListItemShow[] = [
    {
      id: 1,
      note: "便利なアドオン",
      position: 1,
      created_at: "2025-01-01 10:00:00",
      article: {
        id: 100,
        title: "テストアドオン1",
        published_at: "2025-01-01 10:00:00",
        thumbnail: "https://example.com/thumb1.jpg",
        url: "https://example.com/addon1",
        user: {
          name: "テストユーザー",
          avatar: "https://example.com/avatar1.jpg",
        },
      },
    },
    {
      id: 2,
      note: null,
      position: 2,
      created_at: "2025-01-02 12:00:00",
      article: {
        id: 101,
        title: "テストアドオン2",
      },
    },
  ];

  it("アイテム一覧が表示される", () => {
    render(
      <ToastProvider>
        <MyListItemsTable listId={1} items={mockItems} onUpdate={vi.fn()} />
      </ToastProvider>
    );

    expect(screen.getByText("テストアドオン1")).toBeInTheDocument();
    expect(screen.getByText("テストアドオン2")).toBeInTheDocument();
  });

  it("メモが表示される", () => {
    render(
      <ToastProvider>
        <MyListItemsTable listId={1} items={mockItems} onUpdate={vi.fn()} />
      </ToastProvider>
    );

    expect(screen.getByText("便利なアドオン")).toBeInTheDocument();
  });

  it("非公開記事は作成者が非表示になる", () => {
    render(
      <ToastProvider>
        <MyListItemsTable listId={1} items={mockItems} onUpdate={vi.fn()} />
      </ToastProvider>
    );

    // 非公開記事は「-」表示
    const minusSigns = screen.getAllByText("-");
    expect(minusSigns.length).toBeGreaterThan(0);
  });

  it("削除ボタンが表示される", () => {
    render(
      <ToastProvider>
        <MyListItemsTable listId={1} items={mockItems} onUpdate={vi.fn()} />
      </ToastProvider>
    );

    const deleteButtons = screen.getAllByLabelText("削除");
    expect(deleteButtons.length).toBe(2);
  });

  it("空のリストでメッセージが表示される", () => {
    render(
      <ToastProvider>
        <MyListItemsTable listId={1} items={[]} onUpdate={vi.fn()} />
      </ToastProvider>
    );

    expect(
      screen.getByText(
        "アイテムがありません。記事をマイリストに追加してください。"
      )
    ).toBeInTheDocument();
  });

  it("メモ編集モードに切り替わる", async () => {
    const user = userEvent.setup();

    render(
      <ToastProvider>
        <MyListItemsTable listId={1} items={mockItems} onUpdate={vi.fn()} />
      </ToastProvider>
    );

    const noteTexts = screen.getAllByText("便利なアドオン");
    await user.click(noteTexts[0]);

    // 編集モードでinputが表示される
    const input = screen.getByDisplayValue("便利なアドオン");
    expect(input).toBeInTheDocument();
  });
});
