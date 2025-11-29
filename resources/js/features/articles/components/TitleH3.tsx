type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export const TitleH3 = ({ children }: HTMLProps) => {
  return (
    <h3 className="text-2xl font-semibold text-brand sm:text-2xl my-8 break-all">
      {children}
    </h3>
  );
};
