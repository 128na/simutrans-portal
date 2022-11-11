/// <reference types="cypress" />

const { mockGuestResponse, mockUserResponse, mockUnverifiedUserResponse } = require('../../__mocks__/auth');
const {
  mockArticlesResponse, mockOptionsResponse, mockAttachmentsResponse, mockTagsResponse,
} = require('../../__mocks__/mypage');
const {
  assertLoginPage, assertEditPage, assertRequiresVerifyPage,
} = require('../../assertion');

// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **
// This test will pass when run against a clean Quasar project
describe('編集画面 未ログイン', () => {
  beforeEach(() => {
    cy.intercept('/api/v2/mypage/user', mockGuestResponse).as('mypage.user');
    cy.intercept('/sanctum/csrf-cookie', { statusCode: 200 }).as('csrf');
    cy.visit('/mypage/edit/1');
    cy.wait('@mypage.user');
  });
  it('ログイン画面へ遷移する', () => {
    assertLoginPage();
  });
});
describe('編集画面 ログイン済み', () => {
  beforeEach(() => {
    cy.intercept('/api/v2/mypage/user', mockUserResponse).as('mypage.user');
    cy.intercept('/api/v2/mypage/articles', mockArticlesResponse).as('mypage.articles');
    cy.intercept('/api/v2/mypage/options', mockOptionsResponse).as('mypage.options');
    cy.intercept('/api/v2/mypage/attachments', mockAttachmentsResponse).as('mypage.attachments');
    cy.intercept('/api/v2/mypage/tags?name=', mockTagsResponse).as('mypage.tags');
    cy.visit('/mypage/edit/1');
    cy.wait('@mypage.user');
    cy.wait('@mypage.articles');
    cy.wait('@mypage.options');
    cy.wait('@mypage.attachments');
    cy.wait('@mypage.tags');
  });
  it('編集画面が表示される', () => {
    assertEditPage(1);
  });
});

describe('Mypage ログイン済み 未認証', () => {
  beforeEach(() => {
    cy.intercept('/api/v2/mypage/user', mockUnverifiedUserResponse).as('mypage.user');
    cy.visit('/mypage/edit/1');
    cy.wait('@mypage.user');
  });
  it('未認証画面が表示される', () => {
    assertRequiresVerifyPage();
  });
});
