import TextError from "@/components/ui/TextError";
import Input from "@/components/ui/Input";
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
        className="w-full"
        required
        maxLength={2048}
        value={section.url ?? ""}
        {...props}
      />
    </>
  );
};
