import LinkExternal from "@/apps/components/ui/LinkExternal";
import { TextPre } from "../../frontArticle/TextPre";
import { TitleH4 } from "../../frontArticle/TitleH4";
import { Thumbnail } from "@/apps/components/ui/Thumbnail";

type Props = {
  article: ArticleShow.Article;
  preview: boolean;
};

export const Page = ({ article, preview }: Props) => {
  const contents = article.contents as ContentPage;
  return (
    <>
      {contents.sections.map((section, index) => {
        if (section.type === "text") {
          return <TextPre key={index}>{section.text || "(本文)"}</TextPre>;
        }
        if (section.type === "caption") {
          return <TitleH4 key={index}>{section.caption || "(見出し)"}</TitleH4>;
        }
        if (section.type === "url") {
          return (
            <LinkExternal
              key={index}
              href={preview ? "#" : (section.url ?? "")}
            >
              {section.url ?? "(URL)"}
            </LinkExternal>
          );
        }
        if (section.type === "image") {
          return (
            <Thumbnail
              key={index}
              attachmentId={section.id}
              attachments={article.attachments}
            />
          );
        }
      })}
    </>
  );
};
