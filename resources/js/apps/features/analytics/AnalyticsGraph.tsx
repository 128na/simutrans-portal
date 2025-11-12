import Button from "@/apps/components/ui/Button";
import { useAnalyticsStore } from "@/apps/state/useAnalyticsStore";

export function AnalyticsGraph() {
  const { selected, start_date, end_date, type, mode } = useAnalyticsStore();

  return (
    <div>
      <div>ここにグラフ</div>
      <Button>グラフを表示</Button>
    </div>
  );
}
