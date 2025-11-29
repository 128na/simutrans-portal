/**
 * 日付フォーマット (year*12+month → YYYY/MM)
 */
export function formatDate(value: number | undefined): string {
  if (value === 0 || value === undefined || value >= 999912) {
    return "";
  }

  const year = Math.floor(value / 12);
  const month = (value % 12) + 1;

  return `${year}/${month.toString().padStart(2, "0")}`;
}

export const formatSpeed = (speed: number | undefined): string => {
  if (speed === undefined) {
    return "";
  }
  return `${speed} km/h`;
};

export const formatPower = (power: number | undefined): string => {
  if (power === undefined) {
    return "";
  }
  return `${power} kW`;
};

export const formatGear = (gear: number | undefined): string => {
  if (gear === undefined) {
    return "";
  }
  return `${gear.toFixed(1)}`;
};

export const formatWeight = (weight: number | undefined): string => {
  if (weight === undefined) {
    return "";
  }
  return `${(weight / 1000).toFixed(1)} t`;
};

export const formatNum = (num: number | undefined): string => {
  if (num === undefined) {
    return "";
  }
  return `${num}`;
};

export const formatPrice = (price: number | undefined): string => {
  if (price === undefined) {
    return "";
  }
  return `${(price * 100).toLocaleString()} Cr`;
};

export const formatRunningCost = (cost: number | undefined): string => {
  if (cost === undefined) {
    return "";
  }
  return `${(cost / 100).toLocaleString()} Cr/km`;
};

export const formatMaintenanceCost = (cost: number | undefined): string => {
  if (cost === undefined) {
    return "";
  }
  return `${(cost / 100).toLocaleString()} Cr/月`;
};

export const formatGoodCategory = (catg: number | undefined): string => {
  if (catg === undefined) {
    return "";
  }

  const categories: Record<number, string> = {
    0: "特殊貨物",
    1: "小口貨物",
    2: "バルク貨物",
    3: "長尺貨物",
    4: "液体貨物",
    5: "冷蔵貨物",
    6: "旅客",
    7: "郵便",
    8: "なし",
  };

  return categories[catg] || "";
};
