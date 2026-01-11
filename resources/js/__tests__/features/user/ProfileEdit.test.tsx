import { describe, it, expect, vi } from "vitest";
import { render, screen } from "@testing-library/react";
import { ProfileEdit } from "@/features/user/ProfileEdit";

// Mock child components to isolate ProfileEdit testing
vi.mock("@/features/user/ProfileShow", () => ({
  ProfileShow: ({
    user,
    preview,
  }: {
    user: User.MypageEdit;
    attachments: Attachment.MypageEdit[];
    preview: boolean;
  }) => (
    <div data-testid="profile-show">
      ProfileShow - {user.name} (preview: {preview ? "true" : "false"})
    </div>
  ),
}));

vi.mock("@/features/user/ProfileForm", () => ({
  ProfileForm: ({ user }: { user: User.MypageEdit }) => (
    <div data-testid="profile-form">ProfileForm - {user.name}</div>
  ),
}));

describe("ProfileEdit Component", () => {
  const mockUser: User.MypageEdit = {
    id: 1,
    name: "Test User",
    email: "test@example.com",
    nickname: "test_nickname",
    profile: {} as Profile.Edit,
  };

  const mockAttachments: Attachment.MypageEdit[] = [
    {
      id: 1,
      user_id: 1,
      url: "/storage/attachments/1.jpg",
      original_name: "image1.jpg",
      type: "image",
      size: 102400,
      created_at: "2024-01-01T00:00:00Z",
      attachmentable_id: 1,
      attachmentable_type: "Profile",
      attachmentable: null,
      thumbnail: "/storage/attachments/1.jpg",
    },
    {
      id: 2,
      user_id: 1,
      url: "/storage/attachments/2.png",
      original_name: "image2.png",
      type: "image",
      size: 51200,
      created_at: "2024-01-01T00:00:00Z",
      attachmentable_id: 1,
      attachmentable_type: "Profile",
      attachmentable: null,
      thumbnail: "/storage/attachments/2.png",
    },
  ];

  it("レンダリング: 全セクション表示", () => {
    const onChangeUser = vi.fn();
    const onChangeAttachments = vi.fn();

    render(
      <ProfileEdit
        user={mockUser}
        onChangeUser={onChangeUser}
        onChangeAttachments={onChangeAttachments}
        attachments={mockAttachments}
      />
    );

    // Check for preview section with badge
    const badge = screen.getByText("プレビュー表示");
    expect(badge.classList.contains("v2-badge-warn")).toBe(true);

    // Check warning text
    expect(screen.getByText(/リンクが反応しない/i)).toBeInTheDocument();

    // Check for ProfileShow and ProfileForm
    expect(screen.getByTestId("profile-show")).toBeInTheDocument();
    expect(screen.getByTestId("profile-form")).toBeInTheDocument();
  });

  it("プレビュー: ProfileShow コンポーネント表示", () => {
    const onChangeUser = vi.fn();
    const onChangeAttachments = vi.fn();

    render(
      <ProfileEdit
        user={mockUser}
        onChangeUser={onChangeUser}
        onChangeAttachments={onChangeAttachments}
        attachments={mockAttachments}
      />
    );

    const profileShow = screen.getByTestId("profile-show");
    expect(profileShow).toBeInTheDocument();
    expect(profileShow.textContent).toContain("Test User");
    expect(profileShow.textContent).toContain("preview: true");
  });

  it("フォーム: ProfileForm コンポーネント表示", () => {
    const onChangeUser = vi.fn();
    const onChangeAttachments = vi.fn();

    render(
      <ProfileEdit
        user={mockUser}
        onChangeUser={onChangeUser}
        onChangeAttachments={onChangeAttachments}
        attachments={mockAttachments}
      />
    );

    const profileForm = screen.getByTestId("profile-form");
    expect(profileForm).toBeInTheDocument();
    expect(profileForm.textContent).toContain("Test User");
  });

  it("プロップ: ユーザー情報を正確に渡す", () => {
    const onChangeUser = vi.fn();
    const onChangeAttachments = vi.fn();

    const customUser: User.MypageEdit = {
      id: 999,
      name: "Custom Name",
      email: "custom@example.com",
      nickname: "custom_nick",
      profile: {} as Profile.Edit,
    };

    render(
      <ProfileEdit
        user={customUser}
        onChangeUser={onChangeUser}
        onChangeAttachments={onChangeAttachments}
        attachments={[]}
      />
    );

    const profileShow = screen.getByTestId("profile-show");
    expect(profileShow.textContent).toContain("Custom Name");
  });

  it("プロップ: 添付ファイル情報を正確に渡す", () => {
    const onChangeUser = vi.fn();
    const onChangeAttachments = vi.fn();

    const customAttachments: Attachment.MypageEdit[] = [
      {
        id: 10,
        user_id: 1,
        url: "/storage/10.jpg",
        original_name: "profile.jpg",
        type: "image",
        size: 204800,
        created_at: "2024-01-01T00:00:00Z",
        attachmentable_id: 1,
        attachmentable_type: "Profile",
        attachmentable: null,
        thumbnail: "/storage/10.jpg",
      },
    ];

    render(
      <ProfileEdit
        user={mockUser}
        onChangeUser={onChangeUser}
        onChangeAttachments={onChangeAttachments}
        attachments={customAttachments}
      />
    );

    const profileShow = screen.getByTestId("profile-show");
    expect(profileShow).toBeInTheDocument();
  });

  it("警告バッジ: プレビュー警告表示", () => {
    const onChangeUser = vi.fn();
    const onChangeAttachments = vi.fn();

    render(
      <ProfileEdit
        user={mockUser}
        onChangeUser={onChangeUser}
        onChangeAttachments={onChangeAttachments}
        attachments={[]}
      />
    );

    // Check for badge and warning text
    const badge = screen.getByText("プレビュー表示");
    expect(badge).toBeInTheDocument();

    const warningText =
      screen.getByText(/リンクが反応しないようになっています/);
    expect(warningText).toBeInTheDocument();
  });

  it("空の添付ファイル: 空配列対応", () => {
    const onChangeUser = vi.fn();
    const onChangeAttachments = vi.fn();

    render(
      <ProfileEdit
        user={mockUser}
        onChangeUser={onChangeUser}
        onChangeAttachments={onChangeAttachments}
        attachments={[]}
      />
    );

    expect(screen.getByTestId("profile-show")).toBeInTheDocument();
    expect(screen.getByTestId("profile-form")).toBeInTheDocument();
  });
});
