import { render, screen, waitFor, fireEvent } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { beforeEach, describe, expect, it, vi } from "vitest";
import axios from "axios";
import { ArticleConvertModal } from "@/features/articles/ArticleConvertModal";
import { ArticleModal } from "@/features/articles/ArticleModal";
import { ToastProvider } from "@/providers/ToastProvider";

vi.mock("axios");
vi.mock("@/features/attachments/AttachmentEdit", () => ({
  AttachmentEdit: ({
    onSelectAttachment,
    types,
  }: {
    onSelectAttachment: (id: number) => void;
    types: string[];
  }) => (
    <button onClick={() => onSelectAttachment(9)}>選択 {types.join()}</button>
  ),
}));

const article = {
  id: 1,
  title: "紹介",
  post_type: "addon-introduction",
  status: "publish",
  slug: "addon",
} as Article.MypageShow;

const setup = () => {
  const onClose = vi.fn();
  const onSuccess = vi.fn();
  render(
    <ToastProvider>
      <ArticleConvertModal
        article={article}
        onClose={onClose}
        onSuccess={onSuccess}
      />
    </ToastProvider>
  );
  return { onClose, onSuccess };
};

const selectFile = async () => {
  const user = userEvent.setup();
  await user.click(
    await screen.findByRole("button", {
      name: "アップロード済みのファイルから選択",
    })
  );
  await user.click(screen.getByRole("button", { name: "選択 file" }));
};

describe("記事の変換", () => {
  beforeEach(() => {
    vi.resetAllMocks();
    vi.mocked(axios.get).mockResolvedValue({ data: { attachments: [] } });
  });

  it.each([
    ["addon-introduction", "draft", true],
    ["addon-introduction", "trash", false],
    ["addon-post", "draft", false],
  ] as const)("%s / %s の操作表示", (post_type, status, visible) => {
    render(
      <ArticleModal
        article={{ ...article, post_type, status }}
        user={{ id: 1 } as User.MypageShow}
      />
    );
    expect(!!screen.queryByRole("button", { name: "投稿記事へ変換" })).toBe(
      visible
    );
  });

  it("開いた時に取得し、失敗時は再取得できる", async () => {
    vi.mocked(axios.get).mockRejectedValueOnce(new Error("失敗"));
    setup();
    expect(await screen.findByRole("alert")).toBeInTheDocument();
    await userEvent.click(screen.getByRole("button", { name: "再取得" }));
    await waitFor(() => expect(axios.get).toHaveBeenCalledTimes(2));
    expect(axios.get).toHaveBeenCalledWith(
      "/api/v2/articles/1/convert-to-post"
    );
    expect(screen.getByRole("button", { name: "変換する" })).toBeDisabled();
  });

  it("送信失敗を表示し、選択を保持して再試行できる", async () => {
    vi.mocked(axios.post)
      .mockRejectedValueOnce(new Error("失敗"))
      .mockResolvedValueOnce({ data: { article_id: 1 } });
    const { onSuccess } = setup();
    await selectFile();
    await userEvent.click(screen.getByRole("button", { name: "変換する" }));
    expect(await screen.findByRole("alert")).toBeInTheDocument();
    await userEvent.click(screen.getByRole("button", { name: "変換する" }));
    await waitFor(() => expect(onSuccess).toHaveBeenCalledWith(1));
    expect(axios.post).toHaveBeenLastCalledWith(
      "/api/v2/articles/1/convert-to-post",
      { file: 9 }
    );
  });

  it("送信中は二重送信と閉じる操作を防ぐ", async () => {
    vi.mocked(axios.post).mockImplementation(() => new Promise(() => {}));
    const { onClose } = setup();
    await selectFile();
    const button = screen.getByRole("button", { name: "変換する" });
    fireEvent.click(button);
    fireEvent.click(button);
    fireEvent.click(screen.getByRole("dialog"));
    expect(axios.post).toHaveBeenCalledTimes(1);
    expect(onClose).not.toHaveBeenCalled();
    expect(screen.getByRole("button", { name: "キャンセル" })).toBeDisabled();
  });

  it("アップロード中は変換と閉じる操作を防ぐ", async () => {
    vi.mocked(axios.post).mockImplementation(() => new Promise(() => {}));
    const { onClose } = setup();
    await selectFile();
    const input = document.querySelector(
      'input[type="file"]'
    ) as HTMLInputElement;
    await userEvent.upload(
      input,
      new File(["addon"], "addon.zip", { type: "application/zip" })
    );
    expect(screen.getByRole("button", { name: "変換する" })).toBeDisabled();
    fireEvent.click(screen.getByRole("dialog"));
    expect(onClose).not.toHaveBeenCalled();
    expect(axios.post).toHaveBeenCalledTimes(1);
    expect(axios.post).toHaveBeenCalledWith(
      "/api/v2/attachments",
      expect.any(FormData),
      expect.any(Object)
    );
  });
});
