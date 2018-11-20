<template>
  <div class="contanier flex flow-col">
    <!-- 头部 -->
    <header class="dent-header flex justify-sb fz-16">

      <div dentHoverclass="hoverclass"
           @click="$router.go(-1)"
           class="isleft flex iconfont icon-left click-icon"></div>

      <div class="iscenter flex justify-ct">数字彩票</div>

      <div class="isright flex iconfont icon-filter click-icon"></div>

    </header>

    <switchTab :tabObj='tabObj'
               flex="true"
               v-model="activeTab"></switchTab>

    <mt-tab-container swipeable="true"
                      v-model="activeTab">

      <mt-tab-container-item v-for="(value,key,index) in tabObj"
                             :id="index"
                             :key="index">
        <template v-if="key ===  '全部注单'">
          <template v-if="allOrder.length">
            <div v-for="order in allOrder"
                 @click="checkOrderDetail(order)"
                 class="order-item flex flow-col fontc-6">
              <div class="game-info flex">
                <div class="avatar"><img :src="hecaiImg"></div>
                <span class="fz-14">香港⑤合彩{{order.issue}}期</span>
              </div>
              <div class="order-status flex justify-sb fz-14">
                <span>投注: {{order.gold.split(' ')[2]}}</span>
                <span v-if="order.status === 0"
                      class="flex"><b class="font-wait-color iconfont icon-wait"></b>待开奖</span>
                <span v-if="order.status === 1"
                      class="flex fontc-9"><b class="iconfont icon-zhongjiang"></b>没有中奖</span>
                <span v-if="order.status === 2"
                      class="flex font-theme"><b class="iconfont icon-zhongjiang"></b>中奖{{order.win.toFixed(3)}}</span>
                <span v-if="order.status === 3"
                      class="flex fontc-9"><b class="iconfont icon-close"></b>已撤单</span>
              </div>
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
        </template>
        <template v-if="key ===  '待开奖'">
          <div v-for="order in waitForOpen"
               @click="checkOrderDetail(order)"
               class="order-item flex flow-col fontc-6">
            <div class="game-info flex">
              <div class="avatar"><img :src="hecaiImg"></div>
              <span class="fz-14">香港⑤合彩{{order.issue}}期</span>
            </div>
            <div class="order-status flex justify-sb fz-14">
              <span>投注: 2.000元</span>
              <span class="flex"><b class="font-wait-color iconfont icon-wait"></b>待开奖</span>
            </div>
          </div>
        </template>
        <template v-if="key ===  '已中奖'">
          <div v-for="order in bingo"
               @click="checkOrderDetail(order)"
               class="order-item flex flow-col fontc-6">
            <div class="game-info flex">
              <div class="avatar"><img :src="hecaiImg"></div>
              <span class="fz-14">香港⑤合彩{{order.issue}}期</span>
            </div>
            <div class="order-status flex justify-sb fz-14">
              <span>投注2.000元</span>
              <span class="flex font-theme"><b class="iconfont icon-zhongjiang"></b>中奖00.000元</span>
            </div>
          </div>
        </template>
        <template v-if="key ===  '已撤单'">
          <div v-for="order in cancle"
               @click="checkOrderDetail(order)"
               class="order-item flex flow-col fontc-6">
            <div class="game-info flex">
              <div class="avatar"><img :src="hecaiImg"></div>
              <span class="fz-14">香港⑤合彩{{order.issue}}期</span>
            </div>
            <div class="order-status flex justify-sb fz-14">
              <span>投注2.000元</span>
              <span class="flex fontc-9"><b class="iconfont icon-close"></b>已撤单</span>
            </div>
          </div>
        </template>
      </mt-tab-container-item>

    </mt-tab-container>

    <!-- currentOrder 当前查看的详细信息-->
    <transition name="orderEnter">
      <div v-show="orderDetailShow"
           class="order-detail flex flow-col">
        <header class="dent-header flex justify-sb fz-16">
          <div dentHoverclass="hoverclass"
               @click="orderDetailShow=false"
               class="isleft flex iconfont icon-left click-icon"></div>
          <div class="iscenter flex justify-ct">投注详情</div>
          <div class="isright"></div>
        </header>

        <!-- 彩种信息 -->
        <template v-if="currentOrder">
          <div class="order-detail-info flex flow-col justify-sa">
            <div class="flex flow-col">
              <div class="game-avatar"><img :src="hecaiImg"></div>
              <div class="game-name fz-16">香港⑥合彩</div>
            </div>
            <div class="game-issue fz-12 fontc-9">第{{currentOrder.issue}}期</div>
            <div v-if="currentOrder.status === 0"
                 class="game-status font-theme">待开奖 ... ...</div>
            <div v-if="currentOrder.status === 1"
                 class="game-status fontc-6">没有中奖，再接再厉</div>
            <div v-if="currentOrder.status === 2"
                 class="game-status font-theme">中奖 {{currentOrder.win.toFixed(3)}} 元！</div>
            <div v-if="currentOrder.status === 3"
                 class="game-status fontc-6">该订单已撤销</div>
          </div>
          <!-- 订单详细 -->
          <div class="order-detail-list">
            <div class="flex order-detail-item">
              <span class="fz-12 fontc-9">开奖号码</span>
              <p v-if="currentOrder.status === 0"
                 class="fz-14 fontc-6">未开将</p>
              <p v-else
                 class="fz-14 fontc-6">{{currentOrder.open_code}}</p>
            </div>
            <div class="flex order-detail-item">
              <span class="fz-12 fontc-9">投注号码</span>
              <p v-if="openNumArray"
                 class="fz-14 fontc-6">
                <template v-for="(n,index) in openNumArray">
                  <span> {{n|keyToCharacter}} </span>
                  <span v-if="index !== openNumArray.length-1">{{' , '}}</span>
                </template>
              </p>
            </div>
            <div class="flex order-detail-item">
              <span class="fz-12 fontc-9">投注单号</span>
              <p class="fz-14 fontc-6">{{currentOrder.order_number}}</p>
            </div>
            <div class="flex order-detail-item">
              <span class="fz-12 fontc-9">投注时间</span>
              <p class="fz-14 fontc-6">{{currentOrder.create_time}}</p>
            </div>
            <div class="flex order-detail-item">
              <span class="fz-12 fontc-9">玩法名称</span>
              <p class="fz-14 fontc-6">{{ splitCurrentOrderName(currentOrder.name) }}</p>
            </div>
            <div class="flex order-detail-item">
              <span class="fz-12 fontc-9">投注金额</span>
              <p class="fz-14 fontc-6">{{currentOrder.gold}}</p>
            </div>
            <div class="flex order-detail-item">
              <span class="fz-12 fontc-9">投注返点</span>
              <p class="fz-14 fontc-6">{{currentOrder.return_point}}</p>
            </div>
            <div class="flex order-detail-item">
              <span class="fz-12 fontc-9">投注赔率</span>
              <p class="fz-14 fontc-6">{{currentOrder.odds}}</p>
            </div>
          </div>
        </template>

        <!-- 退票 -->
        <span v-if="currentOrder.status === 0"
              @click="refundOrder(currentOrder)"
              class="iconfont icon-refund icon-refund-pos"></span>
        <!-- 未中奖 -->
        <span v-if="currentOrder.status === 1"
              class="iconfont icon-weizhongjiang icon-weizhongjiang-pos"></span>
        <!-- 中奖 -->
        <span v-if="currentOrder.status === 2"
              class="iconfont icon-zhongjiangliao icon-zhongjiangliao-pos"></span>

        <!-- 再来一注 -->
        <div @click="$router.go(-1)"
             class="buymore">再来一注</div>
      </div>
    </transition>

  </div>
</template>

<script>
import switchTab from "./6hecai-switch";
import { Spinner, Toast } from "mint-ui";
import { six as keyToCharacter } from "@/js/keyToCharacter";

export default {
  components: {
    switchTab
  },
  filters: {
    // 键值转成中文
    keyToCharacter: function(value) {
      let character = "";
      //有下划线 _ 去_之后的字符串匹配
      let isString = typeof value === "string";
      if (isString && value.match(/_/)) value = value.split("_")[1];

      for (let key in keyToCharacter) {
        if (key === value) {
          character = keyToCharacter[key];
          break;
        }
      }

      if (!character) character = value;
      return character;
    }
  },
  data() {
    return {
      //数据相关
      tabObj: {
        全部注单: { text: "全部注单", id: "allOrder" },
        待开奖: { text: "待开奖", id: "waitForOpen" },
        已中奖: { text: "已中奖", id: "bingo" },
        已撤单: { text: "已撤单", id: "cancle" }
      },
      allOrder: [], //全部的订单
      currentOrder: {}, //当前查看的订单
      hecaiImg: "", //6合彩图标
      //状态相关
      activeTab: 0, //当前激活的tab
      orderDetailShow: false, //订单详情弹窗
      noHistoryOrder: false //没有历史订单
    };
  },
  //过滤数组
  computed: {
    //投注的号码
    openNumArray() {
      return this.currentOrder.number && this.currentOrder.number.split(",");
    },
    //为开奖的数组
    waitForOpen() {
      return this.allOrder.filter(order => {
        return order.status === 0;
      });
    },
    //开奖的数组
    bingo() {
      return this.allOrder.filter(order => {
        return order.status === 2;
      });
    },
    //撤单的数组
    cancle() {
      return this.allOrder.filter(order => {
        return order.status === 3;
      });
    }
  },
  methods: {
    //查看订单详细
    checkOrderDetail(order) {
      this.currentOrder = order;
      this.orderDetailShow = true;
    },

    //撤销订单
    refundOrder(order) {
      this.senddata({
        data: { type: "destroy", game: "six", orderNumber: order.order_number }
      });
    },

    //请求 赔率 数据
    getOddsData() {
      let gameGongHost = this.GLOBAL.gameGongHost;
      let sendDataToServer = () => {
        this.senddata({ data: { type: "betHistory", game: "six" } });
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
        //订单信息
        if (res instanceof Array) {
          this.allOrder = res;
          if (res.length === 0) {
            Toast({
              message: "没有历史注单",
              duration: 1500
            });
            this.noHistoryOrder = true;
          }
        }

        //退票成功
        if (res.cancel && res.cancelGold) {
          Toast({
            message: "退票成功",
            duration: 1500
          });
          this.orderDetailShow = false;
          //将金币保存到全局及本地缓存
          let userInfo = JSON.parse(window.localStorage.getItem("bc_userInfo"));
          userInfo.gold = res.cancelGold * 1 + userInfo.gold * 1;
          this.$set(this.GLOBAL, "userInfo", userInfo);
          window.localStorage.setItem("bc_userInfo", JSON.stringify(userInfo));
          //重新请求订单信息
          this.senddata({ data: { type: "betHistory", game: "six" } });
        }

        //下单后开奖会返回 issue 和 recentInfo 及remaining, 重新请求订单信息
        if (res.issue && res.recentInfo && res.remaining) {
          this.senddata({ data: { type: "betHistory", game: "six" } });
          this.orderDetailShow = false;
        }
      });
    },
    //玩法名称
    splitCurrentOrderName(value) {
      //有下划线 _ 去_之后的字符串匹配
      let isString = typeof value === "string";
      if (isString && value.match(/_/)) {
        let newValue = value.split("_").map(value => {
          for (let key in keyToCharacter) {
            if (key === value) {
              return keyToCharacter[key];
            }
          }
        });
        return newValue[0] + "," + newValue[1];
      } else {
        for (let key in keyToCharacter) {
          if (key === value) {
            return keyToCharacter[key];
          }
        }
      }
    }
  },
  mounted() {
    this.$parent.tabbarShow = false;
    this.hecaiImg = this.GLOBAL.sixImg;
    this.getOddsData();
  }
};
</script>

<style scoped>
.font-wait-color {
  color: #62ba5c;
}
.hoverclass {
  background: rgba(0, 0, 0, 0.1);
}
.contanier {
  position: relative;
  padding-top: 13.8vw;
  height: 100vh;
  background: #fff;
}
.mint-tab-container {
  overflow: auto;
}
.orderEnter-enter-active,
.orderEnter-leave-active {
  will-change: transform;
  transform: translate3d(0, 0, 0);
  transition: 0.5s;
}
.orderEnter-enter,
.orderEnter-leave-to {
  transform: translate3d(0, 5%, 0);
}
.orderEnter-leave-to {
  opacity: 0;
}
/* 头部 */
.dent-header {
  position: fixed;
  z-index: 2;
  top: 0;
  left: 0;
  box-sizing: border-box;
  width: 100%;
  height: 13.8vw;
  background: #57d6dd;
  color: #ffffff;
}
.iscenter {
  flex: 1;
  height: 100%;
}
.isleft,
.isright {
  flex: 0.5;
  padding: 0 2vw;
  height: 100%;
}
.isright {
  justify-content: flex-end;
}
.click-icon {
  font-size: 20px;
}
.mint-tab-container {
  flex: 1;
  width: 100%;
}
.order-item {
  padding: 2vw 0;
  line-height: 2;
  border-bottom: 1px solid #f1f1f1;
}
.game-info {
  align-self: flex-start;
  padding: 0 2vw;
}
.avatar {
  margin-right: 2vw;
  width: 8vw;
  height: 8vw;
  background: #f1f1f1;
  border-radius: 50%;
}
.game-avatar img,
.avatar img {
  width: 100%;
  height: 100%;
}
.order-status {
  margin-top: 2vw;
  padding: 0 2vw;
  width: 100%;
}
/* 订单详情页 */
.order-detail {
  position: absolute;
  z-index: 5;
  top: 0;
  left: 0;
  padding-top: 16vw;
  padding-bottom: 12vw;
  width: 100%;
  height: 100vh;
  background: #fff;
}
.order-detail::before {
  content: "";
  position: absolute;
  z-index: -1;
  top: -17vw;
  left: -10vw;
  height: 40vw;
  width: 120vw;
  background: #57d6dd;
  transition: 0.25s;
  border-radius: 50%;
}
.orderEnter-enter.order-detail::before,
.orderEnter-leave-to.order-detail::before {
  top: -30vw;
  border-radius: 0;
}
.order-detail-list {
  position: relative;
  flex: 1;
  overflow: auto;
}
.order-detail-info {
  margin-top: 0;
  height: 42.6vw;
  transition: 0.25s;
}
.orderEnter-enter .order-detail-info,
.orderEnter-leave-to .order-detail-info {
  margin-top: 4vw;
}
.game-avatar {
  margin: 1vw 0;
  width: 14vw;
  height: 14vw;
  background: #f1f1f1;
  border-radius: 50%;
  box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
}
.game-status {
  font-size: 18px;
}
.order-detail-item {
  padding: 2vw 0;
  line-height: 2;
}
.order-detail-item > span {
  margin: 0 6vw;
}
.order-detail-item > p {
  margin-left: 4vw;
}
.empty-size {
  font-size: 35vw;
}
.icon-refund-pos,
.icon-zhongjiangliao-pos,
.icon-weizhongjiang-pos {
  position: fixed;
  bottom: 20vw;
  color: #c90000;
  background: linear-gradient(135deg, #ee6200, #c90000);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.icon-weizhongjiang-pos {
  color: #aaaaaa;
  background: linear-gradient(135deg, #e6e6e6, #aaaaaa);
}
.icon-refund-pos {
  right: 4vw;
  font-size: 60px;
}
.icon-zhongjiangliao-pos,
.icon-weizhongjiang-pos {
  right: -4vw;
  font-size: 80px;
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
