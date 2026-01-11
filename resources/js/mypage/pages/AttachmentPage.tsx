import { createRoot } from "react-dom/client";
import { useState } from "react";
import { AttachmentManage } from "../../features/attachments/AttachmentManage";
import { AppWrapper } from "../../components/AppWrapper";

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
    <AppWrapper boundaryName="AttachmentPage">
      <App />
    </AppWrapper>
  );
}
