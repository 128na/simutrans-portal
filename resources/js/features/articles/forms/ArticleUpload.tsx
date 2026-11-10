import type { ComponentProps } from "react";
import { Upload } from "@/components/form/Upload";
import { useArticleEditor } from "@/hooks/useArticleEditor";

type Props = Omit<ComponentProps<typeof Upload>, "onUploadingChange">;

export const ArticleUpload = (props: Props) => (
  <Upload
    {...props}
    onUploadingChange={(uploading) =>
      useArticleEditor.setState((state) => {
        state.uploadsInProgress = Math.max(
          0,
          state.uploadsInProgress + (uploading ? 1 : -1)
        );
      })
    }
  />
);
