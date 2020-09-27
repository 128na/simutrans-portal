<template>
  <div>
    <form-common
      :article="article"
      :attachments="attachments"
      :options="options"
      :errors="errors"
      @update:attachments="$emit('update:attachments', $event)"
    />
    <form-addon-post
      :article="article"
      :attachments="attachments"
      :options="options"
      :errors="errors"
      @update:attachments="$emit('update:attachments', $event)"
    />
    <form-addon :article="article" :options="options" :errors="errors" />
  </div>
</template>
<script>
export default {
  name: "addon-post",
  props: ["article", "attachments", "options", "errors"],
};
</script>
