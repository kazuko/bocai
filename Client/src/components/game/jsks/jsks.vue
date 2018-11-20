<template>
  <div class="contanier">

    <!-- 固定表头 -->
    <dentGameHeader v-on:onSubSelect="mixins_onSwitch"
                    v-on:onClickOpr="mixins_onClickOpr"
                    :rightOrder="haveGLOBALOrderList"
                    v-on:onClickRightOrder="mixins_goTOPay(false)"
                    :obj="jsksobj"
                    :gameKey="gameKey"
                    subType="multipleSub"
                    rightNormal="true">
      <span slot="tips">玩法</span>
    </dentGameHeader>

    <div class="flex-wrap flex flow-col">
      <!-- 开奖相关信息 -->
      <div class="open flex bgc-fff">
        <div class="time-left flex flow-col justify-sa">
          <p class="fz12">距离{{nextIssue}}期</p>
          <div class="font-theme">
            {{timer.timeToOpen}}
          </div>
        </div>
        <div v-if="recentInfo.length"
             @click="openLately=!openLately"
             dentHoverclass="hoverclass"
             class="open-num flex flow-col justify-sa">
          <p class="flex">第{{recentInfo[0].issue}}期 <span :class="openLately?'_arrow-down':''"
                  class="iconfont icon-arrow-down"></span> </p>
          <div class="dice-wrap flex justify-ct">
            <img v-for="src in recentInfo[0].code_img"
                 :src="src"
                 class="dice">
          </div>
        </div>
        <div v-else
             class="connecting flex flow-col justify-sa">
          <span>正在连接服务器,请不要下单</span>
          <mt-spinner color="#e62b00"
                      type="fading-circle"></mt-spinner>
        </div>
      </div>
      <!-- 近期开奖 -->
      <div :class="openLately?'open-lately-expand':''"
           class="open-lately">
        <table>
          <colgroup>
            <col width='20%'>
            <col width='50%'>
            <col width='10%'>
            <col width='10%'>
            <col width='10%'>
          </colgroup>
          <thead>
            <tr class="bg-grey">
              <th>期次</th>
              <th>开奖号码</th>
              <th>和值</th>
              <th>大小</th>
              <th>单双</th>
            </tr>
          </thead>
          <tbody v-if="recentInfo.length">
            <tr v-for="(tr,index) in recentInfo"
                :class="index%2===1?'bg-grey':''">
              <td>{{tr.issue}}</td>
              <td class="td-dice-wrap flex justify-sa">
                <img v-for="src in tr.code_img"
                     :src="src"
                     class="dice">
                <span v-for="n in tr.opencode">{{n}}</span>
              </td>
              <td>{{tr.sum}}</td>
              <td>{{tr.size}}</td>
              <td>{{tr.s_b}}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 下注信息展示区 -->
      <div class="odds-wrap flex flow-col">

        <bets v-model="shouldCommit"
              :betsObj="betsObj"
              :currentOdds="currentOdds"
              v-on:ruleModalShow="ruleModalShow=true"></bets>

        <!-- 手动选号符合规则的注单 -->
        <transition name="popUp">
          <div v-if="handy"
               v-show="totalBets"
               class="bets-handy">
            <transition-group name="betsDropin"
                              tag="div">
              <div v-for="(n,index) in shouldCommit"
                   :key="index"
                   class="bets-handy-item">
                <span>{{n}}</span><span v-if="index !== shouldCommit.length-1"> , </span>
              </div>
            </transition-group>
          </div>
        </transition>

        <!-- 下了几注展示 -->
        <transition name="slideUp">
          <div v-show="totalBets"
               class="bets-total flex justify-sb">
            <div>
              <span>共<span class="font-theme">{{totalBets}}</span>注</span>
              <span>共<span class="font-theme">{{totalBets*default_gold}}</span>元</span>
            </div>
            <span>单注最多可盈利<span class="font-theme">{{canEarn}}</span>元</span>
          </div>
        </transition>

      </div>

      <!-- 底部下注栏 -->
      <div class="hecai-bottom flex justify-sb bgc-fff">
        <div v-if="totalBets"
             class="hecai-btn random-btn">清空</div>
        <div v-else
             class="hecai-btn random-btn">机选</div>

        <div class="bets-per-gold">单注<input v-model="default_gold"
                 type="text">元</div>

        <div v-if="totalBets"
             @click="sendOrderToPage"
             class="hecai-btn submit-btn cansubmit">确定</div>
        <div v-else
             class="hecai-btn submit-btn">确定</div>
      </div>

    </div>

    <!-- 玩法规则弹窗 -->
    <dentGameRuleModal ruleKey="jsks-rules"
                       v-model="ruleModalShow"
                       :currentOdds='currentOdds'></dentGameRuleModal>

  </div>
</template>

<script>
import dentGameHeader from "@/components/common/dentGameHeader";
import bets from "./jsks-bets";
import { jsksobj } from "@/js/jsks";
import {gameFunc, countDownFunc, toggleShouldKeepAlive} from '@/mixins/mixins'

export default {
  name:'jsks',
  mixins: [gameFunc, countDownFunc, toggleShouldKeepAlive],
  components: {
    dentGameRuleModal: () => import('@/components/common/dentGameRuleModal'),
    dentGameHeader,
    bets
  },
  data() {
    return {
      gameKey:'jsks',
      gameName: '江苏快三',
      /* 数据相关 */
      odds:{},  //全部赔率信息
      jsksobj, //用于生成子玩法的对象
      currentOdds: '',  //当前选择的大玩法（默认第一种）
      default_gold: 2,  //每单的金额
      shouldCommit:[],  //下注信息
      totalBets:0,  //下了多少注
      maxOdds:0,  //注单最大赔率

      /* 状态相关 */
      openLately: false,  //近期开奖 展示收起
      ruleModalShow: false, //玩法规则 弹窗
    }
  },
  computed:{
    //需要传递给 compoent-bets 渲染的对象
    betsObj(){
      let betsObj;
      let ifNeedBetsObj = ['sum-sum-size','sum-sum-sum'].includes(this.currentOdds);
      if(ifNeedBetsObj){
        let [type, key, subkey ] = this.currentOdds.split('-');
        betsObj = this.odds[key][subkey];
      }

      return betsObj;
    },

    handy(){
      //有 handy 字符串，都是手动选号
      return new RegExp(/handy/).test(this.currentOdds);
    },

    //单注最多可以赢
    canEarn(){
      return this.maxOdds * this.default_gold;
    }
  },
  mounted() {
    this.mixins_getOddsData({gameKey:'jsks',断网了:false});
  },
  methods: {

    //把下注信息传递到 waitforPay(生成订单) 页面
    sendOrderToPage () {
      //先判断是否登录
      this.checkLogin(() => {
        let id = JSON.parse(localStorage.getItem("bc_userInfo")).id;
        let {nextIssue, totalBets, currentOdds, default_gold, shouldCommit,gameKey,gameName} = this;
        let type = this.currentOdds.split('-')[0];
        let order = {
          id,
          issue: nextIssue,
          zhuShu : totalBets,
          type,
          leiXin: currentOdds,
          gold: default_gold,
          allGold: totalBets*default_gold,
          number: shouldCommit,
          key:gameKey,
          gameName,
          avatar: this.GLOBAL.jsksImg
        }

        //清空注单信息
        this.shouldCommit = [];
        //将信息保存到全局变量
        this.GLOBAL.jsks.order = order;
        this.$router.push({name:'dentGameWaiForPay',
                          query:{getOrder:true,gameKey}});
      });
    }

  },

  watch: {
    //shouldCommit 根据用户选择的号码 和 当前的玩法 生成相应的订单信息
    shouldCommit:{
      handler(shouldCommit){
        let totalBets = 0;
        let maxOdds;

        // 不同的玩法 选择不同的计算注数计算方式
        switch( this.currentOdds ) {

          case "twoDiffrent-twoDiffrent-commom":
              twoDiffrentCommom();
            break;

          case "threeDiffrent-threeDiffrent-commom":
            threeDiffrentCommom();
            break

          case "twoSame-twoSameSg-commom":
          case "twoDiffrent-twoDiffrent-dantuo":
              dantuo();
            break;

          case "threeSame-threeSameDb-threeSameDb":
          case "threeConAll-threeConAllCommom-threeConAllCommom":
              three();
            break;

          default:
              normal();
            break;
        }

        //二不同号-标准选号
        function twoDiffrentCommom(){
          let bets = shouldCommit.length;
          totalBets = calcBets(bets,2);
        }

        //三不同号-标准选号
        function threeDiffrentCommom(){
          let bets = shouldCommit.length;
          totalBets = calcBets(bets,3);
        }

        //胆拖选号
        function dantuo(){
          let length = shouldCommit.second && shouldCommit.second.length;
          totalBets = shouldCommit.first   && length;
        }

        //三同号通选 和 三连号通选
        function three(){
          totalBets = shouldCommit.length && 1;
        }

        //一个号码为一注 [二不同号-手动选号]
        function normal(){
          totalBets = shouldCommit.length;
        }

        /* 求最大赔率 */
        function getMaxOdds(){
          if( ["twoSame-twoSameSg-commom","twoSame-twoSameSg-handy"].includes(this.currentOdds) ) {
            //二同号单选的 标准选号 和 手动选号
            maxOdds = this.odds.twoSame.twoSameSg;
          }else if( ["sum-sum-size","sum-sum-sum"].includes(this.currentOdds) ) {
            //和值 大小单双 和 和值
            let key = this.currentOdds.split('-')[1];
            let oddsArray = shouldCommit.map( subkey => {
              return this.odds.sum[key][subkey];
            })

            maxOdds = Math.max(...oddsArray);
          }else {
            let [temp,subtemp] = this.currentOdds.split('-'); //从 当前玩法 字段，截取key和subKey
            if( this.odds[temp] instanceof Object ) {
              maxOdds = this.odds[temp][subtemp];
            }else {
              maxOdds = this.odds[temp];
            }
          }
        }
        getMaxOdds.call(this);

        /* 组合公式求值 */
        function calcBets( bets , n ) {
          if(bets < n){return 0}
          function Factorial(num) {
            return num <= 1 ? 1 : num*Factorial(num-1);
          }

          let totalBets = Factorial(bets) / (Factorial (bets - n) * Factorial(n))
          return totalBets;
        }

        this.maxOdds = maxOdds;
        this.totalBets = totalBets;
      },
      deep:true
    },
  }
}
</script>

<style scoped>
.hoverclass {
  background: rgba(230, 70, 0, 0.1);
}
.slideUp-enter-active,
.slideUp-leave-active {
  will-change: transform;
  transition: 0.35s;
  transform: translate3d(0, 0, 0);
}
.slideUp-enter,
.slideUp-leave-to {
  transform: translate3d(0, 100%, 0);
}

.contanier {
  padding-top: 13.8vw;
  background: #ffffff;
}
.flex-wrap {
  height: calc(100vh - 13.8vw);
}
.fz12 {
  font-size: 12px;
}
.hidden {
  display: none;
}
.connecting {
  flex: 1;
  align-self: stretch;
  padding: 2vw 0;
}
.odds-wrap {
  flex: 1;
  position: relative;
  width: 100%;
  overflow: auto;
}
.mint-tab-container {
  width: 100%;
  height: 100%;
  overflow: auto;
}
.mint-tab-container-wrap {
  height: 100%;
}
/* 开奖信息 */
.open {
  width: 100%;
  height: 24vw;
  border-bottom: 1px solid #ebebeb;
}
.time-left {
  width: 36vw;
  padding-right: 3vw;
  height: 14.4vw;
  border-right: 1px solid #ebebeb;
}
.open-num {
  flex: 1;
  height: 20vw;
}
.open-num .icon-arrow-down {
  font-size: 12px;
  transition: 0.5s;
}
._arrow-down {
  transform: rotate(180deg);
}
.dice {
  margin: 0 2vw;
  width: 7vw;
  height: 7vw;
  background: #f2f5f8;
}
.td-dice-wrap {
  height: 9vw;
}
.td-dice-wrap .dice {
  margin: 0.5vw 1vw;
  width: 6vw;
  height: 6vw;
}
.td-dice-wrap > span {
  margin: 0 0.5vw;
}
.open-lately {
  width: 100%;
  transition: 0.5s;
  height: 0;
  overflow: auto;
}
.open-lately-expand {
  height: 65.8vw;
}
table {
  border-spacing: 0;
  border-collapse: collapse;
  background: #fff;
  color: #8994b1;
}
.open-lately th {
  line-height: 7.8vw;
  font-weight: normal;
}
.open-lately td {
  border-right: 1px solid #e6eaee;
}
.bg-grey {
  background: #f2f5f8;
}

/* 手动选号 符合规则的订单 */
.popUp-enter-active,
.popUp-leave-active {
  will-change: transform;
  transition: 0.35s;
  transform: translate3d(0, 0, 0);
}
.popUp-enter,
.popUp-leave-to {
  transform: translate3d(40%, 40%, 0);
}

.betsDropin-enter-active {
  transition: all 0.35s;
}
.betsDropin-leave-active {
  position: absolute;
  transition: all 0.35s;
}
.betsDropin-enter,
.betsDropin-leave-to {
  opacity: 0.5;
  transform: translate3d(-25%, -50%, 0);
}
.betsDropin-move {
  transition: transform 0.35s;
}

.bets-handy {
  position: absolute;
  bottom: 10vw;
  right: 2vw;
  padding: 0 4vw;
  background: #fff;
  line-height: 8vw;
  border-radius: 4vw;
  box-shadow: 0 0 8px rgba(144, 144, 144, 0.5);
}
.bets-handy > div {
  display: flex;
  flex-wrap: wrap;
}
.bets-handy-item {
  margin-right: 1vw;
}

/* 下注总信息 */
.bets-total {
  position: absolute;
  bottom: 0;
  z-index: 1;
  width: 100%;
  padding: 0 2vw;
  background: #eee;
  line-height: 8vw;
}

/* 底部固定栏 */
.hecai-bottom {
  width: 100%;
  padding: 2vw 2vw;
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
  transition: 0.5s;
}
.cansubmit {
  background: #e64600;
  border-color: #e64600;
  color: #fff;
}
.bets-per-gold input {
  margin: 0 1vw;
  width: 15vw;
  border: 1px solid #d4d4d4;
  line-height: 6.5vw;
  color: #e64600;
  border-radius: 4px;
  transition: 0.5s;
}
.bets-per-gold input:focus {
  border-color: currentColor;
}
</style>
