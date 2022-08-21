<template>
  <div>
    <page-description>
      Simutransに関する記事をマークダウン形式で作成します。<br>
      利用可能な書式については<a
        href="https://docs.github.com/ja/free-pro-team@latest/github/writing-on-github/basic-writing-and-formatting-syntax"
        target="=_blank" rel="noopener noreferrer">こちら</a>を参照して下さい。
    </page-description>
    <form-common :article="article" />
    <form-markdown :article="article" />
    <slot />
  </div>
</template>
<script>
export default {
  props: ['article']
};
</script>
