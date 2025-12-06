/**
 * ユーティリティ型のエクスポート
 * Utility types exports
 */

export type {
  ApiResponse,
  PaginatedResponse,
  ValidationError,
  ErrorResponse,
} from "./response";

export type {
  PaginationInfo,
  PaginationLinks,
  PaginationParams,
} from "./pagination";

export type ProfileService = {
  service: string;
  src: string;
  match: boolean;
};
