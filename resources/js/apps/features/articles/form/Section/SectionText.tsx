import Textarea from "@/apps/components/ui/Textarea";

type Props = {
  section: SectionText;
} & React.TextareaHTMLAttributes<HTMLTextAreaElement>;
export const SectionText = ({ section, ...props }: Props) => {
  return (
    <Textarea value={section.text ?? ""} rows={5} {...props}>
      テキスト
    </Textarea>
  );
};
