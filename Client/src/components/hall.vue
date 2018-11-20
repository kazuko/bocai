<template>
  <div style="padding-bottom:18vw;background:white;">
    <!-- <radioText :list="radioText"></radioText> -->
    <div class="hall">
      <div class="header">
        <div class="logo">
          <img v-if="this.GLOBAL.appInfo.logo_src" :src="this.GLOBAL.logoHost+this.GLOBAL.appInfo.logo_src" alt="">
          <span v-else>LOGO</span>
          <!-- <span>{{this.GLOBAL.appInfo.app_name}}</span> -->
        </div>
        <div class="header-usr" v-if="this.GLOBAL.userInfo.nickname">
          {{this.GLOBAL.userInfo.nickname}},欢迎您
        </div>
        <div class="header-btn" v-else>
          <mt-button class="regist-btn" @click="toRegist" style="font-size:3vw !important;">注册</mt-button>
          <mt-button class="login-btn" @click="toLogin" style="font-size:3vw !important;">登录</mt-button>
        </div>
      </div>
      <div class="banner">
        <img src="./../assets/游戏banner1.jpg">
      </div>
      <div class="radio pageContent hall-radio">
        <img src="./../assets/喇叭@2x.png">
        <div class="radio-text hall-radio-box">
          <div v-if="Object.keys(system_news).length" style="width:100%" class="system-news-list">
            <span id="transitionbox">
              <span v-for="(item, index) in system_news" :key="index">&nbsp;&nbsp;&nbsp;&nbsp;{{index+1}}、{{item}}</span>
            </span>
            <!-- <span id="transitionbox1">
              <span v-for="(item, index) in system_news" :key="index">&nbsp;&nbsp;&nbsp;&nbsp;{{index+1}}、{{item}}</span>
            </span> -->
          </div>
          <span v-else class="radio-list">暂无公告！</span>
        </div>
      </div>
      <div class="pageContent mt10">
        <div class="border-box"></div>
        <div class="module">
          <div class="title" style="margin-top: 0px;">棋牌游戏</div>
          <div class="game-list">
            <router-link class="game-box" to="">
              <div class="game-img">
                <img src="static/gameIcon/baijiale.png">
              </div>
              <div class="game">
                <div class="game-name">百家乐</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </router-link>
            <router-link class="game-box" to="/sgHall">
              <div class="game-img">
                <img src="static/gameIcon/sangong.png">
              </div>
              <div class="game">
                <div class="game-name">三公</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </router-link>
          </div>
        </div>
        <div class="border-box"></div>
        <div class="module mt20 HOTAGME">
          <div class="title">热门彩票</div>
          <div class="game-list game-caipiao">
            <router-link class="game-box" to="">
              <div class="game-img">
                <img src="static/gameIcon/cqssc.png">
              </div>
              <div class="game">
                <div class="game-name">重庆时时彩</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </router-link>
            <router-link class="game-box" to="/6hecai/">
              <div class="game-img">
                <img src="static/gameIcon/liuhecai.png">
              </div>
              <div class="game">
                <div class="game-name">香港六合彩</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </router-link>
          </div>
          <div class="game-list game-caipiao">
            <router-link class="game-box" to="/bjpk10">
              <div class="game-img">
                <img src="static/gameIcon/pk10.png">
              </div>
              <div class="game">
                <div class="game-name">北京PK10</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </router-link>
            <router-link class="game-box" to="/11xuan5">
              <div class="game-img">
                <img src="static/gameIcon/11x5.png">
              </div>
              <div class="game">
                <div class="game-name">广东11选5</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </router-link>
          </div>
          <div class="game-list game-caipiao">
            <router-link class="game-box" to="/jsks">
              <div class="game-img">
                <img src="static/gameIcon/jsk3.png">
              </div>
              <div class="game">
                <div class="game-name">江苏快3</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </router-link>
            <router-link class="game-box" to="">
              <div class="game-img">
                <img src="static/gameIcon/fenfencai.png">
              </div>
              <div class="game">
                <div class="game-name">分分彩</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </router-link>
          </div>
        </div>
        <div class="border-box"></div>
        <div class="module mt20">
          <div class="title">体育竞技</div>
          <div class="game-list">
            <router-link class="game-box" to="">
              <div class="game-img">
                <img src="static/gameIcon/football.png">
              </div>
              <div class="game">
                <div class="game-name">足球</div>
                <div class="game-info">超过百万用户</div>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// import radioText from "@/components/common/radioText";
export default {
  data() {
    return {
      system_news: [],
      news_content: "",
      prevElementId: ["transitionbox"],
      indexStart: 0,
      indexEnd: 0,
      nowElementId: "transitionbox",
      first: true
    };
  },
  methods: {
    toLogin: function() {
      this.$router.push("/login");
    },
    toRegist: function() {
      this.$router.push("/regist");
    },
    /**
     *
     */
    animate() {
      console.log(
        "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++"
      );
      console.log(
        "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++"
      );
      console.log(
        "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++"
      );
      this.$nextTick(function() {
        if (this.first) {
          var obj = document.querySelector("#" + this.nowElementId);
          obj.parentElement.style.position = "relative";
          var n = 1;
          if (obj.offsetWidth < obj.parentElement.offsetWidth) {
            n = Math.ceil(obj.parentElement.offsetWidth / obj.offsetWidth);
          }
          var htmlStr = obj.outerHTML,
            appendHtml = "";
          for (var i = 0; i < n; i++) {
            appendHtml += htmlStr.replace(
              this.nowElementId,
              this.nowElementId +
                i +
                '" style="transform:translate(85vw, 0);display:inline-block;position:absolute;'
            );
            this.prevElementId.push(this.nowElementId + i);
          }
          obj.parentElement.innerHTML += appendHtml;
          this.first = false;
        }
        console.log(this.nowElementId);
        this.$nextTick(() => {
          var that = this;
          this.mytransition({
            elementId: that.prevElementId[that.indexStart],
            speed: 50,
            beforeCallback: function() {
              // console.log("开始滚动");
            },
            middleCallback: function() {
              // console.log("中转站");
              that.indexStart++;
              if (that.indexStart >= Object.keys(that.prevElementId).length) {
                that.indexStart = 0;
              }
              // console.log("length="+Object.keys(that.prevElementId).length);
              // console.log("indexStart="+that.indexStart);
              // console.log("indexEnd="+that.indexEnd);
              // console.log("elementID="+that.nowElementId);
              // that.nowElementId = that.prevElementId[that.indexStart];
              that.animate();
            },
            afterCallback: function() {
              // console.log("滚动完成");
              // console.log("indexEnd="+that.indexEnd);
              // console.log(that.prevElementId[that.indexEnd]);
              var obj = document.querySelector(
                "#" + that.prevElementId[that.indexEnd]
              );
              obj.style.transform = "translate(85vw, 0)";
              that.indexEnd++;
              if (that.indexEnd >= Object.keys(that.prevElementId).length) {
                that.indexEnd = 0;
              }
            }
          });
        });
      });
    }
  },
  mounted: function() {
    var that = this;
    // this.first = true;
    //   检测socket是否正常
    this.checkSocket(false, function() {
      that.system_news = that.GLOBAL.sysNews;
      that.animate();
    });
    this.onmessage();
    // 判断是否创建了游戏socket，是的话就把它关闭
    if (this.GLOBAL.gameSocketHand) {
      console.log("game socket is closing...");
      this.GLOBAL.gameSocketHand.close();
      this.GLOBAL.gameSocketHand = null;
    }
    // 标志退出游戏
    this.GLOBAL.gameOrNot = false;
    // 显示导航栏
    this.$parent.tabbarShow = true;
    this.$parent.tabbarWhic = true;
  }
};
</script>
<style>
#transitionbox {
  display: inline-block;
  position: absolute;
  /* z-index: 2; */
}
.transitionbox-before {
  transform: translate(85vw, 0);
}
.transitionbox-begin {
  will-change: transform;
  transform: translate(-100%, 0);
}
.system-news-list {
  white-space: nowrap;
  /* position: relative; */
}
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
  font-size: 5vw !important;
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

/* .module { */
/* height: 24vw; */
/* border-top: 2.5vw solid #F5F5F5; */
/* } */
/* .HOTAGME { */
/* height: 56vw; */
/* } */
.radio {
  height: 10vw;
  /* line-height: 10vw; */
  text-align: left;
  padding: 0 4vw;
}
.hall-radio {
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
  /* height: 10vw; */
  line-height: 10vw;
  /* border: 1px solid red; */
  width: 100vw;
}
.hall-radio-box {
  height: 10vw;
}
/* .radio-text span {
  height: 100%;
  position: relative;
  font-size: 3.5vw;
  margin-right: 30px;
} */
.title {
  font-size: 4.5vw;
  /* font-weight: 600; */
  text-align: left;
  /* height: 8vw; */
}
.game-list {
  display: flex;
  justify-content: space-between;
  /* margin-top: 15px; */
  height: 19vw;
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
  height: 9.5vw;
  line-height: 16vw;
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
.border-box {
  height: 2.5vw;
  background: #f5f5ff;
  width: 100vw;
  margin-left: -4vw;
}
</style>