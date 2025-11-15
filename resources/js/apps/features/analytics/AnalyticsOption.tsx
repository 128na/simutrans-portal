import Button from "@/apps/components/ui/Button";
import Checkbox from "@/apps/components/ui/Checkbox";
import Input from "@/apps/components/ui/Input";
import Label from "@/apps/components/ui/Label";
import Select from "@/apps/components/ui/Select";
import { useAnalyticsStore } from "@/apps/state/useAnalyticsStore";
import { format, subMonths, subYears } from "date-fns";

const TYPES: Record<Analytics.Type, string> = {
  daily: "日次",
  monthly: "月次",
  yearly: "年次",
};

const AXES: { value: Analytics.Axis; label: string }[] = [
  { value: "pv", label: "ページ表示回数 (PV)" },
  { value: "cv", label: "DL・リンククリック回数 (CV)" },
];

export function AnalyticsOption() {
  const { start_date, end_date, type, axes, set } = useAnalyticsStore();

  return (
    <>
      <Label>期間</Label>
      <div className="gap-2 flex items-end">
        <Button
          onClick={() =>
            set({
              start_date: subMonths(new Date(), 3),
              end_date: new Date(),
              type: "daily",
            })
          }
        >
          3ヵ月
        </Button>
        <Button
          onClick={() =>
            set({
              start_date: subMonths(new Date(), 12),
              end_date: new Date(),
              type: "monthly",
            })
          }
        >
          12ヵ月
        </Button>
        <Button
          onClick={() =>
            set({
              start_date: subYears(new Date(), 3),
              end_date: new Date(),
              type: "yearly",
            })
          }
        >
          3年
        </Button>
      </div>
      <div className="gap-2 flex items-end">
        <Input
          type="date"
          value={format(start_date, "yyyy-MM-dd")}
          onChange={(e) => set({ start_date: new Date(e.target.value) })}
        >
          開始日
        </Input>
        <Input
          type="date"
          value={format(end_date, "yyyy-MM-dd")}
          onChange={(e) => set({ end_date: new Date(e.target.value) })}
        >
          終了日
        </Input>
        <Select
          value={type}
          onChange={(e) => set({ type: e.target.value as Analytics.Type })}
          options={TYPES}
        >
          間隔
        </Select>
      </div>
      <Label>表示データ</Label>
      <div className="gap-2 flex items-end">
        {AXES.map((axis) => (
          <Checkbox
            value={axis.value}
            key={axis.value}
            checked={axes.includes(axis.value)}
            onChange={() =>
              set({
                axes: axes.includes(axis.value)
                  ? axes.filter((a) => a !== axis.value)
                  : [axis.value, ...axes],
              })
            }
          >
            {axis.label}
          </Checkbox>
        ))}
      </div>
    </>
  );
}
