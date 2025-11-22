import LinkExternal from "@/apps/components/ui/LinkExternal";
import { formatArticleDate } from "../../articles/articleUtil";
import { TextPre } from "../TextPre";
import { TitleH3 } from "../TitleH3";
import { TitleH4 } from "../TitleH4";

type Props = {
  article: ArticleShow.Article;
};
export const AddonIntroduction = ({ article }: Props) => {
  const contents = article.contents as ContentAddonIntroduction;
  return (
    <div>
      <TextPre>{contents.description}</TextPre>

      <TitleH3>詳細情報</TitleH3>
      <div className="overflow-x-auto">
        <table className="border-collapse whitespace-nowrap">
          <tbody>
            <tr>
              <td className="border border-gray-300 px-4 py-2 bg-gray-500 text-white">
                作者
              </td>
              <td className="border border-gray-300 px-4 py-2">
                {contents.author ?? article.user.name}
              </td>
            </tr>
            <tr>
              <td className="border border-gray-300 px-4 py-2 bg-gray-500 text-white">
                作者による掲載許可
              </td>
              <td className="border border-gray-300 px-4 py-2">
                {contents.agreement ? "取得済み" : "未取得"}
              </td>
            </tr>
            <tr>
              <td className="border border-gray-300 px-4 py-2 bg-gray-500 text-white">
                公開日時
              </td>
              <td className="border border-gray-300 px-4 py-2">
                {formatArticleDate(article.published_at)}
              </td>
            </tr>
            <tr>
              <td className="border border-gray-300 px-4 py-2 bg-gray-500 text-white">
                最終更新日時
              </td>
              <td className="border border-gray-300 px-4 py-2">
                {formatArticleDate(article.modified_at)}
              </td>
            </tr>
            <tr>
              <td className="border border-gray-300 px-4 py-2 bg-gray-500 text-white">
                掲載URL
              </td>
              <td className="border border-gray-300 px-4 py-2">
                <LinkExternal
                  href={
                    contents.link ? `/articles/${article.id}/conversion` : "#"
                  }
                >
                  {contents.link ?? "未設定"}
                </LinkExternal>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      {contents.thanks && (
        <>
          <TitleH4>謝辞</TitleH4>
          <TextPre>{contents.thanks}</TextPre>
        </>
      )}

      {contents.license && (
        <>
          <TitleH4>参考にしたアドオン</TitleH4>
          <TextPre>{contents.license}</TextPre>
        </>
      )}
    </div>
  );
};
