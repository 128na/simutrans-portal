<template>
  <div>
    <form-common :article="article" />
    <form-addon-post :article="article" />
    <form-addon :article="article" />
  </div>
</template>
<script>
export default {
  name: "addon-post",
  props: ["article"],
};
</script>
