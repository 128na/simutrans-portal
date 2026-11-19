import { ArticleUpload } from "@/features/articles/forms/ArticleUpload";
import TextError from "@/components/ui/TextError";
import { useAxiosError } from "@/hooks/useAxiosError";

type Props = {
  section: ArticleContent.Section.Image;
  idx: number;
  onUploaded: (a: Attachment.MypageEdit) => void;
  onSelectAttachment: (id: number | null) => void;
} & React.InputHTMLAttributes<HTMLInputElement>;

export const SectionImage = ({
  section,
  idx,
  onUploaded,
  onSelectAttachment,
}: Props) => {
  const { getError } = useAxiosError();

  return (
    <>
      <div>
        <TextError>{getError(`article.contents.sections.${idx}.id`)}</TextError>
        <ArticleUpload
          kind="image"
          selected={section.id}
          onUploaded={onUploaded}
          onSelectAttachment={onSelectAttachment}
        />
      </div>
    </>
  );
};
