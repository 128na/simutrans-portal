import { act, screen, waitFor } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { afterEach, expect, it, vi } from "vitest";
import axios from "axios";

vi.mock("axios");

const mounted = vi.hoisted(() => ({
  root: null as import("react-dom/client").Root | null,
}));

vi.mock("react-dom/client", async (importOriginal) => {
  const original = await importOriginal<typeof import("react-dom/client")>();
  return {
    ...original,
    createRoot: (...args: Parameters<typeof original.createRoot>) => {
      mounted.root = original.createRoot(...args);
      return mounted.root;
    },
  };
});

const originalLocation = Object.getOwnPropertyDescriptor(window, "location");

afterEach(async () => {
  await act(async () => mounted.root?.unmount());
  mounted.root = null;
  document.body.replaceChildren();
  if (originalLocation) {
    Object.defineProperty(window, "location", originalLocation);
  }
  vi.restoreAllMocks();
});

it("一覧から紹介記事を変換し、記事一覧へ戻る", async () => {
  const article: Article.MypageShow = {
    id: 73,
    user_id: 4,
    slug: "moving-addon",
    title: "移行するアドオン",
    status: "publish",
    post_type: "addon-introduction",
    published_at: "2026-10-01T00:00:00Z",
    modified_at: "2026-10-01T00:00:00Z",
    total_view_count: null,
    total_conversion_count: null,
    attachments: [],
  };
  document.body.innerHTML = `
    <div id="app-article-list"></div>
    <script id="data-user" type="application/json">{"id":4,"name":"投稿者","nickname":null}</script>
    <script id="data-articles" type="application/json">${JSON.stringify([article])}</script>
  `;
  const assign = vi.fn();
  Object.defineProperty(window, "location", {
    configurable: true,
    value: { ...window.location, assign },
  });
  vi.mocked(axios.get).mockResolvedValue({ data: { attachments: [] } });
  vi.mocked(axios.post)
    .mockResolvedValueOnce({
      data: { data: { id: 51, type: "file", original_name: "moving.zip" } },
    })
    .mockResolvedValueOnce({ data: { article_id: 73 } });

  await act(async () => {
    await import("@/mypage/pages/ArticleListPage");
  });
  expect(axios.get).not.toHaveBeenCalled();
  const user = userEvent.setup();
  await user.click(screen.getByText(article.title));
  await user.click(screen.getByRole("button", { name: "投稿記事へ変換" }));
  await waitFor(() =>
    expect(axios.get).toHaveBeenCalledWith(
      "/api/v2/articles/73/convert-to-post"
    )
  );
  await waitFor(() =>
    expect(
      screen.queryByText("添付ファイルを取得中...")
    ).not.toBeInTheDocument()
  );
  const input = document.querySelector<HTMLInputElement>('input[type="file"]');
  expect(input).not.toBeNull();
  await user.upload(input!, new File(["addon bytes"], "moving.zip"));
  expect(await screen.findByText(/選択中: moving.zip/)).toBeInTheDocument();
  await user.click(screen.getByRole("button", { name: "変換する" }));

  await waitFor(() => expect(assign).toHaveBeenCalledWith("/mypage/articles"));
  expect(axios.post).toHaveBeenNthCalledWith(
    1,
    "/api/v2/attachments",
    expect.any(FormData),
    expect.any(Object)
  );
  expect(axios.post).toHaveBeenNthCalledWith(
    2,
    "/api/v2/articles/73/convert-to-post",
    { file: 51, without_update_modified_at: false }
  );
});
