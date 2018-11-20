<template>
  <div class="contanier">

    <!-- 固定头部 -->
    <dentGameHeader v-on:onselect="onSwitch"
                    v-on:onClickRightIcon="onClickRightIcon"
                    v-on:onClickOpr="onClickOpr"
                    :obj="odds"
                    rightOrder="true"
                    rightNormal="true">
      <span slot="tips">玩法</span>
    </dentGameHeader>

    <div class="flex-wrap flex flow-col">
      <!-- 开奖相关信息 -->
      <div class="open flex bgc-fff">
        <div class="time-left flex flow-col justify-sa">
          <p class="fz12">距离8114期截止时间</p>
          <div class="font-theme">
            10:17:26
          </div>
        </div>
        <div @click="openLately=!openLately"
             dentHoverclass="hoverclass"
             class="open-num flex flow-col justify-sa">
          <p class="flex">第8113期 <span :class="openLately?'_arrow-down':''"
                  class="iconfont icon-arrow-down"></span> </p>
          <openNum :arr=[42,19,29,7,19,9,41]
                   :showAnimals="true"></openNum>
        </div>
      </div>
      <!-- 近期开奖 -->
      <div :class="openLately?'open-lately-expand':''"
           class="open-lately">
        <table style="width: 120%;">
          <colgroup>
            <col width='18%'>
            <col width='40%'>
            <col width='14%'>
            <col width='14%'>
            <col width='14%'>
          </colgroup>
          <thead>
            <tr class="bg-grey">
              <th>期次</th>
              <th>开奖号码</th>
              <th>五行</th>
              <th>特头</th>
              <th>特尾</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>8114期</td>
              <td>
                <openNum :arr=[42,19,29,7,39,9,41]
                         :showAnimals="true"></openNum>
              </td>
              <td>木</td>
              <td>头0</td>
              <td>尾8</td>
            </tr>
            <tr class="bg-grey">
              <td>8114期</td>
              <td>
                <openNum :arr=[45,18,44,7,27,9,15]
                         :showAnimals="true"></openNum>
              </td>
              <td>木</td>
              <td>头0</td>
              <td>尾8</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 子玩法切换 -->
      <switchTab v-if="needSwitch"
                 :tabObj="odds[currentOdds]"
                 :type="currentOdds"
                 v-model="activeTab"></switchTab>
      <!-- 合肖特殊处理 -->
      <switchTab v-else-if="currentOdds === 'hq'"
                 :tabObj="odds.hq.odds.fenlei"
                 :type="currentOdds"
                 v-model="activeTab"></switchTab>

      <!-- 下注信息展示区 -->
      <div class="odds-wrap flex flow-col">

        <template v-if="needSwitch">
          <mt-tab-container swipeable="true"
                            v-model="activeTab">

            <mt-tab-container-item v-for="(value,key,index) in odds[currentOdds]"
                                   :id="index"
                                   :key="index">
              <bets :betsObj="value"
                    v-model="shouldCommit"
                    :type="currentOdds"
                    :activeTab="activeTab"
                    :currentSubOdds="currentSubOdds"
                    v-on:oddsTips="oddsTips=!oddsTips"></bets>
            </mt-tab-container-item>

          </mt-tab-container>
        </template>
        <!-- 合肖特殊处理 -->
        <template v-else-if="currentOdds === 'hq'">
          <bets :betsObj="odds.hq.show"
                v-model="activeTab"
                :currentSubOdds="currentOdds"
                v-on:oddsTips="oddsTips=!oddsTips"></bets>
        </template>
        <template v-else>
          <bets :betsObj="odds[currentOdds]"
                v-model="shouldCommit"
                :currentSubOdds="currentOdds"
                v-on:oddsTips="oddsTips=!oddsTips"></bets>
        </template>

        <!-- 下了几注展示 -->
        <transition name="slideUp">
          <div v-show="totalBets"
               class="bets-total flex justify-sb">
            <div>
              <span>共<span class="font-theme">{{totalBets}}</span>注</span>
              <span>共<span class="font-theme">{{totalBets*default_gold}}</span>元</span>
            </div>
            <span>单注最多可盈利<span class="font-theme">3.960</span>元</span>
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
             class="hecai-btn submit-btn cansubmit">确定</div>
        <div v-else
             class="hecai-btn submit-btn">确定</div>
      </div>

    </div>

    <!-- 玩法规则弹窗 -->
    <transition name="fadeIn">
      <div v-show="oddsTips"
           @click.self="oddsTips=!oddsTips"
           class="modal-mask">
        <div class="modal example-modal">
          <div class="modal-title">
            玩法规则
          </div>
          <div class="modal-body">
            <div class="example-tips">
              <p class="example-title">
                <span class="iconfont icon-idea font-theme"></span>玩法提示</p>
              <p class="example-content">
                {{currentSubOdds ? rules[currentSubOdds].tips : rules[currentOdds].tips}}
              </p>
            </div>
            <div class="example-tips">
              <p class="example-title">
                <span class="iconfont icon-jihua font-theme"></span>中奖说明</p>
              <p class="example-content">
                {{currentSubOdds ? rules[currentSubOdds].rule : rules[currentOdds].rule}}
              </p>
            </div>
            <div class="example-tips">
              <p class="example-title">
                <span class="iconfont icon-case font-theme"></span>范例</p>
              <p class="example-content">
                {{currentSubOdds ? rules[currentSubOdds].case : rules[currentOdds].case}}
              </p>
            </div>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script>
import dentGameHeader from "@/components/common/dentGameHeader";
import openNum from "./compoent-openNum";
import switchTab from "./compoent-switch";
import bets from "./compoent-bets";
import { odds,rules } from "@/js/6hecai-odds";

export default {
  components: {
    dentGameHeader,
    openNum,
    switchTab,
    bets
  },
  data() {
    return {
      /* 数据相关 */
      odds, //全部赔率信息
      rules, //全部的玩法规则
      currentOdds: 'lm',  //当前选择的大玩法（默认第一种）
      default_gold: 2,  //每单的金额
      shouldCommit:[],  //两面的下注信息
      totalBets:0,  //下了多少注
      /* 状态相关 */
      activeTab: 0,  //当前激活的 子玩法的id
      openLately: false,  //近期开奖 展示收起
      oddsTips: false //玩法规则 弹窗
    }
  },
  computed:{
    // 玩法是否有子玩法切换
    needSwitch (){
      let needSwitchOdds = ['qmwx','wsh','sb','shx','zxbzh','lqlw','lma','zhm1-6','zhmt','zhy'];

      return needSwitchOdds.includes(this.currentOdds);
    },
    // //当前选择的子玩法
    currentSubOdds (){
      let {odds,currentOdds,activeTab} = this;
      return this.needSwitch && Object.keys(odds[currentOdds])[activeTab];
    }
  },
  mounted: function() {
    this.$parent.tabbarShow = false;

    this.getOddsData();
  },
  methods: {

    //切换不同的玩法
    onSwitch(val) {
      this.currentOdds = val;
      //将激活的tab改为0 ，清空下注信息
      this.activeTab = 0;
      this.shouldCommit = [];
    },

    //点击了常用操作弹窗
    onClickOpr(index){
      switch(index){
        case 0:
            this.$router.push('/6hecai/6hecai-charts')
          break;
        case 1:
            this.$router.push('/6hecai/6hecai-open')
          break;
        case 2:
          this.$router.push('/6hecai/6hecai-order')
        break;
      }
    },

    //请求 赔率 数据
    getOddsData() {
      let first = true;
      let weijie  = "ws://192.168.2.100:2310";
      let sendDataToServer = () => {
        this.senddata({type:"deskInfo",game:'Six',first});
      }

      try{
        let odds = this.getLocalCache('6hecai-odds');
        first = false;
      }catch(e){
        first = true;
      }

      //请求数据
      if (!this.GLOBAL.gameSocketHand) {
        this.createSocket(weijie, () => {
          this.dealOnMessage(); //创建连接成功之后 监听返回的信息
          sendDataToServer();
        });
      } else {
        sendDataToServer();
      }

    },

    //处理返回的数据
    dealOnMessage() {
      this.onmessage( (res) => {
        res = JSON.parse(res);
        debugger
        let checkRes =  res instanceof Object || res instanceof Array;
        //oddsStatus = 0 赔率没有修改 ,oddsStatus = 1 赔率发生改变
        let modified = !!res.odds.oddsStatus;

        if(checkRes && modified) {
          res.odds && (this.deskInfo = res.odds);
          this.setLocalCache('11xuan5-odds',res.odds);
        }else if(checkRes && !modified){
          this.deskInfo = this.getLocalCache('11xuan5-odds');
          console.log('11选5赔率没有更改，从localStorage读取成功');
        }
        res.default_gold && (this.default_gold = res.default_gold);
        res.open && (this.open = res.open);

      })
    },

  },

  watch: {
    // watch shouldCommit 用户的下注信息 做相应的处理
    shouldCommit(newVal) {
      let totalBets = 0;
      // 不同的玩法 选择不同的计算注数计算方式
      switch( this.currentOdds ) {
        default:
            totalBets += newVal.length;
          break;
      }

      this.totalBets = totalBets;
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

.fadeIn-enter-active,
.fadeIn-leave-active {
  will-change: opacity;
  transition: opacity 500ms;
}
.fadeIn-enter,
.fadeIn-leave-to {
  opacity: 0;
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
.odds-wrap {
  flex: 1;
  position: relative;
  width: 100%;
  overflow: auto;
}
.mint-tab-container {
  flex: 1;
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
.balls-item,
.balls-spec-item {
  margin: 0.5vw;
  width: 6vw;
  height: 6vw;
  background: #e64600;
  font-size: 12px;
  line-height: 6vw;
  text-align: center;
  color: #fff;
  border-radius: 50%;
}
.balls-spec-item {
  background: #008000;
}
.balls-text {
  color: #535353;
}
.balls-link {
  position: relative;
  top: -0.5em;
  margin: 1vw;
}

.open-lately {
  width: 100%;
  transition: 0.5s;
  max-height: 1px;
  overflow: auto;
}
.open-lately-expand {
  max-height: 100vh;
}
table {
  border-spacing: 0;
  border-collapse: collapse;
  background: #fff;
  border-bottom: 1px solid #dedede;
  color: #8994b1;
}
.open-lately th {
  line-height: 7.8vw;
  font-weight: normal;
}
.bg-grey {
  background: #f2f5f8;
}

/* 下注总信息 */
.bets-total {
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

/* 玩法规则弹窗 */
.modal {
  transition: 0.25s;
}
.fadeIn-enter .modal {
  transform: translate3d(100%, 0, 0);
}
.fadeIn-leave-active .modal {
  transform: translate3d(100%, 0, 0);
}
.example-modal .modal-title {
  background: #e64600;
  color: #fff;
  line-height: 40px;
}
.example-modal .modal-body {
  padding: 10px;
  padding-bottom: 30px;
}
.example-title {
  line-height: 2;
  font-weight: bold;
}
.example-title .iconfont {
  margin-right: 5px;
}
.example-content {
  padding: 0 1.5em 0;
  font-size: 14px;
}
</style>
