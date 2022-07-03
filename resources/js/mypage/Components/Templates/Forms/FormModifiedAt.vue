<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        更新日時
      </template>
      <b-form-checkbox :value="without_update_modified_at" @input="$emit('input', $event.target.value)">
        記事保存時に更新日時を更新しない
      </b-form-checkbox>
    </b-form-group>
  </div>
</template>
<script>
export default {
  props: ['withoutUpdateModifiedAt']
};
</script>
