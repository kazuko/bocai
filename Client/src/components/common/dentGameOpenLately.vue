<template>
  <div class="contanier flex flow-col">
    <!-- 头部 -->
    <header class="dent-header flex justify-sb">

      <span dentHoverclass="hoverclass"
            @click="$router.go(-1)"
            class="flex iconfont icon-left click-icon"></span>

      <div class="hecai flex justify-sb">

        <div class="brief flex">
          <div class="avatar">
            <img :src="avatar">
          </div>
          <div class="brief-info flex flow-col justify-sa">
            <span class="fz-18 font-theme">
              {{gameName}}
            </span>
            <span v-if="Object.keys(recentInfo).length"
                  class="fz-12 fontc-6">第{{nextIssue}}期</span>
          </div>
        </div>

        <div class="time-left flex flow-col justify-sa">
          <span class="fz-18 font-theme">{{timer.timeToOpen}}</span>
          <span class="fz-12 fontc-3">即将开奖......</span>
        </div>

      </div>
    </header>

    <!-- 开奖列表 -->
    <div class="hecai-list bgc-fff">
      <template v-if="Object.keys(recentInfo).length">
        <div v-for="(item,index) in recentInfo"
             class="hecai-item">
          <div class="hecai-item-info flex justify-sb fontc-6 fz-12">
            <span>第{{item.issue || item.expect}}期</span>
            <span>{{item.open_time}}</span>
          </div>
          <!-- 6合彩 -->
          <template v-if="gameKey === '6hecai'">
            <openNum :openNumObj="JSON.parse(item.code)"
                     :showAnimals="false"></openNum>
          </template>
          <!-- 江苏快三 -->
          <template v-if="gameKey === 'jsks'">
            <div class="jsks flex">
              <div class="dice-wrap flex justify-ct">
                <img v-for="src in item.code_img"
                     :src="src"
                     class="dice">
              </div>
              <span class="fontc-9">和值: <span class="fontc-3">{{item.sum}}</span></span>
            </div>
          </template>
          <!-- 北京PK10 广东11选5-->
          <template v-if="['bjpk10','gd'].includes(gameKey)">
            <p :class="index===0?'currentopen':''"
               class="bjpk10">
              <span v-for="n in item.open_code"
                    class="bjpk10-item font-theme">{{n}}</span>
            </p>
          </template>
        </div>
      </template>
      <!-- loading提示 -->
      <div v-else
           style="height: 50vh;"
           class="flex justify-ct flow-col">
        <template v-if="noHistoryOrder">
          <span class="iconfont icon-empty fontc-9 empty-size"></span>
          <span class="fz-16"
                style="line-height: 3;">没有数据</span>
        </template>
        <mt-spinner v-else
                    color="#e62b00"
                    type="fading-circle"></mt-spinner>
      </div>
    </div>

    <!-- 再来一注 -->
    <div @click="$router.go(-1)"
         class="buymore">再来一注</div>
  </div>
</template>

<script>
import { countDownFunc } from "@/mixins/mixins";
import { Toast } from "mint-ui";

export default {
  mixins: [countDownFunc],
  components: {
    openNum: () => import("@/components/game/6hecai/6hecai-openNum")
  },
  data() {
    return {
      /* 数据 */
      recentInfo:[],  //近期开奖信息
      gameKey:'', //游戏的key

      /* 状态 */
      gameName: "", //游戏的名字
      avatar: "", //游戏的图标
      noHistoryOrder: false //没有跟多订单
    };
  },
  methods: {
    //请求 赔率 数据
    getOddsData() {
      let gameGongHost = this.GLOBAL.gameGongHost;
      let sendDataToServer = () => {
        this.senddata({ data: { type: "recentOpen", game: this.gameKey } });
      };

      this.checkGameSocket(() => {
        this.dealOnMessage(); //监听返回的信息
        sendDataToServer();
      });
    },

    //处理返回的数据
    dealOnMessage() {
      this.onmessage(res => {
        res = JSON.parse(res);
        //心跳
        if (res.code === "pong") {
          this.senddata({ data: { type: "ping" } });
          return;
        }
        //订单信息
        if (res instanceof Array) {
          this.recentInfo = res;
          if (res.length === 0) {
            Toast({
              message: "没有历史注单",
              duration: 1500
            });
            this.noHistoryOrder = true;
          }
          console.log("%c近期开奖", "color:red;");
          console.dir(res);
        }
      });
    }
  },
  mounted() {
    let { gameKey, gameName } = this.$route.query;
    this.gameKey = gameKey;
    this.gameName = gameName;
    this.nextIssue = this.GLOBAL[gameKey].nextIssue;
    this.avatar = this.GLOBAL[gameKey + "Img"];
    //从全局变量读取并且启动倒计时
    let { countdown, interval } = this.GLOBAL[gameKey];
    countdown && this.mixins_countTime(countdown, interval, "timeToOpen");

    this.$parent.tabbarShow = false;
    this.getOddsData();
  }
};
</script>

<style scoped>
.hoverclass {
  background: rgba(0, 0, 0, 0.1);
}
.contanier {
  padding-top: 0;
  padding-bottom: 14vw;
  height: 100vh;
}
.hecai-list {
  flex: 1;
  width: 100%;
  overflow: auto;
}
/* 头部 */
.dent-header {
  width: 100%;
  height: 18vw;
  background: #57d6dd;
}
.click-icon {
  flex: 1;
  padding-left: 2vw;
  height: 100%;
  color: #fff;
  font-size: 20px;
}
.hecai {
  width: 87.5vw;
  height: 14vw;
  background: white;
  border-top-left-radius: 7vw;
  border-bottom-left-radius: 7vw;
}
.avatar {
  margin-left: 1vw;
  margin-right: 2vw;
  width: 12vw;
  height: 12vw;
  border-radius: 50%;
  background: #e3e5e8;
  overflow: hidden;
}
.avatar img {
  width: 100%;
  height: 100%;
}
.brief {
  height: 100%;
}
.brief-info {
  align-items: flex-start;
  height: 100%;
}
.empty-size {
  font-size: 35vw;
}
/* 开奖列表 */
/* 6hecai */
.hecai-item {
  padding: 0 2vw 2vw;
  border-bottom: 1px solid #e5e5e5;
}
.hecai-item-info {
  line-height: 10vw;
}
/* jsks */
.jsks {
  margin: 1vw 0;
}
.dice-wrap {
  margin-right: 4vw;
  padding: 2vw 5vw;
  background: #7777bf;
  border-radius: 4vw;
}
.dice {
  width: 6vw;
  margin: 0 2vw;
  height: 6vw;
  background: #f2f5f8;
}
/* bjpk10 */
.bjpk10 {
  text-align: left;
}
.bjpk10-item {
  margin: 0 0.5em;
  font-size: 14px;
}
.currentopen .bjpk10-item {
  display: inline-block;
  margin: 0 0.1em;
  width: 1.5em;
  height: 1.5em;
  background: #e62b00;
  color: #fff;
  text-align: center;
  border-radius: 50%;
}
.buymore {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background: #e62b00;
  line-height: 12vw;
  font-size: 16px;
  color: #fff;
}
</style>
