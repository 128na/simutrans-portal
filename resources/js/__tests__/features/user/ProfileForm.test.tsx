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
  isValidationError: (error: any) => Boolean(error?.isValidationError),
}));

vi.mock("@/components/form/Upload", () => ({
  Upload: ({ onUploaded }: any) => (
    <button data-testid="upload" onClick={() => onUploaded({ id: 42 })}>
      upload
    </button>
  ),
}));

vi.mock("@/features/attachments/AttachmentEdit", () => ({
  AttachmentEdit: () => <div data-testid="attachment-edit" />,
}));

vi.mock("@/components/ui/ModalFull", () => ({
  ModalFull: ({ children }: any) => <div>{children({ close: () => {} })}</div>,
}));

vi.mock("@/components/ui/Avatar", () => ({
  Avatar: () => <div data-testid="avatar" />,
}));

vi.mock("@/components/ui/TextError", () => ({
  default: ({ children }: any) => (
    <div data-testid="text-error">{children}</div>
  ),
}));

vi.mock("@/components/ui/TextSub", () => ({
  default: ({ children, ...props }: any) => <p {...props}>{children}</p>,
}));

vi.mock("@/components/ui/FormCaption", () => ({
  FormCaption: ({ children }: any) => <div>{children}</div>,
}));

vi.mock("@/components/ui/TextBadge", () => ({
  default: ({ children }: any) => <span>{children}</span>,
}));

vi.mock("@/components/ui/MultiColumn", () => ({
  default: ({ children, classNames, ...rest }: any) => (
    <div {...rest}>{children}</div>
  ),
}));

vi.mock("@/components/ui/SortableList", () => ({
  SortableList: ({ items, renderItem }: any) => (
    <div>
      {items.map((item: any, idx: number) => (
        <div key={idx}>{renderItem(item, idx)}</div>
      ))}
    </div>
  ),
}));

vi.mock("@/components/ui/Input", () => ({
  default: (props: any) => <input {...props} />,
}));

vi.mock("@/components/ui/Textarea", () => ({
  default: (props: any) => <textarea {...props} />,
}));

vi.mock("@/components/ui/Button", () => ({
  default: ({ children, ...props }: any) => (
    <button {...props}>{children}</button>
  ),
}));

describe("ProfileForm", () => {
  const baseUser: User.MypageEdit = {
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
  } as any;

  const baseAttachments: Attachment.MypageEdit[] = [];

  it("adds and removes website entries", () => {
    vi.spyOn(window, "confirm").mockReturnValue(true);
    vi.spyOn(window, "alert").mockImplementation(() => {});

    const Wrapper = () => {
      const [user, setUser] = useState(baseUser);
      const [attachments, setAttachments] = useState(baseAttachments);
      return (
        <ProfileForm
          user={user}
          onChangeUser={setUser}
          attachments={attachments}
          onChangeAttachments={setAttachments}
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
