import Input from "@/apps/components/ui/Input";
import TextError from "@/apps/components/ui/TextError";
import { useAxiosError } from "@/apps/state/useAxiosError";

type Props = {
  section: ArticleContent.Section.Url;
  idx: number;
} & React.InputHTMLAttributes<HTMLInputElement>;

export const SectionUrl = ({ section, idx, ...props }: Props) => {
  const { getError } = useAxiosError();

  return (
    <Input
      type="url"
      value={section.url ?? ""}
      labelClassName="font-medium"
      className="font-normal"
      {...props}
    >
      URL
      <TextError>{getError(`article.contents.sections.${idx}.url`)}</TextError>
    </Input>
  );
};
