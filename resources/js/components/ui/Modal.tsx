import { twMerge } from "tailwind-merge";
import ButtonClose from "./ButtonClose";

type Props = {
  children:
    | React.ReactNode
    | ((props: { close: () => void }) => React.ReactNode);
  title: string;
  modalClass?: string;
  onClose?: () => void;
};

export const Modal = ({ title, children, modalClass, onClose }: Props) => {
  return (
    <div
      className="fixed inset-0 z-50 flex justify-center items-center bg-black/40 overflow-hidden p-4"
      aria-modal="true"
      role="dialog"
      onClick={onClose}
    >
      <div
        className={twMerge(
          "relative w-full max-w-md max-h-[90vh] flex flex-col",
          modalClass
        )}
        onClick={
          (e) => e.stopPropagation() // モーダル内のクリックイベントが親要素に伝播しないようにする
        }
      >
        <div className="bg-white rounded-lg shadow flex flex-col max-h-full">
          <div className="flex justify-between items-center py-3 px-5 border-b shrink-0">
            <h3 className="v2-text-h3">{title}</h3>
            <ButtonClose onClick={onClose} />
          </div>
          <div className="p-5 overflow-y-auto">
            {typeof children === "function" ? children({ close }) : children}
          </div>
        </div>
      </div>
    </div>
  );
};
