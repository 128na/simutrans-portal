import React from "react";

type TwoColumnProps = {
  children: [React.ReactNode, React.ReactNode];
  grow?: "left" | "right" | "none";
};

export default function TwoColumn({ children, grow = "none" }: TwoColumnProps) {
  if (children.length !== 2) {
    throw new Error("TwoColumn must have two children");
  }
  const [left, right] = children;
  const leftGrow = grow === "left" ? "flex-grow" : "";
  const rightGrow = grow === "right" ? "flex-grow" : "";

  return (
    <div className="flex w-full gap-4 items-end">
      <div className={`min-w-0 ${leftGrow}`}>{left}</div>
      <div className={`min-w-0 ${rightGrow}`}>{right}</div>
    </div>
  );
}
