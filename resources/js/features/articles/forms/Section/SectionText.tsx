import TextError from "@/components/ui/TextError";
import Textarea from "@/components/ui/Textarea";
import { useAxiosError } from "@/hooks/useAxiosError";

type Props = {
  section: ArticleContent.Section.Text;
  idx: number;
} & React.TextareaHTMLAttributes<HTMLTextAreaElement>;

export const SectionText = ({ section, idx, ...props }: Props) => {
  const { getError } = useAxiosError();

  return (
    <>
      <TextError>{getError(`article.contents.sections.${idx}.text`)}</TextError>
      <Textarea
        className="w-full"
        required
        maxLength={2048}
        value={section.text ?? ""}
        rows={5}
        {...props}
      />
    </>
  );
};
