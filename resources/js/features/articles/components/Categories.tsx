import { t } from "@/utils/translate";

type Props = {
  categories: Category.Show[];
  preview?: boolean;
};
export const Categories = ({ categories, preview }: Props) => {
  return (
    <>
      {categories.map((category) => (
        <a
          href={preview ? "#" : `/search?categoryIds[]=${category.id}`}
          key={`category-${category.id}`}
          className="v2-category"
        >
          {t(`category.${category.type}.${category.slug}`)}
        </a>
      ))}
    </>
  );
};
