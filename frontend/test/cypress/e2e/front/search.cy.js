/// <reference types="cypress" />

const { assertFrontSearchPage } = require('../../assertion');
const { mockGuestResponse } = require('../../__mocks__/auth');
const { mockSidebarResponse, createMockArticleData } = require('../../__mocks__/front');

// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **

// This test will pass when run against a clean Quasar project
describe('Search', () => {
  beforeEach(() => {
    cy.intercept('/api/mypage/user', mockGuestResponse).as('mypage.user');
    cy.intercept('/storage/json/sidebar.json', mockSidebarResponse).as('front.sidebar');
    cy.intercept('/api/front/search?*', {
      statusCode: 200,
      body: {
        description: { title: 'dummy の検索結果' },
        data: [createMockArticleData()],
      },
    }).as('front.search');
    cy.visit('/search?word=dummy');
    cy.wait('@mypage.user');
    cy.wait('@front.sidebar');
    cy.wait('@front.search');
  });
  it('表示内容', () => {
    assertFrontSearchPage('dummy');
  });
});
