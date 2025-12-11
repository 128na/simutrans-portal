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
      <div className="v2-table-wrapper">
        <table className="v2-table">
          <tbody>
            {rows.map((row, index) => (
              <tr key={index}>
                <th>{row.label}</th>
                <td>
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
