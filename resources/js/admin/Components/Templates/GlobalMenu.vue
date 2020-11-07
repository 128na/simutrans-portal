<template>
  <b-nav vertical>
    <nav-link :to="{ name: 'index' }">管理トップ</nav-link>
    <nav-link :to="{ name: 'phpinfo' }">PHP info</nav-link>
    <nav-link :to="{ name: 'users' }">ユーザー管理</nav-link>
    <nav-link :to="{ name: 'articles' }">記事管理</nav-link>
  </b-nav>
</template>
