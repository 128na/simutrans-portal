/// <reference types="cypress" />

const { assertFrontTopPage } = require('../../assertion');
const { mockGuestResponse } = require('../../__mocks__/auth');
const { mockSidebarResponse } = require('../../__mocks__/front');

// Use `cy.dataCy` custom command for more robust tests
// See https://docs.cypress.io/guides/references/best-practices.html#Selecting-Elements

// ** This file is an example of how to write Cypress tests, you can safely delete it **

// This test will pass when run against a clean Quasar project
describe('フロントトップ', () => {
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
