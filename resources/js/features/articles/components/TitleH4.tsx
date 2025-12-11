import { twMerge } from "tailwind-merge";

type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export const TitleH4 = ({ children, className }: HTMLProps) => {
  return <h4 className={twMerge("v2-text-h4 my-4", className)}>{children}</h4>;
};
