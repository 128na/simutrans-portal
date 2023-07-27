export const mockGuestResponse = { statusCode: 401 };
export const mockUserResponse = {
  statusCode: 200,
  body: {
    data: {
      id: 1,
      name: 'dummy user',
      email: 'dummy@example.com',
      invitation_url: 'http://example.com/123',
      profile: {
        id: 1,
        data: {
          avatar: null,
          description: 'dummy description',
          website: 'http://example.com/456',
        },
      },
      admin: false,
      verified: true,
      attachments: [],
    },
  },
};

export const mockUnverifiedUserResponse = {
  statusCode: 200,
  body: {
    data: {
      id: 1,
      name: 'dummy user',
      email: 'dummy@example.com',
      invitation_url: 'http://example.com/123',
      profile: {
        id: 1,
        data: {
          avatar: null,
          description: 'dummy description',
          website: 'http://example.com/456',
        },
      },
      admin: false,
      verified: false,
      attachments: [],
    },
  },
};

export const mockAdminResponse = {
  statusCode: 200,
  body: {
    data: {
      id: 1,
      name: 'dummy user',
      email: 'dummy@example.com',
      invitation_url: 'http://example.com/123',
      profile: {
        id: 1,
        data: {
          avatar: null,
          description: 'dummy description',
          website: 'http://example.com/456',
        },
      },
      admin: true,
      verified: true,
      attachments: [],
    },
  },
};
