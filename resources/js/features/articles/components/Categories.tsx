import { t } from "@/utils/translate";

type Props = {
  categories: Category.Show[];
  preview?: boolean;
};

/**
 * pak の slug ごとのタグ色。主要3種以外の pak はまとめて other にする。
 * 色だけに頼らず、タグには必ず pak 名を表示する。
 */
const pakVariants: Record<string, string> = {
  "128-japan": "v2-tag-pak-128-japan",
  "128": "v2-tag-pak-128",
  "64": "v2-tag-pak-64",
};

const categoryClassName = (category: Category.Show): string =>
  category.type === "pak"
    ? `v2-tag-pak ${pakVariants[category.slug] ?? "v2-tag-pak-other"}`
    : "v2-category";

export const Categories = ({ categories, preview }: Props) => {
  return (
    <>
      {categories.map((category) => (
        <a
          href={preview ? "#" : `/search?categoryIds[]=${category.id}`}
          key={`category-${category.id}`}
          className={categoryClassName(category)}
        >
          {t(`category.${category.type}.${category.slug}`)}
        </a>
      ))}
    </>
  );
};
