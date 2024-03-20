/// <reference types="cypress" />

const { mockGuestResponse, mockUserResponse, mockUnverifiedUserResponse } = require('../../__mocks__/auth');
const { mockOptionsResponse, mockAttachmentsResponse, mockTagsResponse, mockArticlesResponse } = require('../../__mocks__/mypage');
const {
  assertLoginPage, assertRequiresVerifyPage, assertCreatePage,
} = require('../../assertion');

// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **
// This test will pass when run against a clean Quasar project
describe('新規作成画面', () => {
  describe('未ログイン', () => {
    beforeEach(() => {
      cy.intercept('/api/mypage/user', mockGuestResponse).as('mypage.user');
      cy.intercept('/sanctum/csrf-cookie', { statusCode: 200 }).as('csrf');
      cy.visit('/mypage/create/addon-post');
      cy.wait('@mypage.user');
    });
    it('ログイン画面へ遷移する', () => {
      assertLoginPage();
    });
  });
  describe('ログイン済み', () => {
    beforeEach(() => {
      cy.intercept('/api/mypage/user', mockUserResponse).as('mypage.user');
      cy.intercept('/api/mypage/options', mockOptionsResponse).as('mypage.options');
      cy.intercept('/api/mypage/attachments', mockAttachmentsResponse).as('mypage.attachments');
      cy.intercept('/api/mypage/articles', mockArticlesResponse).as('mypage.articles');
      cy.intercept('/api/mypage/tags?name=', mockTagsResponse).as('mypage.tags');
      cy.visit('/mypage/create/addon-post');
      cy.wait('@mypage.user');
      cy.wait('@mypage.options');
      cy.wait('@mypage.attachments');
      cy.wait('@mypage.articles');
      cy.wait('@mypage.tags');
    });
    it('新規作成画面が表示される', () => {
      assertCreatePage('addon-post');
    });
  });

  describe('ログイン済み 未認証', () => {
    beforeEach(() => {
      cy.intercept('/api/mypage/user', mockUnverifiedUserResponse).as('mypage.user');
      cy.visit('/mypage/create/addon-post');
      cy.wait('@mypage.user');
    });
    it('未認証画面が表示される', () => {
      assertRequiresVerifyPage();
    });
  });
});
