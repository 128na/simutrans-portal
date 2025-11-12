import Input from "@/apps/components/ui/Input";
import { useArticleEditor } from "@/apps/state/useArticleEditor";
import Select from "@/apps/components/ui/Select";
import { addHours, format } from "date-fns";
import { StatusText } from "../articleUtil";
import { useAxiosError } from "@/apps/state/useAxiosError";
import TextError from "@/apps/components/ui/TextError";

export const StatusForm = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);
  const { getError } = useAxiosError();

  return (
    <>
      <Select
        options={StatusText}
        value={article.status}
        onChange={(e) =>
          update((draft) => (draft.status = e.target.value as Article.Status))
        }
      >
        ステータス
        <TextError className="mb-2">{getError("article.status")}</TextError>
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
          <TextError className="mb-2">
            {getError("article.published_at")}
          </TextError>
        </Input>
      )}
    </>
  );
};
