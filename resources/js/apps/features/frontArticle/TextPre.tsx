type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export const TextPre = ({ children }: HTMLProps) => {
  return (
    <pre className="whitespace-pre-wrap text-gray-800 break-all">
      {children}
    </pre>
  );
};
