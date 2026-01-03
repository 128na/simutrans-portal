import { describe, expect, it } from "vitest";
import { render, screen } from "@testing-library/react";
import { ProfileShow } from "@/features/user/ProfileShow";

vi.mock("@/components/ui/Avatar", () => ({
  Avatar: () => <div data-testid="avatar" />,
}));

vi.mock("@/features/user/ProfileLink", () => ({
  ProfileLink: ({ url }: { url: string }) => (
    <span data-testid="profile-link">{url}</span>
  ),
}));

describe("ProfileShow", () => {
  const baseUser = {
    id: 1,
    name: "User Name",
    nickname: "nick",
    profile: {
      id: 10,
      data: {
        avatar: 99,
        description: "hello",
        website: ["https://twitter.com/user", ""],
      },
      attachments: [],
    },
  } as unknown as User.Show;

  it("renders avatar, name, description and websites", () => {
    render(<ProfileShow user={baseUser} />);

    expect(screen.getByText("User Name")).toBeInTheDocument();
    expect(screen.getByText("hello")).toBeInTheDocument();
    expect(screen.getAllByTestId("profile-link").length).toBe(1);
    expect(screen.getByTestId("avatar")).toBeInTheDocument();
  });
});
