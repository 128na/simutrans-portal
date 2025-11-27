import React from "react";
import {
  PakInfoTable,
  type TableRow,
} from "@/features/articles/components/pak/PakInfoTable";
import type { PakMetadata } from "@/types/models";

interface Props {
  metadata: PakMetadata;
}

/**
 * 汎用的なPakオブジェクトメタデータ表示コンポーネント
 * vehicle以外のオブジェクトタイプ（way, building, factory等）に対応
 */
const PakGenericMetadata: React.FC<Props> = ({ metadata }) => {
  const typeData = getTypeSpecificData(metadata);

  // 基本情報テーブル
  const basicRows: TableRow[] = [
    { label: "オブジェクト名", value: metadata.name },
    { label: "著作権", value: metadata.copyright || "" },
    {
      label: "オブジェクトタイプ",
      value: getObjectTypeLabel(metadata.objectType),
    },
  ];

  // タイプ固有データが存在しない場合は基本情報のみ表示
  if (!typeData || Object.keys(typeData).length === 0) {
    return (
      <div className="space-y-4">
        <PakInfoTable title="基本情報" rows={basicRows} />
      </div>
    );
  }

  // タイプ固有の詳細情報テーブル
  const detailRows = buildDetailRows(metadata.objectType, typeData);

  return (
    <div className="space-y-4">
      <PakInfoTable title="基本情報" rows={basicRows} />
      {detailRows.length > 0 && (
        <PakInfoTable
          title={getDetailTableTitle(metadata.objectType)}
          rows={detailRows}
        />
      )}
    </div>
  );
};

/**
 * オブジェクトタイプのラベルを取得
 */
function getObjectTypeLabel(objectType: string): string {
  const labels: Record<string, string> = {
    vehicle: "車両",
    way: "軌道",
    wayobj: "軌道オブジェクト",
    bridge: "橋",
    tunnel: "トンネル",
    sign: "信号・標識",
    crossing: "踏切",
    citycar: "市内車両",
    factory: "産業施設",
    good: "輸送品目",
    building: "建物",
    pedestrian: "歩行者",
    tree: "樹木",
    groundobj: "地表オブジェクト",
    ground: "地形",
    sound: "サウンド",
    skin: "スキン",
  };

  return labels[objectType] || objectType;
}

/**
 * 詳細テーブルのタイトルを取得
 */
function getDetailTableTitle(objectType: string): string {
  const titles: Record<string, string> = {
    way: "軌道仕様",
    bridge: "橋梁仕様",
    tunnel: "トンネル仕様",
    building: "建物仕様",
    factory: "産業仕様",
    good: "輸送品目仕様",
  };

  return titles[objectType] || "詳細情報";
}

/**
 * メタデータからタイプ固有データを抽出
 */
function getTypeSpecificData(
  metadata: PakMetadata
): Record<string, unknown> | null {
  // wayData, buildingData, factoryData等のキーを探す
  const dataKeys = (Object.keys(metadata) as Array<keyof PakMetadata>).filter(
    (key) => typeof key === "string" && key.endsWith("Data")
  );

  if (dataKeys.length === 0) {
    return null;
  }

  const dataKey = dataKeys[0];
  const data = metadata[dataKey];

  if (typeof data !== "object" || data === null) {
    return null;
  }

  return data as Record<string, unknown>;
}

/**
 * タイプ固有の詳細情報行を構築
 */
function buildDetailRows(
  objectType: string,
  typeData: Record<string, unknown>
): TableRow[] {
  switch (objectType) {
    case "way":
      return buildWayRows(typeData);
    case "bridge":
      return buildBridgeRows(typeData);
    case "tunnel":
      return buildTunnelRows(typeData);
    case "building":
      return buildBuildingRows(typeData);
    case "factory":
      return buildFactoryRows(typeData);
    case "good":
      return buildGoodRows(typeData);
    default:
      return buildGenericRows(typeData);
  }
}

/**
 * Way（軌道）の詳細行
 */
function buildWayRows(data: Record<string, unknown>): TableRow[] {
  return [
    { label: "軌道タイプ", value: String(data.wtyp_str || data.wtyp || "") },
    {
      label: "システムタイプ",
      value: String(data.styp_str || data.styp || ""),
    },
    {
      label: "最高速度",
      value: data.topspeed ? `${data.topspeed} km/h` : "",
    },
    {
      label: "最大積載量",
      value: data.max_weight ? `${data.max_weight} t` : "",
    },
    {
      label: "建設費",
      value: data.price ? `${Number(data.price) / 100} Cr` : "",
    },
    {
      label: "維持費",
      value: data.maintenance ? `${Number(data.maintenance) / 100} Cr/月` : "",
    },
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    },
  ];
}

/**
 * Bridge（橋）の詳細行
 */
function buildBridgeRows(data: Record<string, unknown>): TableRow[] {
  return [
    { label: "軌道タイプ", value: String(data.wtyp_str || data.wtyp || "") },
    {
      label: "最高速度",
      value: data.topspeed ? `${data.topspeed} km/h` : "",
    },
    {
      label: "最大積載量",
      value: data.max_weight ? `${data.max_weight} t` : "",
    },
    {
      label: "建設費",
      value: data.price ? `${Number(data.price) / 100} Cr` : "",
    },
    {
      label: "維持費",
      value: data.maintenance ? `${Number(data.maintenance) / 100} Cr/月` : "",
    },
    { label: "最大長", value: data.max_length ? `${data.max_length}` : "" },
    {
      label: "最小長",
      value: data.min_length ? `${data.min_length}` : "",
    },
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    },
  ];
}

/**
 * Tunnel（トンネル）の詳細行
 */
function buildTunnelRows(data: Record<string, unknown>): TableRow[] {
  return [
    { label: "軌道タイプ", value: String(data.wtyp_str || data.wtyp || "") },
    {
      label: "最高速度",
      value: data.topspeed ? `${data.topspeed} km/h` : "",
    },
    {
      label: "最大積載量",
      value: data.max_weight ? `${data.max_weight} t` : "",
    },
    {
      label: "建設費",
      value: data.price ? `${Number(data.price) / 100} Cr` : "",
    },
    {
      label: "維持費",
      value: data.maintenance ? `${Number(data.maintenance) / 100} Cr/月` : "",
    },
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    },
  ];
}

/**
 * Building（建物）の詳細行
 */
function buildBuildingRows(data: Record<string, unknown>): TableRow[] {
  return [
    { label: "建物タイプ", value: String(data.type_str || data.type || "") },
    { label: "レベル", value: String(data.level || "") },
    { label: "サイズ", value: `${data.size_x || 1} x ${data.size_y || 1}` },
    { label: "レイアウト数", value: String(data.layouts || "") },
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    },
  ];
}

/**
 * Factory（産業）の詳細行
 */
function buildFactoryRows(data: Record<string, unknown>): TableRow[] {
  return [
    { label: "生産力", value: String(data.productivity || "") },
    { label: "生産範囲", value: String(data.range || "") },
    { label: "配置確率", value: String(data.distribution_weight || "") },
    {
      label: "登場年月",
      value: data.intro_date ? formatDate(Number(data.intro_date)) : "",
    },
    {
      label: "引退年月",
      value: data.retire_date ? formatDate(Number(data.retire_date)) : "",
    },
  ];
}

/**
 * Good（輸送品目）の詳細行
 */
function buildGoodRows(data: Record<string, unknown>): TableRow[] {
  return [
    { label: "カテゴリ", value: String(data.catg_index || "") },
    { label: "重量", value: data.weight ? `${data.weight}` : "" },
    { label: "価値", value: String(data.value || "") },
    { label: "速度ボーナス", value: String(data.speed_bonus || "") },
  ];
}

/**
 * 汎用的な詳細行（不明なタイプの場合）
 */
function buildGenericRows(data: Record<string, unknown>): TableRow[] {
  return Object.entries(data)
    .filter(([key]) => !key.endsWith("_str")) // *_str系は除外（元データと重複）
    .map(([key, value]) => ({
      label: key,
      value: String(value ?? ""),
    }));
}

/**
 * 日付フォーマット (year*12+month → YYYY/MM)
 */
function formatDate(monthsSinceZero: number): string {
  if (monthsSinceZero === 0 || monthsSinceZero >= 999912) {
    return "";
  }

  const year = Math.floor(monthsSinceZero / 12);
  const month = (monthsSinceZero % 12) + 1;

  return `${year}/${month.toString().padStart(2, "0")}`;
}

export default PakGenericMetadata;
