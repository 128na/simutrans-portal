import Textarea from "@/apps/components/ui/Textarea";
import TextError from "@/apps/components/ui/TextError";
import { useAxiosError } from "@/apps/state/useAxiosError";

type Props = {
  section: ArticleContent.Section.Text;
  idx: number;
} & React.TextareaHTMLAttributes<HTMLTextAreaElement>;

export const SectionText = ({ section, idx, ...props }: Props) => {
  const { getError } = useAxiosError();

  return (
    <Textarea
      value={section.text ?? ""}
      labelClassName="font-medium"
      className="font-normal"
      rows={5}
      {...props}
    >
      テキスト
      <TextError className="mb-2">
        {getError(`article.contents.sections.${idx}.text`)}
      </TextError>
    </Textarea>
  );
};
