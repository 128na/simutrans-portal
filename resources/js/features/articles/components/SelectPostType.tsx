import { FormCaption } from "@/components/ui/FormCaption";
import { TitleH4 } from "./TitleH4";
import TextSub from "@/components/ui/TextSub";
import { Card } from "@/components/ui/Card";

type Props = {
  onClick: (postType: ArticlePostType) => void;
};
export const SelectPostType = ({ onClick }: Props) => {
  return (
    <div className="mx-auto px-6 lg:px-8">
      <FormCaption>記事の形式</FormCaption>
      <div className="grid grid-cols-1 md:grid-cols-3 md:grid-rows-2 gap-4">
        <Card onClick={() => onClick("addon-post")} className="md:row-span-2">
          <TitleH4 className="mb-2 mt-0">アドオン投稿</TitleH4>
          <TextSub>
            pakやzipファイルをアップロードして投稿記事を作成します。
          </TextSub>
        </Card>
        <Card
          onClick={() => onClick("addon-introduction")}
          className="md:row-span-2"
        >
          <TitleH4 className="mb-2 mt-0">アドオン紹介</TitleH4>
          <TextSub>
            Wikiや個人サイトなどアドオン掲載ページへのリンクがある紹介記事を作成します。
          </TextSub>
        </Card>
        <Card onClick={() => onClick("page")} className="md:row-span-1">
          <TitleH4 className="mb-2 mt-0">一般記事</TitleH4>
          <TextSub>
            アドオン以外の文章記事を作成します。Simutransに関する話題であれば自由に使ってください。
          </TextSub>
        </Card>
        <Card onClick={() => onClick("markdown")} className="md:row-span-1">
          <TitleH4 className="mb-2 mt-0">一般記事（マークダウン）</TitleH4>
          <TextSub>マークダウン形式で文章記事を作成します。</TextSub>
        </Card>
      </div>
    </div>
  );
};
