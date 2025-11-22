type Props = {
  categories: ArticleShow.Category[];
};
export const Categories = ({ categories }: Props) => {
  return (
    <>
      {categories.map((category) => (
        <a
          href={`/search?categoryIds[]=${category.id}`}
          key={category.id}
          className="rounded bg-category px-2.5 py-0.5 text-white inline-block"
        >
          {category.name}
        </a>
      ))}
    </>
  );
};
