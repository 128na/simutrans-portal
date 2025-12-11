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
        <div className="v2-table-wrapper">
          <table className="v2-table">
            <tbody>
              <tr>
                <th>作者</th>
                <td>{contents.author ?? article.user.name}</td>
              </tr>
              <tr>
                <th>作者による掲載許可</th>
                <td>{contents.agreement ? "取得済み" : "未取得"}</td>
              </tr>
              <tr>
                <th>公開日時</th>
                <td>{formatArticleDate(article.published_at)}</td>
              </tr>
              <tr>
                <th>最終更新日時</th>
                <td>{formatArticleDate(article.modified_at)}</td>
              </tr>
              <tr>
                <th>掲載URL</th>
                <td>
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
