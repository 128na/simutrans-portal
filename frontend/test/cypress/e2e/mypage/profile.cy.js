/// <reference types="cypress" />

const { mockGuestResponse, mockUserResponse, mockUnverifiedUserResponse } = require('../../__mocks__/auth');
const { mockAttachmentsResponse } = require('../../__mocks__/mypage');
const { assertLoginPage, assertRequiresVerifyPage, assertProfilePage } = require('../../assertion');

// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **
// This test will pass when run against a clean Quasar project
describe('プロフィール編集画面', () => {
  describe('未ログイン', () => {
    beforeEach(() => {
      cy.intercept('/api/mypage/user', mockGuestResponse).as('mypage.user');
      cy.intercept('/sanctum/csrf-cookie', { statusCode: 200 }).as('csrf');
      cy.visit('/mypage/profile');
      cy.wait('@mypage.user');
    });
    it('ログイン画面へ遷移する', () => {
      assertLoginPage();
    });
  });
  describe('ログイン済み', () => {
    beforeEach(() => {
      cy.intercept('/api/mypage/user', mockUserResponse).as('mypage.user');
      cy.intercept('/api/mypage/attachments', mockAttachmentsResponse).as('mypage.attachments');
      cy.visit('/mypage/profile');
      cy.wait('@mypage.user');
      cy.wait('@mypage.attachments');
    });
    it('画面表示', () => {
      assertProfilePage();
    });
  });

  describe('プロフィール編集 ログイン済み 未認証', () => {
    beforeEach(() => {
      cy.intercept('/api/mypage/user', mockUnverifiedUserResponse).as('mypage.user');
      cy.visit('/mypage/profile');
      cy.wait('@mypage.user');
    });
    it('未認証画面が表示される', () => {
      assertRequiresVerifyPage();
    });
  });
});
