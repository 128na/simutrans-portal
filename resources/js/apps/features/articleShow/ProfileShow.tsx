import { Avatar } from "@/apps/components/ui/Avatar";
import { ProfileLink } from "./ProfileLink";

type Props = {
  user: ArticleShow.User;
};
export const ProfileShow = ({ user }: Props) => {
  return (
    <div className="flex items-center gap-x-3">
      <Avatar
        attachmentId={user.profile.data.avatar}
        attachments={user.profile.attachments}
      />
      <div className="text-sm">
        <p className="font-semibold text-gray-900 break-all">
          <a href={`/users/${user.nickname ?? user.id}`}>{user.name}</a>
        </p>
        <p className="text-gray-600 break-all">
          {user.profile.data.description}
        </p>

        {user.profile.data.website.map((website) =>
          website ? <ProfileLink key={website} url={website} /> : null,
        )}
      </div>
    </div>
  );
};
