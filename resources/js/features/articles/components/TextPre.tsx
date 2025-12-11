type HTMLProps = React.HTMLAttributes<HTMLElement> & {
  children: React.ReactNode;
};

export const TextPre = ({ children }: HTMLProps) => {
  return <pre className="whitespace-pre-wrap break-all">{children}</pre>;
};
