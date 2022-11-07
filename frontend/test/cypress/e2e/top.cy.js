/// <reference types="cypress" />
// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **

// This test will pass when run against a clean Quasar project
describe('FrontTop', () => {
  beforeEach(() => {
    cy.intercept('/api/v2/mypage/user', { statusCode: 401 }).as('mypage.user');
    cy.intercept('/api/v3/front/sidebar', { statusCode: 200, body: { data: [] } }).as('front.sidebar');
    cy.intercept('/api/v3/front/category/pak/*?simple', { statusCode: 200, body: { data: [] } }).as('front.categoryPak');
    cy.intercept('/api/v3/front/ranking?simple', { statusCode: 200, body: { data: [] } }).as('front.ranking');
    cy.intercept('/api/v3/front/pages?simple', { statusCode: 200, body: { data: [] } }).as('front.pages');
    cy.intercept('/api/v3/front/announces?simple', { statusCode: 200, body: { data: [] } }).as('front.announces');
    cy.visit('/');
  });
  it('表示内容', () => {
    cy.wait('@mypage.user');
    cy.wait('@front.sidebar');
    cy.wait('@front.categoryPak');
    cy.wait('@front.categoryPak');
    cy.wait('@front.categoryPak');
    cy.wait('@front.ranking');
    cy.wait('@front.pages');
    cy.wait('@front.announces');
    cy.title().should('include', 'Simutrans Addon Portal');
  });
});
