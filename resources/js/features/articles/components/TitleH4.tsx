type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export const TitleH4 = ({ children }: HTMLProps) => {
  return (
    <h4 className="text-xl font-semibold sm:text-xl my-8 break-all">
      {children}
    </h4>
  );
};
