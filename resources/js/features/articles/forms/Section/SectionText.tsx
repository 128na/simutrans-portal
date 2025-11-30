import Textarea from "@/components/ui/Textarea";
import TextError from "@/components/ui/TextError";
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
        value={section.text ?? ""}
        labelClassName="font-medium"
        className="font-normal"
        rows={5}
        {...props}
      />
    </>
  );
};
