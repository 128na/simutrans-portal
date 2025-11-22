import { t } from "@/lang/translate";

type Props = {
  categories: Category.Search[];
  preview?: boolean;
};
export const Categories = ({ categories, preview }: Props) => {
  return (
    <>
      {categories.map((category) => (
        <a
          href={preview ? "#" : `/search?categoryIds[]=${category.id}`}
          key={category.id}
          className="rounded bg-category px-2.5 py-0.5 text-white inline-block"
        >
          {t(`category.${category.type}.${category.slug}`)}
        </a>
      ))}
    </>
  );
};
