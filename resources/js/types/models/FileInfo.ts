/**
 * FileInfo関連の型定義
 * FileInfo-related type definitions
 */

/**
 * Pakメタデータ型
 * Pak metadata type
 */
export interface PakMetadata {
  name: string;
  copyright: string | null;
  objectType: string;
  compilerVersionCode: number;
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
