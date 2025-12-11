import { useState } from "react";
import { twMerge } from "tailwind-merge";
import ButtonClose from "./ButtonClose";
import V2Button from "./v2/V2Button";

type Props = {
  children:
    | React.ReactNode
    | ((props: { close: () => void }) => React.ReactNode);
  buttonTitle: string;
  title: string;
  buttonClass?: string;
  modalClass?: string;
};

export const ModalFull = ({
  buttonTitle,
  title,
  children,
  buttonClass,
  modalClass,
}: Props) => {
  const [isOpen, setIsOpen] = useState(false);
  const close = () => setIsOpen(false);

  return (
    <>
      <V2Button
        variant="subOutline"
        onClick={() => setIsOpen(!isOpen)}
        className={buttonClass}
      >
        {buttonTitle}
      </V2Button>
      {isOpen && (
        <div
          className="fixed inset-0 z-50 flex justify-center bg-black/40 overflow-hidden"
          aria-modal="true"
          role="dialog"
        >
          <div className={twMerge("relative w-full h-screen", modalClass)}>
            <div className="bg-white shadow px-5 pb-5 h-full overflow-y-auto">
              <div className="flex justify-between items-center py-3 mb-4 sticky top-0 bg-white z-10">
                <h3 className="v2-text-h3">{title}</h3>
                <ButtonClose onClick={() => setIsOpen(false)} />
              </div>
              {typeof children === "function" ? children({ close }) : children}
            </div>
          </div>
        </div>
      )}
    </>
  );
};
