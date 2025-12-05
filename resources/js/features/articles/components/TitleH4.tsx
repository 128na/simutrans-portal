import { twMerge } from "tailwind-merge";

type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export const TitleH4 = ({ children, className }: HTMLProps) => {
  return (
    <h4
      className={twMerge(
        "text-xl font-semibold sm:text-xl my-2 break-all",
        className
      )}
    >
      {children}
    </h4>
  );
};
