<template>
  <div>
    <div class="hall">
      <div class="header">
        <div class="logo">
          <img v-if="this.GLOBAL.appInfo.logo_src"
               :src="this.GLOBAL.logoHost+this.GLOBAL.appInfo.logo_src"
               alt="">
          <span v-else>LOGO</span>
          <!-- <span>{{this.GLOBAL.appInfo.app_name}}</span> -->
        </div>
        <div class="header-usr"
             v-if="this.GLOBAL.userInfo.nickname">
          {{this.GLOBAL.userInfo.nickname}},欢迎您
        </div>
        <div class="header-btn"
             v-else>
          <mt-button class="regist-btn"
                     @click="toRegist">注册</mt-button>
          <mt-button class="login-btn"
                     @click="toLogin">登录</mt-button>
        </div>
      </div>
      <div class="banner">
        <img src="./../assets/游戏banner1.jpg">
      </div>
      <div class="radio pageContent">
        <img src="./../assets/喇叭@2x.png">
        <div class="radio-text">
          <transition-group name="radio-list">
            <span class="radio-list"
                  v-for="item in this.GLOBAL.sysNews"
                  :key="item">{{item}}</span>
          </transition-group>
        </div>
      </div>
      <div class="pageContent mt10">
        <div class="module">
          <div class="title"
               style="margin-top: 0px;">棋牌游戏</div>
          <div class="game-list">
            <div class="game-box">
              <div class="game-img">
                <img src="./../assets/形状3@2x.png">
              </div>
              <div class="game">
                <div class="game-name">百家乐</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </div>
            <router-link class="game-box"
                         to="/sgHall">
              <div class="game-img">
                <img src="./../assets/椭圆4拷贝3@2x.png">
              </div>
              <div class="game">
                <div class="game-name">三公</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </router-link>
          </div>
        </div>
        <div class="module mt20 HOTAGME">
          <div class="title">热门彩票</div>
          <div class="game-list game-caipiao">
            <div class="game-box">
              <div class="game-img">
                <img src="./../assets/21@2x.png">
              </div>
              <div class="game">
                <div class="game-name">重庆时时彩</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </div>
            <router-link to="/6hecai/"
                         class="game-box">
              <div class="game-img">
                <img src="./../assets/形状4拷贝@2x.png">
              </div>
              <div class="game">
                <div class="game-name">香港六合彩</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </router-link>
          </div>
          <div class="game-list game-caipiao">
            <div class="game-box">
              <div class="game-img">
                <img src="./../assets/10@2x.png">
              </div>
              <div class="game">
                <div class="game-name">北京PK10</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </div>
            <router-link to="/11xuan5"
                         class="game-box">
              <div class="game-img">
                <img src="./../assets/形状4拷贝@2x.png">
              </div>
              <div class="game">
                <div class="game-name">广东11选5</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </router-link>
          </div>
          <div class="game-list game-caipiao">
            <div class="game-box">
              <div class="game-img">
                <img src="./../assets/组5@2x.png">
              </div>
              <div class="game">
                <div class="game-name">江苏快3</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </div>
            <div class="game-box">
              <div class="game-img">
                <img src="./../assets/彩@2x.png">
              </div>
              <div class="game">
                <div class="game-name">分分彩</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </div>
          </div>
        </div>
        <div class="module mt20">
          <div class="title">体育竞技</div>
          <div class="game-list">
            <div class="game-box">
              <div class="game-img">
                <img src="./../assets/组7@2x.png">
              </div>
              <div class="game">
                <div class="game-name">足球</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// console.log("HALL_VUE");
// import root from "@/config/root.js";
export default {
  created: function() {
    document.getElementsByTagName("body")[0].className = "bgc-fff";
  },
  methods: {
    toLogin: function() {
      this.$router.push("/login");
    },
    toRegist: function() {
      this.$router.push("/regist");
    },
    checkSocket: function() {
      if (!this.GLOBAL.socketHand || !this.GLOBAL.socketStatus) {
        this.createSocket();
        this.onmessage();
      } else {
        if (this.GLOBAL.userInfo.id && !this.GLOBAL.loginStatus) {
          // 请求未读消息列表和好友列表
          let data = {
            con_id: this.GLOBAL.connectionId,
            send_id: this.GLOBAL.userInfo.id,
            nickname: this.GLOBAL.userInfo.nickname,
            type: "handle",
            rType: 1,
            fType: 0 // 标志是否为好友消息、陌生人消息，和user_message表的status对应
          };
          this.senddata(data);
        }
      }
    }
  },
  mounted: function() {
    this.checkSocket();

    if (this.GLOBAL.gameSocketHand) {
      this.GLOBAL.gameSocketHand.close();
      this.GLOBAL.gameSocketHand = "";
    }
    this.GLOBAL.gameOrNot = false;
    // 通过api获取app信息
    // this.$axios.post(root.appInfo).then(Response => {
    //   this.GLOBAL.appInfo = Response.data.appInfo;
    //   this.GLOBAL.sysNews = Response.data.sysNews;
    // });
    this.$parent.tabbarShow = true;
    this.$parent.tabbarWhic = true;
  },
  updated: function() {
    // this.checkSocket();
  },
  beforeDestroy: function() {
    document.body.removeAttribute("class", "bgc-fff");
  }
};
</script>

<style scoped>
.header {
  display: flex;
  justify-content: space-between;
  height: 10vw;
  line-height: 10vw;
  font-size: 4vw;
}
.header-btn {
  padding-right: 3vw;
}
.header-usr {
  padding-right: 3vw;
  color: #ffba00;
}
.regist-btn,
.login-btn {
  height: 7vw;
  border-radius: 3px;
  font-size: 3vw;
  color: #fff;
  width: 14vw;
  margin: 0 3px;
}
.regist-btn {
  background: -webkit-linear-gradient(#debf26, #ff8800); /* Safari 5.1 - 6.0 */
  background: -o-linear-gradient(#debf26, #ff8800); /* Opera 11.1 - 12.0 */
  background: -moz-linear-gradient(#debf26, #ff8800); /* Firefox 3.6 - 15 */
  background: linear-gradient(#debf26, #ff8800); /* 标准的语法 */
}
.login-btn {
  background-color: #0e56e8;
  background: -webkit-linear-gradient(#7a96ea, #0e56e8); /* Safari 5.1 - 6.0 */
  background: -o-linear-gradient(#7a96ea, #0e56e8); /* Opera 11.1 - 12.0 */
  background: -moz-linear-gradient(#7a96ea, #0e56e8); /* Firefox 3.6 - 15 */
  background: linear-gradient(#7a96ea, #0e56e8); /* 标准的语法 */
}
.banner {
  height: 32vw;
}
.banner img {
  width: 100%;
  height: 100%;
}

.module {
  height: 24vw;
}
.HOTAGME {
  height: 56vw;
}
.radio {
  height: 10vw;
  line-height: 10vw;
  text-align: left;
  padding: 0 4vw;
  display: flex;
  flex-wrap: nowrap;
}
.radio img {
  margin: 2.5vw 3vw 0 0;
  height: 5vw;
  width: auto;
}
.radio-text {
  overflow: hidden;
  height: 10vw;
  line-height: 10vw;
}

.radio-text span {
  height: 100%;
  position: relative;
  font-size: 3.5vw;
  margin-right: 30px;
}
.title {
  font-size: 5vw;
  font-weight: 600;
  text-align: left;
  height: 8vw;
}
.game-list {
  display: flex;
  justify-content: space-between;
  /* margin-top: 15px; */
  height: 16vw;
}
.game-img {
  /* width: 10vw; */
  /* height: 10vw; */
  margin: auto 0px;
}
.game-img img {
  width: 13vw;
  height: auto;
}
.game-box {
  width: 48%;
  display: flex;
  flex-wrap: nowrap;
  /* font-size: 14px; */
  /* height: 50px; */
  /* line-height: 50px; */
}
.game {
  text-align: left;
  /* line-height: 5vw; */
  padding-left: 10px;
}
.game-caipiao .game {
  padding: 0 0 0 10px;
}
.game-name {
  color: #000;
  height: 7.5vw;
  line-height: 10vw;
  font-size: 4vw;
}
.game-info {
  height: 7.5vw;
  line-height: 7.5vw;
  font-size: 3vw;
  color: #999;
}
a {
  display: block;
}
.logo {
  /* position: relative; */
  width: 50vw;
  text-align: left;
}
.logo img {
  height: 100%;
  widows: auto;
  margin-left: 4vw;
}
</style>
