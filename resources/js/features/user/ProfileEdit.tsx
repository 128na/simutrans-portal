import TextBadge from "@/components/ui/TextBadge";
import TextSub from "@/components/ui/TextSub";
import { ProfileShow } from "./ProfileShow";
import { ProfileForm } from "./ProfileForm";

type Props = {
  user: User.MypageEdit;
  onChangeUser: (user: User.MypageEdit) => void;
  attachments: Attachment.MypageEdit[];
  onChangeAttachments: (attachments: Attachment.MypageEdit[]) => void;
};
export const ProfileEdit = ({
  user,
  onChangeUser,
  attachments,
  onChangeAttachments,
}: Props) => {
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

      <ProfileForm
        user={user}
        onChangeUser={onChangeUser}
        attachments={attachments}
        onChangeAttachments={onChangeAttachments}
      />
    </>
  );
};
