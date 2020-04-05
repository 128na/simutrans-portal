<template>
  <div>
    <input
      type="file"
      :ref="file_uploader_id"
      :accept="accept"
      class="d-none"
      @change="handleUploader"
    />
    <b-img v-if="can_preview" :src="current_thumbnail" thumbnail />
    <p>{{ current_filename }}</p>
    <b-button variant="outline-secondary" @click="handleModal">Select File</b-button>
    <b-modal :id="id" title="Select File" size="xl" scrollable>
      <template v-slot:modal-header>
        <div>Select File</div>
        <b-form inline>
          <b-form-input v-model="search" placeholder="search" class="mr-1" />
          <b-btn variant="primary" @click="handleUpload">Upload</b-btn>
        </b-form>
      </template>
      <div class="attachment-list">
        <div
          v-for="attachment in filtered_attachments"
          :key="attachment.id"
          :class="selectedClass(attachment.id)"
          class="attachment-item"
          @click="handleAttachment(attachment.id)"
        >
          <div class="attachment-thumbnail">
            <b-img :src="attachment.thumbnail" fluid class="m-auto" />
            <b-button
              class="position-absolute btn-close"
              pill
              size="sm"
              @click="handleDelete(attachment.id)"
            >&times;</b-button>
          </div>
          <small class="ellipsis">{{ attachment.original_name }}</small>
        </div>
        <div v-show="filtered_attachments.length < 1">No Items</div>
      </div>
      <template v-slot:modal-footer>
        <div class="flex-1">
          <div>{{ selected_filename }}</div>
        </div>
        <div>
          <b-btn @click="handleCancel">Cancel</b-btn>
          <b-btn variant="primary" @click="handleOK">OK</b-btn>
        </div>
      </template>
    </b-modal>
  </div>
</template>
<script>
import api from "../../api";
import { toastable } from "../../mixins";
export default {
  name: "media-manager",
  props: { id: {}, value: {}, attachments: {}, only_image: { default: false } },
  mixins: [toastable],
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
      const items = this.only_image
        ? this.attachments.filter(a => a.type === "image")
        : this.attachments;

      return this.criteria
        ? items.filter(a => a.original_name.includes(this.criteria))
        : items;
    },
    file_uploader_id() {
      return `uploader_${this.id}`;
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
      return "not selected";
    }
  },
  methods: {
    initialize() {
      this.selected = this.value || null;
      this.search = "";

      if (this.$refs[this.file_uploader_id]) {
        this.$refs[this.file_uploader_id].value = null;
      }
    },
    handleModal() {
      this.$bvModal.show(this.id);
    },
    handleUpload() {
      this.$refs[this.file_uploader_id].click();
    },
    handleUploader(e) {
      const file = e.target.files.item(0);

      if (file) {
        this.upload(file);
      }
    },
    async upload(file) {
      const formData = new FormData();
      formData.append("file", file);
      if (this.only_image) {
        formData.append("only_image", true);
      }
      const res = await api
        .storeAttachment(formData)
        .catch(this.handleErrorToast);

      if (res && res.status === 200) {
        this.$emit("attachmentsUpdated", res.data.data);
        this.$refs[this.file_uploader_id].value = null;
        this.toastSuccess("File Uploaded");
      }
    },
    handleAttachment(id) {
      this.selected = this.selected == id ? null : id;
    },
    handleDelete(id) {
      if (window.confirm("sure?")) {
        this.delete(id);
      }
    },
    async delete(id) {
      const res = await api.deleteAttachment(id).catch(this.handleErrorToast);

      if (res && res.status === 200) {
        this.$emit("attachmentsUpdated", res.data.data);
        this.toastSuccess("File Deleted");
      }
    },
    handleCancel() {
      this.$bvModal.hide(this.id);
    },
    handleOK() {
      this.$emit("input", this.selected);
      this.$bvModal.hide(this.id);
    },

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
