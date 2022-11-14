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
      cy.intercept('/api/v3/front/announces?simple', { statusCode: 500, body: { data: [] } }).as('front.announces');
      cy.visit('/');
      cy.wait('@mypage.user');
      cy.wait('@front.sidebar');
      cy.wait('@front.categoryPak');
      cy.wait('@front.categoryPak');
      cy.wait('@front.categoryPak');
      cy.wait('@front.ranking');
      cy.wait('@front.pages');
      cy.wait('@front.announces');
      cy.wait('@front.announces');
      cy.wait('@front.announces');
    });
    it('表示内容', () => {
      assertFrontTopPage();
      cy.get('.q-notification__message').should('contain', 'お知らせ一覧の取得に失敗しました');
    });
  });
});
