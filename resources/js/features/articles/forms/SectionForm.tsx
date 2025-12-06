import Button from "@/components/ui/Button";
import { useArticleEditor } from "@/hooks/useArticleEditor";
import { match } from "ts-pattern";
import { SectionCaption } from "./Section/SectionCaption";
import { SectionText } from "./Section/SectionText";
import { SectionImage } from "./Section/SectionImage";
import { SectionUrl } from "./Section/SectionUrl";
import ButtonDanger from "@/components/ui/ButtonDanger";
import { FormCaption } from "@/components/ui/FormCaption";
import MultiColumn from "@/components/ui/MultiColumn";

const template = {
  caption: { type: "caption", caption: "" } as ArticleContent.Section.Caption,
  text: { type: "text", text: "" } as ArticleContent.Section.Text,
  image: { type: "image", id: null } as ArticleContent.Section.Image,
  url: { type: "url", url: "" } as ArticleContent.Section.Url,
};
const SectionName = {
  caption: "見出し",
  text: "テキスト",
  image: "画像",
  url: "URL",
} as const;
type SectionType = keyof typeof template;

export const SectionForm = () => {
  const article = useArticleEditor((s) => s.article);
  const contents = article.contents as ArticleContent.Page;
  const updateContents = useArticleEditor((s) => s.updateContents);

  const add = (type: SectionType) => {
    updateContents<ArticleContent.Page>((draft) => {
      draft.sections.push(template[type]);
    });
  };
  const remove = (index: number) => {
    if (!window.confirm("削除しますか？")) {
      return;
    }
    updateContents<ArticleContent.Page>((draft) => {
      draft.sections = [...draft.sections.filter((_, i) => i !== index)];
    });
  };

  return (
    <div className="space-y-4">
      {contents.sections.map((section, idx) => {
        return (
          <div>
            <FormCaption>{SectionName[section.type]}</FormCaption>
            <MultiColumn classNames={["grow", "shrink-0"]} key={idx}>
              {match(section)
                .with({ type: "caption" }, (s) => (
                  <SectionCaption
                    idx={idx}
                    section={s}
                    onChange={(e) =>
                      updateContents<ArticleContent.Page>((draft) => {
                        if ("caption" in draft.sections[idx]) {
                          draft.sections[idx].caption = e.currentTarget.value;
                        }
                      })
                    }
                  />
                ))
                .with({ type: "text" }, (s) => (
                  <SectionText
                    idx={idx}
                    section={s}
                    onChange={(e) =>
                      updateContents<ArticleContent.Page>((draft) => {
                        if ("text" in draft.sections[idx]) {
                          draft.sections[idx].text = e.currentTarget.value;
                        }
                      })
                    }
                  />
                ))
                .with({ type: "url" }, (s) => (
                  <SectionUrl
                    idx={idx}
                    section={s}
                    onChange={(e) =>
                      updateContents<ArticleContent.Page>((draft) => {
                        if ("url" in draft.sections[idx]) {
                          draft.sections[idx].url = e.currentTarget.value;
                        }
                      })
                    }
                  />
                ))
                .with({ type: "image" }, (s) => (
                  <SectionImage
                    idx={idx}
                    section={s}
                    articleId={article.id}
                    onUploaded={(a) => {
                      useArticleEditor.setState((state) => {
                        // アップロードした画像を同時にセットする
                        state.attachments.unshift(a);
                        if (
                          "sections" in state.article.contents &&
                          "id" in state.article.contents.sections[idx]
                        ) {
                          state.article.contents.sections[idx].id = a.id;
                        }
                      });
                    }}
                    onSelectAttachment={(id) => {
                      useArticleEditor.setState((state) => {
                        if (
                          "sections" in state.article.contents &&
                          "id" in state.article.contents.sections[idx]
                        ) {
                          state.article.contents.sections[idx].id = id;
                        }
                      });
                    }}
                    onChange={(e) =>
                      updateContents<ArticleContent.Page>((draft) => {
                        if ("id" in draft.sections[idx]) {
                          draft.sections[idx].id = Number(
                            e.currentTarget.value
                          );
                        }
                      })
                    }
                  />
                ))
                .exhaustive()}
              <ButtonDanger onClick={() => remove(idx)}>削除</ButtonDanger>
            </MultiColumn>
          </div>
        );
      })}
      <div className="space-x-2">
        <FormCaption>項目の追加</FormCaption>
        <Button onClick={() => add("caption")}>見出し</Button>
        <Button onClick={() => add("text")}>テキスト</Button>
        <Button onClick={() => add("image")}>画像</Button>
        <Button onClick={() => add("url")}>URL</Button>
      </div>
    </div>
  );
};
