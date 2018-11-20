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
            <img :src="hecaiImg">
          </div>
          <div class="brief-info flex flow-col justify-sa">
            <span class="fz-18 font-theme">
              香港⑥合彩
            </span>
            <span v-if="nextIssue"
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
        <div v-for="item in recentInfo"
             class="hecai-item">
          <div class="hecai-item-info flex justify-sb fontc-6 fz-12">
            <span>第{{item.issue}}期</span>
            <span>{{item.time}}</span>
          </div>
          <openNum :openNumObj="JSON.parse(item.code)"
                   :showAnimals="false"></openNum>
        </div>
      </template>
    </div>

  </div>
</template>

<script>
import openNum from "./6hecai-openNum";
import { countDownFunc } from "@/mixins/mixins";

export default {
  mixins: [countDownFunc],
  components: {
    openNum
  },
  data() {
    return {
      recentInfo: [],
      nextIssue: 0, //最新期数
      hecaiImg: "" //6合彩图标
    };
  },
  methods: {
    //请求 赔率 数据
    getOddsData() {
      let gameGongHost = this.GLOBAL.gameGongHost;
      let sendDataToServer = () => {
        this.senddata({ data: { type: "recentOpen", game: "six" } });
      };

      //请求数据
      if (!this.GLOBAL.gameSocketHand) {
        this.createSocket(gameGongHost, () => {
          this.dealOnMessage(); //创建连接成功之后 监听返回的信息
          sendDataToServer();
        });
      } else {
        this.dealOnMessage(); //已经创建连接 监听返回的信息
        sendDataToServer();
      }
    },

    //处理返回的数据
    dealOnMessage() {
      this.onmessage(res => {
        res = JSON.parse(res);
        //心跳
        if (res.code === "pong") return;
        //订单信息
        if (res instanceof Array) this.recentInfo = res;
        console.log("%c近期开奖", "color:red;");
        console.dir(res);
      });
    }
  },
  mounted() {
    this.nextIssue = this.GLOBAL.six.nextIssue;
    this.hecaiImg = this.GLOBAL.sixImg;
    //从全局变量读取并且启动倒计时
    let countdown = this.GLOBAL.six.countdown;
    let interval = this.GLOBAL.six.interval;
    this.mixins_countTime(countdown, interval, "timeToOpen");

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
  padding: 0;
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
/* 开奖列表 */
.hecai-item {
  padding: 0 2vw 2vw;
  border-bottom: 1px solid #e5e5e5;
}
.hecai-item-info {
  line-height: 10vw;
}
</style>
