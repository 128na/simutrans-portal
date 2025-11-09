import Input from "@/apps/components/ui/Input";
import TextError from "@/apps/components/ui/TextError";
import { useAxiosError } from "@/apps/state/useAxiosError";

type Props = {
  section: SectionCaption;
  idx: number;
} & React.InputHTMLAttributes<HTMLInputElement>;

export const SectionCaption = ({ section, idx, ...props }: Props) => {
  const { getError } = useAxiosError();
  return (
    <Input
      value={section.caption ?? ""}
      labelClassName="font-medium"
      className="font-normal"
      {...props}
    >
      見出し
      <TextError className="mb-2">
        {getError(`article.contents.sections.${idx}.caption`)}
      </TextError>
    </Input>
  );
};
