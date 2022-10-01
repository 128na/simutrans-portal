<template>
  <q-list>
    <q-tree :nodes="items" no-connectors node-key="key" v-model:expanded="expanded" v-model:selected="selected">
      <template v-slot:default-header="prop">
        <span class="front-menu-link cursor-pointer">
          {{prop.node.label}}
        </span>
      </template>
    </q-tree>
    <q-separator />
    <MetaLinks />
  </q-list>

</template>

<script>
import { defineComponent, ref } from 'vue';
import { useRouter } from 'vue-router';
import { api } from '../../boot/axios';
import { routeTo } from '../../composables/routeShortcut';
import MetaLinks from './MetaLinks.vue';

export default defineComponent({
  name: 'FrontMenu',
  setup() {
    const router = useRouter();
    const handler = routeTo(router);
    const baseItems = [
      {
        key: 'loading',
        label: 'Loading...',
        children: [],
      },
      {
        key: 'tags',
        label: 'タグ一覧',
        handler,
        to: { name: 'tags' },
      },
      {
        key: 'mypage',
        label: 'マイページ',
        handler,
        to: { name: 'mypage' },
      },
      {
        key: 'about',
        label: 'サイトの使い方',
        handler,
        to: { name: 'show', params: { slug: 'about' } },
      },
      {
        key: 'privacy',
        label: 'プライバシーポリシー',
        handler,
        to: { name: 'show', params: { slug: 'privacy' } },
      },
    ];
    const toPakAddonItem = (pakAddon) => pakAddon.map((p) => ({
      key: `pak-addon-${p.pak_slug}-${p.addon_slug}`,
      label: `${p.addon} (${p.count})`,
      handler,
      to: { name: 'categoryPak', params: { size: p.pak_slug, slug: p.addon_slug } },
    }));
    const toPakAddonItems = (pakAddonCounts) => Object.keys(pakAddonCounts).map((k) => ({
      key: `pak-addon-${k}`,
      label: k,
      handler,
      selectable: false,
      children: toPakAddonItem(pakAddonCounts[k]),
    }));
    const toUserAddonItem = (pakAddonCounts) => ({
      key: 'users',
      label: 'ユーザー一覧',
      selectable: false,
      children: pakAddonCounts.map((p) => ({
        key: `users-${p.user_id}`,
        label: `${p.name} (${p.count})`,
        to: { name: 'user', params: { id: p.user_id } },
      })),
    });
    const fetch = async (items) => {
      const res = await api.get('/api/v3/front/sidebar');
      if (res.status === 200) {
        items.value.splice(0, 1, ...toPakAddonItems(res.data.pakAddonCounts), toUserAddonItem(res.data.userAddonCounts));
      }
    };
    const expanded = ref([]);
    const selected = ref('');
    const items = ref(baseItems);
    fetch(items);
    return {
      expanded,
      selected,
      items,
    };
  },
  components: { MetaLinks },
});
</script>
<style lang="scss">
.q-tree__node--selected .front-menu-link {
  color: $primary;
  font-weight: bold;
}
</style>
