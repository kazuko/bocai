<template>
  <div class="contanier">

    <!-- 固定头部 -->
    <dentGameHeader v-on:onselect="onSwitch"
                    v-on:onClickRightOrder="$router.push({name:'6hecaiwaiForPay',query:{getOrder:false}})"
                    v-on:onClickOpr="onClickOpr"
                    :obj="odds"
                    :rightOrder="haveGLOBALOrderList"
                    subType="normal"
                    gameKey="six"
                    rightNormal="true">
      <span slot="tips">玩法</span>
    </dentGameHeader>

    <div class="flex-wrap flex flow-col">
      <!-- 开奖相关信息 -->
      <div class="open flex bgc-fff">
        <div class="time-left flex flow-col justify-sa">
          <p class="fz12">距离{{nextIssue}}期截止</p>
          <div class="count-down font-theme">
            {{timer.timeToOpen}}
          </div>
        </div>
        <div v-if="recentInfo.length"
             @click="openLately=!openLately"
             dentHoverclass="hoverclass"
             class="open-num flex flow-col justify-sa">
          <p class="flex">第{{recentInfo[0].issue}}期 <span :class="openLately?'_arrow-down':''"
                  class="iconfont icon-arrow-down"></span> </p>
          <openNum :openNumObj="JSON.parse(recentInfo[0].code)"
                   :showAnimals="true"></openNum>
        </div>
        <!-- loading提示 -->
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
            <tr v-if="Object.keys(recentInfo).length"
                v-for="(item,index) in recentInfo"
                :class="index%2===1?'bg-grey':''">
              <td>{{item.issue+'期'}}</td>
              <td>
                <openNum :openNumObj="JSON.parse(item.code)"
                         :showAnimals="true"></openNum>
              </td>
              <td>{{mixin_KeyToCharacter(item.wx)}}</td>
              <td>{{'头'+item.tt}}</td>
              <td>{{'尾'+item.tw}}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 子玩法切换 -->
      <switchTab v-if="needSwitch && currentOdds !== 'lqlw'"
                 :tabObj="odds[currentOdds]"
                 :type="currentOdds"
                 v-model="activeTab"></switchTab>
      <!-- 连肖连尾特殊处理                  -->
      <switchTab v-else-if="needSwitch && currentOdds === 'lqlw'"
                 :tabObj="odds.lqlw.show"
                 :type="currentOdds"
                 v-model="activeTab"></switchTab>
      <!-- 合肖特殊处理 -->
      <switchTab v-else-if="currentOdds === 'hq'"
                 :tabObj="odds.hq.odds.fenlei"
                 :type="currentOdds"
                 v-model="shouldCommit"></switchTab>

      <!-- 下注信息展示区 -->
      <div :class="!Object.keys(odds).length?'justify-ct':''"
           class="odds-wrap flex flow-col">
        <!-- loading提示 -->
        <mt-spinner v-if="!Object.keys(odds).length"
                    color="#e62b00"
                    type="fading-circle"></mt-spinner>
        <!-- 内容区 -->
        <template v-else>
          <template v-if="needSwitch">
            <mt-tab-container swipeable="true"
                              v-model="activeTab">
              <!-- 连肖连尾特殊处理 -->
              <mt-tab-container-item v-if="currentOdds === 'lqlw'"
                                     v-for="(value,key,index) in odds.lqlw.show"
                                     :id="index"
                                     :key="index">
                <bets v-if="activeTab === index"
                      :betsObj="value"
                      v-model="shouldCommit"
                      :type="currentOdds"
                      :activeTab="activeTab"
                      :haoma="haoma"
                      :currentSubOdds="currentSubOdds"
                      v-on:ruleModalShow="ruleModalShow=true"></bets>
              </mt-tab-container-item>
              <mt-tab-container-item v-for="(value,key,index) in odds[currentOdds]"
                                     :id="index"
                                     :key="index">
                <bets v-if="activeTab === index"
                      :betsObj="value"
                      v-model="shouldCommit"
                      :type="currentOdds"
                      :activeTab="activeTab"
                      :haoma="haoma"
                      :currentSubOdds="currentSubOdds"
                      v-on:ruleModalShow="ruleModalShow=true"></bets>
              </mt-tab-container-item>

            </mt-tab-container>
          </template>
          <!-- 合肖特殊处理 -->
          <template v-else-if="currentOdds === 'hq'">
            <bets :betsObj="odds.hq.show"
                  v-model="shouldCommit"
                  :haoma="haoma"
                  :currentSubOdds="currentOdds"
                  v-on:ruleModalShow="ruleModalShow=true"></bets>
          </template>
          <template v-else>
            <bets :betsObj="odds[currentOdds]"
                  v-model="shouldCommit"
                  :haoma="haoma"
                  :currentSubOdds="currentOdds"
                  v-on:ruleModalShow="ruleModalShow=true"></bets>
          </template>

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
        </template>
      </div>

      <!-- 底部下注栏 -->
      <div class="hecai-bottom flex justify-sb bgc-fff">
        <div @click="shouldCommit = []"
             dentHoverclass="theme-hoverclass"
             class="hecai-btn random-btn">清空</div>

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
    <dentGameRuleModal ruleKey="six-rules"
                       v-model="ruleModalShow"
                       :currentOdds='currentSubOdds || currentOdds'></dentGameRuleModal>

  </div>
</template>

<script>
import dentGameHeader from "@/components/common/dentGameHeader";
import openNum from "./6hecai-openNum";
import switchTab from "./6hecai-switch";
import bets from "./6hecai-bets";
import {gameFunc,countDownFunc,toggleShouldKeepAlive,filterMethods} from '@/mixins/mixins'

export default {
  name:'6hecai',
  mixins: [gameFunc, countDownFunc, toggleShouldKeepAlive, filterMethods],
  components: {
    dentGameHeader,
    dentGameRuleModal: () => import('@/components/common/dentGameRuleModal'),
    openNum,
    switchTab,
    bets
  },
  data() {
    return {
      gameKey:'six',
      /* 数据相关 */
      shengxiao:"", //当前生肖年 用在合肖和连肖连尾
      odds:{}, //全部赔率信息
      haoma:{}, //生肖和五行号码
      currentOdds: 'lm',  //当前选择的大玩法（默认第一种）
      shouldCommit:[],  //用户的下注信息
      default_gold: 10,  //每单的金额
      totalBets:0,  //下了多少注
      canEarn:0,  //单注最多可盈利
      maxOdds:0,  //注单的赔率 用在 连肖连尾 和 和肖
      /* 状态相关 */
      activeTab: 0,  //当前激活的 子玩法的id
      openLately: false,  //近期开奖 展示收起
      ruleModalShow: false, //玩法规则 弹窗
      haveGLOBALOrderList:false,  //有未处理订单
    }
  },
  computed:{
    // 玩法是否有子玩法切换
    needSwitch (){
      let needSwitchOdds = ['qmwx','wsh','sb','shx','zxbzh','lqlw','lma','zhm1-6','zhmt','zhy'];

      return needSwitchOdds.includes(this.currentOdds);
    },
    //当前选择的子玩法
    currentSubOdds (){
      let currentSubOdds = '';
      let {odds,currentOdds,activeTab} = this;
      let lqlw = currentOdds === 'lqlw';  //连肖连尾特殊处理
      if(lqlw) {
        currentSubOdds = Object.keys(odds.lqlw.show)[activeTab];
      }else {
        currentSubOdds = this.needSwitch && Object.keys(odds[currentOdds])[activeTab];
      }

      return currentSubOdds;
    }
  },
  mounted: function() {
    this.mixins_getOddsData({gameKey:'six',断网了:false});
  },
  methods: {

    //把下注信息传递到 waitforPay(生成订单) 页面
    sendOrderToPage () {
      //先判断是否登录
      this.checkLogin(() => {
        let {shengxiao,currentOdds,currentSubOdds,shouldCommit} = this;
        let issue = this.nextIssue;
        let order = {
          id: 11,
          issue,
          zhuShu : 1,
          type: currentOdds,
          leiXin: currentSubOdds,
          gold: this.default_gold,
          allGold: this.default_gold,
          number: shouldCommit,
          key:'six',
          gameName:'香港⑤合彩',
          avatar: this.GLOBAL.sixImg
        }

        if(currentOdds === 'hq'){
          // 合肖特殊处理
          let shengxiaoOrNot = shouldCommit.includes(shengxiao);
          let leiXin = shengxiaoOrNot ? shengxiao + shouldCommit.length : 'else' + shouldCommit.length;
          order.leiXin = leiXin;
          order.odds = this.maxOdds;
        }
        if(currentOdds === 'lqlw'){
          //连肖连尾
          order.odds = this.odds.lqlw.odds.lqpeilv[currentSubOdds];
        }
        if(currentOdds === 'zhmgg'){
          //正码过关
          order.odds = this.maxOdds;
        }
        //清空注单信息
        this.shouldCommit = [];
        if(currentOdds === 'zhmgg') this.shouldCommit = {zhy:'',zher:'',zhs:'',zhsi:'',zhw:'',zhl:'',}
        //将信息保存到全局变量
        this.GLOBAL.six.order = order;
        this.$router.push({name:'6hecaiwaiForPay',query:{getOrder:true}});
      });
    },

    //切换不同的玩法
    onSwitch(val) {
      this.currentOdds = val;
      //将激活的tab改为0 ，清空下注信息
      this.canEarn = this.totalBets = this.activeTab = 0;
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

  },

  watch: {
    //activeTab 切换子玩法清空下注信息
    activeTab(){
      this.totalBets = this.canEarn = 0;
      this.shouldCommit = [];
    },
    //shouldCommit 用户的下注信息 做相应的处理
    shouldCommit: {
      handler(shouldCommitVal){
        let totalBets = 0;
        let maxOdds = 0;
        let {odds,currentOdds,currentSubOdds,default_gold,shengxiao} = this;

        // 不同的玩法 选择不同的计算注数计算方式
        switch( currentOdds ) {

          case "hq":
            hqFunc.call(this);
          break;

          case "lqlw":
            lqlwFunc.call(this);
          break;

          case "zhmgg":
            zhmggFunc.call(this);
          break;

          case "lma":
          case "zxbzh":
          case "zhy":
            minSelectFunc();
          break;

          default:
            defaultFunc();
          break;
        }

        this.canEarn = (maxOdds*default_gold).toFixed(3);
        this.totalBets = totalBets;

        function defaultFunc(){
          //找出数组中赔率最高的 以计算单注最多可盈利多少
            let obj = currentSubOdds ? odds[currentOdds][currentSubOdds] : odds[currentOdds];

            maxOdds = Math.max(...shouldCommitVal.map( key => {
              return obj[key];
            }));
            totalBets += shouldCommitVal.length;
        }

        function minSelectFunc(){
          //至少选择 minLengtn 个号码 才能组成一注
            let minLengtn = currentSubOdds.match(/\d+/)[0];
            if(shouldCommitVal.length < minLengtn){return};

            maxOdds = odds[currentOdds][currentSubOdds];
            totalBets = calcBets(shouldCommitVal.length,minLengtn);
        }

        function zhmggFunc(){
          let notNull = 0;
            for(let key in shouldCommitVal){
              if( shouldCommitVal[key] !== ''){
                notNull += 1;
              }
              if(notNull === 2){break};
            }

            totalBets = notNull === 2 ? 1 : 0;
            maxOdds = 1;
            for(let subkey in shouldCommitVal){ //正码过关 赔率乘起来
              if(!!shouldCommitVal[subkey]){
                maxOdds *= odds[currentOdds][subkey][ shouldCommitVal[subkey] ];
              }
            }

            this.maxOdds = maxOdds;
        }

        function hqFunc(){
          //至少2个生肖组成一注
          if(shouldCommitVal.length < 2) { return };
          //最多只能选择11个生肖
          if(shouldCommitVal.length > 11) {
            Toast({
              message:'最多只能选择11个生肖哟',
              duration: 2000
            });
            shouldCommitVal.pop();
            return;
          };

          // 合肖的赔率
          let shengxiaoOrNot = shouldCommitVal.includes(shengxiao);
          let maxOddsKey = shengxiaoOrNot ? shengxiao + shouldCommitVal.length : 'else' + shouldCommitVal.length;

          totalBets = 1;
          maxOdds = shengxiaoOrNot ? odds.hq.odds.fensang[shengxiao][maxOddsKey] : odds.hq.odds.fensang.else[maxOddsKey];
          this.maxOdds = maxOdds;
        }

        function lqlwFunc() {
          //至少2个生肖组成一注
          if(shouldCommitVal.length < 2) { return };
          //最多只能选择6个生肖
          if(shouldCommitVal.length > 6) {
            Toast({
              message:'最多只能选择6个哟',
              duration: 2000
            });
            shouldCommitVal.pop();
            return;
          };

          // 连肖连尾的注数
          let minLengtn = currentSubOdds.match(/\d+/)[0];
          totalBets = calcBets(shouldCommitVal.length,minLengtn);

          // 连肖连尾的赔率
          if((/lw/i).test(currentSubOdds)){
            // 连尾赔率
            maxOdds = odds.lqlw.odds.lqpeilv[currentSubOdds];
          }else {
            // 连肖赔率
            let shengxiaoOrNot = shouldCommitVal.includes(shengxiao);
            let maxOddsKey = shengxiaoOrNot ? shengxiao : 'no' + shengxiao;
            maxOdds = odds.lqlw.odds.lqpeilv[currentSubOdds][maxOddsKey];
          }
          this.maxOdds = maxOdds;
        }

        function calcBets( bets , n ) {
          if(bets < n){return 0}
          function Factorial(num) {
            return num <= 1 ? 1 : num*Factorial(num-1);
          }

          let totalBets = Factorial(bets) / (Factorial (bets - n) * Factorial(n))
          return totalBets;
        }
      },
      deep:true,
    },
  },

}
</script>

<style scoped>
.hoverclass {
  background: rgba(230, 70, 0, 0.1);
  box-shadow: none;
}
.theme-hoverclass {
  background: rgba(230, 65, 0, 0.2) !important;
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
.odds-wrap {
  flex: 1;
  position: relative;
  width: 100%;
  overflow: auto;
}
.connecting {
  flex: 1;
  align-self: stretch;
  padding: 2vw 0;
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
  border-bottom: 1px solid #ebebeb;
}
.count-down {
  font-size: 3.73vw;
  letter-spacing: 0.1em;
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
  font-size: 3.2vw;
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
  font-size: 3.2vw;
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
</style>
