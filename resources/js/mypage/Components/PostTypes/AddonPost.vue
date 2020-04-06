<template>
  <div>
    <form-common
      :article="article"
      :attachments="attachments"
      :options="options"
      @update:attachments="$emit('update:attachments', $event)"
    />
    <form-addon-post
      :article="article"
      :attachments="attachments"
      :options="options"
      @update:attachments="$emit('update:attachments', $event)"
    />
    <form-addon :article="article" :options="options" />
  </div>
</template>
<script>
export default {
  name: "addon-post",
  props: ["article", "attachments", "options"]
};
</script>
