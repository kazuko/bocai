<template>
  <div>
    <section style="background:#B34B39;height:30vh;position:relative;">
      <mt-header id="header" title="红包详情" fixed="true" style="height:10vw;background: #B34B39 !important;color:#ECD7D4;">
        <router-link to="/chat" slot="left">
          <mt-button icon="back">返回</mt-button>
        </router-link>
      </mt-header>
      <div class="red-detail-box">
        <div class="send-user-head-box">
          <img :src="host + redInfo.head" alt="">
        </div>
        <div class="send-user-nickname">{{redInfo.nickname}}</div>
        <div class="send-user-remark">{{redInfo.remark}}</div>
        <div class="send-user-title">1个红包，共{{redInfo.money}}元</div>
        <div v-if="redInfo.detail" class="get-user-info-box" v-for="(item,index) in redInfo.detail" :key="index">
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
        <div v-else>{{tips}}</div>
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
      tips: '暂无人领取'
    };
  },
  created: function() {
    this.checkLogin();
    this.onmessage();
  },
  mounted: function() {
    console.log("red_id:" + this.$route.query.red_id);
    if (!this.$route.query.red_id) {
      this.$messagebox.alert("红包数据已丢失！").then(action => {
        this.$router.push("/chat");
      });
    } else {
      console.log(
        "(((((((((((((((" + this.GLOBAL.connectionId + ")))))))))))))))))"
      );
      if (this.GLOBAL.connectionId) {
        this.getRedInfo();
      }
      this.$parent.tabbarShow = false;
    }
  },
  updated: function() {
    console.log("<<<<<<<<<<" + this.GLOBAL.connectionId + ">>>>>>>>>>>>>>");
    this.connectionchange = this.GLOBAL.connectionId;
  },
  watch: {
    connectionchange: function() {
      this.getRedInfo();
    }
  },
  methods: {
    getRedInfo() {
      let data = {
        type: "RedDetail",
        red_id: this.$route.query.red_id,
        send_id: this.GLOBAL.userInfo.id,
        con_id: this.GLOBAL.connectionId
      };
      console.log("***************" + this.connectionId + "******************");
      let that = this;
      this.senddata(data, function(respone) {
        console.log("-------------redDetail----------");
        console.log(respone);
        that.redInfo = respone;
        console.log("-------------redDetail----------");
      });
    }
  },
  destroyed: function() {}
};
</script>
<style>
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
  background: yellow;
  margin: auto;
  padding: 0px;
  overflow: hidden;
}
.send-user-head-box img {
  width: auto;
  height: 100%;
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
  border-bottom: 1px solid #fdc8fd;
  height: 14vw;
  line-height: 15vw;
  color: #999;
  font-weight: bold;
  font-size: 4.5vw;
}
.get-user-info-box {
  display: flex;
  border-bottom: 1px solid #fdc8fd;
}
.get-user-head-box {
  width: 18vw;
  height: 18vw;
  /* background: red; */
  overflow: hidden;
  border-radius: 3vw;
}
.get-user-head-box img {
  width: auto;
  height: 100%;
  margin: auto;
  padding: 0px;
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
  height: 9vw;
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
  margin-top: 4.5vw;
}
</style>

