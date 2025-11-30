type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export const FormCaption = ({ children, ...props }: HTMLProps) => {
  return (
    <div className="mb-2 px-2 py-1 bg-gray-100" {...props}>
      {children}
    </div>
  );
};
