import V2Checkbox from "./V2Checkbox";

type Props = React.InputHTMLAttributes<HTMLInputElement> & {
  options: Record<string, string>;
  checkedOptions?: string[];
};

export default function V2Checkboxes({
  options,
  checkedOptions,
  ...props
}: Props) {
  return (
    <div className="v2-checkboxes">
      {Object.entries(options).map(([key, label], index) => (
        <V2Checkbox
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
