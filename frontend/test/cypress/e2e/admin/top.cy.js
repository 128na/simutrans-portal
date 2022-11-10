/// <reference types="cypress" />

const { mockGuestResponse, mockAdminResponse, mockUserResponse } = require('../../__mocks__/auth');

// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **

// This test will pass when run against a clean Quasar project
describe('Admin 未ログイン', () => {
  beforeEach(() => {
    cy.intercept('/api/v2/mypage/user', mockGuestResponse).as('mypage.user');
    cy.intercept('/sanctum/csrf-cookie', { statusCode: 200 }).as('csrf');
    cy.visit('/admin');
    cy.wait('@mypage.user');
  });
  it('エラーページへ遷移する', () => {
    // タイトル
    cy.title().should('equal', 'エラー - Simutrans Addon Portal');
    cy.url().should('equal', 'http://localhost:8080/error/404');
  });
});
describe('Admin ログイン済み 管理者', () => {
  beforeEach(() => {
    cy.intercept('/api/v2/mypage/user', mockAdminResponse).as('mypage.user');
    cy.visit('/admin');
    cy.wait('@mypage.user');
  });
  it('管理ページが表示される', () => {
    // タイトル
    cy.title().should('equal', '管理トップ - Simutrans Addon Portal');
    cy.url().should('equal', 'http://localhost:8080/admin');
  });
});

describe('Mypage ログイン済み 一般ユーザー', () => {
  beforeEach(() => {
    cy.intercept('/api/v2/mypage/user', mockUserResponse).as('mypage.user');
    cy.visit('/admin');
    cy.wait('@mypage.user');
  });
  it('エラーページへ遷移する', () => {
    // タイトル
    cy.title().should('equal', 'エラー - Simutrans Addon Portal');
    cy.url().should('equal', 'http://localhost:8080/error/404');
  });
});