<template>
  <b-navbar class="fixed-left py-2 py-lg-4" type="dark" variant="primary" toggleable="lg">
    <b-navbar-brand class="p-0 mb-lg mb-2 mb-0" :to="toTop">
      {{ appName }}
    </b-navbar-brand>
    <b-navbar-toggle target="global-menu" />
    <b-collapse id="global-menu" is-nav>
      <b-navbar-nav>
        <form-search />
        <div v-for="(addons, pakName) in pakAddonCounts" :key="pakName">
          <toggle-collapse-button :open="isOpen('pak', pakName)" @click="toggleCollapse('pak', pakName)">
            {{pakName}}
          </toggle-collapse-button>
          <b-collapse :id="collapseId('pak', pakName)">
            <b-nav-item v-for="addon in addons" :key="addon.addon" :to="toCategoryPakByAddon(addon)"
              link-classes="py-none">
              {{addon.addon}} ({{addon.count}})
            </b-nav-item>
          </b-collapse>
        </div>
        <div>
          <toggle-collapse-button :open="isOpen('user')" @click="toggleCollapse('user')">
            ユーザー一覧
          </toggle-collapse-button>
          <b-collapse :id="collapseId('user')">
            <b-nav-item v-for="user_addon in userAddonCounts" :key="user_addon.name" :to="toUserByAddon(user_addon)">
              {{user_addon.name}} ({{user_addon.count}})
            </b-nav-item>
          </b-collapse>
        </div>
        <b-nav-item :to="toTags">タグ一覧</b-nav-item>
        <b-dropdown-divider />
        <b-nav-item href="/mypage">マイページ</b-nav-item>
        <b-dropdown-divider />
        <b-nav-item :to="toAbout">サイトの使い方</b-nav-item>
        <b-nav-item :to="toPrivacy">プライバシーポリシー</b-nav-item>
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
import axios from 'axios';
import { routeLink, appInfo } from '../../mixins';
export default {
  mixins: [routeLink, appInfo],
  data() {
    return {
      pakAddonCounts: null,
      userAddonCounts: null,
      toggleStatus: []
    };
  },
  created() {
    this.fetchSidebar();
  },
  methods: {
    async fetchSidebar() {
      const res = await axios.get('/api/v3/front/sidebar');
      if (res.status === 200) {
        this.pakAddonCounts = res.data.pakAddonCounts;
        this.userAddonCounts = res.data.userAddonCounts;
      }
    },
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
