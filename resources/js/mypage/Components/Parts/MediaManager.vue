<template>
  <div>
    <input type="file" :ref="file_uploader_id" :accept="accept" class="d-none" @change="handleFile" />
    <b-img v-if="can_preview" :src="current_thumbnail" thumbnail />
    <p>{{ current_filename }}</p>
    <b-button variant="outline-secondary" @click="handleShow">{{$t('Open File Manager')}}</b-button>
    <b-modal :id="name" title="Select File" size="xl" scrollable>
      <template v-slot:modal-header>
        <div>{{$t('File Manager')}}</div>
        <b-form inline>
          <b-form-input v-model="search" :placeholder="$t('Search')" class="mr-1" />
          <b-btn :disabled="fetching" variant="primary" @click="handleUpload">{{$t('Upload File')}}</b-btn>
        </b-form>
      </template>
      <div class="attachment-list">
        <div
          v-for="attachment in filtered_attachments"
          :key="attachment.id"
          :class="selectedClass(attachment.id)"
          class="attachment-item"
          @click="handleSelect(attachment.id)"
        >
          <div class="attachment-thumbnail">
            <b-img :src="attachment.thumbnail" fluid class="m-auto" />
            <b-button
              class="position-absolute btn-close"
              pill
              size="sm"
              @click.stop="handleDelete(attachment.id)"
            >&times;</b-button>
          </div>
          <small class="ellipsis">{{ attachment.original_name }}</small>
        </div>
        <div v-show="filtered_attachments.length < 1">{{$t('No file.')}}</div>
      </div>
      <template v-slot:modal-footer>
        <div class="flex-1">
          <div>{{ selected_filename }}</div>
        </div>
        <div>
          <b-btn :disabled="fetching" @click="handleCancel" size="sm">{{$t('Cancel')}}</b-btn>
          <b-btn :disabled="fetching" variant="primary" @click="handleOK">{{$t('Select File')}}</b-btn>
        </div>
      </template>
    </b-modal>
  </div>
</template>
<script>
import { toastable, api_handlable } from "../../mixins";
export default {
  name: "media-manager",
  props: {
    id: {},
    name: {},
    type: {},
    attachments: {},
    value: {},
    only_image: { default: false }
  },
  mixins: [api_handlable, toastable],
  data() {
    return {
      search: "",
      selected: null
    };
  },
  created() {
    this.initialize();
  },
  computed: {
    criteria() {
      return this.search.trim().toLowerCase();
    },
    filtered_attachments() {
      const image_filtered = this.only_image
        ? this.attachments.filter(a => a.type === "image")
        : this.attachments;

      const type_filtered = this.id
        ? image_filtered.filter(
            a =>
              a.attachmentable_id === null ||
              (a.attachmentable_id == this.id &&
                a.attachmentable_type === this.type)
          )
        : image_filtered.filter(a => a.attachmentable_id === null);

      return this.criteria
        ? type_filtered.filter(a => a.original_name.includes(this.criteria))
        : type_filtered;
    },
    file_uploader_id() {
      return `uploader_${this.name}`;
    },
    accept() {
      return this.only_image ? "image/*" : "";
    },
    can_preview() {
      return this.only_image && this.value;
    },
    selected_attachment() {
      if (this.selected) {
        return this.attachments.find(a => a.id == this.selected);
      }
    },
    selected_filename() {
      if (this.selected_attachment) {
        return this.selected_attachment.original_name;
      }
      return "";
    },
    current_attachment() {
      if (this.value) {
        return this.attachments.find(a => a.id == this.value);
      }
    },
    current_thumbnail() {
      if (this.current_attachment) {
        return this.current_attachment.thumbnail;
      }
    },
    current_filename() {
      if (this.current_attachment) {
        return this.current_attachment.original_name;
      }
      return this.$t("Not selected.");
    }
  },
  methods: {
    getFileElement() {
      return this.$refs[this.file_uploader_id];
    },
    getFile() {
      return this.getFileElement().files.item(0);
    },
    initialize() {
      this.selected = this.value || null;
      this.search = "";

      const el = this.getFileElement();
      if (el) {
        el.value = null;
      }
    },
    setAttachments(attachments) {
      const file = this.getFile();
      if (file) {
        this.selected = attachments.find(a => a.original_name === file.name).id;
        this.getFileElement().value = null;
      }

      this.$emit("update:attachments", attachments);
    },
    // modal controll
    handleShow() {
      this.$bvModal.show(this.name);
    },
    // modal actions
    handleCancel() {
      this.$bvModal.hide(this.name);
    },
    handleOK() {
      this.$emit("input", this.selected);
      this.$bvModal.hide(this.name);
    },
    handleSelect(id) {
      this.selected = this.selected == id ? null : id;
    },
    handleUpload() {
      this.getFileElement().click();
    },
    async handleFile(e) {
      const file = this.getFile();
      if (file) {
        await this.storeAttachment(file);
        this.toastSuccess("Uploaded");
      }
    },
    async handleDelete(id) {
      if (window.confirm(this.$t("Are you sure you want to delete?"))) {
        if (this.selected == id) {
          this.selected = null;
        }
        await this.deleteAttachment(id);
        this.toastSuccess("Deleted");
      }
    },
    // style
    selectedClass(attachment_id) {
      return attachment_id == this.selected ? "selected" : "";
    }
  }
};
</script>
<style lang="scss" scoped>
.attachment-list {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
  align-items: flex-start;
}
.attachment-item {
  cursor: pointer;
  padding: 4px;
  width: 136px;
  height: 156px;
  border: 1px solid rgb(0, 0, 0, 0);
}
.attachment-thumbnail {
  position: relative;
  display: flex;
  height: 128px;
  width: 128px;
}
.selected {
  border: 1px solid #48a3e0;
  background-color: rgba(72, 163, 224, 0.5);
}
.flex-1 {
  flex: 1;
}
.ellipsis {
  display: block;
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
}
.btn-close {
  cursor: pointer;
  top: -0.1rem;
  right: -0.1rem;
  width: 1.2rem;
  height: 1.2rem;
  line-height: 0.9rem;
  font-weight: bold;
  padding: 0;
  font-size: 0.9rem;
}
</style>
