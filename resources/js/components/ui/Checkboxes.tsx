import Checkbox from "./Checkbox";

type Props = React.InputHTMLAttributes<HTMLInputElement> & {
  options: Record<string, string>;
  checkedOptions?: string[];
};

export default function Checkboxes({
  options,
  checkedOptions,
  ...props
}: Props) {
  return (
    <div className="v2-checkboxes">
      {Object.entries(options).map(([key, label], index) => (
        <Checkbox
          key={index}
          value={key}
          checked={checkedOptions?.includes(key)}
          {...props}
        >
          {label}
        </Checkbox>
      ))}
    </div>
  );
}
