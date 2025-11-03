import { TagTable } from "@/apps/features/tags/TagTable";
import { TagModal } from "@/apps/features/tags/TagModal";
import { useState } from "react";

type Props = {
  tags: Tag.Listing[];
  onChangeTags: (tags: Tag.Listing[]) => void;
};

export const TagEdit = ({ tags, onChangeTags }: Props) => {
  const [selected, setSelected] = useState<Tag.Listing | Tag.Creating | null>(
    null,
  );
  const updateTag = (tag: Tag.Listing) => {
    const index = tags.findIndex((t) => t.id === tag.id);
    if (index !== -1) {
      onChangeTags([...tags.slice(0, index), tag, ...tags.slice(index + 1)]);
    } else {
      onChangeTags([...tags, tag]);
    }
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
