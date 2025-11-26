/**
 * FileInfo関連の型定義
 * FileInfo-related type definitions
 */

/**
 * 車両データ型
 * Vehicle data type
 */
export interface VehicleData {
  capacity?: number;
  price?: number;
  topspeed?: number;
  weight?: number;
  power?: number;
  running_cost?: number;
  maintenance?: number;
  loading_time?: number;
  axle_load?: number;
  intro_date?: number;
  retire_date?: number;
  gear?: number;
  wtyp?: number;
  sound?: number;
  engine_type?: number;
  engine_type_str?: string;
  len?: number;
  leader_count?: number;
  trailer_count?: number;
  freight_image_type?: number;
  freight_type?: string;
}

/**
 * Pakメタデータ型
 * Pak metadata type
 */
export interface PakMetadata {
  name: string;
  copyright: string | null;
  objectType: string;
  compilerVersionCode: number;
  vehicleData?: VehicleData;
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
