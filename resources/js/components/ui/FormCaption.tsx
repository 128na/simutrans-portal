type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export const FormCaption = ({ children, ...props }: HTMLProps) => {
  return (
    <div className="v2-text-caption mb-2" {...props}>
      {children}
    </div>
  );
};
