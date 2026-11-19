import { render, screen, waitFor, within } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { useState } from "react";
import { beforeEach, describe, expect, it, vi } from "vitest";
import axios from "axios";
import { AttachmentField } from "@/features/attachments/AttachmentField";

vi.mock("axios");

const makeAttachment = (
  overrides: Partial<Attachment.MypageEdit>
): Attachment.MypageEdit => ({
  id: 0,
  user_id: 1,
  attachmentable_id: 7,
  attachmentable_type: "Article",
  attachmentable: null,
  type: "file",
  original_name: "",
  thumbnail: "",
  url: "",
  size: 0,
  // どのタイムゾーンで整形しても日付が 11/01 になる時刻
  created_at: "2026-11-01T12:00:00Z",
  ...overrides,
});

const addon = makeAttachment({
  id: 31,
  original_name: "current-addon.zip",
  size: 2048,
});
const otherAddon = makeAttachment({
  id: 32,
  original_name: "other-addon.zip",
  size: 4096,
  created_at: "2026-10-01T00:00:00+09:00",
});
const image = makeAttachment({
  id: 41,
  type: "image",
  original_name: "thumb.png",
  thumbnail: "/storage/thumb.png",
});

type WrapperProps = {
  kind: "file" | "image";
  initialSelected: number | null;
  initialAttachments: Attachment.MypageEdit[];
};

const Wrapper = ({
  kind,
  initialSelected,
  initialAttachments,
}: WrapperProps) => {
  const [selected, setSelected] = useState(initialSelected);
  const [attachments, setAttachments] = useState(initialAttachments);
  return (
    <AttachmentField
      kind={kind}
      attachments={attachments}
      selected={selected}
      attachmentableId={7}
      onUploaded={(a) => {
        setAttachments((prev) => [a, ...prev]);
        setSelected(a.id);
      }}
      onSelectAttachment={setSelected}
      onChangeAttachments={setAttachments}
    />
  );
};

const currentBox = (name = "現在のファイル") =>
  within(screen.getByRole("group", { name }));

describe("AttachmentField", () => {
  beforeEach(() => {
    vi.resetAllMocks();
  });

  it("設定済みのファイルの名前・サイズ・アップロード日を枠に出す", () => {
    render(
      <Wrapper
        kind="file"
        initialSelected={31}
        initialAttachments={[otherAddon, addon]}
      />
    );
    const box = currentBox();
    expect(box.getByText("current-addon.zip")).toBeInTheDocument();
    expect(box.getByText(/2 KB/)).toBeInTheDocument();
    expect(box.getByText(/アップロード日 2026\/11\/01/)).toBeInTheDocument();
    expect(box.queryByText("other-addon.zip")).not.toBeInTheDocument();
    expect(box.queryByText("ファイルが未選択です")).not.toBeInTheDocument();
  });

  it("未設定のときだけ「ファイルが未選択です」を出す", () => {
    render(
      <Wrapper
        kind="file"
        initialSelected={null}
        initialAttachments={[addon]}
      />
    );
    const box = currentBox();
    expect(box.getByText("ファイルが未選択です")).toBeInTheDocument();
    expect(box.queryByText("current-addon.zip")).not.toBeInTheDocument();
  });

  it("画像は小さな画像と名前を出し、未設定は「未設定」と出す", () => {
    const { unmount } = render(
      <Wrapper kind="image" initialSelected={41} initialAttachments={[image]} />
    );
    const box = currentBox("現在の画像");
    expect(box.getByText("thumb.png")).toBeInTheDocument();
    expect(
      screen.getByRole("group", { name: "現在の画像" }).querySelector("img")
    ).toHaveAttribute("src", "/storage/thumb.png");
    expect(
      screen.getByRole("button", { name: "新しい画像をアップロード" })
    ).toBeInTheDocument();
    unmount();

    render(
      <Wrapper
        kind="image"
        initialSelected={null}
        initialAttachments={[image]}
      />
    );
    expect(currentBox("現在の画像").getByText("未設定")).toBeInTheDocument();
  });

  it("一覧に無いファイルが設定されているときは、未選択ではなく取得できない旨を出す", () => {
    render(
      <Wrapper kind="file" initialSelected={99} initialAttachments={[addon]} />
    );
    const box = currentBox();
    expect(
      box.getByText("設定済みのファイル（ID: 99）の詳細を表示できません")
    ).toBeInTheDocument();
    expect(box.queryByText("ファイルが未選択です")).not.toBeInTheDocument();
  });

  it("標準のファイル入力は見せず、ボタンからファイル選択を開く", async () => {
    const user = userEvent.setup();
    render(
      <Wrapper kind="file" initialSelected={null} initialAttachments={[]} />
    );
    const input =
      document.querySelector<HTMLInputElement>('input[type="file"]')!;
    expect(input).not.toBeVisible();
    expect(input).toHaveAttribute("tabindex", "-1");
    expect(screen.queryByText(/選択されていません/)).not.toBeInTheDocument();

    const click = vi.spyOn(input, "click");
    const button = screen.getByRole("button", {
      name: "新しいファイルをアップロード",
    });
    await user.tab();
    expect(button).toHaveFocus();
    await user.keyboard("{Enter}");
    expect(click).toHaveBeenCalledTimes(1);
  });

  it("アップロードすると枠がアップロードしたファイルに切り替わる", async () => {
    const uploaded = makeAttachment({
      id: 55,
      original_name: "uploaded-addon.zip",
      size: 1024 * 1024 * 3,
    });
    vi.mocked(axios.post).mockResolvedValueOnce({ data: { data: uploaded } });
    render(
      <Wrapper kind="file" initialSelected={31} initialAttachments={[addon]} />
    );
    expect(currentBox().getByText("current-addon.zip")).toBeInTheDocument();

    await userEvent.upload(
      document.querySelector<HTMLInputElement>('input[type="file"]')!,
      new File(["addon"], "uploaded-addon.zip", { type: "application/zip" })
    );

    await waitFor(() =>
      expect(currentBox().getByText("uploaded-addon.zip")).toBeInTheDocument()
    );
    expect(currentBox().getByText(/3 MB/)).toBeInTheDocument();
    expect(
      currentBox().queryByText("current-addon.zip")
    ).not.toBeInTheDocument();
  });

  it("アップロード済みから選ぶと枠が切り替わり、選択画面が閉じる", async () => {
    const user = userEvent.setup();
    render(
      <Wrapper
        kind="file"
        initialSelected={31}
        initialAttachments={[addon, otherAddon]}
      />
    );
    await user.click(
      screen.getByRole("button", { name: "アップロード済みから変更" })
    );
    const dialog = screen.getByRole("dialog");
    await user.click(within(dialog).getByText("other-addon.zip"));

    expect(screen.queryByRole("dialog")).not.toBeInTheDocument();
    expect(currentBox().getByText("other-addon.zip")).toBeInTheDocument();
    expect(
      currentBox().queryByText("current-addon.zip")
    ).not.toBeInTheDocument();
  });
});
