/// <reference types="cypress" />

const { assertFrontShowPage, assertBodyHas, assertImageExists } = require('../../assertion');
const { mockGuestResponse } = require('../../__mocks__/auth');
const {
  mockSidebarResponse, mockAddonPost, mockAddonIntroduction, mockMarkdown, mockPage,
} = require('../../__mocks__/front');

const assertAddonPost = () => {
  assertBodyHas('ダミーユーザー', 'dummy description', 'dummy license', 'dummy thanks', 'ダウンロード');
};
const assertAddonIntroduction = () => {
  assertBodyHas('ダミーユーザー', 'dummy description', 'dummy license', 'dummy thanks', 'http://example.com');
};
const assertPage = () => {
  assertBodyHas('dummy caption', 'dummy text', 'http://example.com');
  assertImageExists('https://placehold.jp/150x150.png');
};
const assertMarkdown = () => {
  assertBodyHas('dummy markdown');
};
// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **

// This test will pass when run against a clean Quasar project
describe('フロント記事詳細', () => {
  [
    ['アドオン投稿', mockAddonPost, assertAddonPost],
    ['アドオン紹介', mockAddonIntroduction, assertAddonIntroduction],
    ['一般記事', mockPage, assertPage],
    ['マークダウン記事', mockMarkdown, assertMarkdown],
  ].forEach(([name, mock, assetion]) => {
    describe(`post type: ${name}`, () => {
      const article = mock();
      beforeEach(() => {
        cy.intercept('/api/mypage/user', mockGuestResponse).as('mypage.user');
        cy.intercept('/api/front/sidebar', mockSidebarResponse).as('front.sidebar');
        cy.intercept('post', `/api/shown/${article.slug}`, { statusCode: 200 }).as('front.shown');
        cy.intercept(`/api/front/articles/${article.slug}`, { statusCode: 200, body: { data: article } }).as('front.article');

        cy.visit(`/articles/${article.slug}`);
        cy.wait('@mypage.user');
        cy.wait('@front.article');
        cy.wait('@front.sidebar');
        cy.get('.fullscreen.q-drawer__backdrop').click();
      });
      it('表示', () => {
        assertFrontShowPage(article);
        assetion();
      });
    });
  });
});
