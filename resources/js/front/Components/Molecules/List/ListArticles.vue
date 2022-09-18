<template>
  <div class="articles">
    <article class="mb-4" v-for="a in articles" :key="a.id">
      <div class="article-header border-bottom mb-1">
        <router-link :to="{name:'show', params:{slug:a.slug}}">
          <strong>{{ a.title }}</strong>
        </router-link>
        <div class="article-main d-flex mb-1">
          <router-link :to="{name:'show', params:{slug:a.slug}}">
            <list-thumbnail :article="a" />
          </router-link>
          <span class="pl-2 article-description">
            {{ description(a) }}
          </span>
        </div>
        <div class="article-footer">
          <content-categories :article="a" />
          <content-tags :article="a" />
          <content-user :user="a.user" />
          <content-meta :article="a" />
        </div>
      </div>
    </article>
  </div>
</template>
<script>
import { render, sanitizeAll } from '../../../plugins/markdownParser';
const sectionTextableTypes = ['caption', 'text', 'url'];

export default {
  props: {
    articles: {
      type: Array,
      required: false
    }
  },
  methods: {
    handlePage(sections) {
      return this.trimed(sections
        .filter(s => sectionTextableTypes.includes(s.type))
        .map(s => s[s.type])
        .join('')
      );
    },
    description(article) {
      switch (article.post_type) {
        case 'page':
          return this.handlePage(article.contents.sections);
        case 'markdown':
          return this.trimed(sanitizeAll(render(article.contents.markdown)));
        default:
          return this.trimed(article.contents.description);
      }
    },
    trimed(str, len = 100) {
      return str.length > len ? `${str.substring(0, len)}...` : str;
    }
  }
};
</script>
