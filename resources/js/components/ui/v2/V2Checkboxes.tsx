import V2Checkbox from "./V2Checkbox";

type Props = React.InputHTMLAttributes<HTMLInputElement> & {
  id: string;
  options: Record<string, string>;
  checkedOptions?: string[];
};

export default function V2Checkboxes({
  options,
  checkedOptions,
  id,
  ...props
}: Props) {
  return (
    <div className="space-x-4 space-y-1">
      {Object.entries(options).map(([key, label], index) => (
        <V2Checkbox
          id={`${id}-${index}`}
          key={index}
          value={key}
          checked={checkedOptions?.includes(key)}
          {...props}
        >
          {label}
        </V2Checkbox>
      ))}
    </div>
  );
}
