import { createRoot } from "react-dom/client";
import { useState } from "react";
import { ProfileEdit } from "./features/user/ProfileEdit";
import { ProfileShow } from "./features/frontend/ProfileShow";
import TextBadge from "./components/ui/TextBadge";
import TextSub from "./components/ui/TextSub";

const app = document.getElementById("app-profile-edit");

if (app) {
  const App = () => {
    const [user, setUser] = useState<User.MypageEdit>(
      JSON.parse(document.getElementById("data-user")?.textContent || "{}"),
    );
    const [attachments, setAttachments] = useState<Attachment.MypageEdit[]>(
      JSON.parse(
        document.getElementById("data-attachments")?.textContent || "[]",
      ),
    );

    const props = {
      user,
      onChangeUser: setUser,
      attachments,
      onChangeAttachments: setAttachments,
    };

    return (
      <>
        <div>
          <TextBadge className="bg-yellow-500">プレビュー表示</TextBadge>
          <TextSub>
            プレビュー表示ではリンクが反応しないようになっています。
          </TextSub>
          <div className="mt-2 mb-8">
            <ProfileShow user={user} attachments={attachments} preview={true} />
          </div>
        </div>
        <ProfileEdit {...props} />
      </>
    );
  };

  createRoot(app).render(<App />);
}
