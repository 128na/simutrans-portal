<template>
  <div>
    <form-common
      :article="article"
      :attachments="attachments"
      :options="options"
      :errors="errors"
      @update:attachments="$emit('update:attachments', $event)"
    />
    <form-page
      :article="article"
      :attachments="attachments"
      :options="options"
      :errors="errors"
      @update:attachments="$emit('update:attachments', $event)"
    />
  </div>
</template>
<script>
export default {
  name: "page",
  props: ["article", "attachments", "options", "errors"]
};
</script>
