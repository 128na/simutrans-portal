<template>
  <div>
    <page-description>
      アドオン掲載ページのリンクを貼った紹介記事を作成します。
    </page-description>
    <form-common :article="article" />
    <form-addon-introduction :article="article" />
    <form-addon :article="article" />
    <slot />
  </div>
</template>
<script>
export default {
  props: ['article']
};
</script>
