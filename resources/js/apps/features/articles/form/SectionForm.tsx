import Button from "@/apps/components/ui/Button";
import { useArticleEditor } from "@/apps/state/useArticleEditor";
import { match } from "ts-pattern";
import { SectionCaption } from "./Section/SectionCaption";
import { SectionText } from "./Section/SectionText";
import { SectionImage } from "./Section/SectionImage";
import { SectionUrl } from "./Section/SectionUrl";
import Label from "@/apps/components/ui/Label";

const template = {
  caption: { type: "caption", caption: "" } as SectionCaption,
  text: { type: "text", text: "" } as SectionText,
  image: { type: "image", id: null } as SectionImage,
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

  return (
    <div className="space-y-4">
      {contents.sections.map((section, idx) => {
        return (
          <div key={idx}>
            {match(section)
              .with({ type: "caption" }, (s) => (
                <SectionCaption
                  idx={idx}
                  section={s}
                  onChange={(e) =>
                    updateContents<ContentPage>((draft) => {
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
                    updateContents<ContentPage>((draft) => {
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
                    updateContents<ContentPage>((draft) => {
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
                    updateContents<ContentPage>((draft) => {
                      if ("id" in draft.sections[idx]) {
                        draft.sections[idx].id = Number(e.currentTarget.value);
                      }
                    })
                  }
                />
              ))
              .exhaustive()}
          </div>
        );
      })}
      <div className="space-x-2">
        <Label>項目の追加</Label>
        <Button onClick={add("caption")}>見出し</Button>
        <Button onClick={add("text")}>テキスト</Button>
        <Button onClick={add("image")}>画像</Button>
        <Button onClick={add("url")}>URL</Button>
      </div>
    </div>
  );
};
