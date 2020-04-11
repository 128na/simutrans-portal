<template>
  <div>
    <form-common
      :article="article"
      :attachments="attachments"
      :options="options"
      :errors="errors"
      @update:attachments="$emit('update:attachments', $event)"
    />
    <form-markdown
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
  name: "markdown",
  props: ["article", "attachments", "options", "errors"]
};
</script>
