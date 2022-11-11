export const assertTitleAndUrl = (title, url) => {
  cy.title().should('equal', title ? `${title} - Simutrans Addon Portal` : 'Simutrans Addon Portal');
  cy.url().should('equal', url);
};

export const assertBodyHas = (...words) => {
  words.forEach((word) => {
    cy.get('body').should('include.text', word);
  });
};
// front
export const assertFrontTopPage = () => {
  assertTitleAndUrl('', 'http://localhost:8080/');
};

export const assertFrontSearchPage = (word) => {
  assertTitleAndUrl(`${word} の検索結果`, `http://localhost:8080/search?word=${word}`);
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

// admin
export const assertAdminTopPage = () => {
  assertTitleAndUrl('管理トップ', 'http://localhost:8080/admin');
};

// error
export const assertErrorPage = (status) => {
  assertTitleAndUrl('エラー', `http://localhost:8080/error/${status}`);
};
