import { describe, it, expect } from "vitest";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { AttachmentTable } from "@/features/attachments/AttachmentTable";

/**
 * ファイル一覧のページ送り（#688）。
 * 2ページ目以降で検索・並べ替えをしたり件数が減ったりしても、空の表にならないこと。
 */

const LIMIT = 15;

/**
 * name の順に作成日を1時間ずつ新しくする（既定の並びは作成日の降順なので、後ろの要素ほど先頭に来る）。
 * id・user_id はほかの数と重ならない値にする。
 */
const makeAttachments = (names: string[]): Attachment.MypageEdit[] =>
  names.map((name, i) => ({
    id: 1001 + i,
    user_id: 77,
    url: `/storage/${name}`,
    original_name: name,
    type: "file",
    size: 1024 + i,
    created_at: new Date(Date.UTC(2024, 0, 1, i)).toISOString(),
    attachmentable_id: null,
    attachmentable_type: "Article",
    attachmentable: null,
    thumbnail: "",
  }));

const numbered = (prefix: string, count: number) =>
  Array.from(
    { length: count },
    (_, i) => `${prefix}-${String(i + 1).padStart(2, "0")}.zip`
  );

const renderTable = (attachments: Attachment.MypageEdit[]) =>
  render(
    <AttachmentTable
      attachments={attachments}
      limit={LIMIT}
      attachmentableId={null}
      attachmentableType={null}
      types={["file"]}
      selected={null}
    />
  );

const goToPage = async (
  user: ReturnType<typeof userEvent.setup>,
  page: number
) => {
  for (let p = 2; p <= page; p++) {
    await user.click(screen.getByRole("button", { name: String(p) }));
  }
};

describe("AttachmentTable のページ送り", () => {
  it("16件のとき2ページ目で検索すると、1件に絞った結果が出る", async () => {
    const user = userEvent.setup();
    renderTable(makeAttachments([...numbered("other", 15), "target.zip"]));

    await goToPage(user, 2);
    await user.type(screen.getByPlaceholderText("検索"), "target");

    expect(screen.getByText("target.zip")).toBeInTheDocument();
    expect(screen.getByRole("button", { name: "1" })).toHaveClass(
      "v2-pagination-item-active"
    );
  });

  it("絞り込んでも2ページ以上残るときは、1ページ目の先頭から出す", async () => {
    const user = userEvent.setup();
    // alpha と beta を交互に作り、alpha だけで20件（2ページ）残るようにする
    const names = numbered("alpha", 20).flatMap((alpha, i) => [
      alpha,
      `beta-${String(i + 1).padStart(2, "0")}.zip`,
    ]);
    renderTable(makeAttachments(names));

    await goToPage(user, 3);
    await user.type(screen.getByPlaceholderText("検索"), "alpha");

    // 作成日の降順なので、1ページ目は alpha-20 〜 alpha-06、2ページ目は alpha-05 〜 alpha-01
    expect(screen.getByText("alpha-20.zip")).toBeInTheDocument();
    expect(screen.queryByText("alpha-05.zip")).not.toBeInTheDocument();
  });

  it("2ページ目で並べ替えると、新しい並びの1ページ目を出す", async () => {
    const user = userEvent.setup();
    renderTable(makeAttachments(numbered("file", 20)));

    await goToPage(user, 2);
    await user.click(screen.getByRole("columnheader", { name: /ファイル名/ }));

    // ファイル名の昇順の1ページ目は file-01 〜 file-15
    expect(screen.getByText("file-01.zip")).toBeInTheDocument();
    expect(screen.queryByText("file-16.zip")).not.toBeInTheDocument();
  });

  it("件数が減って今のページが無くなったら、最後のページを出す", async () => {
    const user = userEvent.setup();
    const { rerender } = renderTable(makeAttachments(numbered("file", 20)));

    await goToPage(user, 2);
    rerender(
      <AttachmentTable
        attachments={makeAttachments(numbered("file", 10))}
        limit={LIMIT}
        attachmentableId={null}
        attachmentableType={null}
        types={["file"]}
        selected={null}
      />
    );

    expect(screen.getByText("file-10.zip")).toBeInTheDocument();
    expect(screen.getByText("file-01.zip")).toBeInTheDocument();
  });

  it("件数が減って丸めた後に件数が戻っても、古いページへ勝手に移らない", async () => {
    const user = userEvent.setup();
    const props = {
      limit: LIMIT,
      attachmentableId: null,
      attachmentableType: null,
      types: ["file"] as AttachmentType[],
      selected: null,
    };
    const { rerender } = render(
      <AttachmentTable
        attachments={makeAttachments(numbered("file", 20))}
        {...props}
      />
    );

    await goToPage(user, 2);
    rerender(
      <AttachmentTable
        attachments={makeAttachments(numbered("file", 10))}
        {...props}
      />
    );
    rerender(
      <AttachmentTable
        attachments={makeAttachments(numbered("file", 20))}
        {...props}
      />
    );

    // 1ページ目（作成日の降順で file-20 〜 file-06）のまま
    expect(screen.getByText("file-20.zip")).toBeInTheDocument();
    expect(screen.queryByText("file-05.zip")).not.toBeInTheDocument();
  });
});
