import { ReactNode } from "react";
import { twMerge } from "tailwind-merge";

export type DataTableHeader<K extends string = string> = {
  key: K;
  name: string;
  width?: string;
  sortable?: boolean;
};

type Sort = {
  column: string;
  order: "asc" | "desc";
};

type Props<T, K extends string> = {
  headers: DataTableHeader<K>[];
  data: T[];
  sort: Sort;
  limit: number;
  current: number;
  onSort: (key: K) => void;
  renderRow: (item: T) => ReactNode;
};

export function DataTable<T, K extends string>({
  headers,
  data,
  sort,
  limit,
  current,
  onSort,
  renderRow,
}: Props<T, K>) {
  return (
    <table className="v2-table v2-table-fixed">
      <thead>
        <tr>
          {headers.map((header) => (
            <th
              key={header.key}
              onClick={() => header?.sortable && onSort(header.key)}
              className={twMerge(
                header.width,
                header.sortable && "cursor-pointer"
              )}
            >
              {header.name}
              <span className="ml-1 text-[10px]">
                {header?.sortable && sort.column === header.key
                  ? sort.order === "asc"
                    ? "▲"
                    : "▼"
                  : ""}
              </span>
            </th>
          ))}
        </tr>
      </thead>
      <tbody>
        {data
          .slice((current - 1) * limit, current * limit)
          .map((item) => renderRow(item))}
      </tbody>
    </table>
  );
}
