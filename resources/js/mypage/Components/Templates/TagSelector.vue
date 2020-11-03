<template>
  <b-form-tags v-model="value" no-outer-focus class="mb-2">
    <template v-slot="{ tags }">
      <ul v-if="tags.length > 0" class="list-inline d-inline-block mb-2">
        <li v-for="tag in tags" :key="tag" class="list-inline-item mb-1">
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
      >
        <template v-slot:button-content>{{ $t("Select tag") }}</template>
        <b-dropdown-form @submit.stop.prevent="() => {}">
          <b-form-group
            class="mb-0"
            label-for="tag-search-input"
            label-cols-md="auto"
            label-size="sm"
            :label="$t('Search words')"
          >
            <b-input-group>
              <b-form-input
                id="tag-search-input"
                type="search"
                size="sm"
                autocomplete="off"
                v-model="search"
              ></b-form-input>
              <b-input-group-append v-if="creatable">
                <fetching-overlay>
                  <b-button
                    variant="primary"
                    size="sm"
                    :disabled="!can_create"
                    @click="handleCreateTagClick"
                  >
                    {{ $t('Create and add tag "{name}"', { name: criteria }) }}
                  </b-button>
                </fetching-overlay>
              </b-input-group-append>
            </b-input-group>
          </b-form-group>
        </b-dropdown-form>
        <b-dropdown-divider></b-dropdown-divider>
        <b-dropdown-item-button
          v-for="tag in items"
          :key="tag"
          @click="handleTagClick(tag)"
        >
          {{ tag }}
        </b-dropdown-item-button>
        <b-dropdown-text v-if="items.length === 0">
          {{ $t("No tags") }}
        </b-dropdown-text>
      </b-dropdown>
    </template>
  </b-form-tags>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "tag-selector",
  props: {
    value: {
      type: Array,
      default: () => [],
    },
    creatable: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      search: "",
    };
  },
  created() {
    if (!this.tagsLoaded) {
      this.$store.dispatch("fetchTags", this.criteria);
    }
  },
  watch: {
    criteria() {
      this.$store.dispatch("fetchTags", this.criteria);
    },
  },
  computed: {
    ...mapGetters(["tags", "tagsLoaded"]),
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
    },
  },
  methods: {
    ...mapActions(["fetchTags", "storeTag"]),
    handleTagClick(option) {
      this.value.push(option);
      this.search = "";
    },
    handleRemoveClick(tag_name) {
      const index = this.value.findIndex((v) => v === tag_name);
      this.value.splice(index, 1);
    },
    async handleCreateTagClick() {
      await this.$store.dispatch("storeTag", this.criteria);
      this.value.push(this.criteria);
      this.search = "";
    },
  },
};
</script>
