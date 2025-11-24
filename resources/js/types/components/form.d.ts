/**
 * フォーム関連コンポーネントの共通Props型
 * Form component common props types
 */

import type { ReactNode } from "react";

/**
 * 基本的な入力フィールドProps
 * Basic input field props
 */
export interface InputProps {
  id?: string;
  name?: string;
  value?: string;
  placeholder?: string;
  disabled?: boolean;
  required?: boolean;
  className?: string;
  onChange?: (value: string) => void;
}

/**
 * テキストエリアProps
 * Textarea props
 */
export interface TextareaProps extends Omit<InputProps, "onChange"> {
  rows?: number;
  onChange?: (value: string) => void;
}

/**
 * セレクトProps
 * Select props
 */
export interface SelectProps<T = string> {
  id?: string;
  name?: string;
  value?: T;
  options: SelectOption<T>[];
  placeholder?: string;
  disabled?: boolean;
  required?: boolean;
  className?: string;
  onChange?: (value: T) => void;
}

/**
 * セレクトオプション
 * Select option
 */
export interface SelectOption<T = string> {
  label: string;
  value: T;
  disabled?: boolean;
}

/**
 * チェックボックスProps
 * Checkbox props
 */
export interface CheckboxProps {
  id?: string;
  name?: string;
  checked?: boolean;
  label?: ReactNode;
  disabled?: boolean;
  required?: boolean;
  className?: string;
  onChange?: (checked: boolean) => void;
}

/**
 * ラジオボタンProps
 * Radio props
 */
export interface RadioProps<T = string> {
  id?: string;
  name?: string;
  value: T;
  checked?: boolean;
  label?: ReactNode;
  disabled?: boolean;
  required?: boolean;
  className?: string;
  onChange?: (value: T) => void;
}

/**
 * ファイル入力Props
 * File input props
 */
export interface FileInputProps {
  id?: string;
  name?: string;
  accept?: string;
  multiple?: boolean;
  disabled?: boolean;
  required?: boolean;
  className?: string;
  onChange?: (files: FileList | null) => void;
}
