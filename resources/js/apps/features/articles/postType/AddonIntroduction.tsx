import Input from "@/apps/components/ui/Input";
import Textarea from "@/apps/components/ui/Textarea";
import { SelectCategories } from "../SelectCategories";
import { SelectableSearch } from "@/apps/components/form/SelectableSearch";
import Label from "@/apps/components/ui/Label";
import { Accordion } from "@/apps/components/ui/Accordion";
import Select from "@/apps/components/ui/Select";
import { t } from "@/lang/translate";
import { Image } from "../../../components/ui/Image";
import { TagEdit } from "../../tags/TagEdit";
import { AttachmentEdit } from "../../attachments/AttachmentEdit";
import { useState } from "react";

export const AddonIntroduction = ({
  article,
  categories,
  tags,
  attachments,
  relationalArticles,
  onChange,
  onChangeTags,
  onChangeAttachments,
}: ArticleEditProps) => {
  const contents = article.contents as ContentAddonIntroduction;

  const onCategoryChange = (category: Category.Search) => {
    const idx = article.categories.findIndex((c) => c === category.id);
    if (idx >= 0) {
      article.categories.splice(idx, 1);
    } else {
      article.categories.push(category.id);
    }
    onChange({ ...article });
  };
  const onTagChange = (tagIds: number[]) => {
    onChange({ ...article, tags: tagIds });
  };
  const onRelationalChange = (articleIds: number[]) => {
    onChange({ ...article, articles: articleIds });
  };

  const options = {
    publish: t("statuses.publish"),
    reservation: t("statuses.reservation"),
    draft: t("statuses.draft"),
    private: t("statuses.private"),
    trash: t("statuses.trash"),
  };

  return (
    <div className="grid gap-4">
      <Input
        labelClassName="font-medium"
        className="font-normal"
        value={article.title || ""}
        onChange={(e) => onChange({ ...article, title: e.target.value })}
      >
        タイトル
      </Input>
      <Input
        labelClassName="font-medium"
        className="font-normal"
        value={article.slug || ""}
        onChange={(e) => onChange({ ...article, slug: e.target.value })}
      >
        記事URL
      </Input>
      <Label className="font-medium">
        サムネイル
        <Image
          attachmentId={article.contents.thumbnail}
          attachments={attachments}
        />
        <Accordion title="画像の選択・アップロード">
          <div className="pl-4 grid gap-4">
            <AttachmentEdit
              attachments={attachments}
              attachmentableId={article.id}
              selected={article.contents.thumbnail}
              onSelectAttachment={(attachmentId) => {
                onChange({
                  ...article,
                  contents: { ...contents, thumbnail: attachmentId },
                });
              }}
            />
          </div>
        </Accordion>
      </Label>
      <Input
        labelClassName="font-medium"
        className="font-normal"
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
        labelClassName="font-medium"
        className="font-normal"
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
        labelClassName="font-medium"
        className="font-normal"
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
      <SelectCategories
        typeClassName="font-medium"
        className="font-normal"
        categories={categories}
        selected={article.categories}
        only={["pak", "addon", "pak128_position", "license", "double_slope"]}
        onSelect={onCategoryChange}
      />
      <Accordion
        title="その他の項目"
        defaultOpen={
          !!(
            contents.thanks ||
            contents.license ||
            article.tags.length ||
            article.articles.length
          )
        }
      >
        <div className="pl-4 grid gap-4">
          <Textarea
            labelClassName="font-medium"
            className="font-normal"
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
            labelClassName="font-medium"
            className="font-normal"
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
          <Label className="font-medium">
            タグ
            <SelectableSearch
              className="font-normal"
              labelKey="name"
              options={tags}
              selectedIds={article.tags}
              onChange={onTagChange}
            />
          </Label>
          <Accordion title="タグの作成・編集">
            <div className="pl-4 grid gap-4">
              <TagEdit tags={tags} onChangeTags={onChangeTags} />
            </div>
          </Accordion>

          <Label className="font-medium">
            関連記事
            <SelectableSearch
              className="font-normal"
              labelKey="title"
              options={relationalArticles}
              selectedIds={article.articles}
              onChange={onRelationalChange}
              render={(o) => `${o.title} (${o.user_name})`}
            />
          </Label>
        </div>
      </Accordion>
      <Select
        options={options}
        value={article.status}
        onChange={(e) =>
          onChange({ ...article, status: e.target.value as Status })
        }
      >
        ステータス
      </Select>
    </div>
  );
};
