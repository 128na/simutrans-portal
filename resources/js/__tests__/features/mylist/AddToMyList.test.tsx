import { render, screen, waitFor } from "@testing-library/react";
import { describe, expect, it, vi, beforeEach, afterEach } from "vitest";
import userEvent from "@testing-library/user-event";
import {
  AddToMyListButton,
  AddToMyListModal,
} from "@/features/mylist/AddToMyList";
import { ToastProvider } from "@/providers/ToastProvider";
import axios, { AxiosError } from "axios";

describe("AddToMyListButton コンポーネント", () => {
  beforeEach(() => {
    vi.spyOn(axios, "get").mockResolvedValue({ data: [] });
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it("ボタンが表示される", () => {
    render(
      <ToastProvider>
        <AddToMyListButton articleId={1} />
      </ToastProvider>
    );

    expect(screen.getByLabelText("マイリストに追加")).toBeInTheDocument();
  });

  it("ボタンクリックでモーダルが開く", async () => {
    const user = userEvent.setup();
    render(
      <ToastProvider>
        <AddToMyListButton articleId={1} />
      </ToastProvider>
    );

    const button = screen.getByLabelText("マイリストに追加");
    await user.click(button);

    expect(screen.getByRole("dialog")).toBeInTheDocument();
    expect(
      screen.getByText("マイリストに追加", { selector: "h3" })
    ).toBeInTheDocument();
  });

  it("モーダルにリスト選択UIが表示される", async () => {
    const user = userEvent.setup();
    render(
      <ToastProvider>
        <AddToMyListButton articleId={1} />
      </ToastProvider>
    );

    const button = screen.getByLabelText("マイリストに追加");
    await user.click(button);

    expect(screen.getByText("新しいリストを作成")).toBeInTheDocument();
  });

  it("複数の記事を 1 件ずつ保存し、409 は無視して 429 は待ってから送り直す", async () => {
    vi.spyOn(axios, "get").mockResolvedValue({
      data: { data: [{ id: 7, title: "新作", note: "", is_public: false }] },
    });
    const posted: number[] = [];
    let inFlight = 0;
    let maxInFlight = 0;
    const tooMany = new AxiosError("429", "ERR", undefined, undefined, {
      status: 429,
      headers: { "retry-after": "5" },
    } as never);
    const conflict = new AxiosError("409", "ERR", undefined, undefined, {
      status: 409,
      headers: {},
    } as never);
    const outcomes: Array<Error | null> = [null, tooMany, null, conflict];
    vi.spyOn(axios, "post").mockImplementation(async (_url, body) => {
      inFlight++;
      maxInFlight = Math.max(maxInFlight, inFlight);
      await Promise.resolve();
      inFlight--;
      posted.push((body as { article_id: number }).article_id);
      const outcome = outcomes.shift();
      if (outcome) {
        throw outcome;
      }
      return { data: {} };
    });
    const sleep = vi.fn(async () => {});
    const onSuccess = vi.fn();
    const user = userEvent.setup();
    render(
      <ToastProvider>
        <AddToMyListModal
          articleIds={[11, 22, 33]}
          onClose={() => {}}
          onSuccess={onSuccess}
          sleep={sleep}
        />
      </ToastProvider>
    );

    expect(screen.getByText("選択中の 3 件を追加します。")).toBeInTheDocument();
    await user.click(await screen.findByRole("checkbox"));
    await user.click(screen.getByRole("button", { name: "追加" }));

    await waitFor(() => expect(onSuccess).toHaveBeenCalledTimes(1));
    // 22 は 429 のあと送り直す。33 は 409（重複）でも成功扱い
    expect(posted).toEqual([11, 22, 22, 33]);
    expect(sleep).toHaveBeenCalledWith(5000);
    expect(maxInFlight).toBe(1);
  });

  it("キャンセルボタンでモーダルが閉じる", async () => {
    const user = userEvent.setup();
    render(
      <ToastProvider>
        <AddToMyListButton articleId={1} />
      </ToastProvider>
    );

    const button = screen.getByLabelText("マイリストに追加");
    await user.click(button);

    const cancelButton = screen.getByText("キャンセル");
    await user.click(cancelButton);

    expect(screen.queryByRole("dialog")).not.toBeInTheDocument();
  });
});
