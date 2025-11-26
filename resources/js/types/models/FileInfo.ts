/**
 * FileInfo関連の型定義
 * FileInfo-related type definitions
 */

/**
 * 車両データ型
 * Vehicle data type
 */
export interface VehicleData {
  /** 輸送容量 */
  capacity?: number;
  /** 購入価格 (単位: 100cr) */
  price?: number;
  /** 最高速度 (km/h) */
  topspeed?: number;
  /** 重量 (ton) */
  weight?: number;
  /** 出力 (kW) */
  power?: number;
  /** 運用コスト (単位: 0.01cr/km) */
  running_cost?: number;
  /** 維持費 (単位: 0.01cr/月) */
  maintenance?: number;
  /** 乗降時間 (ms) */
  loading_time?: number;
  /** 軸重 (ton) */
  axle_load?: number;
  /** 導入日 (YYYYMMフォーマット) */
  intro_date?: number;
  /** 引退日 (YYYYMMフォーマット) */
  retire_date?: number;
  /** ギア比 (64が標準) */
  gear?: number;
  /** 道路タイプID */
  wtyp?: number;
  /** サウンドID */
  sound?: number;
  /** エンジンタイプID (0-5) */
  engine_type?: number;
  /** エンジンタイプ名 (例: "Steam", "Diesel", "Electric") */
  engine_type_str?: string;
  /** 車両長 (タイル数の8倍) */
  len?: number;
  /** 先頭車両数 */
  leader_count?: number;
  /** 付随車両数 */
  trailer_count?: number;
  /** 貨物画像タイプ */
  freight_image_type?: number;
  /** 輸送品目名 (例: "Passagiere", "Post") */
  freight_type?: string;
}

/**
 * 道路データ型
 * Way data type
 */
export interface WayData {
  /** 建設価格 (単位: 100cr/タイル) */
  price?: number;
  /** 維持費 (単位: 0.01cr/月/タイル) */
  maintenance?: number;
  /** 制限速度 (km/h) */
  topspeed?: number;
  /** 最大重量制限 (ton) */
  max_weight?: number;
  /** 導入日 (YYYYMMフォーマット) */
  intro_date?: number;
  /** 引退日 (YYYYMMフォーマット) */
  retire_date?: number;
  /** 軸重制限 (ton) */
  axle_load?: number;
  /** 道路タイプID (0=道路, 1=線路, 2=単軌鉄道, etc) */
  wtyp?: number;
  /** 道路タイプ名 (例: "Road", "Track", "Monorail") */
  wtyp_str?: string;
  /** システムタイプID (0=平地, 1=高架, 7=地下, etc) */
  styp?: number;
  /** システムタイプ名 (例: "Flat", "Elevated", "Tram") */
  styp_str?: string;
  /** オブジェクトとして描画するか */
  draw_as_obj?: boolean | number;
  /** 季節画像数 */
  number_of_seasons?: number;
}

/**
 * Pakメタデータ型
 * Pak metadata type
 */
export interface PakMetadata {
  /** オブジェクト名 */
  name: string;
  /** 著作権情報 */
  copyright: string | null;
  /** オブジェクトタイプ (例: "vehicle", "way", "building") */
  objectType: string;
  /** コンパイラバージョンコード */
  compilerVersionCode: number;
  /** 車両データ (objectType="vehicle"の場合のみ) */
  vehicleData?: VehicleData;
  /** 道路データ (objectType="way"の場合のみ) */
  wayData?: WayData;
  /** 建物データ (未実装) */
  buildingData?: Record<string, unknown>;
  /** 橋データ (未実装) */
  bridgeData?: Record<string, unknown>;
  /** 樹木データ (未実装) */
  treeData?: Record<string, unknown>;
  /** 商品データ (未実装) */
  goodData?: Record<string, unknown>;
}

/**
 * ファイル情報表示型
 * FileInfo display type
 */
export interface FileInfoShow {
  data: {
    dats: Record<string, string[]>;
    tabs: Record<string, Record<string, string>>;
  };
}

/**
 * マイページファイル情報編集型
 * Mypage FileInfo edit type
 */
export interface FileInfoMypageEdit {
  data: {
    dats: Record<string, string[]>;
    tabs: Record<string, Record<string, string>>;
    paks: Record<string, string[]>;
    paks_metadata?: Record<string, PakMetadata[]>;
    readmes: Record<string, string[]>;
  };
}
