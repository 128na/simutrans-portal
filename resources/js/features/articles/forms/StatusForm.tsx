import { useArticleEditor } from "@/hooks/useArticleEditor";
import { addHours, format } from "date-fns";
import { StatusText } from "../utils/articleUtil";
import { useAxiosError } from "@/hooks/useAxiosError";
import TextError from "@/components/ui/TextError";
import { FormCaption } from "@/components/ui/FormCaption";
import V2Select from "@/components/ui/v2/V2Select";
import V2Input from "@/components/ui/v2/V2Input";

export const StatusForm = () => {
  const article = useArticleEditor((s) => s.article);
  const update = useArticleEditor((s) => s.update);
  const { getError } = useAxiosError();

  return (
    <>
      <div>
        <FormCaption>ステータス</FormCaption>
        <TextError>{getError("article.status")}</TextError>
        <V2Select
          className="w-full"
          options={StatusText}
          value={article.status}
          onChange={(e) =>
            update((draft) => (draft.status = e.target.value as ArticleStatus))
          }
        />
      </div>
      {article.status === "reservation" && (
        <div>
          <FormCaption>予約日時</FormCaption>
          <TextError>{getError("article.published_at")}</TextError>
          <V2Input
            className="w-full"
            type="datetime-local"
            value={article.published_at ?? ""}
            min={format(addHours(new Date(), 1), "yyyy-MM-dd'T'HH:mm")}
            onChange={(e) =>
              update((draft) => (draft.published_at = e.target.value))
            }
          />
        </div>
      )}
    </>
  );
};
