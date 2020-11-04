<template>
  <div>
    <form-common :article="article" />
    <form-page :article="article" />
    <slot />
  </div>
</template>
<script>
export default {
  props: ["article"],
};
</script>
