namespace AttachmentEdit {
  type Attachment = SearchableOption & {
    id: number;
    user_id: number;
    attachmentable_id: number;
    attachmentable_type: AttachmentableType;
    attachmentable: {
      id: number;
      title: string;
    } | null;
    type: Type;
    original_name: string;
    thumbnail: string;
    url: string;
    size: number;
    file_info?: FileInfo;
    caption?: string;
    order?: number;
    created_at: string;
  };
  type AttachmentableType = "Article" | "Profile";
  type Type = "image" | "video" | "text" | "file";
  type FileInfo = {
    id: number;
    attachment_id: number;
    data: {
      readmes?: {
        [string]: string[];
      };
      paks?: {
        [string]: string[];
      };
      dats?: {
        [string]: string[];
      };
      tabs?: {
        [string]: { [string]: string };
      };
    };
    created_at: string;
    updated_at: string;
  };
}
