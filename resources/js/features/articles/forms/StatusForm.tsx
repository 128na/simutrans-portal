import Input from "@/components/ui/Input";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import Select from "@/components/ui/Select";
import { addHours, format } from "date-fns";
import { StatusText } from "../utils/articleUtil";
import { useAxiosError } from "@/hooks/useAxiosError";
import TextError from "@/components/ui/TextError";

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
          update((draft) => (draft.status = e.target.value as ArticleStatus))
        }
      >
        ステータス
        <TextError>{getError("article.status")}</TextError>
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
          <TextError>{getError("article.published_at")}</TextError>
        </Input>
      )}
    </>
  );
};
