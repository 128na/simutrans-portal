import { useState } from "react";
import { Pagination } from "@/apps/components/layout/Pagination";
import {
  attachmentFilter,
  compareAttachmentValues,
  displaySize,
} from "./attachmentUtil";
import Input from "@/apps/components/ui/Input";
import { twMerge } from "tailwind-merge";
import { DataTable, DataTableHeader } from "@/apps/components/layout/DataTable";
import { format } from "date-fns";
import { Image } from "@/apps/components/ui/Image";
import { t } from "@/lang/translate";
import TextSub from "@/apps/components/ui/TextSub";
import Link from "@/apps/components/ui/Link";
import axios from "axios";
import ButtonDanger from "@/apps/components/ui/ButtonDanger";

type Props = {
  attachments: Attachment[];
  limit: number;
  attachmentableId: number | null;
  attachmentableType: AttachmentableType | null;
  types: AttachmentType[];
  selected: number | null;
  onSelectAttachment?: (attachment: Attachment | null) => void;
  onChangeAttachments?: (attachments: Attachment[]) => void;
};

type Sort = {
  column: keyof Attachment;
  order: "asc" | "desc";
};

const headers: DataTableHeader<keyof Attachment>[] = [
  { name: "サムネイル", key: "thumbnail", width: "w-2/12", sortable: false },
  { name: "ファイル名", key: "original_name", width: "w-3/12", sortable: true },
  { name: "形式", key: "type", width: "w-1/12", sortable: true },
  {
    name: "利用先",
    key: "attachmentable_id",
    width: "w-1/12",
    sortable: true,
  },
  { name: "サイズ", key: "size", width: "w-1/12", sortable: true },
  {
    name: "アップロード日",
    key: "created_at",
    width: "w-1/12",
    sortable: true,
  },
  { name: "操作", key: "id", width: "w-1/12", sortable: false },
];

export const AttachmentTable = ({
  attachments,
  attachmentableId,
  attachmentableType,
  types,
  limit,
  selected,
  onSelectAttachment,
  onChangeAttachments,
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
    types,
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

  const handleDelete = async (attachmentId: number) => {
    if (window.confirm("本当に削除しますか？")) {
      try {
        const res = await axios.delete(`/api/v2/attachments/${attachmentId}`);
        if (res.status === 200) {
          onChangeAttachments?.(
            attachments.filter((a) => a.id !== attachmentId),
          );
        }
      } catch (error) {
        console.log(error);
        alert("削除に失敗しました");
      }
    }
  };

  return (
    <div className="relative overflow-x-auto">
      <div className="gap-4 flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between py-4">
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
      <TextSub>
        利用先の無いファイルのみ削除できます。利用先の記事でファイルを差し替えてから削除してください。
      </TextSub>
      <DataTable
        headers={headers}
        data={sorted}
        sort={sort}
        limit={limit}
        current={current}
        onSort={onSort}
        renderRow={(a) => (
          <tr
            key={a.id}
            className={twMerge(
              "bg-white border-b border-gray-200",
              onSelectAttachment &&
                (selected === a.id
                  ? "cursor-pointer bg-brand/20 hover:bg-brand/30"
                  : "cursor-pointer hover:bg-gray-100"),
            )}
            onClick={() => onSelectAttachment?.(selected === a.id ? null : a)}
          >
            <td className="px-6 py-4 ">
              <Image attachmentId={a.id} attachments={[a]} />
            </td>
            <td className="px-6 py-4 font-medium">{a.original_name}</td>
            <td className="px-6 py-4">{t(`attachments.type.${a.type}`)}</td>
            <td className="px-6 py-4">
              {a.attachmentable_type === "Profile" ? (
                <Link href="/mypage/profile">プロフィール</Link>
              ) : (
                a.attachmentable?.title && (
                  <Link href={`/mypage/articles/edit/${a.attachmentable_id}`}>
                    {a.attachmentable?.title}
                  </Link>
                )
              )}
            </td>
            <td className="px-6 py-4">{displaySize(a.size)}</td>
            <td className="px-6 py-4">
              {format(new Date(a.created_at), "yyyy/MM/dd H:mm")}
            </td>
            <td className="px-6 py-4">
              {a.attachmentable_id === null && a.id !== selected && (
                <ButtonDanger
                  onClick={(e) => {
                    e.stopPropagation();
                    handleDelete(a.id);
                  }}
                >
                  削除
                </ButtonDanger>
              )}
            </td>
          </tr>
        )}
      />
    </div>
  );
};
