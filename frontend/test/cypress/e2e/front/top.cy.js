/// <reference types="cypress" />

const { assertFrontTopPage } = require('../../assertion');
const { mockGuestResponse } = require('../../__mocks__/auth');
const { mockSidebarResponse, createMockArticleData } = require('../../__mocks__/front');

// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **

// This test will pass when run against a clean Quasar project
describe('フロントトップ', () => {
  describe('モード切替', () => {
    beforeEach(() => {
      cy.intercept('/api/mypage/user', mockGuestResponse).as('mypage.user');
      cy.intercept('/storage/json/sidebar.json', mockSidebarResponse).as('front.sidebar');
      cy.intercept('/storage/json/top.json', {
        statusCode: 200,
        body: {
          pak128japan: [createMockArticleData()],
        },
      }).as('front.top');
      cy.visit('/');
      cy.wait('@mypage.user');
      cy.wait('@front.sidebar');
      cy.wait('@front.top');
      cy.get('.fullscreen.q-drawer__backdrop').click();
    });
    it('表示内容', () => {
      assertFrontTopPage();
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
      // cy.get('.fullscreen.q-drawer__backdrop').click();
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
  describe('API自動リトライ', () => {
    beforeEach(() => {
    // https://github.com/quasarframework/quasar/issues/2233#issuecomment-678115434
      const resizeObserverLoopErrRe = /^[^(ResizeObserver loop limit exceeded)]/;
      cy.on('uncaught:exception', (err) => {
      /* returning false here prevents Cypress from failing the test */
        if (resizeObserverLoopErrRe.test(err.message)) {
          return false;
        }
        return true;
      });
      cy.intercept('/api/mypage/user', mockGuestResponse).as('mypage.user');
      cy.intercept('/storage/json/sidebar.json', mockSidebarResponse).as('front.sidebar');
      cy.intercept('/storage/json/top.json', {
        statusCode: 500,
        body: {},
      }).as('front.topFailed');
      cy.visit('/');
      cy.wait('@mypage.user');
      cy.wait('@front.sidebar');
      cy.wait('@front.topFailed');
      cy.wait('@front.topFailed');
      cy.wait('@front.topFailed');
    });
    it('表示内容', () => {
      assertFrontTopPage();
      cy.get('.q-notification__message').should('contain', '記事取得に失敗しました');
    });
  });
});
