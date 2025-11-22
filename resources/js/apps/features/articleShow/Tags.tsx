type Props = {
  tags: ArticleShow.Tag[];
};
export const Tags = ({ tags }: Props) => {
  return (
    <>
      {tags.map((tag) => (
        <a
          href={`/tags/${tag.id}`}
          key={tag.id}
          className="rounded bg-tag px-2.5 py-0.5 text-white inline-block"
        >
          {tag.name}
        </a>
      ))}
    </>
  );
};
