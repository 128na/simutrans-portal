import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useNotify } from 'src/composables/notify';

const api = useMypageApi();
const createAddonPost = () => ({
  post_type: 'addon-post',
  title: '',
  slug: '',
  status: 'draft',
  contents: {
    thumbnail: null,
    author: '',
    description: '',
    file: null,
    license: '',
    thanks: '',
  },
  categories: [],
  tags: [],
  published_at: null,
});
const createAddonIntroduction = () => ({
  post_type: 'addon-introduction',
  title: '',
  slug: '',
  status: 'draft',
  contents: {
    thumbnail: null,
    agreement: false,
    exclude_link_check: false,
    author: '',
    description: '',
    license: '',
    link: '',
    thanks: '',
  },
  categories: [],
  tags: [],
  published_at: null,
});
const createPage = () => ({
  post_type: 'page',
  title: '',
  slug: '',
  status: 'draft',
  contents: {
    thumbnail: null,
    sections: [{ type: 'text', text: '' }],
  },
  categories: [],
  published_at: null,
});
const createMarkdown = () => ({
  post_type: 'markdown',
  title: '',
  slug: '',
  status: 'draft',
  contents: {
    thumbnail: null,
    markdown: '',
  },
  categories: [],
  published_at: null,
});
const unloadListener = (event) => {
  event.preventDefault();
  event.returnValue = '';
};

const createTextSection = () => ({ type: 'text', text: '' });
const createCaptionSection = () => ({ type: 'caption', caption: '' });
const createUrlSection = () => ({ type: 'url', url: '' });
const createImageSection = () => ({ type: 'image', id: null });
const createSection = (type) => {
  switch (type) {
    case 'text':
      return createTextSection();
    case 'caption':
      return createCaptionSection();
    case 'url':
      return createUrlSection();
    case 'image':
      return createImageSection();
    default:
      throw new Error('invalid type');
  }
};

export const useArticleEditStore = defineStore('articleEdit', () => {
  // 記事変更検知用
  let original = null;
  // article
  const article = ref(null);
  const options = ref(null);
  const withoutUpdateModifiedAt = ref(false);
  const tweet = ref(false);
  const setArticle = (a) => {
    article.value = JSON.parse(JSON.stringify(a));
    original = JSON.stringify(a);
  };
  const createArticle = (postType) => {
    switch (postType) {
      case 'addon-introduction':
        return setArticle(createAddonIntroduction());
      case 'addon-post':
        return setArticle(createAddonPost());
      case 'page':
        return setArticle(createPage());
      case 'markdown':
        return setArticle(createMarkdown());
      default:
        throw new Error('invalid post type');
    }
  };
  const notify = useNotify();
  const saveArticle = async () => {
    const params = {
      article: article.value,
      should_tweet: tweet.value,
    };
    const res = await api.createArticle(params);
    notify.success('保存しました');
    window.removeEventListener('beforeunload', unloadListener);

    return res.data.data;
  };

  // article page
  const addSection = (type) => {
    const section = createSection(type);
    article.value.contents.sections.push(section);
  };
  const changeSecstionOrder = (target, dest) => {
    const sections = [...article.value.contents.sections];
    [sections[target], sections[dest]] = [article.value.contents.sections[dest], article.value.contents.sections[target]];
    article.value.contents.sections = sections;
  };
  const deleteSection = (index) => {
    // eslint-disable-next-line no-alert
    if (window.confirm('削除しますか？')) {
      article.value.contents.sections.splice(index, 1);
    }
  };

  watch(article, (v) => {
    const current = JSON.stringify(v);
    if (original !== current) {
      window.addEventListener('beforeunload', unloadListener);
    } else {
      window.removeEventListener('beforeunload', unloadListener);
    }
  }, { deep: true });

  // option
  const statuses = computed(() => options.value?.statuses);
  const categories = computed(() => options.value?.categories);
  const canReservation = computed(() => article.value?.published_at === null || article.value?.status === 'reservation');
  const ready = computed(() => article.value && options.value);
  const fetchOptions = () => api.fetchOptions()
    .then((res) => { options.value = res.data; })
    .catch(() => {
      notify.failedRetryable('カテゴリ一覧取得に失敗しました', fetchOptions);
    });

  // preview
  const split = ref(50);
  const togglePreview = () => {
    split.value = split.value ? 0 : 50;
  };

  // category
  const getCategory = computed(() => (id) => options.value?.categories?.addon?.find((c) => c.id === id)
    || options.value?.categories?.license?.find((c) => c.id === id)
    || options.value?.categories?.page?.find((c) => c.id === id)
    || options.value?.categories?.pak?.find((c) => c.id === id)
    || options.value?.categories?.pak128_position?.find((c) => c.id === id));
  const pak128CategoryId = computed(() => options.value?.categories?.pak?.find((c) => c.name === 'Pak128')?.id);
  const includesPak128 = computed(() => article.value?.categories?.some((c) => c.id === pak128CategoryId.value));
  const pak = computed(() => options.value?.categories?.pak?.map((c) => Object.create({ label: c.name, value: c.id })));
  const addon = computed(() => options.value?.categories?.addon?.map((c) => Object.create({ label: c.name, value: c.id })));
  const pak128Position = computed(() => options.value?.categories?.pak128_position?.map((c) => Object.create({ label: c.name, value: c.id })));
  const license = computed(() => options.value?.categories?.license?.map((c) => Object.create({ label: c.name, value: c.id })));

  return {
    article,
    tweet,
    withoutUpdateModifiedAt,
    canReservation,
    setArticle,
    createArticle,
    saveArticle,
    addSection,
    changeSecstionOrder,
    deleteSection,

    options,
    statuses,
    fetchOptions,
    ready,

    split,
    togglePreview,

    categories,
    getCategory,
    pak128CategoryId,
    includesPak128,
    pak,
    addon,
    pak128Position,
    license,
  };
});
