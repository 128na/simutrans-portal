import { describe, it, expect, vi } from "vitest";
import { render, screen } from "@testing-library/react";
import { ProfileEdit } from "@/features/user/ProfileEdit";

// Mock child components to isolate ProfileEdit testing
vi.mock("@/features/user/ProfileShow", () => ({
  ProfileShow: ({ user, attachments, preview }: any) => (
    <div data-testid="profile-show">
      ProfileShow - {user.name} (preview: {preview ? "true" : "false"})
    </div>
  ),
}));

vi.mock("@/features/user/ProfileForm", () => ({
  ProfileForm: ({ user }: any) => (
    <div data-testid="profile-form">ProfileForm - {user.name}</div>
  ),
}));

describe("ProfileEdit Component", () => {
  const mockUser: User.MypageEdit = {
    id: 1,
    name: "Test User",
    description: "Test Description",
    website: "https://example.com",
    twitter: "https://twitter.com/testuser",
    mastodon: "https://mastodon.social/@testuser",
    imageUrl: "https://example.com/image.jpg",
  };

  const mockAttachments: Attachment.MypageEdit[] = [
    {
      id: 1,
      filename: "image1.jpg",
      size: 102400,
      path: "/storage/attachments/1.jpg",
    },
    {
      id: 2,
      filename: "image2.png",
      size: 51200,
      path: "/storage/attachments/2.png",
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
      description: "Custom Desc",
      website: "https://custom.com",
      twitter: "https://twitter.com/custom",
      mastodon: "https://mastodon.social/@custom",
      imageUrl: "https://custom.com/profile.jpg",
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
        filename: "profile.jpg",
        size: 204800,
        path: "/storage/10.jpg",
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
