import { useState } from "react";
import { Pagination } from "@/apps/components/layout/Pagination";
import { attachmentFilter, compareAttachmentValues } from "./attachmentUtil";
import Button from "@/apps/components/ui/Button";
import Input from "@/apps/components/ui/Input";
import { twMerge } from "tailwind-merge";
import { DataTable, DataTableHeader } from "@/apps/components/layout/DataTable";
import { format } from "date-fns";
import { Image } from "@/apps/components/ui/Image";
import { t } from "@/lang/translate";

type Props = {
  attachments: Attachment[];
  limit: number;
  attachmentableId: number | null;
  attachmentableType: AttachmentableType | null;
  selected: number | null;
  onSelectAttachment?: (attachment: Attachment | null) => void;
};

type Sort = {
  column: keyof Tag.Listing;
  order: "asc" | "desc";
};

const headers: DataTableHeader<keyof Attachment>[] = [
  { name: "サムネイル", key: "thumbnail", width: "w-2/12", sortable: false },
  { name: "ファイル名", key: "original_name", width: "w-3/12", sortable: true },
  { name: "形式", key: "type", width: "w-1/12", sortable: true },
  {
    name: "アップロード日",
    key: "created_at",
    width: "w-2/12",
    sortable: true,
  },
];

export const AttachmentTable = ({
  attachments,
  attachmentableId,
  attachmentableType,
  limit,
  selected,
  onSelectAttachment,
}: Props) => {
  const [criteria, setCriteria] = useState("");
  const [sort, setSort] = useState<Sort>({
    column: "created_at",
    order: "desc",
  });
  const [current, setCurrent] = useState(1);

  const filtered = attachmentFilter(
    attachments,
    criteria,
    attachmentableId,
    attachmentableType,
  );

  const totalPages = Math.ceil(filtered.length / limit);

  const sorted = [...filtered].sort((a, b) => {
    const result = compareAttachmentValues(a[sort.column], b[sort.column]);
    return sort.order === "asc" ? result : -result;
  });

  const onSort = (column: keyof Tag.Listing) => {
    setSort((prev) =>
      prev.column === column
        ? { column, order: prev.order === "asc" ? "desc" : "asc" }
        : { column, order: "asc" },
    );
  };

  return (
    <div className="relative overflow-x-auto">
      <div className="gap-4 flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between py-4">
        <div>
          <Button>アップロード</Button>
        </div>
        <div>
          <Input
            type="text"
            value={criteria}
            onChange={(e) => setCriteria(e.target.value)}
            placeholder="検索"
          />
        </div>
        <div className="grow"></div>
        <Pagination
          total={totalPages}
          current={current}
          onChange={setCurrent}
        />
      </div>
      <DataTable
        headers={headers}
        data={sorted}
        sort={sort}
        limit={limit}
        current={current}
        onSort={onSort}
        renderRow={(attachment) => (
          <tr
            key={attachment.id}
            className={twMerge(
              "bg-white border-b border-gray-200 cursor-pointer",
              selected === attachment.id
                ? "bg-brand/20 hover:bg-brand/30"
                : "hover:bg-gray-100",
            )}
            onClick={() =>
              onSelectAttachment?.(
                selected === attachment.id ? null : attachment,
              )
            }
          >
            <td className="px-6 py-4 ">
              <Image attachmentId={attachment.id} attachments={[attachment]} />
            </td>
            <td className="px-6 py-4 font-medium">
              {attachment.id}/{attachment.original_name}
            </td>
            <td className="px-6 py-4">
              {t(`attachments.type.${attachment.type}`)}
            </td>
            <td className="px-6 py-4">
              {format(new Date(attachment.created_at), "yyyy/MM/dd H:mm")}
            </td>
          </tr>
        )}
      />
    </div>
  );
};
