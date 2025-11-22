import LinkExternal from "@/apps/components/ui/LinkExternal";
import { TextPre } from "../TextPre";
import { TitleH4 } from "../TitleH4";
import { Thumbnail } from "@/apps/components/ui/Thumbnail";

type Props = {
  article: ArticleShow.Article;
};

export const Page = ({ article }: Props) => {
  const contents = article.contents as ContentPage;
  return (
    <>
      {contents.sections.map((section, index) => {
        if (section.type === "text") {
          return <TextPre key={index}>{section.text}</TextPre>;
        } else if (section.type === "caption") {
          return <TitleH4 key={index}>{section.caption}</TitleH4>;
        } else if (section.type === "url") {
          return (
            <LinkExternal key={index} href={section.url ?? ""}>
              {section.url ?? "link"}
            </LinkExternal>
          );
        } else if (section.type === "image") {
          <Thumbnail
            key={index}
            attachmentId={section.id}
            attachments={article.attachments}
          />;
        }
      })}
    </>
  );
};
