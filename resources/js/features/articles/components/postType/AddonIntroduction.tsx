import { TextPre } from "../TextPre";
import { TitleH4 } from "../TitleH4";
import { ADDON_LICENSE_ANCHOR } from "../AddonAcquisition";

type Props = {
  article: Article.Show;
};
export const AddonIntroduction = ({ article }: Props) => {
  const contents = article.contents as ArticleContent.AddonIntroduction;
  return (
    <>
      <TextPre>{contents.description || "(本文)"}</TextPre>

      {contents.thanks && (
        <div>
          <TitleH4>謝辞</TitleH4>
          <TextPre>{contents.thanks}</TextPre>
        </div>
      )}

      {contents.license && (
        <div id={ADDON_LICENSE_ANCHOR} className="scroll-mt-6">
          <TitleH4>ライセンス・利用条件</TitleH4>
          <TextPre>{contents.license}</TextPre>
        </div>
      )}
    </>
  );
};
