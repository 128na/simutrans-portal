/// <reference types="cypress" />

const { mockGuestResponse, mockUserResponse, mockUnverifiedUserResponse } = require('../../__mocks__/auth');
const { mockArticlesResponse } = require('../../__mocks__/mypage');

// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **

// This test will pass when run against a clean Quasar project
describe('Mypage 未ログイン', () => {
  beforeEach(() => {
    cy.intercept('/api/v2/mypage/user', mockGuestResponse).as('mypage.user');
    cy.intercept('/sanctum/csrf-cookie', { statusCode: 200 }).as('csrf');
    cy.visit('/mypage');
    cy.wait('@mypage.user');
  });
  it('ログインページへ遷移する', () => {
    // タイトル
    cy.title().should('equal', 'ログイン - Simutrans Addon Portal');
    cy.url().should('equal', 'http://localhost:8080/mypage/login');
  });
});
describe('Mypage ログイン済み', () => {
  beforeEach(() => {
    cy.intercept('/api/v2/mypage/user', mockUserResponse).as('mypage.user');
    cy.intercept('/api/v2/mypage/articles', mockArticlesResponse).as('mypage.articles');
    cy.visit('/mypage');
    cy.wait('@mypage.user');
    cy.wait('@mypage.articles');
  });
  it('マイページが表示される', () => {
    // タイトル
    cy.title().should('equal', 'マイページトップ - Simutrans Addon Portal');
    cy.url().should('equal', 'http://localhost:8080/mypage');
    cy.get('body').should('include.text', 'dummy title');
  });
});

describe('Mypage ログイン済み 未認証', () => {
  beforeEach(() => {
    cy.intercept('/api/v2/mypage/user', mockUnverifiedUserResponse).as('mypage.user');
    cy.visit('/mypage');
    cy.wait('@mypage.user');
  });
  it('未認証画面が表示される', () => {
    // タイトル
    cy.title().should('equal', '未認証 - Simutrans Addon Portal');
    cy.url().should('equal', 'http://localhost:8080/mypage/requires-verified');
    cy.get('body').should('include.text', 'メールアドレスの確認が済んでいません');
  });
});
