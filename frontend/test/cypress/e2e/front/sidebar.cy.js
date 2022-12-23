/// <reference types="cypress" />

const { mockGuestResponse } = require('../../__mocks__/auth');
const { mockSidebarResponse, createMockArticleData } = require('../../__mocks__/front');

// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **

// This test will pass when run against a clean Quasar project
describe('Sidebar', () => {
  beforeEach(() => {
    cy.intercept('/api/mypage/user', mockGuestResponse).as('mypage.user');
    cy.intercept('/api/front/sidebar', mockSidebarResponse).as('front.sidebar');
    cy.intercept('/api/front/top', {
      statusCode: 200,
      body: {
        pak128japan: [createMockArticleData()],
      },
    }).as('front.top');
    cy.visit('/');
    cy.wait('@mypage.user');
    cy.wait('@front.sidebar');
    cy.wait('@front.top');
  });
  it('API依存サイドバー内容', () => {
    cy.get('body')
      .should('contain.text', 'DummyPak')
      .should('contain.text', 'Dummy Addon (334)')
      .should('contain.text', 'ユーザー一覧')
      .should('contain.text', 'dummy user (72)');
  });
});
