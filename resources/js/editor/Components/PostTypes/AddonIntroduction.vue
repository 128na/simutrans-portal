<template>
  <div>
    <form-common
      :article="article"
      :attachments="attachments"
      :options="options"
      @attachmentsUpdated="handleAttachmentsUpdated"
    />
    <form-addon-introduction :article="article" :options="options" />
    <form-addon :article="article" :options="options" />
  </div>
</template>
<script>
import { attachments_updatable } from "../../mixins";
export default {
  name: "addon-introduction",
  props: ["article", "attachments", "options"],
  mixins: [attachments_updatable]
};
</script>
