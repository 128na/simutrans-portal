import Link from "@/apps/components/ui/Link";
import React, { useState } from "react";
import { TitleH4 } from "../TitleH4";
import { TitleH3 } from "../TitleH3";
import { TextPre } from "../TextPre";
import { formatArticleDate } from "../../articles/articleUtil";
import { Accordion } from "@/apps/components/ui/Accordion";

type Props = {
  article: ArticleShow.Article;
};

export const AddonPost = ({ article }: Props) => {
  const contents = article.contents as ContentAddonPost;
  const file = article.attachments.find((att) => att.id === contents.file) as
    | ArticleShow.Attachment
    | undefined;
  const fileInfo = file?.fileInfo as ArticleShow.FileInfo | undefined;
  const dats = fileInfo?.data?.dats;
  const tabs = fileInfo?.data?.tabs;

  const [openDat, setOpenDat] = useState(false);
  const [openTab, setOpenTab] = useState(false);

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
                <Link href={file ? `/articles/${article.id}/download` : "#"}>
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

          {dats && (
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

          {tabs && (
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
                                ),
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
