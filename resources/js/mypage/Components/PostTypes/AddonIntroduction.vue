<template>
  <div>
    <form-common
      :article="article"
      :attachments="attachments"
      :options="options"
      :errors="errors"
      @update:attachments="$emit('update:attachments', $event)"
    />
    <form-addon-introduction
      :article="article"
      :options="options"
      :errors="errors"
    />
    <form-addon :article="article" :options="options" :errors="errors" />
  </div>
</template>
<script>
export default {
  name: "addon-introduction",
  props: ["article", "attachments", "options", "errors"],
};
</script>
