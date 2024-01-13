// 汎用
export const assertTitleAndUrl = (title, url) => {
  cy.title().should('equal', title ? `${title} - Simutrans Addon Portal` : 'Simutrans Addon Portal');
  cy.url().should('equal', url);
};

export const assertBodyHas = (...words) => {
  words.forEach((word) => {
    cy.get('body').should('include.text', word);
  });
};

export const assertImageExists = (src) => {
  cy.get(`img[src="${src}"]`).should('exist');
};

// front
export const assertFrontTopPage = () => {
  assertTitleAndUrl('', 'http://localhost:8080/');
};

export const assertFrontSearchPage = (word) => {
  assertTitleAndUrl(`${word} の検索結果`, `http://localhost:8080/search?word=${word}`);
};

export const assertFrontShowPage = (article) => {
  assertTitleAndUrl(article.title, `http://localhost:8080/users/${article.user.id}/${article.slug}`);
};

// mypage
export const assertMypageTopPage = () => {
  assertTitleAndUrl('マイページトップ', 'http://localhost:8080/mypage');
};

export const assertLoginPage = () => {
  assertTitleAndUrl('ログイン', 'http://localhost:8080/mypage/login');
};

export const assertRequiresVerifyPage = () => {
  assertTitleAndUrl('未認証', 'http://localhost:8080/mypage/requires-verified');
  assertBodyHas('メールアドレスの確認が済んでいません');
};

export const assertEditPage = (id = 1) => {
  assertTitleAndUrl('編集', `http://localhost:8080/mypage/edit/${id}`);
};

export const assertCreatePage = (postType) => {
  assertTitleAndUrl('新規作成', `http://localhost:8080/mypage/create/${postType}`);
};

export const assertProfilePage = () => {
  assertTitleAndUrl('プロフィール編集', 'http://localhost:8080/mypage/profile');
};

// admin
export const assertAdminTopPage = () => {
  assertTitleAndUrl('管理トップ', 'http://localhost:8080/admin');
};

// error
export const assertErrorPage = (status) => {
  assertTitleAndUrl('エラー', `http://localhost:8080/error/${status}`);
};
