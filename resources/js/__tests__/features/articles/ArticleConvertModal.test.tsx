import { render, screen, waitFor, fireEvent } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { beforeEach, describe, expect, it, vi } from "vitest";
import axios from "axios";
import { ArticleConvertModal } from "@/features/articles/ArticleConvertModal";
import { ArticleModal } from "@/features/articles/ArticleModal";
import { ToastProvider } from "@/providers/ToastProvider";

vi.mock("axios");

const attachment: Attachment.MypageEdit = {
  id: 9,
  user_id: 1,
  attachmentable_id: 1,
  attachmentable_type: "Article",
  attachmentable: null,
  type: "file",
  original_name: "existing-addon.zip",
  thumbnail: "",
  url: "/attachments/9",
  size: 1024,
  created_at: "2026-10-24T00:00:00Z",
};

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
  await user.click(screen.getByText("existing-addon.zip"));
};

describe("記事の変換", () => {
  beforeEach(() => {
    vi.resetAllMocks();
    vi.mocked(axios.get).mockResolvedValue({
      data: { attachments: [attachment] },
    });
  });

  it.each([
    ["addon-introduction", "draft", true],
    ["addon-introduction", "publish", true],
    ["addon-introduction", "private", true],
    ["addon-introduction", "reservation", true],
    ["addon-introduction", "trash", false],
    ["addon-post", "draft", false],
    ["markdown", "publish", false],
    ["page", "publish", false],
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
    if (visible) {
      const actions = screen
        .getAllByRole("button")
        .map((button) => button.textContent);
      expect(actions.slice(-3)).toEqual([
        "URLをコピー",
        "投稿記事へ変換",
        "削除する",
      ]);
    }
  });

  it("配布ファイル未選択では送信できない", async () => {
    setup();
    await screen.findByText("選択中: 未選択");
    const button = screen.getByRole("button", { name: "変換する" });
    expect(button).toBeDisabled();
    fireEvent.click(button);
    expect(axios.post).not.toHaveBeenCalled();
  });

  it("実際の添付選択UIで配布ファイルのみ表示し、選択結果を送信する", async () => {
    vi.mocked(axios.get).mockResolvedValue({
      data: {
        attachments: [
          attachment,
          {
            ...attachment,
            id: 10,
            type: "image",
            original_name: "preview.png",
          },
          {
            ...attachment,
            id: 11,
            attachmentable_id: 2,
            original_name: "other-article.zip",
          },
        ],
      },
    });
    vi.mocked(axios.post).mockResolvedValue({ data: { article_id: 1 } });
    const { onSuccess } = setup();
    await userEvent.click(
      await screen.findByRole("button", {
        name: "アップロード済みのファイルから選択",
      })
    );
    expect(screen.queryByText("preview.png")).not.toBeInTheDocument();
    expect(screen.queryByText("other-article.zip")).not.toBeInTheDocument();
    await userEvent.click(screen.getByText("existing-addon.zip"));
    expect(screen.getByText("選択中: existing-addon.zip")).toBeInTheDocument();
    expect(screen.getAllByRole("dialog")).toHaveLength(1);
    await userEvent.click(screen.getByRole("button", { name: "変換する" }));
    await waitFor(() => expect(onSuccess).toHaveBeenCalledWith(1));
    expect(axios.post).toHaveBeenCalledWith(
      "/api/v2/articles/1/convert-to-post",
      { file: 9 }
    );
  });

  it("アップロード成功でファイルを自動選択し、その添付IDで変換する", async () => {
    const uploaded = {
      ...attachment,
      id: 15,
      original_name: "uploaded-addon.zip",
    };
    vi.mocked(axios.post)
      .mockResolvedValueOnce({ data: { data: uploaded } })
      .mockResolvedValueOnce({ data: { article_id: 1 } });
    const { onSuccess } = setup();
    await screen.findByText("選択中: 未選択");
    const input = document.querySelector(
      'input[type="file"]'
    ) as HTMLInputElement;
    await userEvent.upload(
      input,
      new File(["addon"], "uploaded-addon.zip", { type: "application/zip" })
    );
    expect(
      await screen.findByText("選択中: uploaded-addon.zip")
    ).toBeInTheDocument();
    await userEvent.click(screen.getByRole("button", { name: "変換する" }));
    await waitFor(() => expect(onSuccess).toHaveBeenCalledWith(1));
    expect(axios.post).toHaveBeenLastCalledWith(
      "/api/v2/articles/1/convert-to-post",
      { file: 15 }
    );
  });

  it("422のフィールドエラーを表示し選択を保持して再送できる", async () => {
    vi.mocked(axios.post)
      .mockRejectedValueOnce({
        isAxiosError: true,
        response: {
          status: 422,
          data: { errors: { file: ["このファイルは利用できません"] } },
        },
      })
      .mockResolvedValueOnce({ data: { article_id: 1 } });
    const { onSuccess } = setup();
    await selectFile();
    await userEvent.click(screen.getByRole("button", { name: "変換する" }));
    expect(await screen.findByRole("alert")).toHaveTextContent(
      "このファイルは利用できません"
    );
    expect(screen.getByText("選択中: existing-addon.zip")).toBeInTheDocument();
    await userEvent.click(screen.getByRole("button", { name: "変換する" }));
    await waitFor(() => expect(onSuccess).toHaveBeenCalledWith(1));
    expect(axios.post).toHaveBeenLastCalledWith(
      "/api/v2/articles/1/convert-to-post",
      { file: 9 }
    );
  });

  it("アップロード失敗をモーダル内に表示し再アップロードできる", async () => {
    vi.spyOn(window, "alert").mockImplementation(() => {});
    vi.mocked(axios.post)
      .mockRejectedValueOnce({
        isAxiosError: true,
        response: {
          status: 500,
          data: { message: "アップロードに失敗しました" },
        },
      })
      .mockResolvedValueOnce({ data: { data: attachment } });
    setup();
    await screen.findByText("選択中: 未選択");
    const input = document.querySelector(
      'input[type="file"]'
    ) as HTMLInputElement;
    await userEvent.upload(
      input,
      new File(["failed"], "failed.zip", { type: "application/zip" })
    );
    expect(await screen.findByRole("alert")).toHaveTextContent(
      "アップロードに失敗しました"
    );
    expect(screen.getByRole("button", { name: "変換する" })).toBeDisabled();
    await userEvent.upload(
      input,
      new File(["retry"], "retry.zip", { type: "application/zip" })
    );
    expect(
      await screen.findByText("選択中: existing-addon.zip")
    ).toBeInTheDocument();
    expect(screen.queryByRole("alert")).not.toBeInTheDocument();
    expect(screen.getByRole("button", { name: "変換する" })).toBeEnabled();
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
