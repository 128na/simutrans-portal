import TextError from "@/components/ui/TextError";
import V2Input from "@/components/ui/v2/V2Input";
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
      <V2Input
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
