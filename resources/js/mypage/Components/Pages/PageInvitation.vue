<template>
  <div>
    <page-title>ユーザー招待</page-title>
    <page-description>
      このサイトへユーザーを招待、登録できるようにします。<br />
      ユーザーを招待にするには招待URLを生成し、招待したいユーザーにURLを伝えてください。<br />
    </page-description>
    <page-sub-title>注意事項</page-sub-title>
    <page-description>
      <ul>
        <li>招待URLは招待者のみに共有してください</li>
        <li>SNSなどに誰でも見れる場所への招待URLの公開は禁止です</li>
        <li>
          URLを再生成、削除すると以前の招待URLからはユーザー登録できなくなります
        </li>
        <li>
          招待されたユーザーに何らかの問題がある場合、招待したユーザーのアカウントが制限されることがあります
        </li>
      </ul>
    </page-description>

    <page-sub-title>招待URL</page-sub-title>
    <b-form-group>
      <b-input-group>
        <b-input-group-prepend v-if="user.invitation_url">
          <b-button variant="outline-secondary" @click="handleCopy">
            <b-icon icon="clipboard-data" />
          </b-button>
        </b-input-group-prepend>
        <b-form-input
          :value="user.invitation_url || '未生成'"
          :readonly="true"
        />
        <b-input-group-append>
          <template v-if="user.invitation_url">
            <b-button variant="primary" @click="updateInvitationCode">
              再生成
            </b-button>
            <b-button variant="danger" @click="deleteInvitationCode">
              削除
            </b-button>
          </template>
          <template v-else>
            <b-button variant="primary" @click="updateInvitationCode">
              生成
            </b-button>
          </template>
        </b-input-group-append>
      </b-input-group>
    </b-form-group>
    <page-sub-title>招待したユーザー</page-sub-title>
    <template v-if="hasInvites">
      <ul>
        <li v-for="invite in invites">{{ invite.name }}</li>
      </ul>
    </template>
    <template v-else> 招待したユーザーはいません。 </template>
  </div>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import { validateVerified } from "../../mixins/auth";
import api from "../../api";
export default {
  mixins: [validateVerified],
  data() {
    return {
      invites: null,
    };
  },
  created() {
    this.fetch();
  },
  computed: {
    ...mapGetters(["isVerified", "user"]),
    hasInvites() {
      return this.invites && this.invites.length;
    },
  },
  methods: {
    ...mapActions([
      "updateInvitationCode",
      "deleteInvitationCode",
      "setInfoMessage",
    ]),
    async fetch() {
      const res = await api.fetchInvites();
      if (res.status !== 200) {
        return;
      }
      this.invites = res.data.data;
    },
    handleCopy() {
      this.$copyText(this.user.invitation_url);
      this.setInfoMessage({
        message: "クリップボードにコピーしました",
      });
    },
  },
};
</script>
