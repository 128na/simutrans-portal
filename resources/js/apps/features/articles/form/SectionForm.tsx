import Button from "@/apps/components/ui/Button";
import { useArticleEditor } from "@/apps/state/useArticleEditor";
import { JSX } from "react";
import { match } from "ts-pattern";
import { SectionCaption } from "./Section/SectionCaption";
import { SectionText } from "./Section/SectionText";
import { SectionImage } from "./Section/SectionImage";
import { SectionUrl } from "./Section/SectionUrl";

const template = {
  caption: { type: "caption", caption: "" } as SectionCaption,
  text: { type: "text", text: "" } as SectionText,
  image: { type: "image", image: null } as SectionImage,
  url: { type: "url", url: "" } as SectionUrl,
};
export const SectionForm = () => {
  const article = useArticleEditor((s) => s.article);
  const contents = article.contents as ContentPage;
  const updateContents = useArticleEditor((s) => s.updateContents);

  const add = (type: SectionType) => () => {
    updateContents<ContentPage>((draft) => {
      draft.sections.push(template[type]);
    });
  };

  console.log(contents.sections);
  return (
    <div className="">
      {contents.sections.map((section, idx) => {
        const props = {};
        return match(section)
          .with({ type: "caption" }, (s) => (
            <SectionCaption
              key={idx}
              section={s}
              onChange={(e) =>
                updateContents<ContentPage>((draft) => {
                  if ("caption" in draft.sections[idx]) {
                    draft.sections[idx].caption = e.currentTarget.value;
                  }
                })
              }
              {...props}
            />
          ))
          .with({ type: "text" }, (s) => (
            <SectionText
              key={idx}
              section={s}
              onChange={(e) =>
                updateContents<ContentPage>((draft) => {
                  if ("text" in draft.sections[idx]) {
                    draft.sections[idx].text = e.currentTarget.value;
                  }
                })
              }
              {...props}
            />
          ))
          .with({ type: "image" }, (s) => (
            <SectionImage
              key={idx}
              section={s}
              onChange={(e) =>
                updateContents<ContentPage>((draft) => {
                  if ("image" in draft.sections[idx]) {
                    draft.sections[idx].image = Number(e.currentTarget.value);
                  }
                })
              }
              {...props}
            />
          ))
          .with({ type: "url" }, (s) => (
            <SectionUrl
              key={idx}
              section={s}
              onChange={(e) =>
                updateContents<ContentPage>((draft) => {
                  if ("url" in draft.sections[idx]) {
                    draft.sections[idx].url = e.currentTarget.value;
                  }
                })
              }
              {...props}
            />
          ))
          .exhaustive();
      })}
      <div className="space-x-2">
        <Button onClick={add("caption")}>見出し</Button>
        <Button onClick={add("text")}>テキスト</Button>
        <Button onClick={add("image")}>画像</Button>
        <Button onClick={add("url")}>URL</Button>
      </div>
    </div>
  );
};
