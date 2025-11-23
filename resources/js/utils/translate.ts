// resources/js/lang/t.ts
import ja from "./ja.json";

const translations = { ja } as Record<string, Record<string, string>>;

export function getTranslation(key: string, locale = "ja"): string {
  return translations[locale]?.[key] ?? key;
}

export function t(
  key: string,
  vars?: Record<string, string>,
  locale = "ja"
): string {
  const template = getTranslation(key, locale);
  return vars
    ? Object.entries(vars).reduce(
        (acc, [k, v]) => acc.replaceAll(`:${k}`, v),
        template
      )
    : template;
}
