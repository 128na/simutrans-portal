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
      className="fixed inset-0 z-50 flex justify-center items-center bg-black/40 overflow-hidden"
      aria-modal="true"
      role="dialog"
      onClick={onClose}
    >
      <div
        className={twMerge("relative p-4 w-full max-w-md", modalClass)}
        onClick={
          (e) => e.stopPropagation() // モーダル内のクリックイベントが親要素に伝播しないようにする
        }
      >
        <div className="bg-white rounded-lg shadow p-5">
          <div className="flex justify-between items-center py-3 mb-4">
            <h3 className="title-md">{title}</h3>
            <ButtonClose onClick={onClose} />
          </div>
          {typeof children === "function" ? children({ close }) : children}
        </div>
      </div>
    </div>
  );
};
