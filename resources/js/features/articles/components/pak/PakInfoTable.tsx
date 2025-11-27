export type TableRow = {
  label: string;
  value: string | number | null | undefined;
};

type Props = {
  title: string;
  rows: TableRow[];
};

export const PakInfoTable = ({ title, rows }: Props) => {
  return (
    <div>
      <h5 className="font-semibold text-gray-700 mb-2">{title}</h5>
      <div className="overflow-x-auto">
        <table className="border-collapse whitespace-nowrap">
          <tbody>
            {rows.map((row, index) => (
              <tr key={index}>
                <td className="border border-gray-300 px-4 py-2 bg-gray-500 text-white">
                  {row.label}
                </td>
                <td className="border border-gray-300 px-4 py-2">
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
