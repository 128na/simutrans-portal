<template>
  <div>
    <page-title>認証管理</page-title>
    <page-description>
      PortalAPIへアクセス可能な認証クライアント・アクセストークンを管理できます。
    </page-description>

    <page-sub-title>認証クライアント</page-sub-title>
    <page-description>
      PortalAPIへアクセス可能な認証クライアントを作成できます。<br />
      認証クライアントは自身以外のユーザーがPortalAPIを利用する場合に使用します。
    </page-description>
    <manage-client class="ml-3 mb-5" />

    <page-sub-title>アクセストークン</page-sub-title>
    <page-description>
      PortalAPIへアクセス可能な認証クライアントのアクセストークンを管理できます。
    </page-description>
    <manage-token class="ml-3 mb-5" :scopes="scopes" />

    <page-sub-title>パーソナルアクセストークン (PAT)</page-sub-title>
    <page-description>
      PortalAPIへアクセス可能なパーソナルアクセストークン
      (PAT)を作成できます。<br />
      PATは発行者自身のデータにのみアクセス可能です。
    </page-description>
    <manage-personal-access-token class="ml-3 mb-5" :scopes="scopes" />
  </div>
</template>
<script>
import axios from "axios";
export default {
  data() {
    return {
      scopes: [],
    };
  },
  created() {
    this.fetchScopes();
  },
  methods: {
    async fetchScopes() {
      const res = await axios.get("/oauth/scopes");
      this.scopes = res.data;
    },
  },
};
</script>
