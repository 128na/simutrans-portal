type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export const TitleH3 = ({ children }: HTMLProps) => {
  return <h3 className="title-md my-4">{children}</h3>;
};
