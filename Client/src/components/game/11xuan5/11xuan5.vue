<template>
  <div class="contanier">

    <!-- 固定头部 -->
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
          <p class="flex">第{{recentInfo[0].expect}}期 <span :class="openLately?'_arrow-down':''"
                  class="iconfont icon-arrow-down"></span> </p>
          <div class="flex justify-ct">
            <span v-for="n in ['07','06','10','01','11']"
                  class="open-num-ball">{{n}}</span>
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
            <col width='15%'>
            <col width='15%'>
          </colgroup>
          <thead>
            <tr class="bg-grey">
              <th>期次</th>
              <th>开奖号码</th>
              <th>和值</th>
              <th>跨度</th>
            </tr>
          </thead>
          <tbody v-if="recentInfo.length">
            <tr v-for="(tr,index) in recentInfo"
                :class="index%2===1?'bg-grey':''">
              <td>{{tr.expect}}</td>
              <td class="td-dice-wrap flex justify-sa">
                <span v-for="n in tr.open_code"
                      class="open-num-ball">{{n}}</span>
              </td>
              <td>{{tr.sum}}</td>
              <td>{{tr.kuadu}}</td>
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
    <dentGameRuleModal ruleKey="gd-rules"
                       v-model="ruleModalShow"
                       :currentOdds='currentOdds'></dentGameRuleModal>

  </div>
</template>

<script>
import dentGameHeader from "@/components/common/dentGameHeader";
import bets from "./11xuan5-bets";
import {gameFunc, countDownFunc, toggleShouldKeepAlive} from '@/mixins/mixins'

export default {
  name:'11xuan5',
  mixins: [gameFunc, countDownFunc,toggleShouldKeepAlive],
  components: {
    dentGameRuleModal: () => import('@/components/common/dentGameRuleModal'),
    dentGameHeader,
    bets
  },
  data() {
    return {
      gameKey: "gd",
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
    //需要传递给 11xuan5-bets 渲染的赔率
    betsObj(){
      //从 当前玩法 字段，截取key和subKey finallykey
      let [ key,subkey ,finallykey] = this.currentOdds.split('-');

      return key && this.odds[key][subkey][finallykey];
    },

    handy(){
      // 玩法有字符串 "danshi" 单式，单式手动选号
      return new RegExp(/danshi/).test(this.currentOdds);
    },

    //单注最多可以赢
    canEarn(){
      return this.maxOdds * this.default_gold;
    },

    // 开奖信息的第一条
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
        let {nextIssue, totalBets, default_gold, shouldCommit, gameKey, currentOdds} = this;
        let [ type, leiXin, name] = currentOdds.split('-');
        //根据用户选择的号码 和 当前的玩法 生成对应订单
        let number = this.createOrder();
        let order = {
              id,
              issue: nextIssue,
              zhuShu: totalBets,
              type,
              leiXin,
              name,
              gold: default_gold,
              allGold: default_gold*totalBets,
              number,
              key:gameKey,
              gameName:'广东11选5',
              avatar: this.GLOBAL.gdImg
            }

        //将信息保存到全局变量
        this.GLOBAL[gameKey].order = order;

        //清空注单信息
        this.shouldCommit = [];
        this.mixins_goTOPay();
      });
    },

    //根据用户选择的号码 和 当前的玩法 生成对应订单
    createOrder(){
      let shouldCommit = this.shouldCommit;
      let ifObject = shouldCommit instanceof Object;
      let dingweidan = this.currentOdds === "dingweidan-dingweidan-dingweidan";

      // 正常情况，没有选择号码的第几名不需要显示，不需要传递给 订单生成页
      //定位胆需要把没有选择号码的第几名也显示出来
      if(ifObject && !dingweidan){
        for( let key in shouldCommit ){
          if( shouldCommit[key].length === 0 ){
            delete shouldCommit[key]
          }
        }
      }

      return shouldCommit;
    },

    //右上角常用操作弹窗
    mixins_onClickOpr(index) {
      let { gameKey, gameName } = this;
      switch (index) {
        case 0:
          this.$router.push(`/11xuan5/11xuan5-charts`);
          break;
        case 1:
          this.$router.push({
            name: "dentGameOpenLately",
            query: { gameKey, gameName }
          });
          break;
        case 2:
          this.$router.push({
            name: "dentGameOrder",
            query: { gameKey }
          });
          break;
      }
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
          ["sanma-qiansanzhixuan-zx_fushi",qiansanzhixuanFunc], //前三直选-复式
          ["erma-qianerzhixuan-zx_fushi",qianerzhixuanFunc], //前二直选-复式
          [/-zux_fushi|renxuanfushi-fx_(?!one)/,calcFunc], //前三组选-复式 前二组选-复式 任选除了一中一
          [/danshi/,handyFunc], //手动选号
          ['',normal] //默认方法
        ]
        switchFunc.every(arrayLike => {
          if( new RegExp(arrayLike[0]).test( currentOdds ) ) {
            arrayLike[1].call(this);
            return false;
          }else{
            return true;
          }
        })

        //前二直选-复式 计算规则
        function qianerzhixuanFunc(){
          let n = shouldCommit[0] , m = shouldCommit[1];
          totalBets = n && getTwoXtwo(n,m);
        }

        //前三直选-复式 计算规则
        function qiansanzhixuanFunc(){
          let a = shouldCommit[0] , b = shouldCommit[1] , c = shouldCommit[2];
          totalBets = a && getThreeXthree(a,b,c);
        }

        //前三组选-复式 前二组选-复式 任选除了一中一 计算规则
        function calcFunc(){
          let temp = {
                "sanma-qiansanzuxuan-zux_fushi": 3,
                "erma-qianerzuxuan-zux_fushi": 2,
                "renxuan-renxuanfushi-fx_two": 2,
                "renxuan-renxuanfushi-fx_three": 3,
                "renxuan-renxuanfushi-fx_four": 4,
                "renxuan-renxuanfushi-fx_five": 5,
                "renxuan-renxuanfushi-fx_fiveInSix": 6,
                "renxuan-renxuanfushi-fx_fiveInSeven": 7,
                "renxuan-renxuanfushi-fx_fiveInEight": 8
              }
          let bets = shouldCommit[0].length;

          totalBets = calcBets(bets, temp[currentOdds])
        }

        //手动选号 计算规则
        function handyFunc(){
          let currentOdds = this.currentOdds;
          let maxLength;
          let temp = [
              [/dx_one$/, 1],  //任选 1中1
              [/sanma|dx_three$/, 3], //三码-单式 任选 3中3
              [/erma|dx_two$/, 2],  //二码-单式  任选 2中2
              [/dx_four$/, 4],  //任选 4中4
              [/dx_five$/, 5],  //任选 5中5
              [/dx_fiveInSix$/, 6],  //任选 6中5
              [/dx_fiveInSeven$/, 7],  //任选 7中5
              [/dx_fiveInEight$/, 8],  //任选 8中5
            ]

          temp.every( arrayLike => {
            if( new RegExp( arrayLike[0] ).test( currentOdds ) ){
              maxLength = arrayLike[1];
              return false
            }else {
              return true
            }
          })

          totalBets = shouldCommit.length === maxLength ? 1 : 0;
        }

        //普通 计算规则
        function normal(){
          for( let key in shouldCommit ){
            totalBets += shouldCommit[key].length;
          }
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

        this.maxOdds = maxOdds;
        this.totalBets = totalBets;

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
._arrow-down {
  transform: rotate(180deg);
}
.td-dice-wrap {
  height: 9vw;
}
.open-num-ball {
  margin: 0 0.25em;
  width: 1.8em;
  height: 1.8em;
  background: #e64100;
  color: #fff;
  font-size: 12px;
  line-height: 1.8em;
  text-align: center;
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
