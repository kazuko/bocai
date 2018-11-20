<template>
  <div>
    <section style="background:#B34B39;height:30vh;position:relative;">
      <mt-header id="header" title="红包详情" fixed="true" style="height:10vw;background: #B34B39 !important;color:#ECD7D4;">
        <div @click="goback" slot="left">
          <mt-button icon="back">返回</mt-button>
        </div>
      </mt-header>
      <div class="red-detail-box">
        <div class="send-user-head-box">
          <img :src="host + redInfo.head" alt="">
        </div>
        <div class="send-user-nickname">{{redInfo.nickname}}</div>
        <div class="send-user-remark">{{redInfo.remark}}</div>
        <div v-if="!redInfo.status&&!redInfo.recived_num " class="send-user-title">红包金额{{redInfo.money}}个，待领取</div>
        <div v-else-if="redInfo.status==1||redInfo.recived_num" class="send-user-title">{{redInfo.recive_num}}个红包，共{{redInfo.money}}元</div>
        <div v-else class="send-user-title">红包金额{{redInfo.money}}个，已过期</div>
        <div v-if="!redInfo.status&&!redInfo.recived_num" class="get-user-info-tips">{{tips}}</div>
        <div v-else-if="redInfo.status==1||redInfo.recived_num" class="get-user-info-box" v-for="(item,index) in redInfo.detail" :key="index">
          <div class="get-user-head-box">
            <img :src="host + item.head" alt="">
          </div>
          <div class="get-user-name-time">
            <p class="nickname">{{item.nickname }}</p>
            <p class="time">{{item.time }}</p>
          </div>
          <div class="get-gold-box">
            <span>{{item.gold }}</span>
            <img src="static/64-金币.png" alt="">
          </div>
        </div>
        <div v-else class="get-user-info-tips">{{redInfo.send_id==this.GLOBAL.userInfo.id?'红包金额已退还至我的账户！':'红包金额已退还至对方的账户！'}}</div>
      </div>
    </section>
  </div>
</template>
<script>
export default {
  data() {
    return {
      redInfo: {},
      connectionchange: this.GLOBAL.connectionId,
      host: this.GLOBAL.Host,
      tips: "24小时后未领取的红包将退还至我的账户中"
    };
  },
  mounted: function() {
    this.$parent.tabbarShow = false;
    let that = this;
    if (!that.$route.query.red_id) {
      that.$messagebox.alert("红包数据已丢失！").then(action => {
        that.$router.push("/chat");
      });
    }
    this.checkLogin(function() {
      that.getRedInfo();
    });
    this.onmessage();
    console.log("red_id:" + this.$route.query.red_id);
  },
  updated: function() {
    // console.log("<<<<<<<<<<" + this.GLOBAL.connectionId + ">>>>>>>>>>>>>>");
    // this.connectionchange = this.GLOBAL.connectionId;
  },
  watch: {
    // connectionchange: function() {
    //   this.getRedInfo();
    // }
  },
  methods: {
    goback() {
      console.log("goback from redbagdetail");
      if (this.GLOBAL.friendInfo.info) {
        this.$router.push("/chat");
      } else if (this.$route.query.chatRoom) {
        this.$router.push("/chatroom");
      } else {
        this.$router.push({
          path: "/redbag",
          query: { flag: !this.$route.query.flag }
        });
      }
    },
    getRedInfo() {
      let data = {
        type: "RedDetail",
        red_id: this.$route.query.red_id,
        send_id: this.GLOBAL.userInfo.id,
        con_id: this.GLOBAL.connectionId
      };
      console.log("***************" + this.connectionId + "******************");
      let that = this;
      this.senddata({
        data: data,
        callback: function(respone) {
          console.log("-------------redDetail----------");
          console.log(respone);
          that.redInfo = respone.result;
          console.log("-------------redDetail----------");
        },
        callbackFlag: "responseRedDetail",
        handType: "user"
      });
    }
  },
  destroyed: function() {}
};
</script>
<style>
.get-user-info-tips {
  height: 20vw;
  line-height: 20vw;
  color: #999;
  font-size: 4.5vw;
}
.red-detail-box {
  position: absolute;
  top: 21vh;
  width: 100vw;
  text-align: center;
}
.send-user-head-box {
  width: 25vw;
  height: 25vw;
  border-radius: 25vw;
  background: #9accf1;
  margin: auto;
  padding: 0px;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
}
.send-user-head-box img {
  /* width: auto; */
  /* height: 100%; */
  max-width: 100%;
  max-height: 100%;
  margin: auto;
  padding: 0px;
}
.send-user-nickname {
  /* border: 1px solid red; */
  height: 10vw;
  line-height: 10vw;
  font-weight: bold;
  color: black;
  font-size: 5vw;
}
.send-user-remark {
  font-size: 5vw;
  font-weight: 500;
  color: #999999;
  height: 10vw;
  /* border: 1px solid red; */
}
.send-user-title {
  text-align: left;
  box-sizing: border-box;
  padding: 0px 1vw;
  border-bottom: 0.5vw solid #eaeaea;
  height: 14vw;
  line-height: 15vw;
  color: #999;
  font-weight: bold;
  font-size: 4.5vw;
}
.get-user-info-box {
  display: flex;
  border-bottom: .5vw solid #eaeaea;
}
.get-user-head-box {
  width: 18vw;
  height: 18vw;
  /* background: red; */
  border-radius: 18vw;
  display: flex;
  justify-content: center;
  align-items: center;
  /* padding: 2vw; */
  background: #92c7f5;
  /* border: 1px solid #888; */
  overflow: hidden;
}
.get-user-head-box img {
  /* width: auto; */
  /* height: 100%; */
  margin: auto;
  padding: 0px;
  max-width: 100%;
  max-height: 100%;
}
.get-user-name-time {
  width: 50vw;
  height: 18vw;
  /* background: pink; */
  text-align: left;
  box-sizing: border-box;
  padding: 0px 2.5vw;
}
.get-user-name-time .nickname {
  font-size: 5vw;
  font-weight: bold;
  color: black;
  height: 10vw;
  line-height: 12vw;
}
.get-user-name-time .time {
  font-size: 4.5vw;
  color: #999;
  height: 9vw;
  line-break: 6vw;
}
.get-gold-box {
  text-align: right !important;
  /* background: yellow; */
  display: flex;
  height: 18vw;
  line-height: 18vw;
  width: 32vw;
}
.get-gold-box span {
  font-size: 5.5vw;
  font-weight: bold;
  color: black;
  width: 23vw;
}
.get-gold-box img {
  width: 7vw;
  height: 7vw;
  margin-top: 5vw;
}
</style>

