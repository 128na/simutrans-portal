import { Avatar } from "@/components/ui/Avatar";
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("Avatar コンポーネント", () => {
  const mockAttachments: Attachment.Show[] = [
    {
      id: 1,
      thumbnail: "/storage/avatars/avatar1.jpg",
      url: "/storage/avatars/avatar1.jpg",
      original_name: "avatar1.jpg",
      size: 1,
    },
  ];

  it("アバター画像が表示される", () => {
    render(<Avatar attachmentId={1} attachments={mockAttachments} />);
    const img = screen.getByRole("img");
    expect(img).toHaveAttribute("src", "/storage/avatars/avatar1.jpg");
  });

  it("デフォルトアバターが表示される", () => {
    render(<Avatar attachmentId={null} attachments={mockAttachments} />);
    const img = screen.getByRole("img");
    expect(img).toHaveAttribute("src", "/storage/default/avatar.png");
  });

  it("カスタムデフォルトアバターが設定される", () => {
    render(
      <Avatar
        attachmentId={null}
        attachments={mockAttachments}
        defaultUrl="/custom-avatar.png"
      />
    );
    const img = screen.getByRole("img");
    expect(img).toHaveAttribute("src", "/custom-avatar.png");
  });

  it("アバター用のクラス名が適用される", () => {
    render(<Avatar attachmentId={1} attachments={mockAttachments} />);
    const img = screen.getByRole("img");
    expect(img).toHaveClass("w-10");
    expect(img).toHaveClass("h-10");
    expect(img).toHaveClass("rounded-full");
  });

  it("Image コンポーネントの props が渡される", () => {
    render(
      <Avatar
        attachmentId={1}
        attachments={mockAttachments}
        openFullSize={true}
      />
    );
    const link = screen.getByRole("link");
    expect(link).toBeInTheDocument();
  });
});
