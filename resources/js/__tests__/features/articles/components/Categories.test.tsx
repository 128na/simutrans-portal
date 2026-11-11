import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import { Categories } from "@/features/articles/components/Categories";

describe("カテゴリのタグ", () => {
  it("pak は pak ごとに色を分け、pak 名を表示する", () => {
    render(
      <Categories
        categories={[
          { id: 11, type: "pak", slug: "128-japan" },
          { id: 12, type: "pak", slug: "64" },
          { id: 13, type: "pak", slug: "128" },
        ]}
      />
    );

    const japan = screen.getByRole("link", { name: "Pak128.Japan" });
    expect(japan).toHaveClass("v2-tag-pak", "v2-tag-pak-128-japan");
    expect(japan).toHaveAttribute("href", "/search?categoryIds[]=11");

    const pak64 = screen.getByRole("link", { name: "Pak64" });
    expect(pak64).toHaveClass("v2-tag-pak", "v2-tag-pak-64");
    expect(pak64).not.toHaveClass("v2-tag-pak-128-japan");

    expect(screen.getByRole("link", { name: "Pak128" })).toHaveClass(
      "v2-tag-pak-128"
    );
  });

  it("主要3種以外の pak は other の色にする", () => {
    render(<Categories categories={[{ id: 21, type: "pak", slug: "256" }]} />);

    expect(screen.getByRole("link", { name: "Pak256" })).toHaveClass(
      "v2-tag-pak",
      "v2-tag-pak-other"
    );
  });

  it("種類・ライセンスは枠だけのタグにする", () => {
    render(
      <Categories
        categories={[
          { id: 31, type: "addon", slug: "trains" },
          { id: 32, type: "license", slug: "cc-by" },
        ]}
      />
    );

    const links = screen.getAllByRole("link");
    expect(links).toHaveLength(2);
    for (const link of links) {
      expect(link).toHaveClass("v2-category");
      expect(link).not.toHaveClass("v2-tag-pak");
    }
  });

  it("プレビューではリンク先を # にする", () => {
    render(
      <Categories
        preview
        categories={[{ id: 41, type: "addon", slug: "trains" }]}
      />
    );

    expect(screen.getByRole("link", { name: "鉄道車両" })).toHaveAttribute(
      "href",
      "#"
    );
  });
});
