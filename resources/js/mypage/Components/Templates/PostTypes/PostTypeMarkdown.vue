<template>
  <div>
    <form-common :article="article" />
    <form-markdown :article="article" />
    <slot />
  </div>
</template>
<script>
export default {
  props: ["article"],
};
</script>
