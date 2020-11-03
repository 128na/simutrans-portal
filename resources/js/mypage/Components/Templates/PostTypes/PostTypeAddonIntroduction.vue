<template>
  <div>
    <form-common :article="article" />
    <form-addon-introduction :article="article" />
    <form-addon :article="article" />
  </div>
</template>
<script>
export default {
  name: "addon-introduction",
  props: ["article"],
};
</script>
