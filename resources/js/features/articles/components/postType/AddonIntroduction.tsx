import Link from "@/components/ui/Link";
import { formatArticleDate } from "../../utils/articleUtil";
import { TextPre } from "../TextPre";
import { TitleH3 } from "../TitleH3";
import { TitleH4 } from "../TitleH4";

type Props = {
  article: Article.Show;
  preview: boolean;
};
export const AddonIntroduction = ({ article, preview }: Props) => {
  const contents = article.contents as ArticleContent.AddonIntroduction;
  return (
    <>
      <TextPre>{contents.description || "(本文)"}</TextPre>

      <div>
        <TitleH3>詳細情報</TitleH3>
        <div className="overflow-x-auto">
          <table className="border-collapse whitespace-nowrap">
            <tbody>
              <tr>
                <td className="v2-table-header">
                  作者
                </td>
                <td className="v2-table-cell">
                  {contents.author ?? article.user.name}
                </td>
              </tr>
              <tr>
                <td className="v2-table-header">
                  作者による掲載許可
                </td>
                <td className="v2-table-cell">
                  {contents.agreement ? "取得済み" : "未取得"}
                </td>
              </tr>
              <tr>
                <td className="v2-table-header">
                  公開日時
                </td>
                <td className="v2-table-cell">
                  {formatArticleDate(article.published_at)}
                </td>
              </tr>
              <tr>
                <td className="v2-table-header">
                  最終更新日時
                </td>
                <td className="v2-table-cell">
                  {formatArticleDate(article.modified_at)}
                </td>
              </tr>
              <tr>
                <td className="v2-table-header">
                  掲載URL
                </td>
                <td className="v2-table-cell">
                  <Link
                    href={preview ? "#" : `/articles/${article.id}/conversion`}
                  >
                    {contents.link ?? "未設定"}
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      {contents.thanks && (
        <div>
          <TitleH4>謝辞</TitleH4>
          <TextPre>{contents.thanks}</TextPre>
        </div>
      )}

      {contents.license && (
        <div>
          <TitleH4>参考にしたアドオン</TitleH4>
          <TextPre>{contents.license}</TextPre>
        </div>
      )}
    </>
  );
};

