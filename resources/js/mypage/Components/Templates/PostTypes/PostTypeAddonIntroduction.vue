<template>
  <div>
    <form-common :article="article" />
    <form-addon-introduction :article="article" />
    <form-addon :article="article" />
    <slot />
  </div>
</template>
<script>
export default {
  props: ["article"],
};
</script>
