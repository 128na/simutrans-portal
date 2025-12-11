type Props = {
  tags: Tag.MypageEdit[] | Tag.Show[];
  preview?: boolean;
};
export const Tags = ({ tags, preview }: Props) => {
  return (
    <>
      {tags.map((tag) => (
        <a
          href={preview ? "#" : `/tags/${tag.id}`}
          key={`tag-${tag.id}`}
          className="v2-tag"
        >
          {tag.name}
        </a>
      ))}
    </>
  );
};
