import { twMerge } from "tailwind-merge";
type Props = React.SelectHTMLAttributes<HTMLSelectElement> & {
  options: Record<string, string>;
};

export default function V2Select({ className, options, ...props }: Props) {
  return (
    <select
      className={twMerge("v2-input", "v2-input-select", className)}
      {...props}
    >
      {Object.entries(options).map(([key, label]) => (
        <option key={key} value={key} selected={props.value === key}>
          {label}
        </option>
      ))}
    </select>
  );
}
