import React from "react";

type MultiColumnProps = {
  children: React.ReactNode[];
  classNames?: string[];
};

export default function MultiColumn({
  children,
  classNames = [],
}: MultiColumnProps) {
  return (
    <div className="flex w-full gap-4 items-end">
      {children.map((child, index) => (
        <div key={index} className={classNames[index] || ""}>
          {child}
        </div>
      ))}
    </div>
  );
}
