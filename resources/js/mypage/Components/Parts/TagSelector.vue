<template>
  <b-form-tags v-model="value" no-outer-focus class="mb-2">
    <template v-slot="{ tags }">
      <ul v-if="tags.length > 0" class="list-inline d-inline-block mb-2">
        <li v-for="tag in tags" :key="tag" class="list-inline-item mb-1">
          <b-form-tag @remove="handleRemoveClick(tag)" :title="tag" variant="primary">{{ tag }}</b-form-tag>
        </li>
      </ul>

      <b-dropdown size="sm" variant="outline-secondary" block menu-class="w-100">
        <template v-slot:button-content>{{$t('Select tag')}}</template>
        <b-dropdown-form @submit.stop.prevent="() => {}">
          <b-form-group
            label-for="tag-search-input"
            :label="$t('Search words')"
            label-cols-md="auto"
            class="mb-0"
            label-size="sm"
          >
            <b-input-group>
              <b-form-input
                v-model="search"
                id="tag-search-input"
                type="search"
                size="sm"
                autocomplete="off"
              ></b-form-input>
              <b-input-group-append v-if="creatable">
                <b-button
                  variant="primary"
                  size="sm"
                  :disabled="!can_create || fetching"
                  @click="handleCreateTagClick"
                >{{$t('Create and add tag "{name}"', {name:criteria})}}</b-button>
              </b-input-group-append>
            </b-input-group>
          </b-form-group>
        </b-dropdown-form>
        <b-dropdown-divider></b-dropdown-divider>
        <b-dropdown-item-button
          v-for="tag in items"
          :key="tag"
          @click="handleTagClick(tag)"
        >{{ tag }}</b-dropdown-item-button>
        <b-dropdown-text v-if="items.length === 0">{{$t('No tags')}}</b-dropdown-text>
      </b-dropdown>
    </template>
  </b-form-tags>
</template>

<script>
import { toastable, api_handlable } from "../../mixins";

export default {
  name: "tag-selector",
  mixins: [toastable, api_handlable],
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
      tags: [],
      search: ""
    };
  },
  created() {
    this.fetchTags();
  },
  watch: {
    criteria() {
      this.fetchTags();
    }
  },
  computed: {
    items() {
      return this.tags;
    },
    criteria() {
      return this.search.trim().toLowerCase();
    },
    just_match() {
      return this.tags.find(o => o === this.criteria);
    },
    can_create() {
      return this.creatable && this.criteria && !this.just_match;
    }
  },
  methods: {
    setTags(tags) {
      this.tags = tags;
    },
    handleTagClick(option) {
      this.value.push(option);
      this.search = "";
    },
    handleRemoveClick(tag_name) {
      const index = this.value.findIndex(v => v === tag_name);
      this.value.splice(index, 1);
    },
    async handleCreateTagClick() {
      await this.storeTag(this.criteria);
      this.value.push(this.criteria);
      this.toastSuccess("Tag Created");
    }
  }
};
</script>
