<template>
  <div>
    <input
      type="file"
      class="d-none"
      :ref="file_uploader_id"
      :accept="accept"
      @change="handleFileChange"
    />
    <b-img v-if="can_preview" :src="current_thumbnail" thumbnail />
    <p>{{ current_filename }}</p>
    <b-button :variant="button_variant" @click="handleShow">
      ファイルを選択する
    </b-button>
    <b-modal :id="name" title="Select File" size="xl" scrollable>
      <template v-slot:modal-header>
        <div>ファイルマネージャー</div>
        <fetching-overlay>
          <b-button variant="primary" @click="handleClickUpload">
            ファイルをアップロード
          </b-button>
        </fetching-overlay>
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
              >&times;</b-button
            >
          </div>
          <small class="ellipsis">{{ attachment.original_name }}</small>
        </div>
        <div v-show="filtered_attachments.length < 1">ファイルがありません</div>
      </div>
      <template v-slot:modal-footer>
        <div class="flex-grow-1 flex-shrink-0">
          <div>{{ selected_filename }}</div>
        </div>
        <div>
          <fetching-overlay>
            <b-button @click="handleCancel" size="sm"> キャンセル </b-button>
          </fetching-overlay>
          <fetching-overlay>
            <b-button variant="primary" @click="handleOK"> 決定 </b-button>
          </fetching-overlay>
        </div>
      </template>
    </b-modal>
  </div>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
export default {
  props: {
    id: {},
    name: {},
    type: {},
    value: {},
    only_image: { default: false },
    state: { default: null },
  },
  data() {
    return {
      selected: null,
    };
  },
  created() {
    this.initialize();
  },
  computed: {
    ...mapGetters(["attachments"]),
    button_variant() {
      return this.state === null ? "outline-secondary" : "outline-danger";
    },
    filtered_attachments() {
      const image_filtered = this.only_image
        ? this.attachments.filter((a) => a.type === "image")
        : this.attachments;

      return this.id
        ? image_filtered.filter(
            (a) =>
              a.attachmentable_id === null ||
              (a.attachmentable_id == this.id &&
                a.attachmentable_type === this.type)
          )
        : image_filtered.filter((a) => a.attachmentable_id === null);
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
        return this.attachments.find((a) => a.id == this.selected);
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
        return this.attachments.find((a) => a.id == this.value);
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
      return "ファイル未選択";
    },
  },
  methods: {
    ...mapActions(["fetchAttachments", "storeAttachment", "deleteAttachment"]),
    getFileElement() {
      return this.$refs[this.file_uploader_id];
    },
    getFile() {
      return this.getFileElement().files.item(0);
    },
    initialize() {
      this.selected = this.value || null;

      const el = this.getFileElement();
      if (el) {
        el.value = null;
      }
    },
    // modal controll
    handleShow() {
      if (!this.attachmentsLoaded) {
        this.fetchAttachments();
      }

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
    /**
     * 間接的にファイル要素をクリックさせる
     */
    handleClickUpload() {
      this.getFileElement().click();
    },
    async handleFileChange(e) {
      const file = this.getFile();
      if (file) {
        await this.storeAttachment({
          file,
          type: this.type,
          id: this.id,
          only_image: this.only_image,
        });

        const attachment = this.attachments.find(
          (a) => a.original_name === file.name
        );
        if (attachment) {
          this.selected = attachment.id;
          this.getFileElement().value = null;
        }
      }
    },
    async handleDelete(id) {
      if (window.confirm("削除してよろしいですか？")) {
        if (this.selected == id) {
          this.selected = null;
        }
        this.deleteAttachment(id);
      }
    },
    // style
    selectedClass(attachment_id) {
      return attachment_id == this.selected ? "selected" : "";
    },
  },
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
  background-color: rgb(72, 163, 224, 0.5);
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
