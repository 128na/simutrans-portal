import Input from "@/apps/components/ui/Input";
import Textarea from "@/apps/components/ui/Textarea";

export const AddonIntroduction = ({
  user,
  article,
  onChange,
}: ArticleEditProps) => {
  const contents = article.contents as ContentAddonIntroduction;
  return (
    <div>
      <Input
        value={article.title || ""}
        onChange={(e) => onChange({ ...article, title: e.target.value })}
      >
        タイトル
      </Input>
      <Input
        value={article.slug || ""}
        onChange={(e) => onChange({ ...article, slug: e.target.value })}
      >
        記事URL
      </Input>
      サムネ
      <Input
        value={contents.author || ""}
        onChange={(e) =>
          onChange({
            ...article,
            contents: { ...contents, author: e.target.value },
          })
        }
      >
        作者
      </Input>
      <Textarea
        value={contents.description || ""}
        rows={9}
        onChange={(e) =>
          onChange({
            ...article,
            contents: { ...contents, description: e.target.value },
          })
        }
      >
        説明
      </Textarea>
      <Input
        type="url"
        value={contents.link || ""}
        onChange={(e) =>
          onChange({
            ...article,
            contents: { ...contents, link: e.target.value },
          })
        }
      >
        リンク先
      </Input>
      <Textarea
        value={contents.thanks || ""}
        rows={3}
        onChange={(e) =>
          onChange({
            ...article,
            contents: { ...contents, thanks: e.target.value },
          })
        }
      >
        謝辞
      </Textarea>
      <Textarea
        value={contents.license || ""}
        rows={3}
        onChange={(e) =>
          onChange({
            ...article,
            contents: { ...contents, license: e.target.value },
          })
        }
      >
        ライセンス
      </Textarea>
    </div>
  );
};
