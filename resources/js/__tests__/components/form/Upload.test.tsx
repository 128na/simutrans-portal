import { render, screen, waitFor } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi, beforeEach } from "vitest";
import axios from "axios";
import { Upload } from "@/components/form/Upload";

vi.mock("axios");
const mockedAxios = vi.mocked(axios);

describe("Upload", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("アップロードボタンが表示される", () => {
    render(<Upload />);
    const button = screen.getByRole("button", { name: /アップロード/i });
    expect(button).toBeInTheDocument();
  });

  it("ファイルアップロード成功時にonUploadedが呼ばれる", async () => {
    const user = userEvent.setup();
    const mockOnUploaded = vi.fn();
    const mockResponse = {
      data: {
        data: {
          id: 1,
          file: "test.jpg",
          type: "image",
          caption: "",
          size: 1024,
        },
      },
    };

    vi.mocked(mockedAxios.post).mockResolvedValueOnce(mockResponse);

    const { container } = render(<Upload onUploaded={mockOnUploaded} />);

    const file = new File(["test"], "test.jpg", { type: "image/jpeg" });
    const input = container.querySelector(
      'input[type="file"]'
    ) as HTMLInputElement;

    await user.upload(input, file);

    await waitFor(() => {
      expect(mockOnUploaded).toHaveBeenCalledWith(mockResponse.data.data);
    });
  });

  it("ファイルが選択されない場合は何もしない", async () => {
    const user = userEvent.setup();
    const mockOnUploaded = vi.fn();

    const { container } = render(<Upload onUploaded={mockOnUploaded} />);

    const input = container.querySelector(
      'input[type="file"]'
    ) as HTMLInputElement;
    await user.click(input);

    expect(mockedAxios.post).not.toHaveBeenCalled();
    expect(mockOnUploaded).not.toHaveBeenCalled();
  });

  it("アップロードエラー時にエラーハンドリングされる", async () => {
    const user = userEvent.setup();
    const mockError = new Error("Upload failed");

    vi.mocked(mockedAxios.post).mockRejectedValueOnce(mockError);

    const { container } = render(<Upload />);

    const file = new File(["test"], "test.jpg", { type: "image/jpeg" });
    const input = container.querySelector(
      'input[type="file"]'
    ) as HTMLInputElement;

    await user.upload(input, file);

    await waitFor(() => {
      expect(mockedAxios.post).toHaveBeenCalled();
    });
  });

  it("multipleがfalseに設定される", () => {
    const { container } = render(<Upload />);
    const input = container.querySelector('input[type="file"]');
    expect(input).not.toHaveAttribute("multiple");
  });

  it("カスタムプロパティが渡される", () => {
    const { container } = render(<Upload accept="image/*" disabled />);
    const input = container.querySelector('input[type="file"]');
    expect(input).toHaveAttribute("accept", "image/*");
    expect(input).toBeDisabled();
  });

  it("FormDataが正しく構築される", async () => {
    const user = userEvent.setup();
    const mockOnUploaded = vi.fn();
    const mockResponse = {
      data: {
        data: {
          id: 1,
          file: "test.jpg",
          type: "image",
          caption: "",
          size: 1024,
        },
      },
    };

    vi.mocked(mockedAxios.post).mockResolvedValueOnce(mockResponse);

    const { container } = render(<Upload onUploaded={mockOnUploaded} />);

    const file = new File(["test"], "test.jpg", { type: "image/jpeg" });
    const input = container.querySelector(
      'input[type="file"]'
    ) as HTMLInputElement;

    await user.upload(input, file);

    await waitFor(() => {
      expect(mockedAxios.post).toHaveBeenCalled();
      const callArgs = vi.mocked(mockedAxios.post).mock.calls[0];
      const formData = callArgs[1] as FormData;
      expect(formData.get("file")).toBe(file);
    });
  });
});
