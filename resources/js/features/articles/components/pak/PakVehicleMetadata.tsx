import type { VehicleData, PakMetadata } from "@/types/models";
import { PakInfoTable } from "./PakInfoTable";
import {
  getEngineTypeName,
  getFreightTypeName,
  getWaytypeName,
} from "./pakTranslations";

type Props = {
  metadata: PakMetadata;
  vehicleData: VehicleData;
};

/**
 * Vehicle metadata display component
 *
 * Unit conversions (based on Simutrans source code):
 * - weight: stored in kg → display in tons (÷1000)
 * - price: stored in 1/100 Cr → display in Cr (×100)
 * - running_cost: stored in 1/100 Cr/km → display in Cr/km (÷100)
 * - maintenance: stored in 1/100 Cr/month → display in Cr/month (÷100)
 * - intro_date/retire_date: stored as (year*12 + month-1) → display as "YYYY年M月"
 * - len: stored in 1/8 tile units → display as-is (raw value)
 * - gear: stored as gear*64 (64=1.00) → display as-is (raw value)
 *
 * References:
 * - simutrans/descriptor/reader/vehicle_reader.cc (version 10+: weight in kg)
 * - simutrans/descriptor/vehicle_desc.h (get_weight, get_running_cost, etc.)
 */
export const PakVehicleMetadata = ({ metadata, vehicleData }: Props) => {
  const basicInfoRows = [
    { label: "名称", value: metadata.name },
    { label: "作者", value: metadata.copyright },
    {
      label: "導入年月",
      value:
        vehicleData.intro_date !== undefined
          ? `${Math.floor(vehicleData.intro_date / 12)}年${(vehicleData.intro_date % 12) + 1}月`
          : undefined,
    },
    {
      label: "引退年月",
      value:
        vehicleData.retire_date !== undefined
          ? `${Math.floor(vehicleData.retire_date / 12)}年${(vehicleData.retire_date % 12) + 1}月`
          : undefined,
    },
  ];

  const specRows = [
    {
      label: "最高速度",
      value:
        vehicleData.topspeed !== undefined
          ? `${vehicleData.topspeed} km/h`
          : undefined,
    },
    {
      label: "出力",
      value:
        vehicleData.power !== undefined ? `${vehicleData.power} kW` : undefined,
    },
    { label: "ギア", value: vehicleData.gear },
    {
      label: "重量",
      value:
        vehicleData.weight !== undefined
          ? `${(vehicleData.weight / 1000).toFixed(1)} t`
          : undefined,
    },
    {
      label: "軸重",
      value:
        vehicleData.axle_load !== undefined
          ? `${vehicleData.axle_load} t`
          : undefined,
    },
    { label: "定員/容量", value: vehicleData.capacity },
    {
      label: "輸送品目",
      value: getFreightTypeName(vehicleData.freight_type),
    },
    {
      label: "購入価格",
      value:
        vehicleData.price !== undefined
          ? `${(vehicleData.price * 100).toLocaleString()} Cr`
          : undefined,
    },
    {
      label: "運行費用",
      value:
        vehicleData.running_cost !== undefined
          ? `${(vehicleData.running_cost / 100).toLocaleString()} Cr/km`
          : undefined,
    },
    {
      label: "維持費",
      value:
        vehicleData.maintenance !== undefined
          ? `${(vehicleData.maintenance / 100).toLocaleString()} Cr/月`
          : undefined,
    },
    { label: "軌道タイプ", value: getWaytypeName(vehicleData.wtyp) },
    {
      label: "エンジンタイプ",
      value: getEngineTypeName(
        vehicleData.engine_type_str || vehicleData.engine_type
      ),
    },
    { label: "長さ", value: vehicleData.len },
  ];

  return (
    <div className="mt-4 space-y-4">
      <PakInfoTable title="基本情報" rows={basicInfoRows} />
      <PakInfoTable title="諸元" rows={specRows} />
    </div>
  );
};
