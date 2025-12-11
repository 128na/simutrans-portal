import V2TextBadge from "@/components/ui/v2/V2TextBadge";
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
        <V2TextBadge variant="warn">プレビュー表示</V2TextBadge>
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
