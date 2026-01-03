import { describe, expect, it } from "vitest";
import { render, screen } from "@testing-library/react";
import { ProfileIcon } from "@/features/user/ProfileIcon";

describe("ProfileIcon", () => {
  it("renders img with service attributes", () => {
    render(
      <ProfileIcon
        service={{ service: "GitHub", src: "/github.svg", match: true }}
      />
    );

    const img = screen.getByRole("img", { name: "GitHub" });
    expect(img).toHaveAttribute("src", "/github.svg");
    expect(img).toHaveAttribute("title", "GitHub");
  });
});
