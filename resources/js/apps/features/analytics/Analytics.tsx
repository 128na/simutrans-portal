import { useState } from "react";
import { AnalyticsTable } from "./AnalyticsTable";

type Props = {
  articles: Article.Analytics[];
};

export const Analytics = ({ articles }: Props) => {
  const [selected, setSelected] = useState<number[]>([]);

  const props = {
    articles,
    selected,
    onChangeSelected: setSelected,
    limit: 15,
  };

  return <AnalyticsTable {...props} />;
};
