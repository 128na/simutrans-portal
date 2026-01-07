import { describe, expect, it, vi } from "vitest";
import { fireEvent, render, screen } from "@testing-library/react";
import React, { useState } from "react";
import { ProfileForm } from "@/features/user/ProfileForm";

vi.mock("axios", () => ({
  default: {
    post: vi.fn().mockResolvedValue({ status: 200 }),
  },
}));

vi.mock("@/hooks/useAxiosError", () => ({
  useAxiosError: () => ({
    getError: () => undefined,
    setError: vi.fn(),
  }),
}));

vi.mock("@/hooks/useErrorHandler", () => ({
  useErrorHandler: () => ({ handleErrorWithContext: vi.fn() }),
}));

vi.mock("@/lib/errorHandler", () => ({
  isValidationError: (error: unknown) =>
    Boolean((error as { isValidationError?: boolean })?.isValidationError),
}));

vi.mock("@/components/form/Upload", () => ({
  Upload: ({
    onUploaded,
  }: {
    onUploaded: (payload: { id: number }) => void;
  }) => (
    <button data-testid="upload" onClick={() => onUploaded({ id: 42 })}>
      upload
    </button>
  ),
}));

vi.mock("@/features/attachments/AttachmentEdit", () => ({
  AttachmentEdit: () => <div data-testid="attachment-edit" />,
}));

vi.mock("@/components/ui/ModalFull", () => ({
  ModalFull: ({
    children,
  }: {
    children: (args: { close: () => void }) => React.ReactNode;
  }) => <div>{children({ close: () => {} })}</div>,
}));

vi.mock("@/components/ui/Avatar", () => ({
  Avatar: () => <div data-testid="avatar" />,
}));

vi.mock("@/components/ui/TextError", () => ({
  default: ({ children }: { children: React.ReactNode }) => (
    <div data-testid="text-error">{children}</div>
  ),
}));

vi.mock("@/components/ui/TextSub", () => ({
  default: ({ children, ...props }: React.HTMLProps<HTMLParagraphElement>) => (
    <p {...props}>{children}</p>
  ),
}));

vi.mock("@/components/ui/FormCaption", () => ({
  FormCaption: ({ children }: { children: React.ReactNode }) => (
    <div>{children}</div>
  ),
}));

vi.mock("@/components/ui/TextBadge", () => ({
  default: ({ children }: { children: React.ReactNode }) => (
    <span>{children}</span>
  ),
}));

vi.mock("@/components/ui/MultiColumn", () => ({
  default: ({
    children,
    className,
    ...rest
  }: React.HTMLProps<HTMLDivElement>) => (
    <div {...rest} className={className}>
      {children}
    </div>
  ),
}));

vi.mock("@/components/ui/SortableList", () => ({
  SortableList: ({
    items,
    renderItem,
  }: {
    items: unknown[];
    renderItem: (item: unknown, idx: number) => React.ReactNode;
  }) => (
    <div>
      {items.map((item, idx: number) => (
        <div key={idx}>{renderItem(item, idx)}</div>
      ))}
    </div>
  ),
}));

vi.mock("@/components/ui/Input", () => ({
  default: (props: React.InputHTMLAttributes<HTMLInputElement>) => (
    <input {...props} />
  ),
}));

vi.mock("@/components/ui/Textarea", () => ({
  default: (props: React.TextareaHTMLAttributes<HTMLTextAreaElement>) => (
    <textarea {...props} />
  ),
}));

vi.mock("@/components/ui/Button", () => ({
  default: ({
    children,
    ...props
  }: React.ButtonHTMLAttributes<HTMLButtonElement>) => (
    <button {...props}>{children}</button>
  ),
}));

describe("ProfileForm", () => {
  const baseUser = {
    id: 1,
    name: "user",
    email: "user@example.com",
    nickname: "nick",
    profile: {
      id: 10,
      data: {
        avatar: null,
        description: "",
        website: ["https://example.com"],
      },
    },
  } as const satisfies User.MypageEdit;

  const baseAttachments: Attachment.MypageEdit[] = [];

  it("adds and removes website entries", () => {
    vi.spyOn(window, "confirm").mockReturnValue(true);
    vi.spyOn(window, "alert").mockImplementation(() => {});

    const Wrapper = () => {
      const [user, setUser] = useState<User.MypageEdit>(baseUser);
      const [attachments, setAttachments] =
        useState<Attachment.MypageEdit[]>(baseAttachments);
      const handleUserChange = (nextUser: User.MypageEdit) => setUser(nextUser);
      const handleAttachmentsChange = (
        nextAttachments: Attachment.MypageEdit[]
      ) => setAttachments(nextAttachments);
      return (
        <ProfileForm
          user={user}
          onChangeUser={handleUserChange}
          attachments={attachments}
          onChangeAttachments={handleAttachmentsChange}
        />
      );
    };

    render(<Wrapper />);

    expect(document.querySelectorAll('input[type="url"]').length).toBe(1);

    fireEvent.click(screen.getByText("Webサイトを追加"));
    expect(document.querySelectorAll('input[type="url"]').length).toBe(2);

    fireEvent.click(screen.getAllByText("削除")[0]);
    expect(document.querySelectorAll('input[type="url"]').length).toBe(1);
  });
});
