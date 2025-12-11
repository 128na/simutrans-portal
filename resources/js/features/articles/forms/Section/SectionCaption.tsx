import TextError from "@/components/ui/TextError";
import V2Input from "@/components/ui/v2/V2Input";
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
      <V2Input
        className="w-full"
        required
        maxLength={255}
        value={section.caption ?? ""}
        {...props}
      />
    </>
  );
};
