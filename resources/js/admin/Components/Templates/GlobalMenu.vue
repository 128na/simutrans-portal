
<template>
  <b-navbar
    class="fixed-left py-2 py-lg-4"
    type="dark"
    variant="primary"
    toggleable="lg"
  >
    <b-navbar-brand class="p-0 mb-lg mb-2 mb-0">
      管理機能
    </b-navbar-brand>

    <b-navbar-toggle target="global-menu" />

    <b-collapse
      id="global-menu"
      is-nav
    >
      <b-navbar-nav>
        <nav-link :to="{ name: 'index' }">
          管理トップ
        </nav-link>
        <nav-link :to="{ name: 'phpinfo' }">
          PHP info
        </nav-link>
        <nav-link :to="{ name: 'users' }">
          ユーザー管理
        </nav-link>
        <nav-link :to="{ name: 'createUser' }">
          ユーザー登録
        </nav-link>
        <nav-link :to="{ name: 'articles' }">
          記事管理
        </nav-link>
        <b-nav-item href="/registration_orders">
          登録申請一覧
        </b-nav-item>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>
