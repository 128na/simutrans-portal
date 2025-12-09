import Link from "@/components/ui/Link";
import { TextPre } from "../TextPre";
import { TitleH4 } from "../TitleH4";
import { Thumbnail } from "@/components/ui/Thumbnail";

type Props = {
  article: Article.Show;
  preview: boolean;
};

export const Page = ({ article, preview }: Props) => {
  const contents = article.contents as ArticleContent.Page;
  return (
    <>
      {contents.sections.map((section, index) => {
        if (section.type === "text") {
          return <TextPre key={index}>{section.text || "(本文)"}</TextPre>;
        }
        if (section.type === "caption") {
          return (
            <TitleH4 key={index} className="my-0">
              {section.caption || "(見出し)"}
            </TitleH4>
          );
        }
        if (section.type === "url") {
          return (
            <Link key={index} href={preview ? "#" : (section.url ?? "")}>
              {section.url ?? "(URL)"}
            </Link>
          );
        }
        if (section.type === "image") {
          return (
            <Thumbnail
              key={index}
              attachmentId={section.id}
              attachments={article.attachments}
              openFullSize={preview ? false : true}
            />
          );
        }
      })}
    </>
  );
};
