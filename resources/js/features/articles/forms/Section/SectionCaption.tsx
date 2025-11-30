import Input from "@/components/ui/Input";
import TextError from "@/components/ui/TextError";
import { useAxiosError } from "@/hooks/useAxiosError";

type Props = {
  section: ArticleContent.Section.Caption;
  idx: number;
} & React.InputHTMLAttributes<HTMLInputElement>;

export const SectionCaption = ({ section, idx, ...props }: Props) => {
  const { getError } = useAxiosError();
  return (
    <>
      <TextError>
        {getError(`article.contents.sections.${idx}.caption`)}
      </TextError>
      <Input
        value={section.caption ?? ""}
        labelClassName="font-medium"
        className="font-normal"
        {...props}
      />
    </>
  );
};
