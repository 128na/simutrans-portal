import { describe, expect, it } from "vitest";
import { render, screen } from "@testing-library/react";
import { ProfileLink } from "@/features/user/ProfileLink";

vi.mock("@/features/user/ProfileIcon", () => ({
  ProfileIcon: ({ service }: any) => (
    <span data-testid="icon">{service.service}</span>
  ),
}));

describe("ProfileLink", () => {
  it("uses social icon when service is known", () => {
    render(<ProfileLink url="https://twitter.com/example" preview={false} />);
    const icon = screen.getByTestId("icon");
    expect(icon.textContent).toBe("Twitter");
    expect(screen.getByRole("link")).toHaveAttribute(
      "href",
      "https://twitter.com/example"
    );
  });

  it("falls back to host text when service is unknown", () => {
    render(
      <ProfileLink url="https://unknown.example.com/profile" preview={false} />
    );
    expect(screen.getByRole("link")).toHaveTextContent("unknown.example.com");
  });
});
