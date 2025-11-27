import { Thumbnail } from "@/components/ui/Thumbnail";
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("Thumbnail コンポーネント", () => {
  const mockAttachments: Attachment.Show[] = [
    {
      id: 1,
      thumbnail: "/storage/thumbnails/image1.jpg",
      url: "/storage/images/image1.jpg",
      original_name: "image1.jpg",
      size: 1,
    },
  ];

  it("サムネイルが表示される", () => {
    render(<Thumbnail attachmentId={1} attachments={mockAttachments} />);
    const img = screen.getByRole("img");
    expect(img).toHaveAttribute("src", "/storage/thumbnails/image1.jpg");
  });

  it("Thumbnail 用のクラス名が適用される", () => {
    render(<Thumbnail attachmentId={1} attachments={mockAttachments} />);
    const img = screen.getByRole("img");
    expect(img).toHaveClass("mt-6");
    expect(img).toHaveClass("mb-12");
    expect(img).toHaveClass("rounded-lg");
  });

  it("Image コンポーネントの props が渡される", () => {
    render(
      <Thumbnail
        attachmentId={1}
        attachments={mockAttachments}
        openFullSize={true}
      />
    );
    const link = screen.getByRole("link");
    expect(link).toBeInTheDocument();
  });

  it("デフォルト画像が表示される", () => {
    render(<Thumbnail attachmentId={null} attachments={mockAttachments} />);
    const img = screen.getByRole("img");
    expect(img).toHaveAttribute("src", "/storage/default/image.png");
  });
});
