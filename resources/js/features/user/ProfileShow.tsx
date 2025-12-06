import { Avatar } from "@/components/ui/Avatar";
import { ProfileLink } from "./ProfileLink";

type Props = {
  user: User.MypageEdit | User.Show;
  attachments?: Attachment.MypageEdit[];
  preview?: boolean;
};
export const ProfileShow = ({ user, attachments, preview }: Props) => {
  return (
    <div className="flex items-center gap-x-3">
      <Avatar
        attachmentId={user.profile.data.avatar}
        attachments={
          "attachments" in user.profile
            ? user.profile.attachments
            : (attachments ?? [])
        }
      />
      <div className="text-sm">
        <p className="font-semibold text-g9 break-all">
          <a href={preview ? "#" : `/users/${user.nickname ?? user.id}`}>
            {user.name ?? "(名前がありません)"}
          </a>
        </p>
        <p className="text-g5 break-all">{user.profile.data.description}</p>

        {user.profile.data.website.map((website) =>
          website ? (
            <ProfileLink
              key={website}
              url={website}
              preview={preview ?? false}
            />
          ) : null
        )}
      </div>
    </div>
  );
};
