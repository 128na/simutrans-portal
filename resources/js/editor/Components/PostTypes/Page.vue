<template>
  <div>
    <form-common
      :article="article"
      :attachments="attachments"
      :options="options"
      @attachmentsUpdated="handleAttachmentsUpdated"
    />
    <form-page
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
  name: "page",
  props: ["article", "attachments", "options"],
  mixins: [attachments_updatable]
};
</script>
