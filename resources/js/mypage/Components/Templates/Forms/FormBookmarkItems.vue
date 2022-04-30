<template>
  <div>
    <label>ブックマークアイテム一覧</label>
    <draggable
      v-model="items"
      class="mx-3"
    >
      <transition-group>
        <div
          v-for="(item, index) in value"
          :key="item.id"
          class="clickable"
        >
          <div class="d-flex align-items-center">
            <div>{{ icon(item) }} {{ item.title }}</div>
            <b-button
              variant="link"
              class="ml-auto text-danger"
              @click="handleDelete(index)"
            >
              削除
            </b-button>
          </div>
          <b-form-group>
            <b-form-textarea
              v-model="items[index].memo"
              :state="validationState(`bookmarkItems.${index}.memo`)"
            />
            <validation-message :field="`bookmarkItems.${index}.memo`" />
          </b-form-group>
        </div>
      </transition-group>
    </draggable>
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
import draggable from 'vuedraggable';
export default {
  components: {
    draggable
  },
  props: ['value'],
  computed: {
    ...mapGetters(['validationState']),
    items: {
      get() {
        return this.value;
      },
      set(items) {
        this.$emit('input', this.reorder(items));
      }
    }
  },
  methods: {
    icon(item) {
      switch (item.bookmark_itemable_type) {
        case 'App\\Models\\Article':
          return '📄';
        case 'App\\Models\\User\\Bookmark':
          return '🔖';
        case 'App\\Models\\Category':
          return '📁';
        case 'App\\Models\\Tag':
          return '🏷️';
        case 'App\\Models\\User':
          return '👷';
      }
    },
    reorder(items) {
      return items.map((item, index) =>
        Object.assign(item, { order: index + 1 })
      );
    },
    handleDelete(index) {
      if (confirm('削除しますか？')) {
        this.items = this.items.filter((item, i) => i !== index);
      }
    }
  }
};
</script>
