import Input from "@/apps/components/ui/Input";
import { useArticleEditor } from "@/apps/state/useArticleEditor";
import Select from "@/apps/components/ui/Select";
import { addHours, format } from "date-fns";
import { statusText } from "../articleUtil";

export const StatusForm = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);

  return (
    <>
      <Select
        options={statusText}
        value={article.status}
        onChange={(e) =>
          update((draft) => (draft.status = e.target.value as Status))
        }
      >
        ステータス
      </Select>
      {article.status === "reservation" && (
        <Input
          type="datetime-local"
          value={article.published_at ?? ""}
          min={format(addHours(new Date(), 1), "yyyy-MM-dd'T'HH:mm")}
          onChange={(e) =>
            update((draft) => (draft.published_at = e.target.value))
          }
        >
          予約日時
        </Input>
      )}
    </>
  );
};
