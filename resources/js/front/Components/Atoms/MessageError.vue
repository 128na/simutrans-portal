<template>
  <div>
    <b-icon icon="exclamation-triangle" font-scale="1.1" class="mr-1" />
    読み込みに失敗しました。
    <b-button variant="outline-secondary" @click="$emit('reload')">
      <b-icon icon="arrow-clockwise" class="mr-1" />再読み込み
    </b-button>
  </div>
</template>
