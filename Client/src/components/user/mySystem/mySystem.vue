<template>
  <div style="background:#e7e7e7;min-height:100vh;">
    <mt-header title="我的设置" style="background:#57D6DD;">
      <router-link to="/user" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
    </mt-header>
    <div class="mySystem">
      <div class="pageContent mt10">
        <div class="sysList">
          <mt-cell title="修改个人资料" to="/personal" is-link>
            <img slot="icon" src="./../../../assets/修改资料图标@2x.png" width="24" height="24">
          </mt-cell>
        </div>
        <div class="sysList">
          <mt-cell title="交易密码" to="/transPsd" is-link>
            <img slot="icon" src="./../../../assets/交易密码图标@2x.png" width="24" height="24">
          </mt-cell>
        </div>
      </div>
      <div class="pageContent mt10">
        <div class="sysList">
          <mt-cell title="好友验证设置">
            <span>
              <mt-switch v-model="frvalidation"></mt-switch>
            </span>
            <img slot="icon" src="./../../../assets/好友验证图标@2x.png" width="24" height="24">
          </mt-cell>
        </div>
        <div class="sysList">
          <mt-cell title="陌生消息">
            <span>
              <mt-switch v-model="stmessage"></mt-switch>
            </span>
            <img slot="icon" src="./../../../assets/陌生消息图标@2x.png" width="24" height="24">
          </mt-cell>
        </div>
        <div class="sysList">
          <mt-cell title="广播消息">
            <span>
              <mt-switch v-model="broadcast"></mt-switch>
            </span>
            <img slot="icon" src="./../../../assets/广播图标@2x.png" width="24" height="24">
          </mt-cell>
        </div>
        <!-- <mt-button class="switchSubmit" size="large" @click="change">提交修改</mt-button> -->
      </div>
    </div>
  </div>
</template>

<script>
console.log("USER_MYSYSTEM_MYSYSTEM_VUE");
import root from "@/config/root.js";
export default {
  data() {
    return {
      frvalidation: this.GLOBAL.userInfo.frvalidation,
      stmessage: this.GLOBAL.userInfo.stmessage,
      broadcast: this.GLOBAL.userInfo.broadcast
    };
  },
  watch: {
    frvalidation: function() {
      this.changeSetting("frvalidation", this.frvalidation);
    },
    stmessage: function() {
      this.changeSetting("stmessage", this.stmessage);
    },
    broadcast: function() {
      this.changeSetting("broadcast", this.broadcast);
    }
  },
  mounted: function() {
    this.checkLogin();
    this.onmessage();
    this.$parent.tabbarShow = false;
    this.$parent.tabbarWhic = false;
  },
  methods: {
    changeSetting(index, value) {
      var strangers = new Array();
      if (index == "stmessage") {
        var j = 0;
        for (var i in this.GLOBAL.friendLists.strange) {
          if (this.GLOBAL.friendLists.strange[i].connectionID) {
            strangers[j] = {
              stranger_id: this.GLOBAL.friendLists.strange[i].id,
              scon_id: this.GLOBAL.friendLists.strange[i].connectionID
            };
            j++;
          }
        }
      }
      let data = {
        type: "settingChange",
        index: index,
        value: value,
        send_id: this.GLOBAL.userInfo.id,
        con_id: this.GLOBAL.connectionId,
        strangers: strangers
      };
      console.log(data);
      let that = this;
      this.senddata({
        data: data,
        callback: function(response) {
          console.log("(**************************)");
          console.log(response);
          console.log("(**************************)");
          if (response.status) {
            that.GLOBAL.userInfo[index] = value;
            localStorage.setItem(
              "bc_userInfo",
              JSON.stringify(that.GLOBAL.userInfo)
            );
          } else {
            if (index == "frvalidation") {
              that.frvalidation = that.GLOBAL.userInfo.frvalidation;
            } else if (index == "stmessage") {
              that.stmessage = that.GLOBAL.userInfo.stmessage;
            } else {
              that.broadcast = that.GLOBAL.userInfo.broadcast;
            }
            that.$forceUpdate();
          }
        },
        handType: "user"
      });
    }
  }
};
</script>

<style scoped>
.pageContent img {
  display: inline-block;
  width: 5vw;
  height: auto;
}
.pageContent {
  text-align: left;
  margin-top: 2vw;
}
.sysList {
  border-top: 0.6vw solid #e7e7e7;
}
.switchSubmit {
  background-color: #57d6dd;
  margin-top: 5vw;
  color: #fff;
  font-size: 4.5vw;
}
.mint-cell-wrapper {
  height: 13vw !important;
}
</style>