import { Upload } from "@/components/form/Upload";
import Label from "@/components/ui/Label";
import { ModalFull } from "@/components/ui/ModalFull";
import TextError from "@/components/ui/TextError";
import TextSub from "@/components/ui/TextSub";
import { useAxiosError } from "@/hooks/useAxiosError";
import { AttachmentEdit } from "@/features/attachments/AttachmentEdit";
import { Avatar } from "@/components/ui/Avatar";
import Input from "@/components/ui/Input";
import TextBadge from "@/components/ui/TextBadge";
import Textarea from "@/components/ui/Textarea";
import Button from "@/components/ui/Button";
import axios, { AxiosError } from "axios";
import ButtonSub from "@/components/ui/ButtonSub";
import TwoColumn from "@/components/ui/TwoColumn";
import ButtonDanger from "@/components/ui/ButtonDanger";
import { useRef } from "react";

type Props = {
  user: User.MypageEdit;
  onChangeUser: (user: User.MypageEdit) => void;
  attachments: Attachment.MypageEdit[];
  onChangeAttachments: (attachments: Attachment.MypageEdit[]) => void;
};
export const ProfileForm = ({
  user,
  onChangeUser,
  attachments,
  onChangeAttachments,
}: Props) => {
  const containerRef = useRef<HTMLDivElement>(null);
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

  const removeWebsite = (index: number) => {
    if (!window.confirm("削除しますか？")) {
      return;
    }
    onChangeUser({
      ...user,
      profile: {
        ...user.profile,
        data: {
          ...user.profile.data,
          website: [...user.profile.data.website.filter((_, i) => i !== index)],
        },
      },
    });
  };

  const save = async () => {
    try {
      const res = await axios.post("/api/v2/profile", {
        user,
      });
      if (res.status === 200) {
        window.location.href = `/mypage/profile?updated=1`;
      }
    } catch (error) {
      console.log(error);
      if (error instanceof AxiosError) {
        setError(error.response?.data);
        containerRef.current?.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    }
  };

  return (
    <div ref={containerRef} className="grid gap-4">
      <Input
        labelClassName="font-medium"
        className="font-normal"
        value={user.name}
        onChange={(e) => onChangeUser({ ...user, name: e.target.value })}
      >
        <TextBadge className="bg-red-500">必須</TextBadge>
        表示名
        <TextError>{getError("user.name")}</TextError>
      </Input>

      <Input
        labelClassName="font-medium"
        type="email"
        className="font-normal"
        value={user.email}
        onChange={(e) => onChangeUser({ ...user, email: e.target.value })}
      >
        <TextBadge className="bg-red-500">必須</TextBadge>
        メールアドレス
        <TextError>{getError("user.email")}</TextError>
      </Input>

      <div>
        <Input
          labelClassName="font-medium"
          className="font-normal"
          value={user.nickname || ""}
          onChange={(e) => onChangeUser({ ...user, nickname: e.target.value })}
        >
          ニックネーム
          <TextError>{getError("user.nickname")}</TextError>
        </Input>
        <TextSub>
          設定すると記事URLがユーザーIDの代わりに使用されます。 例：
          /users/my-nickname/my-article
        </TextSub>
      </div>
      <div>
        <Label className="font-medium">
          アバター画像
          <TextError>{getError("user.profile.data.avatar")}</TextError>
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
          <ModalFull
            buttonTitle="アップロード済みの画像から選択"
            title="画像を選択"
          >
            {({ close }) => (
              <AttachmentEdit
                attachments={attachments}
                attachmentableId={user.profile.id}
                attachmentableType="Profile"
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
          </ModalFull>
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
        説明
        <TextError>{getError("user.profile.data.description")}</TextError>
      </Textarea>
      <div>
        <Label>
          <div className="font-medium">Webサイト</div>
        </Label>
        <TextSub>SNSなども登録できます。</TextSub>
        <div>
          <ButtonSub onClick={addWebsite}>Webサイトを追加</ButtonSub>
        </div>
        {user.profile.data.website.map((website, idx) => {
          return (
            <TwoColumn grow="left" key={idx}>
              <Input
                type="url"
                labelClassName="font-medium mb-0"
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
                <TextError>
                  {getError(`user.profile.data.website.${idx}`)}
                </TextError>
              </Input>
              <ButtonDanger onClick={() => removeWebsite(idx)}>
                削除
              </ButtonDanger>
            </TwoColumn>
          );
        })}
      </div>
      <div className="border-t border-gray-200 pt-4">
        <Button onClick={save}>保存</Button>
      </div>
    </div>
  );
};
