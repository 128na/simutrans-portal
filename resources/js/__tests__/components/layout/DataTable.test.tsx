import { DataTable, DataTableHeader } from "@/components/layout/DataTable";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";

type TestData = {
  id: number;
  name: string;
  age: number;
};

describe("DataTable コンポーネント", () => {
  const mockHeaders: DataTableHeader<"id" | "name" | "age">[] = [
    { key: "id", name: "ID", width: "w-20", sortable: true },
    { key: "name", name: "名前", sortable: true },
    { key: "age", name: "年齢", sortable: false },
  ];

  const mockData: TestData[] = [
    { id: 1, name: "太郎", age: 25 },
    { id: 2, name: "花子", age: 30 },
    { id: 3, name: "次郎", age: 22 },
  ];

  const mockRenderRow = (item: TestData) => (
    <tr key={item.id}>
      <td>{item.id}</td>
      <td>{item.name}</td>
      <td>{item.age}</td>
    </tr>
  );

  it("テーブルが表示される", () => {
    render(
      <DataTable
        headers={mockHeaders}
        data={mockData}
        sort={{ column: "id", order: "asc" }}
        limit={10}
        current={1}
        onSort={() => {}}
        renderRow={mockRenderRow}
      />
    );
    expect(screen.getByRole("table")).toBeInTheDocument();
  });

  it("ヘッダーが表示される", () => {
    render(
      <DataTable
        headers={mockHeaders}
        data={mockData}
        sort={{ column: "id", order: "asc" }}
        limit={10}
        current={1}
        onSort={() => {}}
        renderRow={mockRenderRow}
      />
    );
    expect(screen.getByText("ID")).toBeInTheDocument();
    expect(screen.getByText("名前")).toBeInTheDocument();
    expect(screen.getByText("年齢")).toBeInTheDocument();
  });

  it("データ行が表示される", () => {
    render(
      <DataTable
        headers={mockHeaders}
        data={mockData}
        sort={{ column: "id", order: "asc" }}
        limit={10}
        current={1}
        onSort={() => {}}
        renderRow={mockRenderRow}
      />
    );
    expect(screen.getByText("太郎")).toBeInTheDocument();
    expect(screen.getByText("花子")).toBeInTheDocument();
    expect(screen.getByText("次郎")).toBeInTheDocument();
  });

  it("ソート可能なヘッダーをクリックすると onSort が呼ばれる", async () => {
    const user = userEvent.setup();
    const onSort = vi.fn();
    render(
      <DataTable
        headers={mockHeaders}
        data={mockData}
        sort={{ column: "id", order: "asc" }}
        limit={10}
        current={1}
        onSort={onSort}
        renderRow={mockRenderRow}
      />
    );
    const nameHeader = screen.getByText("名前");
    await user.click(nameHeader);
    expect(onSort).toHaveBeenCalledWith("name");
  });

  it("ソート不可なヘッダーをクリックしても onSort が呼ばれない", async () => {
    const user = userEvent.setup();
    const onSort = vi.fn();
    render(
      <DataTable
        headers={mockHeaders}
        data={mockData}
        sort={{ column: "id", order: "asc" }}
        limit={10}
        current={1}
        onSort={onSort}
        renderRow={mockRenderRow}
      />
    );
    const ageHeader = screen.getByText("年齢");
    await user.click(ageHeader);
    expect(onSort).not.toHaveBeenCalled();
  });

  it("昇順ソートのインジケーターが表示される", () => {
    render(
      <DataTable
        headers={mockHeaders}
        data={mockData}
        sort={{ column: "id", order: "asc" }}
        limit={10}
        current={1}
        onSort={() => {}}
        renderRow={mockRenderRow}
      />
    );
    expect(screen.getByText("▲")).toBeInTheDocument();
  });

  it("降順ソートのインジケーターが表示される", () => {
    render(
      <DataTable
        headers={mockHeaders}
        data={mockData}
        sort={{ column: "id", order: "desc" }}
        limit={10}
        current={1}
        onSort={() => {}}
        renderRow={mockRenderRow}
      />
    );
    expect(screen.getByText("▼")).toBeInTheDocument();
  });

  it("ページネーションで現在のページのデータのみ表示される", () => {
    const manyData = Array.from({ length: 25 }, (_, i) => ({
      id: i + 1,
      name: `名前${i + 1}`,
      age: 20 + i,
    }));

    render(
      <DataTable
        headers={mockHeaders}
        data={manyData}
        sort={{ column: "id", order: "asc" }}
        limit={10}
        current={2}
        onSort={() => {}}
        renderRow={mockRenderRow}
      />
    );

    // 2ページ目は11-20のデータ
    expect(screen.getByText("名前11")).toBeInTheDocument();
    expect(screen.getByText("名前20")).toBeInTheDocument();
    expect(screen.queryByText("名前10")).not.toBeInTheDocument();
    expect(screen.queryByText("名前21")).not.toBeInTheDocument();
  });

  it("カスタム幅が適用される", () => {
    render(
      <DataTable
        headers={mockHeaders}
        data={mockData}
        sort={{ column: "id", order: "asc" }}
        limit={10}
        current={1}
        onSort={() => {}}
        renderRow={mockRenderRow}
      />
    );
    const idHeader = screen.getByText("ID").closest("th");
    expect(idHeader).toHaveClass("w-20");
  });

  it("ソート可能なヘッダーにカーソルポインターが適用される", () => {
    render(
      <DataTable
        headers={mockHeaders}
        data={mockData}
        sort={{ column: "id", order: "asc" }}
        limit={10}
        current={1}
        onSort={() => {}}
        renderRow={mockRenderRow}
      />
    );
    const nameHeader = screen.getByText("名前").closest("th");
    expect(nameHeader).toHaveClass("cursor-pointer");
  });
});
