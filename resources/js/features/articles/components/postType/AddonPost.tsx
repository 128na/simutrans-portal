import Link from "@/components/ui/Link";
import { TitleH4 } from "../TitleH4";
import { TitleH3 } from "../TitleH3";
import { TextPre } from "../TextPre";
import { formatArticleDate } from "../../utils/articleUtil";
import { Accordion } from "@/components/ui/Accordion";
import { PakMetadata } from "../PakMetadata";
import React from "react";
import { displaySize } from "@/features/attachments/attachmentUtil";
import TextSub from "@/components/ui/TextSub";

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
  const dats = fileInfo?.data?.dats ?? {};
  const tabs = fileInfo?.data?.tabs ?? {};
  const paksMetadata = fileInfo?.data?.paks_metadata ?? {};

  const hasDats = Object.keys(dats).length > 0;
  const hasTabs = Object.keys(tabs).length > 0;
  const hasPaksMetadata = Object.keys(paksMetadata).length > 0;
  const hasFileInfo = hasDats || hasTabs || hasPaksMetadata;

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
                <th>公開日時</th>
                <td>{formatArticleDate(article.published_at)}</td>
              </tr>

              <tr>
                <th>最終更新日時</th>
                <td>{formatArticleDate(article.modified_at)}</td>
              </tr>

              <tr>
                <th>ダウンロード</th>
                <td>
                  <Link
                    href={preview ? "#" : `/articles/${article.id}/download`}
                  >
                    {file
                      ? `${file.original_name} (${displaySize(file.size)})`
                      : "download"}
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      {hasFileInfo && (
        <div>
          <TitleH4>ファイル情報</TitleH4>

          {hasDats && (
            <Accordion title="Datファイル">
              <div className="mt-2 block space-y-2">
                <ul className="list-none">
                  {Object.entries(dats).map(([filename, addonNames]) => (
                    <React.Fragment key={filename}>
                      <li className="mb-1 break-all">{filename}</li>
                      <li className="mb-6">
                        <ul className="v2-list-disc v2-list-item-sub">
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

          {hasTabs && (
            <Accordion title="Tabファイル">
              <div className="mt-2 block space-y-2">
                <ul className="list-none">
                  {Object.entries(tabs).map(([filename, translateMap]) => (
                    <React.Fragment key={filename}>
                      <li className="mb-1 break-all">{filename}</li>
                      <li className="mb-6">
                        <div className="v2-table-wrapper">
                          <table className="v2-table">
                            <thead>
                              <tr>
                                <th>アドオン名</th>
                                <th>翻訳テキスト</th>
                              </tr>
                            </thead>
                            <tbody>
                              {Object.entries(translateMap).map(
                                ([addonName, translateName]) => (
                                  <tr key={addonName}>
                                    <td>{addonName}</td>
                                    <td>{translateName}</td>
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

          {hasPaksMetadata && (
            <Accordion title="Pakファイル（ベータ版）">
              <TextSub>
                独自実装のスキャン処理のため、表示内容が正しくない場合があります。
              </TextSub>
              <PakMetadata paksMetadata={paksMetadata} />
            </Accordion>
          )}
        </div>
      )}

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
