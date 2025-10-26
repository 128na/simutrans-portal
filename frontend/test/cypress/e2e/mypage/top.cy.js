/// <reference types="cypress" />

const {
  mockGuestResponse, mockUserResponse, mockUnverifiedUserResponse, mockTopResponse,
} = require('../../__mocks__/auth');
const { mockArticlesResponse } = require('../../__mocks__/mypage');
const {
  assertBodyHas, assertLoginPage, assertMypageTopPage, assertRequiresVerifyPage,
} = require('../../assertion');

// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **

// This test will pass when run against a clean Quasar project
describe('マイページトップ', () => {
  describe('未ログイン', () => {
    beforeEach(() => {
      cy.intercept('/', mockTopResponse).as('top');
      cy.intercept('/api/mypage/user', mockGuestResponse).as('mypage.user');
      cy.intercept('/sanctum/csrf-cookie', { statusCode: 200 }).as('csrf');
      cy.visit('/mypage');
      cy.wait('@mypage.user');
    });
    it('ログインページへ遷移する', () => {
      assertLoginPage();
    });
  });
  describe('ログイン済み', () => {
    beforeEach(() => {
      cy.intercept('/api/mypage/user', mockUserResponse).as('mypage.user');
      cy.intercept('/api/mypage/articles', mockArticlesResponse).as('mypage.articles');
      cy.visit('/mypage');
      cy.wait('@mypage.user');
      cy.wait('@mypage.articles');
    });
    it('マイページが表示される', () => {
      assertMypageTopPage();
      assertBodyHas('dummy title');
    });
  });

  describe('ログイン済み 未認証', () => {
    beforeEach(() => {
      cy.intercept('/api/mypage/user', mockUnverifiedUserResponse).as('mypage.user');
      cy.visit('/mypage');
      cy.wait('@mypage.user');
    });
    it('未認証画面が表示される', () => {
      assertRequiresVerifyPage();
    });
  });
});
