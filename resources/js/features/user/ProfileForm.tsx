import { Upload } from "@/components/form/Upload";
import { ModalFull } from "@/components/ui/ModalFull";
import TextError from "@/components/ui/TextError";
import TextSub from "@/components/ui/TextSub";
import { useAxiosError } from "@/hooks/useAxiosError";
import { AttachmentEdit } from "@/features/attachments/AttachmentEdit";
import { Avatar } from "@/components/ui/Avatar";
import TextBadge from "@/components/ui/TextBadge";
import axios from "axios";
import { useRef } from "react";
import { isValidationError } from "@/lib/errorHandler";
import { useErrorHandler } from "@/hooks/useErrorHandler";
import { FormCaption } from "@/components/ui/FormCaption";
import MultiColumn from "@/components/ui/MultiColumn";
import { ProfileIcon } from "./ProfileIcon";
import { getService } from "./profileUtil";
import { SortableList } from "@/components/ui/SortableList";
import V2Input from "@/components/ui/v2/V2Input";
import V2Textarea from "@/components/ui/v2/V2Textarea";
import V2Button from "@/components/ui/v2/V2Button";

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
  const { handleErrorWithContext } = useErrorHandler({
    component: "ProfileForm",
  });

  const addWebsite = () => {
    if (user.profile.data.website.length >= 10) {
      window.alert("最大10個までです");
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
      if (isValidationError(error)) {
        setError(error.response.data);
        containerRef.current?.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      } else {
        handleErrorWithContext(error, { action: "save" });
      }
    }
  };

  return (
    <div ref={containerRef} className="grid gap-4">
      <div>
        <FormCaption>
          <TextBadge className="bg-c-danger">必須</TextBadge>
          表示名
        </FormCaption>
        <TextError>{getError("user.name")}</TextError>
        <V2Input
          className="w-full"
          value={user.name}
          onChange={(e) => onChangeUser({ ...user, name: e.target.value })}
          required
          maxLength={255}
        />
      </div>

      <div>
        <FormCaption>
          <TextBadge className="bg-c-danger">必須</TextBadge>
          メールアドレス
        </FormCaption>
        <TextError>{getError("user.email")}</TextError>
        <V2Input
          type="email"
          className="w-full"
          value={user.email}
          onChange={(e) => onChangeUser({ ...user, email: e.target.value })}
          required
          maxLength={255}
        />
      </div>

      <div>
        <FormCaption>ニックネーム</FormCaption>
        <TextError>{getError("user.nickname")}</TextError>
        <V2Input
          className="w-full"
          value={user.nickname || ""}
          onChange={(e) => onChangeUser({ ...user, nickname: e.target.value })}
          maxLength={20}
        />
        <TextSub>
          設定すると記事URLがユーザーIDの代わりに使用されます。 例：
          /users/my-nickname/my-article
        </TextSub>
      </div>

      <div>
        <FormCaption>アバター画像</FormCaption>
        <TextError>{getError("user.profile.data.avatar")}</TextError>
        <Avatar
          attachmentId={user.profile.data.avatar}
          attachments={attachments}
        />
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
          />
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

      <div>
        <FormCaption>説明</FormCaption>
        <TextError>{getError("user.profile.data.description")}</TextError>
        <V2Textarea
          className="w-full"
          value={user.profile.data.description || ""}
          rows={4}
          maxLength={1024}
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
        />
      </div>

      <div>
        <FormCaption>Webサイト</FormCaption>
        <TextSub className="mb-2">SNSなども登録できます。</TextSub>
        <div className="mb-2">
          <V2Button variant="sub" onClick={addWebsite}>
            Webサイトを追加
          </V2Button>
        </div>
        <SortableList
          items={user.profile.data.website}
          onReorder={(newWebsites) => {
            onChangeUser({
              ...user,
              profile: {
                ...user.profile,
                data: {
                  ...user.profile.data,
                  website: newWebsites,
                },
              },
            });
          }}
          getItemId={(_, idx) => `website-${idx}`}
          renderItem={(website, idx) => {
            const service = getService(website);
            return (
              <MultiColumn
                classNames={["grow", "shrink-0"]}
                className="space-y-0"
              >
                <div className="flex items-center flex-1">
                  {website && service ? (
                    <ProfileIcon
                      service={service}
                      className="mr-2 self-start mt-3"
                    />
                  ) : null}
                  <div className="flex-1">
                    <TextError>
                      {getError(`user.profile.data.website.${idx}`)}
                    </TextError>
                    <V2Input
                      type="url"
                      className="w-full"
                      maxLength={255}
                      value={website}
                      onChange={(e) => {
                        onChangeUser({
                          ...user,
                          profile: {
                            ...user.profile,
                            data: {
                              ...user.profile.data,
                              website: user.profile.data.website.map((w, i) =>
                                i === idx ? e.target.value : w
                              ),
                            },
                          },
                        });
                      }}
                    />
                  </div>
                </div>
                <V2Button
                  variant="dangerOutline"
                  size="sm"
                  onClick={() => removeWebsite(idx)}
                >
                  削除
                </V2Button>
              </MultiColumn>
            );
          }}
        />
      </div>
      <div className="border-t border-c-sub/10 pt-4">
        <V2Button size="lg" onClick={save}>
          保存
        </V2Button>
      </div>
    </div>
  );
};
