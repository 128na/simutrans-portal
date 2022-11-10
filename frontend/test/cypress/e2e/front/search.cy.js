/// <reference types="cypress" />

const { mockGuestResponse } = require('../../__mocks__/auth');
const { mockSidebarResponse, createMockArticleData } = require('../../__mocks__/front');

// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **

// This test will pass when run against a clean Quasar project
describe('Search', () => {
  beforeEach(() => {
    cy.intercept('/api/v2/mypage/user', mockGuestResponse).as('mypage.user');
    cy.intercept('/api/v3/front/sidebar', mockSidebarResponse).as('front.sidebar');
    cy.intercept('/api/v3/front/search?*', {
      statusCode: 200,
      body: {
        title: 'Dummy Search Result',
        data: [createMockArticleData()],
      },
    }).as('front.search');
    cy.visit('/search?word=dummy');
    cy.wait('@mypage.user');
    cy.wait('@front.sidebar');
    cy.wait('@front.search');
    cy.get('.fullscreen.q-drawer__backdrop').click();
  });
  it('表示内容', () => {
    // タイトル
    cy.title().should('equal', 'Dummy Search Result - Simutrans Addon Portal');
    cy.get('body')
      .should('contain.text', 'ダミー記事タイトル')
      .should('contain.text', 'Dummy Search Result');
  });
});