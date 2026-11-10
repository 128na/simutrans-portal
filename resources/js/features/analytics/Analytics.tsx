import { AnalyticsTable } from "./AnalyticsTable";
import { AnalyticsOption } from "./AnalyticsOption";
import { AnalyticsGraph } from "./AnalyticsGraph";

type Props = {
  articles: Analytics.Article[];
};

export const Analytics = ({ articles }: Props) => {
  return (
    <div className="grid gap-4">
      <AnalyticsOption />
      <AnalyticsTable articles={articles} limit={15} />
      <AnalyticsGraph articles={articles} />
    </div>
  );
};
