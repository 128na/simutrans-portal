import { useState } from "react";
import { Pagination } from "@/components/layout/Pagination";
import {
  attachmentFilter,
  compareAttachmentValues,
  displaySize,
} from "./attachmentUtil";
import Input from "@/components/ui/Input";
import { twMerge } from "tailwind-merge";
import { DataTable, DataTableHeader } from "@/components/layout/DataTable";
import { format } from "date-fns";
import { Image } from "@/components/ui/Image";
import { t } from "@/utils/translate";
import TextSub from "@/components/ui/TextSub";
import Link from "@/components/ui/Link";
import axios from "axios";
import ButtonDanger from "@/components/ui/ButtonDanger";
import { handleError } from "@/lib/errorHandler";

type Props = {
  attachments: Attachment.MypageEdit[];
  limit: number;
  attachmentableId: number | null;
  attachmentableType: AttachmentableType | null;
  types: AttachmentType[];
  selected: number | null;
  onSelectAttachment?: (attachment: Attachment.MypageEdit | null) => void;
  onChangeAttachments?: (attachments: Attachment.MypageEdit[]) => void;
};

type Sort = {
  column: keyof Attachment.MypageEdit;
  order: "asc" | "desc";
};

const headers: DataTableHeader<keyof Attachment.MypageEdit>[] = [
  { name: "サムネイル", key: "thumbnail", width: "w-3/12", sortable: false },
  { name: "ファイル名", key: "original_name", width: "w-3/12", sortable: true },
  { name: "形式", key: "type", width: "w-1/12", sortable: true },
  {
    name: "利用先",
    key: "attachmentable_id",
    width: "w-2/12",
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
    attachmentableType
  );

  const totalPages = Math.ceil(filtered.length / limit);

  const sorted = filtered.sort((a, b) => {
    const result = compareAttachmentValues(a[sort.column], b[sort.column]);
    return sort.order === "asc" ? result : -result;
  });

  const onSort = (column: keyof Attachment.MypageEdit) => {
    setSort((prev) =>
      prev.column === column
        ? { column, order: prev.order === "asc" ? "desc" : "asc" }
        : { column, order: "asc" }
    );
  };

  const handleDelete = async (attachmentId: number) => {
    if (window.confirm("本当に削除しますか？")) {
      try {
        const res = await axios.delete(`/api/v2/attachments/${attachmentId}`);
        if (res.status === 200) {
          onChangeAttachments?.(
            attachments.filter((a) => a.id !== attachmentId)
          );
        }
      } catch (error) {
        handleError(error, { component: "AttachmentTable", action: "delete" });
      }
    }
  };

  return (
    <>
      <div className="gap-4 flex flex-col sm:flex-row pb-4">
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
      <div className="relative overflow-x-auto">
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
                "bg-white border-b border-muted",
                onSelectAttachment &&
                  (selected === a.id
                    ? "cursor-pointer bg-brand/20 hover:bg-brand/30"
                    : "cursor-pointer hover:bg-muted")
              )}
              onClick={() => onSelectAttachment?.(selected === a.id ? null : a)}
            >
              <td className="px-6 py-4 ">
                <Image
                  attachmentId={a.id}
                  attachments={[a]}
                  // ファイル管理からの操作のみ、ファイル選択と干渉しないので画像リンクにする
                  openFullSize={attachmentableId ? false : true}
                />
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
    </>
  );
};
