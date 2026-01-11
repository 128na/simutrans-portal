import { render, screen, waitFor } from "@testing-library/react";
import { describe, expect, it, vi, beforeEach } from "vitest";
import userEvent from "@testing-library/user-event";
import axios from "axios";
import { MyListItemsTable } from "@/features/mylist/MyListItemsTable";
import { ToastProvider } from "@/providers/ToastProvider";
import type { MyListItemShow } from "@/types/models/MyList";

vi.mock("axios");
const mockAxios = axios as ReturnType<typeof vi.mocked<typeof axios>>;

describe("MyListItemsTable コンポーネント", () => {
  const mockOnUpdate = vi.fn();

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

  beforeEach(() => {
    vi.clearAllMocks();
    mockAxios.patch = vi.fn().mockResolvedValue({ data: {} });
    mockAxios.delete = vi.fn().mockResolvedValue({ data: {} });
    vi.spyOn(window, "confirm").mockReturnValue(false);
  });

  it("アイテム一覧が表示される", () => {
    render(
      <ToastProvider>
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    expect(screen.getByText("テストアドオン1")).toBeInTheDocument();
    expect(screen.getByText("テストアドオン2")).toBeInTheDocument();
  });

  it("メモが表示される", () => {
    render(
      <ToastProvider>
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    expect(screen.getByText("便利なアドオン")).toBeInTheDocument();
  });

  it("非公開記事は作成者が非表示になる", () => {
    render(
      <ToastProvider>
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    // 非公開記事は「-」表示
    const minusSigns = screen.getAllByText("-");
    expect(minusSigns.length).toBeGreaterThan(0);
  });

  it("削除ボタンが表示される", () => {
    render(
      <ToastProvider>
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    const deleteButtons = screen.getAllByLabelText("削除");
    expect(deleteButtons.length).toBe(2);
  });

  it("空のリストでメッセージが表示される", () => {
    render(
      <ToastProvider>
        <MyListItemsTable listId={1} items={[]} onUpdate={mockOnUpdate} />
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
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    const noteTexts = screen.getAllByText("便利なアドオン");
    await user.click(noteTexts[0]);

    // 編集モードでinputが表示される
    const input = screen.getByDisplayValue("便利なアドオン");
    expect(input).toBeInTheDocument();
  });

  it("メモを保存できる", async () => {
    const user = userEvent.setup();

    render(
      <ToastProvider>
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    // 編集モードに切り替え
    const noteTexts = screen.getAllByText("便利なアドオン");
    await user.click(noteTexts[0]);

    // メモを編集
    const input = screen.getByDisplayValue("便利なアドオン");
    await user.clear(input);
    await user.type(input, "更新されたメモ");

    // 保存ボタンをクリック
    const saveButton = screen.getByText("保存");
    await user.click(saveButton);

    // API呼び出しを確認
    await waitFor(() => {
      expect(mockAxios.patch).toHaveBeenCalledWith("/api/v1/mylist/1/items/1", {
        note: "更新されたメモ",
      });
      expect(mockOnUpdate).toHaveBeenCalled();
    });
  });

  it("メモ編集をキャンセルできる", async () => {
    const user = userEvent.setup();

    render(
      <ToastProvider>
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    // 編集モードに切り替え
    const noteTexts = screen.getAllByText("便利なアドオン");
    await user.click(noteTexts[0]);

    // キャンセルボタンをクリック
    const cancelButton = screen.getByText("キャンセル");
    await user.click(cancelButton);

    // 編集モードが終了していることを確認
    expect(
      screen.queryByDisplayValue("便利なアドオン")
    ).not.toBeInTheDocument();
    expect(mockAxios.patch).not.toHaveBeenCalled();
  });

  it("メモを空にして保存できる", async () => {
    const user = userEvent.setup();

    render(
      <ToastProvider>
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    // 編集モードに切り替え
    const noteTexts = screen.getAllByText("便利なアドオン");
    await user.click(noteTexts[0]);

    // メモを空にする
    const input = screen.getByDisplayValue("便利なアドオン");
    await user.clear(input);

    // 保存ボタンをクリック
    const saveButton = screen.getByText("保存");
    await user.click(saveButton);

    // nullで保存されることを確認
    await waitFor(() => {
      expect(mockAxios.patch).toHaveBeenCalledWith("/api/v1/mylist/1/items/1", {
        note: null,
      });
    });
  });

  it("削除確認ダイアログをキャンセルできる", async () => {
    const user = userEvent.setup();

    render(
      <ToastProvider>
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    const deleteButtons = screen.getAllByLabelText("削除");
    await user.click(deleteButtons[0]);

    // confirm が呼ばれ、false を返したので削除は実行されない
    expect(window.confirm).toHaveBeenCalledWith(
      "このアイテムをリストから削除しますか?"
    );
    expect(mockAxios.delete).not.toHaveBeenCalled();
  });

  it("削除を実行できる", async () => {
    const user = userEvent.setup();
    vi.spyOn(window, "confirm").mockReturnValue(true);

    render(
      <ToastProvider>
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    const deleteButtons = screen.getAllByLabelText("削除");
    await user.click(deleteButtons[0]);

    // API呼び出しを確認
    await waitFor(() => {
      expect(mockAxios.delete).toHaveBeenCalledWith("/api/v1/mylist/1/items/1");
      expect(mockOnUpdate).toHaveBeenCalled();
    });
  });

  it("アイテムを上に移動できる", async () => {
    const user = userEvent.setup();

    render(
      <ToastProvider>
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    // 2番目のアイテムの「上へ移動」ボタンをクリック
    const upButtons = screen.getAllByLabelText("上へ移動");
    await user.click(upButtons[1]);

    // API呼び出しを確認
    await waitFor(() => {
      expect(mockAxios.patch).toHaveBeenCalledWith(
        "/api/v1/mylist/1/items/reorder",
        {
          items: [
            { id: 2, position: 1 },
            { id: 1, position: 2 },
          ],
        }
      );
      expect(mockOnUpdate).toHaveBeenCalled();
    });
  });

  it("アイテムを下に移動できる", async () => {
    const user = userEvent.setup();

    render(
      <ToastProvider>
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    // 1番目のアイテムの「下へ移動」ボタンをクリック
    const downButtons = screen.getAllByLabelText("下へ移動");
    await user.click(downButtons[0]);

    // API呼び出しを確認
    await waitFor(() => {
      expect(mockAxios.patch).toHaveBeenCalledWith(
        "/api/v1/mylist/1/items/reorder",
        {
          items: [
            { id: 1, position: 2 },
            { id: 2, position: 1 },
          ],
        }
      );
      expect(mockOnUpdate).toHaveBeenCalled();
    });
  });

  it("最初のアイテムの上移動ボタンは無効化される", () => {
    render(
      <ToastProvider>
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    const upButtons = screen.getAllByLabelText("上へ移動");
    expect(upButtons[0]).toBeDisabled();
  });

  it("最後のアイテムの下移動ボタンは無効化される", () => {
    render(
      <ToastProvider>
        <MyListItemsTable
          listId={1}
          items={mockItems}
          onUpdate={mockOnUpdate}
        />
      </ToastProvider>
    );

    const downButtons = screen.getAllByLabelText("下へ移動");
    expect(downButtons[1]).toBeDisabled();
  });
});
