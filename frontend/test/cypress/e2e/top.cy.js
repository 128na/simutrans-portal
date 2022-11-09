/// <reference types="cypress" />

const { createMockArticleData, mockSidebarResponse } = require('../__mocks__/response');

// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **

// This test will pass when run against a clean Quasar project
describe('FrontTop', () => {
  beforeEach(() => {
    cy.intercept('/api/v2/mypage/user', { statusCode: 401 }).as('mypage.user');
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
    cy.get('.fullscreen.q-drawer__backdrop').click();
  });
  it('表示内容', () => {
    // タイトル
    cy.title().should('equal', 'Simutrans Addon Portal');
  });

  it('ダークモード切替', () => {
    cy.get('body').should('have.class', 'body--light');
    cy.get('[data-cy="btn-light"]')
      .should('exist')
      .click();
    cy.get('body').should('have.class', 'body--dark');
    cy.get('[data-cy="btn-dark"]')
      .should('exist')
      .click();
    cy.get('body').should('have.class', 'body--light');
  });

  it('リストモード切替', () => {
    cy.get('[data-cy="mode-list"]').should('exist');
    cy.get('[data-cy="btn-list"]')
      .should('exist')
      .click();
    cy.get('[data-cy="mode-show"]').should('exist');
    cy.get('[data-cy="btn-list"]')
      .should('exist')
      .click();
    cy.get('[data-cy="mode-gallery"]').should('exist');
    cy.get('[data-cy="btn-list"]')
      .should('exist')
      .click();
  });
});
