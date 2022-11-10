/// <reference types="cypress" />

const { mockGuestResponse } = require('../../__mocks__/auth');
const { mockSidebarResponse, createMockArticleData } = require('../../__mocks__/front');

// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **

// This test will pass when run against a clean Quasar project
describe('Sidebar', () => {
  beforeEach(() => {
    cy.intercept('/api/v2/mypage/user', mockGuestResponse).as('mypage.user');
    cy.intercept('/api/v3/front/sidebar', mockSidebarResponse).as('front.sidebar');
    cy.intercept('/api/v3/front/category/pak/*?simple', {
      statusCode: 200,
      body: {
        title: 'dummy Pak Title',
        data: [createMockArticleData()],
      },
    }).as('front.categoryPak');
    cy.intercept('/api/v3/front/ranking?simple', { statusCode: 200, body: { data: [] } }).as('front.ranking');
    cy.intercept('/api/v3/front/pages?simple', { statusCode: 200, body: { data: [] } }).as('front.pages');
    cy.intercept('/api/v3/front/announces?simple', { statusCode: 200, body: { data: [] } }).as('front.announces');
    cy.visit('/');
    cy.wait('@mypage.user');
    cy.wait('@front.sidebar');
    cy.wait('@front.categoryPak');
    cy.wait('@front.categoryPak');
    cy.wait('@front.categoryPak');
    cy.wait('@front.ranking');
    cy.wait('@front.pages');
    cy.wait('@front.announces');
  });
  it('API依存サイドバー内容', () => {
    cy.get('body')
      .should('contain.text', 'DummyPak')
      .should('contain.text', 'Dummy Addon (334)')
      .should('contain.text', 'ユーザー一覧')
      .should('contain.text', 'dummy user (72)');
  });
});
