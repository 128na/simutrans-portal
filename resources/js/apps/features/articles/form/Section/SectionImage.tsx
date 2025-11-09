import Input from "@/apps/components/ui/Input";

type Props = {
  section: SectionImage;
} & React.InputHTMLAttributes<HTMLInputElement>;
export const SectionImage = ({ section, ...props }: Props) => {
  return (
    <Input value={section.image ?? ""} {...props}>
      画像
    </Input>
  );
};
