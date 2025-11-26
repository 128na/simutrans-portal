import { useState } from "react";
import { ProfileEdit } from "../../features/user/ProfileEdit";
import { createRoot } from "react-dom/client";
import { ErrorBoundary } from "../../components/ErrorBoundary";

const app = document.getElementById("app-profile-edit");

if (app) {
  const App = () => {
    const [user, setUser] = useState<User.MypageEdit>(
      JSON.parse(document.getElementById("data-user")?.textContent || "{}")
    );
    const [attachments, setAttachments] = useState<Attachment.MypageEdit[]>(
      JSON.parse(
        document.getElementById("data-attachments")?.textContent || "[]"
      )
    );

    const props = {
      user,
      onChangeUser: setUser,
      attachments,
      onChangeAttachments: setAttachments,
    };

    return <ProfileEdit {...props} />;
  };

  createRoot(app).render(
    <ErrorBoundary name="ProfileEditPage">
      <App />
    </ErrorBoundary>
  );
}
