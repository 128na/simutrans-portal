<template>
  <div>
    <page-description>Simutransに関する記事を作成します。</page-description>
    <form-common :article="article" />
    <form-page :article="article" />
    <slot />
  </div>
</template>
<script>
export default {
  props: ['article']
};
</script>
