import { createRoot } from "react-dom/client";
import { useState } from "react";
import { AttachmentManage } from "./features/attachments/AttachmentManage";

const app = document.getElementById("app-attachment-edit");

if (app) {
  const App = () => {
    const [attachments, setAttachments] = useState<Attachment[]>(
      JSON.parse(
        document.getElementById("data-attachments")?.textContent || "[]",
      ),
    );

    return (
      <AttachmentManage
        attachments={attachments}
        onChangeAttachments={setAttachments}
      />
    );
  };

  createRoot(app).render(<App />);
}
