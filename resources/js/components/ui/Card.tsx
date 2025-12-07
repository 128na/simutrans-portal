import { twMerge } from "tailwind-merge";

type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export const Card = ({ children, className, ...props }: HTMLProps) => {
  return (
    <div
      className={twMerge(
        "rounded-lg shadow-sm p-4 cursor-pointer hover:bg-c-primary/10",
        className
      )}
      {...props}
    >
      {children}
    </div>
  );
};
