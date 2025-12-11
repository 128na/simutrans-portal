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

export const Pagination = ({ total, current, onChange }: PaginationProps) => {
  const pages = getPagination(total, current);

  return (
    <nav>
      <ul className="v2-pagination">
        <li>
          <button
            className={twMerge(
              "v2-pagination-item v2-pagination-item-start",
              current === 1
                ? "v2-pagination-item-disabled"
                : "v2-pagination-item-hover"
            )}
            onClick={() => onChange(Math.max(1, current - 1))}
            disabled={current === 1}
          >
            前
          </button>
        </li>

        {pages[0] > 1 && (
          <li>
            <button className="v2-pagination-item">...</button>
          </li>
        )}

        {pages.map((p) => (
          <li key={p}>
            <button
              className={twMerge(
                "v2-pagination-item",
                current === p
                  ? "v2-pagination-item-active"
                  : "v2-pagination-item-hover"
              )}
              onClick={() => onChange(p)}
            >
              {p}
            </button>
          </li>
        ))}

        {pages[pages.length - 1] < total && (
          <li>
            <button className="v2-pagination-item">...</button>
          </li>
        )}

        <li>
          <button
            className={twMerge(
              "v2-pagination-item v2-pagination-item-end",
              current === total
                ? "v2-pagination-item-disabled"
                : "v2-pagination-item-hover"
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
