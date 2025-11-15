import { TagTable } from "@/apps/features/tags/TagTable";
import { TagModal } from "@/apps/features/tags/TagModal";
import { useState } from "react";

type Props = {
  tags: TagEdit.Tag[];
  onChangeTags: (tags: TagEdit.Tag[]) => void;
};

export const TagEdit = ({ tags, onChangeTags }: Props) => {
  const [selected, setSelected] = useState<
    TagEdit.Tag | TagEdit.Creating | null
  >(null);
  const updateTag = (tag: TagEdit.Tag) => {
    const idx = tags.findIndex((t) => t.id === tag.id);

    let next: TagEdit.Tag[];
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
