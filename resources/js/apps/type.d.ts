type Tag = {
  id: number;
  name: string;
  description: string | null;
  editable: boolean;
  created_by: SimpleUser | null;
  last_modified_by: SimpleUser | null;
  last_modified_at: string | null;
  created_at: string;
  updated_at: string;
  articles_count: number;
};
type NewTag = {
  id: null;
  name: null | string;
  description: null | string;
};

type SimpleUser = {
  id: number;
  name: string;
};
