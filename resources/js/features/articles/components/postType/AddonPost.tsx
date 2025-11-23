import Link from "@/components/ui/Link";
import { TitleH4 } from "../TitleH4";
import { TitleH3 } from "../TitleH3";
import { TextPre } from "../TextPre";
import { formatArticleDate } from "../../utils/articleUtil";
import { Accordion } from "@/components/ui/Accordion";
import React from "react";

type Props = {
  article: Article.Show;
  preview: boolean;
};

export const AddonPost = ({ article, preview }: Props) => {
  const contents = article.contents as ArticleContent.AddonPost;
  const file = article.attachments.find((att) => att.id === contents.file) as
    | Attachment.Show
    | undefined;
  const fileInfo = file?.fileInfo as FileInfo.Show | undefined;
  const dats = fileInfo?.data?.dats;
  const tabs = fileInfo?.data?.tabs;

  return (
    <div>
      <TextPre>{contents.description || "(本文)"}</TextPre>

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
                ダウンロード
              </td>
              <td className="border border-gray-300 px-4 py-2">
                <Link href={preview ? "#" : `/articles/${article.id}/download`}>
                  {file?.original_name ?? "download"}
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      {(dats || tabs) && (
        <>
          <TitleH4>ファイル情報</TitleH4>

          {dats && Object.keys(dats).length > 0 && (
            <Accordion title="Datファイル">
              <div className="mt-2 block space-y-2">
                <ul className="list-none">
                  {Object.entries(dats).map(([filename, addonNames]) => (
                    <React.Fragment key={filename}>
                      <li className="mb-1 break-all">{filename}</li>
                      <li className="mb-6">
                        <ul className="list-disc text-gray-400 ml-8 break-all">
                          {addonNames.map((name: string) => (
                            <li key={name}>
                              <span className="text-black break-all">
                                {name}
                              </span>
                            </li>
                          ))}
                        </ul>
                      </li>
                    </React.Fragment>
                  ))}
                </ul>
              </div>
            </Accordion>
          )}

          {tabs && Object.keys(tabs).length > 0 && (
            <Accordion title="Tabファイル">
              <div className="mt-2 block space-y-2">
                <ul className="list-none">
                  {Object.entries(tabs).map(([filename, translateMap]) => (
                    <React.Fragment key={filename}>
                      <li className="mb-1 break-all">{filename}</li>
                      <li className="mb-6">
                        <div className="overflow-x-auto">
                          <table className="border-collapse whitespace-nowrap">
                            <thead>
                              <tr>
                                <th className="border border-gray-300 px-4 py-2 bg-gray-500 text-white">
                                  アドオン名
                                </th>
                                <th className="border border-gray-300 px-4 py-2 bg-gray-500 text-white">
                                  翻訳テキスト
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              {Object.entries(translateMap).map(
                                ([addonName, translateName]) => (
                                  <tr key={addonName}>
                                    <td className="border border-gray-300 px-4 py-2">
                                      {addonName}
                                    </td>
                                    <td className="border border-gray-300 px-4 py-2">
                                      {translateName}
                                    </td>
                                  </tr>
                                )
                              )}
                            </tbody>
                          </table>
                        </div>
                      </li>
                    </React.Fragment>
                  ))}
                </ul>
              </div>
            </Accordion>
          )}
        </>
      )}

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
