<template>
  <div>
    <page-description>
      アドオンファイルをアップロードして記事を作成します。
    </page-description>
    <form-common :article="article" />
    <form-addon-post :article="article" />
    <form-addon :article="article" />
    <form-status :article="article" />
    <slot />
  </div>
</template>
<script>
export default {
  props: ['article']
};
</script>
