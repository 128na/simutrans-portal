type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export const FormCaption = ({ children, ...props }: HTMLProps) => {
  return (
    <div className="title-xs mb-2" {...props}>
      {children}
    </div>
  );
};
