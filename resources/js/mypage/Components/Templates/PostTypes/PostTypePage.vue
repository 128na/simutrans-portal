<template>
  <div>
    <form-common :article="article" />
    <form-page :article="article" />
  </div>
</template>
<script>
export default {
  name: "page",
  props: ["article"],
};
</script>
