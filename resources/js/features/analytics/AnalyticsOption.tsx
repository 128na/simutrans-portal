import { FormCaption } from "@/components/ui/FormCaption";
import V2Button from "@/components/ui/v2/V2Button";
import V2Checkbox from "@/components/ui/v2/V2Checkbox";
import V2Input from "@/components/ui/v2/V2Input";
import V2Select from "@/components/ui/v2/V2Select";
import { useAnalyticsStore } from "@/hooks/useAnalyticsStore";
import { format, subMonths, subYears } from "date-fns";

const TYPES = {
  daily: "日次",
  monthly: "月次",
  yearly: "年次",
} as const satisfies Record<Analytics.Type, string>;

const MODES = {
  periodic: "期間別",
  cumulative: "累積",
} as const satisfies Record<Analytics.Mode, string>;

const AXES = [
  { value: "pv", label: "ページ表示回数 (PV)" },
  { value: "cv", label: "DL・リンククリック回数 (CV)" },
] as const satisfies { value: Analytics.Axis; label: string }[];

export function AnalyticsOption() {
  const { start_date, end_date, type, axes, mode, set } = useAnalyticsStore();

  return (
    <>
      <div>
        <FormCaption>期間プリセット</FormCaption>
        <div className="gap-2 flex items-end">
          <V2Button
            variant="sub"
            onClick={() =>
              set({
                start_date: subMonths(new Date(), 3),
                end_date: new Date(),
                type: "daily",
              })
            }
          >
            3ヵ月
          </V2Button>
          <V2Button
            variant="sub"
            onClick={() =>
              set({
                start_date: subMonths(new Date(), 12),
                end_date: new Date(),
                type: "monthly",
              })
            }
          >
            12ヵ月
          </V2Button>
          <V2Button
            variant="sub"
            onClick={() =>
              set({
                start_date: subYears(new Date(), 3),
                end_date: new Date(),
                type: "yearly",
              })
            }
          >
            3年
          </V2Button>
        </div>
      </div>
      <div>
        <FormCaption>表示条件</FormCaption>
        <div className="gap-2 flex flex-col sm:flex-row sm:items-end">
          <div className="flex flex-col">
            <label htmlFor="start-date">開始日</label>
            <V2Input
              id="start-date"
              type="date"
              value={format(start_date, "yyyy-MM-dd")}
              onChange={(e) => set({ start_date: new Date(e.target.value) })}
            />
          </div>
          <div className="flex flex-col">
            <label htmlFor="end-date">終了日</label>
            <V2Input
              id="end-date"
              type="date"
              value={format(end_date, "yyyy-MM-dd")}
              onChange={(e) => set({ end_date: new Date(e.target.value) })}
            />
          </div>
          <div className="flex flex-col">
            <label htmlFor="type">間隔</label>
            <V2Select
              id="type"
              value={type}
              onChange={(e) => set({ type: e.target.value as Analytics.Type })}
              options={TYPES}
            />
          </div>
          <div className="flex flex-col">
            <label htmlFor="mode">モード</label>
            <V2Select
              id="mode"
              value={mode}
              onChange={(e) => set({ mode: e.target.value as Analytics.Mode })}
              options={MODES}
            />
          </div>
        </div>
      </div>
      <div>
        <FormCaption>表示データ</FormCaption>
        <div className="gap-4 flex flex-col sm:flex-row">
          {AXES.map((axis) => (
            <V2Checkbox
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
            </V2Checkbox>
          ))}
        </div>
      </div>
    </>
  );
}
