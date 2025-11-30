import Input from "@/components/ui/Input";
import TextError from "@/components/ui/TextError";
import { useAxiosError } from "@/hooks/useAxiosError";

type Props = {
  section: ArticleContent.Section.Url;
  idx: number;
} & React.InputHTMLAttributes<HTMLInputElement>;

export const SectionUrl = ({ section, idx, ...props }: Props) => {
  const { getError } = useAxiosError();

  return (
    <>
      <TextError>{getError(`article.contents.sections.${idx}.url`)}</TextError>
      <Input
        type="url"
        value={section.url ?? ""}
        labelClassName="font-medium"
        className="font-normal"
        {...props}
      />
    </>
  );
};
