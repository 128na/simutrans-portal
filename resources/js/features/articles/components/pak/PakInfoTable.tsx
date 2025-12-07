export type TableRow = {
  label: string;
  value: string | number | null | undefined;
};

type Props = {
  rows: TableRow[];
};

export const PakInfoTable = ({ rows }: Props) => {
  return (
    <div>
      <div className="overflow-x-auto">
        <table className="border-collapse whitespace-nowrap">
          <tbody>
            {rows.map((row, index) => (
              <tr key={index}>
                <td className="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">
                  {row.label}
                </td>
                <td className="border border-c-sub/10 px-4 py-2">
                  {row.value !== undefined &&
                  row.value !== null &&
                  row.value !== ""
                    ? row.value
                    : "(none)"}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};
