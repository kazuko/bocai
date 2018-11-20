<template>
    <div style="background:#e7e7e7;padding-bottom:18vw;">
        <div class="homeContent">
            <div class="backgroundbox"></div>
            <mt-header title="">
                <router-link to="/" slot="left">
                    <mt-button icon="back">返回</mt-button>
                </router-link>
            </mt-header>
            <div class="pageContent">
                <div class="userInfo">
                    <div class="userPic">
                        <img v-bind:src="host+userInfo.head">
                    </div>
                    <div class="userName">
                        {{userInfo.nickname}}
                    </div>
                    <div class="userId">
                        ID:{{userInfo.id}}
                    </div>
                    <div class="property">
                        <div class="userGold">
                            <div class="proVal">{{userInfo.gold}}</div>
                            {{appInfo.coin1_name}}
                        </div>
                        <div class="devided-line"></div>
                        <div class="userGold">
                            <div class="proVal">{{userInfo.integral}}</div>
                            {{appInfo.coin2_name}}
                        </div>
                    </div>
                </div>
                <div class="userMedal">
                    勋章：
                    <li class="medalsBox" v-for="(item,index) in userInfo.medals" :key="index">
                        <img v-bind:src="host+item">
                    </li>
                </div>
                <div class="userMenu">
                    <div class="user-menu-list">
                        <div class="user-menu-box" @click="goto('/friend')">
                            <div class="menu-icon">
                                <img src="./../../assets/我的好友icon@2x.png">
                                <mt-badge type="error" size="small" class="badge friend-badge" v-show="this.GLOBAL.msgLength">{{this.GLOBAL.msgLength}}</mt-badge>
                            </div>
                            <div class="menu-label">
                                我的好友
                            </div>
                        </div>
                        <router-link class="user-menu-box" to="/bank">
                            <div class="menu-icon">
                                <img src="./../../assets/银行社区@2x.png">
                            </div>
                            <div class="menu-label">
                                我的银行
                            </div>
                        </router-link>
                    </div>
                    <div class="user-menu-list">
                        <router-link class="user-menu-box" to="/mySystem">
                            <div class="menu-icon">
                                <img src="./../../assets/我的设置@2x.png">
                            </div>
                            <div class="menu-label">
                                我的设置
                            </div>
                        </router-link>
                        <router-link class="user-menu-box" to="/redbag">
                            <div class="menu-icon">
                                <img src="./../../assets/我的红包@2x.png">
                            </div>
                            <div class="menu-label">
                                我的红包
                            </div>
                        </router-link>
                    </div>
                    <div class="user-menu-list">
                        <router-link class="user-menu-box" to="/radio">
                            <div class="menu-icon">
                                <img src="./../../assets/社区广播@2x.png">
                            </div>
                            <div class="menu-label">
                                社区广播
                            </div>
                        </router-link>
                        <router-link class="user-menu-box" to="/mytheme">
                            <div class="menu-icon">
                                <img src="./../../assets/我的帖子@2x.png">
                            </div>
                            <div class="menu-label">
                                我的帖子
                            </div>
                        </router-link>
                    </div>
                </div>
                <div class="userMenu">
                    <div class="user-menu-list menu2-list">
                        <router-link class="user-menu2-box" to="/recharge">
                            <div class="menu-icon">
                                <img src="./../../assets/充值@2x.png">
                            </div>
                            <div class="menu-label">
                                充值
                            </div>
                        </router-link>
                        <router-link class="user-menu2-box" to="/putForward">
                            <div class="menu-icon">
                                <img src="./../../assets/提现@2x.png">
                            </div>
                            <div class="menu-label">
                                提现
                            </div>
                        </router-link>
                        <router-link class="user-menu2-box" to="/medal">
                            <div class="menu-icon">
                                <img src="./../../assets/勋章@2x.png">
                            </div>
                            <div class="menu-label">
                                勋章
                            </div>
                        </router-link>
                    </div>
                </div>
                <div class="userMenu">
                    <div class="user-menu-list">
                        <div @click="downLine">
                            <div class="menu-icon" style="float:left;">
                                <img src="./../../assets/安全离线icon.png">
                            </div>
                            <div class="menu-label" style="float:left;">
                                安全离线
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
console.log("USER_INDEX_VUE");
export default {
  data() {
    return {
      host: this.GLOBAL.Host,
      userInfo: this.GLOBAL.userInfo,
      appInfo: this.GLOBAL.appInfo
    };
  },
  //   components: {
  //     mtHeader
  //   },
  methods: {
    /**
     * 安全离线
     */
    downLine: function() {
      let data = {
        con_id: this.GLOBAL.connectionId,
        send_id: this.GLOBAL.userInfo.id,
        type: "downLine"
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(respone) {
          if (respone.status) {
            that.GLOBAL.userInfo = {};
            that.GLOBAL.loginStatus = false;
            localStorage.removeItem("bc_userInfo");
            localStorage.removeItem("bc_friendInfo");
            that.$router.push("/");
          } else {
            that.$messagebox.alert("网络堵塞，请检查网络是否正常！");
          }
        },
        callbackFlag: "responeDownLine",
        handType: "user"
      });
    },
    goto: function(pagePath) {
      this.$router.push(pagePath);
    }
  },
  mounted: function() {
    console.log("index->mounted...");
    this.checkLogin();
    this.onmessage();
    this.$parent.tabbarShow = true;
    this.$parent.tabbarWhic = false;
  }
};
</script>

<style>
/* html {
  background: #e7e7e7;
} */
.pageContent {
  /* margin-top: -4vw; */
  position: relative;
  z-index: 10;
}
.backgroundbox {
  background: linear-gradient(135deg, #63d8e3, #be80e9);
  width: 100vw;
  height: 60vw;
  position: absolute;
  z-index: 0;
}
/* .homeContent {
  background: url("./../../assets/homebg.png") no-repeat;
  background-size: 100%;
  background: #e7e7e7;
} */
.userInfo {
  height: 42vw;
  color: #fff;
  padding: 5px;
}
.userPic {
  width: 18vw;
  height: 18vw;
  border-radius: 100%;
  overflow: hidden;
  margin: 0 auto;
  display: flex;
  align-content: center;
  align-items: center;
  justify-content: center;
  justify-items: center;
}
.userPic img {
  max-width: 100%;
  max-height: 100%;
}
.userName {
  font-size: 4vw;
}

.userId {
  font-size: 4vw;
  transform: scale(0.9);
  color: rgba(255, 255, 255, 0.8);
  line-height: 4vw;
}
.property {
  display: flex;
  flex-wrap: nowrap;
  justify-content: space-between;
  /* height: 50px; */
  /* line-height: 50px; */
  /* font-size: 20px; */
  /* padding: 5px 0; */
}
.userGold {
  width: 49%;
  line-height: 5vw;
  font-size: 4vw;
  box-sizing: border-box;
  padding-top: 3vw;
}
.proVal {
  font-size: 4vw;
  font-weight: 500;
}
.userMedal {
  height: 14vw;
  line-height: 14vw;
  padding: 1.5vw 5vw;
  background-color: #fff;
  border-radius: 2vw;
  text-align: left;
  font-size: 4vw;
  display: flex;
  flex-wrap: nowrap;
}
.medalsBox {
  width: 10vw;
  height: 10vw;
  margin-top: 2vw;
  margin-left: 3vw;
  display: flex;
  justify-content: center;
  align-items: center;
}
.userMedal img {
  /* width: 10vw; */
  max-width: 100%;
  max-height: 100%;
  /* height: 10vw; */
  /* margin: 8px 0 0 15px; */
}
.userMenu {
  background-color: #fff;
  border-radius: 2vw;
  margin-top: 3vw;
  padding: 3vw 0;
  /* position: fixed; */
}
.user-menu-list {
  display: flex;
  flex-wrap: nowrap;
  justify-content: space-between;
  /* padding: 5px 20px; */
  height: 10vw;
}
.user-menu-box {
  width: 48%;
  display: flex;
  height: 100%;
  flex-wrap: nowrap;
}
.user-menu2-box {
  width: 33%;
  display: flex;
  height: 100%;
  flex-wrap: nowrap;
}
.menu-icon {
  /* padding: 10px; */
  height: 10vw;
  position: relative;
}
.menu-icon img {
  width: 6vw;
  height: 6vw;
  margin: 2vw;
}
.menu-label {
  font-size: 4vw;
  height: 10vw;
  line-height: 10vw;
  /* padding: 10px 0px; */
  padding: 0px;
}
.friend-badge {
  display: block;
  min-width: 5vw;
  min-height: 5vw;
  border-radius: 10vw !important;
  font-size: 3vw !important;
  /* padding: 1px 1px; */
  padding: 0px !important;
  line-height: 5vw;
  text-align: center;
  position: absolute;
  left: 4vw;
  top: 0.5vw;
}
.user-menu2-box .menu-icon {
  /* padding: 10px 5px; */
}
.devided-line {
  width: 0.8vw;
  height: 5vw;
  background: white;
  margin-top: 6vw;
}
</style>