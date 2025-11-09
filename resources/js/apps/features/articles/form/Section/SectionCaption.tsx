import Input from "@/apps/components/ui/Input";

type Props = {
  section: SectionCaption;
} & React.InputHTMLAttributes<HTMLInputElement>;
export const SectionCaption = ({ section, ...props }: Props) => {
  return (
    <Input value={section.caption ?? ""} {...props}>
      見出し
    </Input>
  );
};
