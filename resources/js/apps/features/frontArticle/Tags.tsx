type Props = {
  tags: TagEdit.Tag[];
  preview?: boolean;
};
export const Tags = ({ tags, preview }: Props) => {
  return (
    <>
      {tags.map((tag) => (
        <a
          href={preview ? "#" : `/tags/${tag.id}`}
          key={`tag-${tag.id}`}
          className="rounded bg-tag px-2.5 py-0.5 text-white inline-block"
        >
          {tag.name}
        </a>
      ))}
    </>
  );
};
