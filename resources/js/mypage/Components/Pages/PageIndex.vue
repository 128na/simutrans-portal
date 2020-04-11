<template>
  <div>
    <h1>{{$t('Mypage')}}</h1>
    <div v-if="!user.verified">
      <form-verify :user="user" />
    </div>
    <div v-else>
      <b-form-group>
        <b-button variant="primary" :to="to_analytics">
          <b-icon icon="graph-up" class="mr-1" />
          {{$t('Access Analytics')}}
        </b-button>
      </b-form-group>
      <b-form-group>
        <b-button variant="primary" :to="to_addon_post">
          <icon-edit-article class="mr-1" />
          {{
          $t('Create {post_type}', {post_type:$t('post_types.addon-post')})
          }}
        </b-button>
        <b-button variant="primary" :to="to_addon_introduction">
          <icon-edit-article class="mr-1" />
          {{
          $t('Create {post_type}', {post_type:$t('post_types.addon-introduction')})
          }}
        </b-button>
        <b-button variant="primary" :to="to_page">
          <icon-edit-article class="mr-1" />
          {{
          $t('Create {post_type}', {post_type:$t('post_types.page')})
          }}
        </b-button>
        <b-button variant="primary" :to="to_markdown">
          <icon-edit-article class="mr-1" />
          {{
          $t('Create {post_type}', {post_type:$t('post_types.markdown')})
          }}
        </b-button>
      </b-form-group>
      <article-table
        :articles="articles"
        :user="user"
        @update:articles="$emit('update:articles', $event)"
      />
    </div>
  </div>
</template>
<script>
import { linkable } from "../../mixins";
export default {
  props: ["articles", "user"],
  mixins: [linkable]
};
</script>
