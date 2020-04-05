<template>
  <div>
    <form-common
      :article="article"
      :attachments="attachments"
      :options="options"
      @attachmentsUpdated="handleAttachmentsUpdated"
    />
    <form-markdown
      :article="article"
      :attachments="attachments"
      :options="options"
      @attachmentsUpdated="handleAttachmentsUpdated"
    />
  </div>
</template>
<script>
import { attachments_updatable } from "../../mixins";
export default {
  name: "markdown",
  props: ["article", "attachments", "options"],
  mixins: [attachments_updatable]
};
</script>
