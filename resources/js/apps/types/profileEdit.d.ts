namespace ProfileEdit {
  type User = {
    id: number;
    name: string;
    email: string;
    nickname: string | null;
    profile: {
      id: number;
      data: {
        avatar: number | null;
        description: string | null;
        website: string[];
      };
    };
  };
}
