<template>
  <b-navbar class="fixed-left py-2 py-lg-4" type="dark" variant="primary" toggleable="lg">
    <b-navbar-brand class="p-0 mb-lg mb-2 mb-0" href="/">
      {{ appName }}
    </b-navbar-brand>
    <b-navbar-toggle target="global-menu" />
    <b-collapse id="global-menu" is-nav>
      <b-navbar-nav>
        <form class="form-inline my-2 mt-lg-0" action="/search" method="GET">
          <b-input-group>
            <b-form-input name="word" type="search" placeholder="検索" required />
            <b-input-group-append>
              <b-button variant="outline-light" type="submit">検索</b-button>
            </b-input-group-append>
          </b-input-group>
        </form>
        <div v-for="(addons, pakName) in pak_addon_counts" :key="pakName">
          <b-nav-item active class="togglable" link-classes="d-inline-block"
            :class="{'togglable-close': isOpen('pak', pakName)}" @click="toggleCollapse('pak', pakName)">
            {{pakName}}
          </b-nav-item>
          <b-collapse :id="collapseId('pak', pakName)">
            <b-nav-item active v-for="addon in addons" :key="addon.addon" :to="toCategoryByAddon(addon)">
              {{addon.addon}} ({{addon.count}})
            </b-nav-item>
          </b-collapse>
        </div>
        <div>
          <b-nav-item active class="togglable" link-classes="d-inline-block"
            :class="{'togglable-close': isOpen('user')}" @click="toggleCollapse('user')">
            ユーザー一覧
          </b-nav-item>
          <b-collapse :id="collapseId('user')">
            <b-nav-item active v-for="user_addon in user_addon_counts" :key="user_addon.name"
              :to="toUserByAddon(user_addon)">
              {{user_addon.name}} ({{user_addon.count}})
            </b-nav-item>
          </b-collapse>
        </div>
        <b-nav-item active :to="toTags">タグ一覧</b-nav-item>
        <b-nav-item active :to="toAdvancedSearch">詳細検索</b-nav-item>
        <b-dropdown-divider />
        <b-nav-item active href="/mypage">マイページ</b-nav-item>
        <b-dropdown-divider />
        <b-nav-item active :to="toAbout">サイトの使い方</b-nav-item>
        <b-nav-item active :to="toPrivacy">プライバシーポリシー</b-nav-item>
        <b-nav-text>
          <small class="d-block mb-1 text-white">
            {{ appName }}
            v{{ appVersion }}
          </small>
          <small class="d-block mb-1 text-white">
            © 2020 <a class="text-white" href="https://twitter.com/128Na" target="_blank"
              rel="noopener nofollow">@128Na</a>
            /
            <a class="text-white" href="https://github.com/128na/simutrans-portal" target="_blank"
              rel="noopener nofollow">GitHub</a>
          </small>
          <small class="d-block mb-1 text-white">
            <a class="text-white" href="/feed">
              <img src="/storage/default/feed.png" class="feed-icon mr-1">Atom</a>
          </small>
        </b-nav-text>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>
<script>
import { routeLink, appInfo } from '../../mixins';
export default {
  mixins: [routeLink, appInfo],
  props: {
    pak_addon_counts: {
      type: Object,
      default: () => Object.create()
    },
    user_addon_counts: {
      type: Array,
      default: () => Array.create()
    }
  },
  data() {
    return {
      toggleStatus: []
    };
  },
  methods: {
    collapseId(prefix, name = '') {
      return `front_menu_${prefix}_${name}`;
    },
    isOpen(prefix, name = '') {
      return this.toggleStatus.includes(this.collapseId(prefix, name));
    },
    toggleCollapse(prefix, name = '') {
      const key = this.collapseId(prefix, name);
      this.$root.$emit('bv::toggle::collapse', key);
      this.updateToggleStatus(key);
    },
    updateToggleStatus(key) {
      const index = this.toggleStatus.indexOf(key);
      if (index === -1) {
        this.toggleStatus.push(key);
      } else {
        this.toggleStatus.splice(index, 1);
      }
    }
  }
};
</script>
<style lang="scss" scoped>
.togglable {
  &::before {
    content: "";
    display: inline-block;
    vertical-align: baseline;
    margin-bottom: 0.2rem;
    border-right: 0.3em solid transparent;
    border-left: 0.3em solid transparent;
    border-bottom: 0.3em solid var(--white);
    border-top: none;
  }

  &.togglable-close {
    &::before {
      border-bottom: none;
      border-top: 0.3em solid var(--white);
    }
  }
}
</style>
