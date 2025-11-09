import Input from "@/apps/components/ui/Input";

type Props = React.InputHTMLAttributes<HTMLInputElement> & {
  section: SectionUrl;
};
export const SectionUrl = ({ section, ...props }: Props) => {
  return (
    <Input type="url" value={section.url ?? ""} {...props}>
      URL
    </Input>
  );
};
