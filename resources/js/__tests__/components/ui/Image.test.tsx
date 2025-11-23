import { Image } from "@/components/ui/Image";
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("Image コンポーネント", () => {
  const mockAttachments: Attachment.Show[] = [
    {
      id: 1,
      thumbnail: "/storage/thumbnails/image1.jpg",
      url: "/storage/images/image1.jpg",
      original_name: "image1.jpg",
    },
    {
      id: 2,
      thumbnail: "/storage/thumbnails/image2.jpg",
      url: "/storage/images/image2.jpg",
      original_name: "image2.jpg",
    },
  ];

  it("添付ファイルの画像が表示される", () => {
    render(<Image attachmentId={1} attachments={mockAttachments} />);
    const img = screen.getByRole("img");
    expect(img).toHaveAttribute("src", "/storage/thumbnails/image1.jpg");
  });

  it("デフォルト画像が表示される", () => {
    render(<Image attachmentId={null} attachments={mockAttachments} />);
    const img = screen.getByRole("img");
    expect(img).toHaveAttribute("src", "/storage/default/image.png");
  });

  it("カスタムデフォルト画像が設定される", () => {
    render(
      <Image
        attachmentId={null}
        attachments={mockAttachments}
        defaultUrl="/custom-default.jpg"
      />
    );
    const img = screen.getByRole("img");
    expect(img).toHaveAttribute("src", "/custom-default.jpg");
  });

  it("存在しない添付ファイルIDの場合はデフォルト画像を表示", () => {
    render(<Image attachmentId={999} attachments={mockAttachments} />);
    const img = screen.getByRole("img");
    expect(img).toHaveAttribute("src", "/storage/default/image.png");
  });

  it("openFullSize が true の場合はリンクで囲まれる", () => {
    render(
      <Image
        attachmentId={1}
        attachments={mockAttachments}
        openFullSize={true}
      />
    );
    const link = screen.getByRole("link");
    expect(link).toHaveAttribute("href", "/storage/images/image1.jpg");
    expect(link).toHaveAttribute("target", "_blank");
    expect(link).toHaveAttribute("rel", "noopener noreferrer");
  });

  it("openFullSize が false の場合はリンクなし", () => {
    render(
      <Image
        attachmentId={1}
        attachments={mockAttachments}
        openFullSize={false}
      />
    );
    expect(screen.queryByRole("link")).not.toBeInTheDocument();
  });

  it("カスタムクラス名が適用される", () => {
    render(
      <Image
        attachmentId={1}
        attachments={mockAttachments}
        className="custom-class"
      />
    );
    const img = screen.getByRole("img");
    expect(img).toHaveClass("custom-class");
  });

  it("デフォルトのクラス名が適用される", () => {
    render(<Image attachmentId={1} attachments={mockAttachments} />);
    const img = screen.getByRole("img");
    expect(img).toHaveClass("w-80");
    expect(img).toHaveClass("rounded-lg");
  });
});
