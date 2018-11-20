<template>
  <div class="contanier flex flow-col">
    <!-- 头部 -->
    <header class="dent-header flex justify-sb fz-16">
      <div dentHoverclass="hoverclass"
           @click="$router.go(-1)"
           class="isleft flex iconfont icon-left click-icon"></div>

      <div class="iscenter flex justify-ct">投注单</div>
      <div class="isright"></div>
    </header>

    <!-- 主体 -->
    <div class="bets flex flow-col">
      <p class="bets-time-left fontc-6">
        <span>{{order.issue}}</span>期投注截止时间：
        <span class="font-theme">{{timer.timeToOpen}}</span>
      </p>
      <!--出票口-->
      <div class="bets-ticket"></div>
      <!-- 票据 -->
      <div class="ticket-wrap">

        <transition-group name="ticketout"
                          tag="div">
          <div v-for="(item,index) in orderList"
               :key="item.number"
               class="ticket-item flex justify-sb">
            <div class="ticket-item-info flex flow-col">

              <p class="fontc-6 fz-12">
                <span type="type">{{mixin_KeyToCharacter(item.type)}}</span>(<span>{{mixin_KeyToCharacter(item.name) || mixin_KeyToCharacter(item.leiXin) || mixin_KeyToCharacter(item.type) }}</span>)
                共<span class="font-theme">{{item.zhuShu}}</span>注，共<span class="font-theme">{{item.zhuShu*item.gold}}</span>元
              </p>

              <!-- 下注号码显示 -->
              <p v-if="item.number instanceof Array"
                 class="fz-16">
                <template v-for="(n,index) in item.number">
                  <span>{{mixin_KeyToCharacter(n)}}</span><span v-if="index !== item.number.length-1">{{splitDot}}</span>
                </template>
              </p>
              <div v-else-if="item.number instanceof Object"
                   class="flex number-wrap">
                <p v-if="item.needShowKey"
                   class="fz-14 number-key">{{ mixin_KeyToCharacter(Object.keys(item.number)[0]) + ':&nbsp' }}</p>
                <template v-for="(value,key) in item.number">
                  <p v-if="typeof(value) === 'object'"
                     class="number-item fz-15">
                    <template v-for="(n,subindex) in value">
                      <span>{{n|filter_toTwo}}</span>
                      <span v-if="subindex !== value.length-1">{{ splitDot }}</span>
                    </template>
                  </p>
                  <p v-else
                     class="number-item">{{value}}</p>
                </template>
              </div>

            </div>
            <span @click="remove(index)"
                  class="iconfont icon-guanbi cancel-ticket"></span>
          </div>
        </transition-group>

      </div>
    </div>

    <!-- 底部下注栏 -->
    <div class="hecai-bottom flex justify-sb bgc-fff">
      <div @click="resetOrder"
           class="hecai-btn random-btn">清空</div>

      <div class="should-pay flex flow-col justify-sa fontc-3 fz-12">
        <p>合计<span v-if="orderList.length"
                class="font-theme">{{allGold}}元</span></p>
        <p>可用余额<span class="font-theme">{{goldRemaining}}</span></p>
      </div>

      <div v-if="orderList.length && !submitIng"
           @click="submit"
           class="hecai-btn submit-btn cansubmit">投注</div>
      <div v-else
           :class="{submitIng:submitIng}"
           class="hecai-btn submit-btn">
        <span v-if="!submitIng">投注</span>
        <mt-spinner v-else
                    type="snake"
                    size="16"
                    color="#e62b00"></mt-spinner>
      </div>
    </div>
  </div>
</template>

<script>
import { Toast, MessageBox } from "mint-ui"
import { countDownFunc, filterMethods } from '@/mixins/mixins'

export default {
  mixins: [ countDownFunc, filterMethods ],
  data(){
    return{
      /* 数据 */
      odds: {}, //赔率信息 每注订单生成赔率 返给后台
      shengxiao: "", //生肖
      order: {}, //传递进来订单
      orderList: [], //生成的订单
      goldRemaining: 0, //用户可以余额
      gameKey:'', //游戏关键字

      /* 状态 */
      submitIng: false, //避免多次提交的开关
      splitDot:' , '
    }
  },
  computed: {
    allGold() {
      let allGold = 0;
      this.orderList.forEach(order => {
        allGold += order.gold * order.zhuShu;
      });

      return allGold;
    }
  },
  mounted() {
    this.$parent.tabbarShow = false;
    //用户余额
    this.goldRemaining = JSON.parse(localStorage.getItem('bc_userInfo')).gold;

    //从全局变量读取并且启动倒计时
    let gameKey = this.$route.query.gameKey;
    this.gameKey = gameKey;
    let { countdown, interval } = this.GLOBAL[gameKey];
    countdown && this.mixins_countTime(countdown, interval, 'timeToOpen');

    //从全局变量从读取之前为提交的下注信息
    this.orderList = this.GLOBAL[gameKey].orderList || [];

    //判断是否需要重新生成订单
    let getOrder = this.$route.query.getOrder;
    if (getOrder) {
      let ojbk = this.GLOBAL[gameKey].order;
      let haveOrder = Object.keys(ojbk).length;
      if(haveOrder){
        this.order = ojbk;
        console.log("用户下注信息：");
        console.dir(ojbk);
      }else{
        console.log("用户下注信息：");
        console.dir(this.GLOBAL[gameKey].orderList);
      }
    }

    //监听返回的信息
    this.dealOnMessage();
  },
  methods: {
    //提交订单信息
    submit() {
      //用户信息
      let userInfo = JSON.parse(localStorage.getItem("bc_userInfo"));
      let { id, gold: goldRemaining } = userInfo;
      //余额是否够
      if (goldRemaining < this.allGold) {
        Toast({
          message: "余额不足哟",
          duration: 1500
        });
        return;
      }

      //订单信息
      let sendDataToServer = () => {
        this.senddata({
          data: {
            type: "create",
            game: this.gameKey,
            order: this.orderList,
            issue: this.orderList[0].issue,
            id,
            createTime: new Date().Format("yyyy-MM-dd hh:mm:ss"),
            allGold: this.allGold
          }
        });
        //正在提交不可再次提交
        this.submitIng = true;
      };

      //发起请求
      this.checkGameSocket(() => {
        sendDataToServer();
      });

    },

    //删除一条订单信息
    remove: function(index) {
      this.orderList.splice(index, 1);
      this.$set(this.GLOBAL[this.gameKey], "order", this.orderList);
    },

    //清空订单
    resetOrder() {
      MessageBox.confirm("确定清空所有注单吗?", "", {
        confirmButtonClass: "font-theme"
      }).then(action => {
        this.GLOBAL[this.gameKey].orderList = this.orderList = [];
      });
    },

    //处理返回的数据
    dealOnMessage() {
      this.onmessage(res => {
        res = JSON.parse(res);
        //保持心跳
        if (res.code === "pong") {
          this.senddata({ data: { type: "ping" } });
        }

        //订单 生成成功
        if (res.create && res.remaining !== undefined) {
          //提交完成 接收到返回信息
          this.submitIng = false;
          //清空注单信息
          this.GLOBAL[this.gameKey].orderList = this.orderList = [];
          //将金币保存到全局及本地缓存
          let userInfo = JSON.parse(window.localStorage.getItem("bc_userInfo"));
          userInfo = { ...userInfo, gold: res.remaining };
          this.$set(this.GLOBAL, "userInfo", userInfo);
          window.localStorage.setItem("bc_userInfo", JSON.stringify(userInfo));

          Toast({
            message: "下注成功，可用余额为" + res.remaining,
            duration: 1000
          });
          setTimeout(() => {
            this.$router.go(-1);
          }, 1000);
        }
        //订单生成失败
        if (res.create === false) {
          //提交完成 接收到返回信息
          this.submitIng = false;
          Toast({
            message: "下注失败",
            duration: 1000
          });
          setTimeout(() => {
            this.$router.go(-1);
          }, 1000);
        }

        //下注失败
        if (res.code === "期号发生了变更") {
          //提交完成 接收到返回信息
          this.submitIng = false;
          Toast({
            message: "下注失败，期号发生了变更",
            duration: 1500
          });
          this.GLOBAL[this.gameKey].orderList = this.orderList = [];
          this.GLOBAL[this.gameKey].nextIssue = res.issue;
          setTimeout(() => {
            this.$router.go(-1);
          }, 1500);
        }

        //未开奖
        //if(res.code==='')
      });
    },

  },
  watch: {
    //根据传递进来的数据 生成订单信息
    order(order) {
      let orderList = [];
      orderList.push(order);

      this.orderList = [...this.orderList, ...orderList];
      this.$set(this.GLOBAL[this.gameKey], "orderList", this.orderList);
    }
  }
};
</script>

<style scoped>
.fz-15 {
  font-size: 15px;
}
.hoverclass {
  background: rgba(0, 0, 0, 0.1);
}

.ticketout-enter-active {
  transition: all 0.5s;
}
.ticketout-leave-active {
  position: absolute;
  transition: all 0.5s;
}
.ticketout-enter {
  opacity: 0;
  transform: translateY(-100%);
}
.ticketout-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
.ticketout-move {
  transition: transform 0.5s;
}

.contanier {
  padding: 0;
  height: 100vh;
  background: #fff;
}
/* 头部 */
.dent-header {
  box-sizing: border-box;
  width: 100%;
  height: 13.8vw;
  background: #57d6dd;
  color: #ffffff;
  align-items: stretch;
}
.iscenter {
  flex: 1;
}
.isleft,
.isright {
  flex: 0.5;
  padding: 0 2vw;
}
.click-icon {
  font-size: 20px;
}
.number-wrap {
  flex-wrap: wrap;
}
.number-item {
  max-width: 100%;
}
.number-item:not(:last-child)::after {
  content: "|";
  margin: 0 0.5em 0 0.25em;
}
/* 主体 */
.bets {
  flex: 1;
  width: 100%;
}
.bets-time-left {
  line-height: 10vw;
}

.bets-ticket {
  width: 94.6vw;
  height: 4.2vw;
  border: 1.4vw solid #fff;
  border-radius: 2.1vw;
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3), 0 0 3px 1px rgba(0, 0, 0, 0.2);
}
.ticket-wrap {
  position: relative;
  margin-top: -2.2vw;
  padding-top: 2.8vw;
  width: 89vw;
  background: #ffffff;
  border: 1px solid #ddd;
  filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0.3));
}
.ticket-wrap > div {
  max-height: 75vh;
  overflow-y: auto;
}
.ticket-wrap::after {
  content: "";
  position: absolute;
  left: 0;
  bottom: calc(-4vw + 1px);
  width: 100%;
  height: 4vw;
  background: radial-gradient(#fff, #fff 2vw, transparent 2vw) repeat-x;
  background-size: 4vw 4vw;
  background-position: 0 -2vw;
}
.ticket-item + .ticket-item {
  border-top: 1px dashed #c8c8c8;
}
.ticket-item-info {
  flex: 1;
  align-items: flex-start;
  padding: 1vw 2vw;
  line-height: 1.6;
  text-align: left;
}
.cancel-ticket {
  align-self: flex-start;
  padding: 2vw;
  color: #dbdfe6;
}
.number-key {
  flex: none;
}
/* 底部固定栏 */
.hecai-bottom {
  width: 100%;
  padding: 0 2vw;
  height: 10.5vw;
  line-height: 6.5vw;
  border-top: 1px solid #e1e1e1;
}
.hecai-btn {
  border: 1px solid;
  padding: 0 3vw;
  border-radius: 4px;
}
.random-btn {
  background: #ffefef;
  color: #e64100;
  border-color: #e64100;
}
.submit-btn {
  background: #eee;
  border-color: #eee;
  color: #afafaf;
  transition: background 0.5s;
}
.submitIng {
  padding: 1vw 4.5vw;
}
.cansubmit {
  background: #e64600;
  border-color: #e64600;
  color: #fff;
}
.should-pay {
  height: 100%;
  line-height: 1;
}
</style>
