import { useState } from "react";

type Props = {
  children: React.ReactNode;
  title: string;
  defaultOpen?: boolean;
};

export const Accordion = ({ title, children, defaultOpen = false }: Props) => {
  const [isOpen, setIsOpen] = useState(defaultOpen);

  return (
    <>
      <button
        type="button"
        className="v2-accordion"
        onClick={() => setIsOpen(!isOpen)}
        aria-expanded={isOpen}
      >
        <svg
          viewBox="0 0 20 20"
          fill="currentColor"
          className={`size-5 flex-none transition-transform ${
            isOpen ? "rotate-180" : ""
          }`}
        >
          <path
            d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
            clipRule="evenodd"
            fillRule="evenodd"
          />
        </svg>
        {title}
      </button>
      {isOpen && children}
    </>
  );
};
