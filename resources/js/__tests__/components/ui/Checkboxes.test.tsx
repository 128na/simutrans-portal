import Checkboxes from "@/components/ui/Checkboxes";
import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";

describe("Checkboxes コンポーネント", () => {
  const mockOptions = {
    option1: "オプション1",
    option2: "オプション2",
    option3: "オプション3",
  };

  it("複数のチェックボックスが表示される", () => {
    render(<Checkboxes options={mockOptions} />);

    expect(screen.getByLabelText("オプション1")).toBeInTheDocument();
    expect(screen.getByLabelText("オプション2")).toBeInTheDocument();
    expect(screen.getByLabelText("オプション3")).toBeInTheDocument();
  });

  it("checkedOptions で指定した項目がチェックされる", () => {
    render(
      <Checkboxes
        options={mockOptions}
        checkedOptions={["option1", "option3"]}
        readOnly
      />
    );

    expect(screen.getByLabelText("オプション1")).toBeChecked();
    expect(screen.getByLabelText("オプション2")).not.toBeChecked();
    expect(screen.getByLabelText("オプション3")).toBeChecked();
  });

  it("onChange イベントが各チェックボックスで発火する", async () => {
    const user = userEvent.setup();
    const handleChange = vi.fn();
    render(<Checkboxes options={mockOptions} onChange={handleChange} />);

    await user.click(screen.getByLabelText("オプション1"));
    expect(handleChange).toHaveBeenCalled();
  });

  it("disabled 状態で全チェックボックスが無効になる", () => {
    render(<Checkboxes options={mockOptions} disabled />);

    expect(screen.getByLabelText("オプション1")).toBeDisabled();
    expect(screen.getByLabelText("オプション2")).toBeDisabled();
    expect(screen.getByLabelText("オプション3")).toBeDisabled();
  });

  it("name 属性が全チェックボックスに設定される", () => {
    render(<Checkboxes options={mockOptions} name="test-checkboxes" />);

    const checkboxes = screen.getAllByRole("checkbox");
    checkboxes.forEach((checkbox) => {
      expect(checkbox).toHaveAttribute("name", "test-checkboxes");
    });
  });

  it("各チェックボックスに正しい value が設定される", () => {
    render(<Checkboxes options={mockOptions} />);

    expect(screen.getByLabelText("オプション1")).toHaveAttribute(
      "value",
      "option1"
    );
    expect(screen.getByLabelText("オプション2")).toHaveAttribute(
      "value",
      "option2"
    );
    expect(screen.getByLabelText("オプション3")).toHaveAttribute(
      "value",
      "option3"
    );
  });

  it("空のオプションでもエラーにならない", () => {
    const { container } = render(<Checkboxes options={{}} />);
    expect(container.querySelector(".v2-checkboxes")).toBeInTheDocument();
    expect(screen.queryAllByRole("checkbox")).toHaveLength(0);
  });

  it("デフォルトのスタイルが適用される", () => {
    const { container } = render(<Checkboxes options={mockOptions} />);
    expect(container.querySelector(".v2-checkboxes")).toBeInTheDocument();
  });

  it("checkedOptions が未指定の場合、全て未チェック", () => {
    render(<Checkboxes options={mockOptions} />);

    const checkboxes = screen.getAllByRole("checkbox");
    checkboxes.forEach((checkbox) => {
      expect(checkbox).not.toBeChecked();
    });
  });

  it("1つのオプションのみでも表示できる", () => {
    render(<Checkboxes options={{ single: "単一オプション" }} />);
    expect(screen.getByLabelText("単一オプション")).toBeInTheDocument();
  });
});
