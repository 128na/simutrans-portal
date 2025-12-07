import { twMerge } from "tailwind-merge";

type PaginationProps = {
  total: number;
  current: number;
  onChange: (page: number) => void;
};

function getPagination(total: number, current: number) {
  const start = Math.max(1, current - 1);
  const end = Math.min(total, current + 1);
  return Array.from({ length: end - start + 1 }, (_, i) => start + i);
}

const baseClass =
  "flex items-center justify-center px-4 h-10 text-c-sub bg-white border border-c-sub/10";

export const Pagination = ({ total, current, onChange }: PaginationProps) => {
  const pages = getPagination(total, current);

  return (
    <nav>
      <ul className="inline-flex -space-x-px text-base h-10">
        <li>
          <button
            className={twMerge(
              baseClass,
              "ms-0 border-e-0 rounded-s-lg",
              current === 1 ? "" : "hover:bg-c-sub/10 cursor-pointer"
            )}
            onClick={() => onChange(Math.max(1, current - 1))}
            disabled={current === 1}
          >
            前
          </button>
        </li>

        {pages[0] > 1 && (
          <li>
            <button className={twMerge(baseClass)}>...</button>
          </li>
        )}

        {pages.map((p) => (
          <li key={p}>
            <button
              className={twMerge(
                baseClass,
                "cursor-pointer",
                current === p
                  ? "bg-blue-50 text-blue-600 hover:text-blue-700"
                  : "hover:bg-c-sub/10"
              )}
              onClick={() => onChange(p)}
            >
              {p}
            </button>
          </li>
        ))}

        {pages[pages.length - 1] < total && (
          <li>
            <button className={twMerge(baseClass)}>...</button>
          </li>
        )}

        <li>
          <button
            className={twMerge(
              baseClass,
              "rounded-e-lg",
              current === total ? "" : "hover:bg-c-sub/10 cursor-pointer"
            )}
            onClick={() => onChange(Math.min(total, current + 1))}
            disabled={current === total}
          >
            次
          </button>
        </li>
      </ul>
    </nav>
  );
};
