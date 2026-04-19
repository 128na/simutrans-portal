import React from "react";
import {
  PakInfoTable,
  type TableRow,
} from "@/features/articles/components/pak/PakInfoTable";
import type { PakMetadata } from "@/types/models";
import { ObjectType } from "./pakConstants";
import { buildDetailRows } from "./pakRowBuilders";

interface Props {
  metadata: PakMetadata;
}

const PakGenericMetadata: React.FC<Props> = ({ metadata }) => {
  const typeData = getTypeSpecificData(metadata);

  const rows: TableRow[] = [
    { label: "オブジェクト名", value: metadata.name },
    { label: "著作権", value: metadata.copyright || "" },
    {
      label: "タイプ",
      value: getObjectTypeLabel(metadata.objectType as ObjectType),
    },
  ];

  if (typeData && Object.keys(typeData).length) {
    rows.push(...buildDetailRows(metadata.objectType, typeData));
  }

  return <PakInfoTable rows={rows} />;
};

function getObjectTypeLabel(objectType: ObjectType): string {
  const labels: Record<ObjectType, string> = {
    vehicle: "車両",
    way: "軌道",
    wayobj: "軌道オブジェクト",
    bridge: "橋",
    tunnel: "トンネル",
    roadsign: "信号・標識",
    crossing: "踏切",
    citycar: "市内車両",
    factory: "産業施設",
    good: "輸送品目",
    building: "建物",
    pedestrian: "歩行者",
    tree: "樹木",
    groundobj: "地上オブジェクト",
    ground: "地形テクスチャ",
    sound: "効果音",
    menu: "メニュー画像",
    cursor: "カーソル画像",
    symbol: "シンボル画像",
    field: "フィールド画像",
    smoke: "煙エフェクト",
    miscimages: "その他画像",
  };

  return labels[objectType]
    ? `${labels[objectType]} (${objectType})`
    : objectType;
}

function getTypeSpecificData(
  metadata: PakMetadata
): Record<string, unknown> | null {
  const dataKeys = (Object.keys(metadata) as Array<keyof PakMetadata>).filter(
    (key) => typeof key === "string" && key.endsWith("Data")
  );

  if (dataKeys.length === 0) return null;

  const data = metadata[dataKeys[0]];
  if (typeof data !== "object" || data === null) return null;

  return data as Record<string, unknown>;
}

export default PakGenericMetadata;
