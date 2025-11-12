import Input from "@/apps/components/ui/Input";
import Select from "@/apps/components/ui/Select";
import { useAnalyticsStore } from "@/apps/state/useAnalyticsStore";
import { format } from "date-fns";

const TYPES: Record<Analytics.Type, string> = {
  daily: "日次",
  monthly: "月次",
  yearly: "年次",
};
const MODES: Record<Analytics.Mode, string> = {
  line: "推移",
  sum: "合計",
};

export function AnalyticsOption() {
  const { start_date, end_date, type, mode, set } = useAnalyticsStore();

  return (
    <div className="gap-2 flex flex-column">
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
      <Select
        value={mode}
        onChange={(e) => set({ mode: e.target.value as Analytics.Mode })}
        options={MODES}
      >
        集計方式
      </Select>
    </div>
  );
}
