import { TagTable } from "@/features/tags/TagTable";
import { TagModal } from "@/features/tags/TagModal";
import { useState } from "react";

type Props = {
  tags: Tag.MypageEdit[];
  onChangeTags: (tags: Tag.MypageEdit[]) => void;
};

export const TagEdit = ({ tags, onChangeTags }: Props) => {
  const [selected, setSelected] = useState<Tag.MypageEdit | Tag.New | null>(
    null,
  );
  const updateTag = (tag: Tag.MypageEdit) => {
    const idx = tags.findIndex((t) => t.id === tag.id);

    let next: Tag.MypageEdit[];
    if (idx >= 0) {
      next = tags.map((t) => (t.id !== tag.id ? t : tag));
    } else {
      next = [tag, ...tags];
    }
    onChangeTags(next);
    setSelected(null);
  };
  return (
    <>
      <TagTable tags={tags} limit={15} onClick={(tag) => setSelected(tag)} />
      <TagModal
        key={selected?.id ?? "new"}
        tag={selected}
        onClose={() => setSelected(null)}
        onSave={updateTag}
      />
    </>
  );
};
