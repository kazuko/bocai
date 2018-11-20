<template>
  <div class="contanier">

    <!-- 固定表头 -->
    <dentGameHeader v-on:onSubSelect="mixins_onSwitch"
                    v-on:onClickOpr="mixins_onClickOpr"
                    :rightOrder="haveGLOBALOrderList"
                    v-on:onClickRightOrder="mixins_goTOPay(false)"
                    :obj="odds"
                    :gameKey="gameKey"
                    subType="multipleSub"
                    rightNormal="true">
      <span slot="tips">玩法</span>
    </dentGameHeader>

    <div class="flex-wrap flex flow-col">
      <div class="open flex bgc-fff">
        <!-- 倒计时 -->
        <div class="time-left flex flow-col justify-sa">
          <p class="fz12">距离{{nextIssue}}期</p>
          <div style="letter-spacing: .1em;"
               class="font-theme">
            {{timer.timeToOpen}}
          </div>
        </div>
        <!-- 开奖号码 -->
        <div v-if="recentInfo.length"
             @click="openLately=!openLately"
             dentHoverclass="hoverclass"
             class="open-num flex flow-col justify-sa">
          <p class="flex">第{{recentInfo[0].issue}}期 <span :class="openLately?'_arrow-down':''"
                  class="iconfont icon-arrow-down"></span> </p>
          <div class="flex flow-col justify-ct">
            <p><span v-for="(n,index) in recentInfoOpenCode"
                    v-if="index<5"
                    class="font-theme open-num-item">{{n}}</span></p>
            <p><span v-for="(n,index) in recentInfoOpenCode"
                    v-if="index>=5"
                    class="font-theme open-num-item">{{n}}</span></p>
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
            <col width='16%'>
            <col width='45%'>
            <col width='21%'>
            <col width='8%'>
            <col width='8%'>
          </colgroup>
          <thead>
            <tr class="bg-grey">
              <th>期次</th>
              <th>开奖号码</th>
              <th>冠亚和</th>
              <th>大小</th>
              <th>单双</th>
            </tr>
          </thead>
          <tbody v-if="recentInfo.length">
            <tr v-for="(tr,index) in recentInfo"
                :class="index%2===1?'bg-grey':''">
              <td>{{tr.issue}}</td>
              <td class="td-dice-wrap flex justify-sa">
                <span v-for="n in tr.open_code"
                      class="open-num-ball">{{n}}</span>
              </td>
              <td>{{tr.guanyahe}}</td>
              <td>{{tr.daxiao}}</td>
              <td>{{tr.danshuang}}</td>
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
        <div @click="shouldCommit = []"
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
    <dentGameRuleModal ruleKey="bjpk10-rules"
                       v-model="ruleModalShow"
                       :currentOdds='currentOdds'></dentGameRuleModal>

  </div>
</template>

<script>
import dentGameHeader from "@/components/common/dentGameHeader";
import bets from "./bjpk10-bets";
import {gameFunc, countDownFunc, toggleShouldKeepAlive} from '@/mixins/mixins'

export default {
  name:'bjpk10',
  mixins: [gameFunc, countDownFunc, toggleShouldKeepAlive],
  components: {
    dentGameRuleModal: () => import('@/components/common/dentGameRuleModal'),
    dentGameHeader,
    bets
  },
  data() {
    return {
      gameKey: "bjpk10",
      gameName:'北京pk10',
      /* 数据相关 */
      odds:{},  //全部赔率信息
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
      //从 当前玩法 字段，截取key和subKey finallykey
      let [ key,subkey ,finallykey] = this.currentOdds.split('-');

      return key && this.odds[key][subkey][finallykey];
    },

    handy(){
      return ["qianer-qianer-qianerdanshi","qiansan-qiansan-qiansandanshi"].includes(this.currentOdds);
    },

    //单注最多可以赢
    canEarn(){
      return this.maxOdds * this.default_gold;
    },

    recentInfoOpenCode(){
      return this.recentInfo[0].open_code;
    }
  },
  mounted() {
    this.mixins_getOddsData({gameKey:this.gameKey,断网了:false});
  },
  methods: {

    //把下注信息传递到 waitforPay(生成订单) 页面
    sendOrderToPage () {
      //先判断是否登录
      this.checkLogin(() => {
        let id = JSON.parse(localStorage.getItem("bc_userInfo")).id;
        let {nextIssue, totalBets, default_gold, shouldCommit, gameKey, gameName, currentOdds} = this;
        let [ type, leiXin, name] = currentOdds.split('-');
        let order = [];
        let orderTemp = {
              id,
              issue: nextIssue,
              zhuShu: totalBets,
              type,
              leiXin,
              name,
              smallName:'',
              gold: default_gold,
              allGold: default_gold*totalBets,
              key:gameKey,
              gameName,
              avatar: this.GLOBAL.bjpk10Img
            }
        //根据用户选择的号码 和 当前的玩法 生成对应订单
        let number = this.createOrder();
        //定位胆的订单拆开来
        let needSplit = new RegExp(/^dindanwei-/).test(currentOdds);
        if(needSplit){
          for(let objkey in number){
            //将信息保存到全局变量
             this.GLOBAL[gameKey].orderList.push({
              ...orderTemp,
              smallName: objkey,
              zhuShu: number[objkey].length,
              allGold: number[objkey].length* default_gold,
              needShowKey:true,
              number: {
                [objkey]:number[objkey]
              }
            })
          }
        }else{
          order = {
            ...orderTemp,
            number,
          }
          //将信息保存到全局变量
          this.GLOBAL[gameKey].order = order;
        }

        //清空注单信息
        this.shouldCommit = [];
        this.mixins_goTOPay();
      });
    },

    //根据用户选择的号码 和 当前的玩法 生成对应订单
    createOrder(){
      let number={};
      switch( this.currentOdds ){
        case "qianyi-qianyi-qianyi":
            qianyiFunc.call(this);
          break;
        case "qianer-qianer-qianerfushi":
            dindanweiFunc.call(this,2);
          break;
        case "qiansan-qiansan-qiansanfushi":
            dindanweiFunc.call(this,3);
          break;
        case "dindanwei-dindanwei-1~5":
            dindanweiFunc.call(this,5);
          break;
        case "dindanwei-dindanwei-6~10":
            dindanwei610Func.call(this);
          break;
        case "dindanwei-dindanwei-dindanwei":
            dindanweiFunc.call(this);
          break;
        default:
          defaultFunc.call(this);
      }

      function qianyiFunc(){
        number = this.shouldCommit[0];
      }

      function dindanweiFunc(maxNum = 10){
        let shouldCommit = this.shouldCommit;
        let temp = ['guanjun','yajun','jijun','di4min','di5min','di6min','di7min','di8min','di9min','di10min'];
        temp.forEach((key,index) => {
          if(maxNum > index && shouldCommit[index].length !== 0){
            number[key] = shouldCommit[index]
          }
        })
      }

      function dindanwei610Func(){
        let shouldCommit = this.shouldCommit;
        let temp = ['di6min','di7min','di8min','di9min','di10min'];
        temp.forEach((key,index) => {
          if(shouldCommit[index+5].length !== 0){
            number[key] = shouldCommit[index+5]
          }
        })
      }

      function defaultFunc(){
        number = this.shouldCommit;
      }

      return number;
    }

  },

  watch: {
    //shouldCommit 根据用户选择的号码 和 当前的玩法 计算总注数和单注可以赢的最大金额
    shouldCommit:{
      handler(shouldCommit){
        let totalBets = 0;
        let maxOdds;
        let {currentOdds} = this;
        const switchFunc = [
          ["qianer-qianer-qianerdanshi",qianerdanshiFunc],
          ["qianer-qianer-qianerfushi",qianerFunc],
          ["qiansan-qiansan-qiansanfushi",qiansanFunc],
          ["qiansan-qiansan-qiansandanshi",qiansandanshiFunc],
          [/^qian(?!\S+danshi$)|^dindanwei-/,dindanweiFunc],
          ['',normal]
        ]
        switchFunc.every(arrayLike => {
          if( new RegExp(arrayLike[0]).test( currentOdds ) ) {
            arrayLike[1].call(this);
            return false;
          }else{
            return true;
          }
        })

        //前二单式 计算规则
        function qianerdanshiFunc(){
          if(shouldCommit.length >=2) totalBets = 1;
        }

        //前二复式 计算规则
        function qianerFunc(){
          let n = shouldCommit[0] , m = shouldCommit[1];
          totalBets = getTwoXtwo(n,m);
        }

        //前三单式 计算规则
        function qiansandanshiFunc(){
          if(shouldCommit.length >=3) totalBets = 1;
        }

        //前三计算规则
        function qiansanFunc(){
          let a = shouldCommit[0] , b = shouldCommit[1] , c = shouldCommit[2];
          totalBets = getThreeXthree(a,b,c);
        }

        //定位胆 计算规则
        function dindanweiFunc(){
          Object.values(shouldCommit).forEach(arr => totalBets+= arr.length)
        }

        function normal(){
          totalBets = shouldCommit.length;
        }

        /* 求最大赔率 */
        function getMaxOdds(){

          //从 当前玩法 字段，截取key和subKey finallykey
          let [ key,subkey ,finallykey] = currentOdds.split('-');
          let temp = key && this.odds[key][subkey][finallykey];

          if( ["number","string"].includes( typeof(temp) ) ) {
            maxOdds = temp;
          }else{
            let oddsArray = shouldCommit.map(key => {
                return temp[key];
            })
            maxOdds = Math.max(...oddsArray);
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

        /* 两组数组 2个一组 */
        function getTwoXtwo(n,m){
          let unp = Array.from(new Set([...n, ...m]));
          return n.length * m.length - (n.length + m.length - unp.length);
        }

        /* 三个数组 3个一组 */
        function getThreeXthree(a,b,c) {
          let al = a.length, bl = b.length, cl = c.length;
          let total = al * bl * cl;

          a.forEach((item, index) => {
            if (b.find(bitem => bitem === item)) {
              total -= cl;
            }

            if (c.find(citem => citem === item)) {
              total -= bl;
            }

            if (
              b.find(bitem => bitem === item) &&
              c.find(citem => citem === item)
            ) {
              total += 2;
            }
          });

          b.forEach((item, index) => {
            if (c.find(citem => citem === item)) {
              total -= al;
            }
          });

          return total;
        }

        this.maxOdds = maxOdds;
        this.totalBets = totalBets;
      },
      deep:true
    },
  },

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
.open-num-item {
  margin: 0 2vw;
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
.open-num-ball {
  margin: 0 0.05em;
  width: 1.6em;
  height: 1.6em;
  background: #e64100;
  color: #fff;
  font-size: 12px;
  line-height: 1.6em;
  border-radius: 50%;
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
}
.bets-handy-item {
  margin-right: 1vw;
}

/* 下注总信息 */
.bets-total {
  position: relative;
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
