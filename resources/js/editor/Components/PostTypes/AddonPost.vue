<template>
  <div>
    <form-common
      :article="article"
      :attachments="attachments"
      :options="options"
      @attachmentsUpdated="handleAttachmentsUpdated"
    />
    <form-addon-post
      :article="article"
      :attachments="attachments"
      :options="options"
      @attachmentsUpdated="handleAttachmentsUpdated"
    />
    <form-addon :article="article" :options="options" />
  </div>
</template>
<script>
import { attachments_updatable } from "../../mixins";
export default {
  name: "addon-post",
  props: ["article", "attachments", "options"],
  mixins: [attachments_updatable]
};
</script>
