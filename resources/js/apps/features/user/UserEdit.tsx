import { Upload } from "@/apps/components/form/Upload";
import { Image } from "@/apps/components/ui/Image";
import Label from "@/apps/components/ui/Label";
import { Modal } from "@/apps/components/ui/Modal";
import TextError from "@/apps/components/ui/TextError";
import TextSub from "@/apps/components/ui/TextSub";
import { useAxiosError } from "@/apps/state/useAxiosError";
import { AttachmentEdit } from "../attachments/AttachmentEdit";
import { Avatar } from "@/apps/components/ui/Avatar";
import Input from "@/apps/components/ui/Input";
import TextBadge from "@/apps/components/ui/TextBadge";
import Textarea from "@/apps/components/ui/Textarea";
import Button from "@/apps/components/ui/Button";
import ButtonClose from "@/apps/components/ui/ButtonClose";
import axios, { AxiosError } from "axios";

type Props = {
  user: User.ForEdit;
  onChangeUser: (user: User.ForEdit) => void;
  attachments: Attachment[];
  onChangeAttachments: (attachments: Attachment[]) => void;
};
export const UserEdit = ({
  user,
  onChangeUser,
  attachments,
  onChangeAttachments,
}: Props) => {
  const { getError, setError } = useAxiosError();

  const addWebsite = () => {
    if (user.profile.data.website.length >= 10) {
      alert("最大10個までです");
      return;
    }
    onChangeUser({
      ...user,
      profile: {
        ...user.profile,
        data: {
          ...user.profile.data,
          website: [...user.profile.data.website, ""],
        },
      },
    });
  };

  const save = async () => {
    try {
      const res = await axios.post("/api/v2/profile", {
        user,
      });
      console.log(res);
      if (res.status === 200) {
        window.location.href = `/mypage/profile?updated=1`;
      }
    } catch (error) {
      console.log(error);
      if (error instanceof AxiosError) {
        setError(error.response?.data);
      }
    }
  };

  return (
    <div className="grid gap-4">
      <Input
        labelClassName="font-medium"
        className="font-normal"
        value={user.name}
        onChange={(e) => onChangeUser({ ...user, name: e.target.value })}
      >
        <TextBadge color="red">必須</TextBadge>
        表示名
        <TextError className="mb-2">{getError("user.name")}</TextError>
      </Input>
      <Input
        labelClassName="font-medium"
        type="email"
        className="font-normal"
        value={user.email}
        onChange={(e) => onChangeUser({ ...user, email: e.target.value })}
      >
        <TextBadge color="red">必須</TextBadge>
        メールアドレス
        <TextError className="mb-2">{getError("user.email")}</TextError>
      </Input>
      <Input
        labelClassName="font-medium"
        className="font-normal"
        value={user.nickname || ""}
        onChange={(e) => onChangeUser({ ...user, nickname: e.target.value })}
      >
        ニックネーム
        <TextError className="mb-2">{getError("user.nickname")}</TextError>
      </Input>
      <div>
        <Label className="font-medium">
          アバター画像
          <TextError className="mb-2">
            {getError("user.profile.data.avatar")}
          </TextError>
          <Avatar
            attachmentId={user.profile.data.avatar}
            attachments={attachments}
          />
        </Label>
        <TextSub>
          {(user.profile.data.avatar &&
            attachments.find((a) => a.id === user.profile.data.avatar)
              ?.original_name) ??
            "未選択"}
        </TextSub>
        <div className="space-x-2">
          <Upload
            accept="image/*"
            onUploaded={(a) => {
              onChangeUser({
                ...user,
                profile: {
                  ...user.profile,
                  data: {
                    ...user.profile.data,
                    avatar: a.id,
                  },
                },
              });
              onChangeAttachments([a, ...attachments]);
            }}
          >
            画像をアップロード
          </Upload>
          <Modal
            buttonTitle="アップロード済みの画像から選択"
            title="画像を選択"
          >
            {({ close }) => (
              <AttachmentEdit
                attachments={attachments}
                attachmentableId={user.profile.id}
                selected={user.profile.data.avatar}
                types={["image"]}
                onSelectAttachment={(attachmentId) => {
                  onChangeUser({
                    ...user,
                    profile: {
                      ...user.profile,
                      data: {
                        ...user.profile.data,
                        avatar: attachmentId,
                      },
                    },
                  });
                  close();
                }}
                onChangeAttachments={onChangeAttachments}
              />
            )}
          </Modal>
        </div>
      </div>
      <Textarea
        labelClassName="font-medium"
        className="font-normal"
        value={user.profile.data.description || ""}
        rows={2}
        onChange={(e) => {
          onChangeUser({
            ...user,
            profile: {
              ...user.profile,
              data: {
                ...user.profile.data,
                description: e.target.value,
              },
            },
          });
        }}
      >
        <TextBadge color="red">必須</TextBadge>
        説明
        <TextError className="mb-2">
          {getError("user.profile.data.description")}
        </TextError>
      </Textarea>
      <div>
        <Label>
          <div className="font-medium">Webサイト</div>
        </Label>
        <TextSub>SNSなども登録できます。</TextSub>
        <div>
          <Button onClick={addWebsite}>Webサイトを追加</Button>
        </div>
        {user.profile.data.website.map((website, idx) => {
          return (
            <Input
              key={idx}
              type="url"
              labelClassName="font-medium"
              className="font-normal"
              value={website}
              onChange={(e) => {
                onChangeUser({
                  ...user,
                  profile: {
                    ...user.profile,
                    data: {
                      ...user.profile.data,
                      website: user.profile.data.website.map((w, i) =>
                        i === idx ? e.target.value : w,
                      ),
                    },
                  },
                });
              }}
            >
              Webサイト {idx + 1}
              <TextError className="mb-2">
                {getError(`user.profile.data.website.${idx}`)}
              </TextError>
            </Input>
          );
        })}
      </div>

      <div>
        <Button onClick={save}>保存</Button>
      </div>
    </div>
  );
};
