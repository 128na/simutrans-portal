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
    const idx = tags.findIndex((t) => t.id === tag.id);

    let next: Tag.Listing[];
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
