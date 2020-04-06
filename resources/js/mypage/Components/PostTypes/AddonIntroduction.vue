<template>
  <div>
    <form-common
      :article="article"
      :attachments="attachments"
      :options="options"
      @update:attachments="$emit('update:attachments', $event)"
    />
    <form-addon-introduction :article="article" :options="options" />
    <form-addon :article="article" :options="options" />
  </div>
</template>
<script>
export default {
  name: "addon-introduction",
  props: ["article", "attachments", "options"]
};
</script>
