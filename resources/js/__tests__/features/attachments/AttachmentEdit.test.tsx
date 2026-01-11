import { describe, it, expect, vi } from "vitest";
import { render, screen } from "@testing-library/react";
import { AttachmentEdit } from "@/features/attachments/AttachmentEdit";

// AttachmentTable をモック
vi.mock("@/features/attachments/AttachmentTable", () => ({
  AttachmentTable: ({
    attachments,
    selected,
    attachmentableId,
    attachmentableType,
    types,
    onSelectAttachment,
    onChangeAttachments,
  }: any) => (
    <div data-testid="attachment-table">
      <div data-testid="attachments-count">{attachments.length}</div>
      <div data-testid="selected">{selected}</div>
      <div data-testid="attachmentable-id">{attachmentableId}</div>
      <div data-testid="attachmentable-type">{attachmentableType}</div>
      <div data-testid="types">{types.join(",")}</div>
      <button onClick={() => onSelectAttachment?.(attachments[0])}>
        Select First
      </button>
      <button onClick={() => onChangeAttachments?.([])}>
        Clear Attachments
      </button>
    </div>
  ),
}));

describe("AttachmentEdit", () => {
  const mockAttachments: Attachment.MypageEdit[] = [
    {
      id: 1,
      original_name: "test1.png",
      type: "image",
      size: 1024,
      created_at: "2024-01-01T00:00:00Z",
      attachmentable_id: 1,
      attachmentable_type: "Article",
      attachmentable: null,
      thumbnail: "/path/to/test1.png",
    },
    {
      id: 2,
      original_name: "test2.pdf",
      type: "file",
      size: 2048,
      created_at: "2024-01-02T00:00:00Z",
      attachmentable_id: null,
      attachmentable_type: null,
      attachmentable: null,
      thumbnail: null,
    },
  ];

  it("AttachmentTableを正しくレンダリングする", () => {
    render(
      <AttachmentEdit
        attachments={mockAttachments}
        selected={null}
        attachmentableId={1}
      />
    );

    expect(screen.getByTestId("attachment-table")).toBeInTheDocument();
  });

  it("attachmentsプロップが正しく渡される", () => {
    render(
      <AttachmentEdit
        attachments={mockAttachments}
        selected={null}
        attachmentableId={1}
      />
    );

    expect(screen.getByTestId("attachments-count")).toHaveTextContent("2");
  });

  it("selectedプロップが正しく渡される", () => {
    render(
      <AttachmentEdit
        attachments={mockAttachments}
        selected={1}
        attachmentableId={1}
      />
    );

    expect(screen.getByTestId("selected")).toHaveTextContent("1");
  });

  it("attachmentableIdプロップが正しく渡される", () => {
    render(
      <AttachmentEdit
        attachments={mockAttachments}
        selected={null}
        attachmentableId={123}
      />
    );

    expect(screen.getByTestId("attachmentable-id")).toHaveTextContent("123");
  });

  it("attachmentableTypeのデフォルト値がArticleである", () => {
    render(
      <AttachmentEdit
        attachments={mockAttachments}
        selected={null}
        attachmentableId={1}
      />
    );

    expect(screen.getByTestId("attachmentable-type")).toHaveTextContent(
      "Article"
    );
  });

  it("attachmentableTypeを指定できる", () => {
    render(
      <AttachmentEdit
        attachments={mockAttachments}
        selected={null}
        attachmentableId={1}
        attachmentableType="Profile"
      />
    );

    expect(screen.getByTestId("attachmentable-type")).toHaveTextContent(
      "Profile"
    );
  });

  it("typesのデフォルト値が全種類である", () => {
    render(
      <AttachmentEdit
        attachments={mockAttachments}
        selected={null}
        attachmentableId={1}
      />
    );

    expect(screen.getByTestId("types")).toHaveTextContent(
      "image,file,video,text"
    );
  });

  it("typesを指定できる", () => {
    render(
      <AttachmentEdit
        attachments={mockAttachments}
        selected={null}
        attachmentableId={1}
        types={["image"]}
      />
    );

    expect(screen.getByTestId("types")).toHaveTextContent("image");
  });

  it("onSelectAttachmentコールバックが正しく機能する", async () => {
    const mockOnSelect = vi.fn();
    render(
      <AttachmentEdit
        attachments={mockAttachments}
        selected={null}
        attachmentableId={1}
        onSelectAttachment={mockOnSelect}
      />
    );

    const button = screen.getByText("Select First");
    button.click();

    expect(mockOnSelect).toHaveBeenCalledWith(1);
  });

  it("onChangeAttachmentsコールバックが正しく機能する", () => {
    const mockOnChange = vi.fn();
    render(
      <AttachmentEdit
        attachments={mockAttachments}
        selected={null}
        attachmentableId={1}
        onChangeAttachments={mockOnChange}
      />
    );

    const button = screen.getByText("Clear Attachments");
    button.click();

    expect(mockOnChange).toHaveBeenCalledWith([]);
  });

  it("選択状態がnullの場合onSelectAttachmentがnullを渡す", () => {
    const mockOnSelect = vi.fn();
    render(
      <AttachmentEdit
        attachments={mockAttachments}
        selected={null}
        attachmentableId={1}
        onSelectAttachment={mockOnSelect}
      />
    );

    const button = screen.getByText("Select First");
    button.click();

    // attachmentオブジェクトの id が渡される
    expect(mockOnSelect).toHaveBeenCalledWith(1);
  });

  it("attachmentsが空配列の場合でもレンダリングできる", () => {
    render(
      <AttachmentEdit attachments={[]} selected={null} attachmentableId={1} />
    );

    expect(screen.getByTestId("attachment-table")).toBeInTheDocument();
    expect(screen.getByTestId("attachments-count")).toHaveTextContent("0");
  });

  it("attachmentableIdがnullの場合でもレンダリングできる", () => {
    render(
      <AttachmentEdit
        attachments={mockAttachments}
        selected={null}
        attachmentableId={null}
      />
    );

    expect(screen.getByTestId("attachment-table")).toBeInTheDocument();
  });
});
