<template>
  <b-form-tags
    v-model="value"
    no-outer-focus
    class="mb-2"
  >
    <template #default="{ tags }">
      <ul
        v-if="tags.length > 0"
        class="list-inline d-inline-block mb-2"
      >
        <li
          v-for="tag in tags"
          :key="tag"
          class="list-inline-item mb-1"
        >
          <b-form-tag
            variant="primary"
            :title="tag"
            @remove="handleRemoveClick(tag)"
          >
            {{ tag }}
          </b-form-tag>
        </li>
      </ul>
      <b-dropdown
        size="sm"
        variant="outline-secondary"
        menu-class="w-100"
        block
        :no-flip="true"
        :dropup="false"
        @shown="handleShown"
      >
        <template #button-content>
          タグを選択する
        </template>
        <b-dropdown-form @submit.stop.prevent="() => {}">
          <b-form-group
            class="mb-0"
            label-for="tag-search-input"
            label-cols-md="auto"
            label-size="sm"
            label="検索"
          >
            <b-input-group>
              <b-form-input
                id="tag-search-input"
                v-model="search"
                type="search"
                size="sm"
                autocomplete="off"
                maxlength="20"
              />
              <b-input-group-append v-if="creatable">
                <fetching-overlay>
                  <b-button
                    variant="primary"
                    size="sm"
                    :disabled="!can_create"
                    @click="handleCreateTagClick"
                  >
                    「{{ criteria }}」を作成して追加
                  </b-button>
                </fetching-overlay>
              </b-input-group-append>
            </b-input-group>
          </b-form-group>
        </b-dropdown-form>
        <b-dropdown-divider />
        <b-dropdown-item-button
          v-for="tag in items"
          :key="tag"
          @click="handleTagClick(tag)"
        >
          {{ tag }}
        </b-dropdown-item-button>
        <b-dropdown-text v-if="items.length === 0">
          該当タグ無し
        </b-dropdown-text>
      </b-dropdown>
    </template>
  </b-form-tags>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
  props: {
    value: {
      type: Array,
      default: () => []
    },
    creatable: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      search: '',
      timer: null
    };
  },
  watch: {
    criteria() {
      this.fetchTimeout();
    }
  },
  created() {},
  computed: {
    ...mapGetters(['tags', 'tagsLoaded']),
    items() {
      return this.tags;
    },
    criteria() {
      return this.search.trim().toLowerCase();
    },
    just_match() {
      return this.tags.find((o) => o === this.criteria);
    },
    can_create() {
      return this.creatable && this.criteria && !this.just_match;
    }
  },
  methods: {
    ...mapActions(['fetchTags', 'storeTag']),
    fetchTimeout() {
      if (this.timer) {
        clearTimeout(this.timer);
      }
      this.timer = setTimeout(() => {
        this.fetchTags(this.criteria);
      }, 500);
    },
    handleShown() {
      if (!this.tagsLoaded) {
        this.fetchTags(this.criteria);
      }
    },
    handleTagClick(option) {
      this.value.push(option);
      this.search = '';
    },
    handleRemoveClick(tagName) {
      const index = this.value.findIndex((v) => v === tagName);
      this.value.splice(index, 1);
    },
    async handleCreateTagClick() {
      await this.storeTag(this.criteria);
      this.value.push(this.criteria);
      this.search = '';
    }
  }
};
</script>
