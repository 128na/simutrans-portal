import { createRoot } from "react-dom/client";
import { useState } from "react";
import { AttachmentManage } from "../../features/attachments/AttachmentManage";
import { ErrorBoundary } from "../../components/ErrorBoundary";

const app = document.getElementById("app-attachment-edit");

if (app) {
  const App = () => {
    const [attachments, setAttachments] = useState<Attachment.MypageEdit[]>(
      JSON.parse(
        document.getElementById("data-attachments")?.textContent || "[]"
      )
    );

    return (
      <AttachmentManage
        attachments={attachments}
        onChangeAttachments={setAttachments}
      />
    );
  };

  createRoot(app).render(
    <ErrorBoundary name="AttachmentPage">
      <App />
    </ErrorBoundary>
  );
}
