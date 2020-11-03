<template>
  <div>
    <form-common :article="article" />
    <form-markdown :article="article" />
  </div>
</template>
<script>
export default {
  name: "markdown",
  props: ["article"],
};
</script>
