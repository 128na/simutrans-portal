type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export const TitleH3 = ({ children }: HTMLProps) => {
  return <h3 className="v2-text-h3 my-4">{children}</h3>;
};
