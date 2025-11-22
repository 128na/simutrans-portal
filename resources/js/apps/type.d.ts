namespace Category {
  type Search = {
    id: number;
    type: CategoryType;
    slug: string;
    need_admin: boolean;
  };
  type Grouping = {
    [K in CategoryType]: Search[];
  };
  type Type =
    | "pak"
    | "addon"
    | "pak128_position"
    | "license"
    | "page"
    | "double_slope";
}

namespace Article {
  type PostType = "addon-post" | "addon-introduction" | "page" | "markdown";
  type Status = "publish" | "reservation" | "draft" | "trash" | "private";
}

namespace User {
  type Role = "admin" | "user";
}
