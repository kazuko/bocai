<template>
  <div class="contanier">
    <!-- 导航栏 -->
    <mt-header title="广东11选5"
               fixed="true">
      <router-link to="/"
                   slot="left">
        <mt-button icon="back"></mt-button>
      </router-link>

      <div slot="right"
           class="reward-router">
        <router-link to="/11xuan5/currentBets"
                     dentHoverclass="hoverclass-reward"
                     class="reward"><img src="../../../assets/注单.png"
               alt="注单"></router-link>
        <a @click="toggleModal('basicOpr')"
           dentHoverclass="hoverclass-reward"
           class="reward"><img src="../../../assets/opr.png"
               alt="常用操作"></a>
      </div>
    </mt-header>

    <!-- 右上角常用操作 弹窗 -->
    <div @click.self="toggleModal('basicOpr')"
         :class="basicOpr?'modal-basic-opr-show':''"
         class="modal-mask modal-basic-opr">
      <div :class="basicOpr?'basic-opr-show':''"
           class="basic-opr">
        <router-link tag="span"
                     dentHoverclass="hoverclass"
                     to="/11xuan5/openHistory">
          开奖历史
        </router-link>
        <router-link tag="span"
                     dentHoverclass="hoverclass"
                     to="/11xuan5/tendency">开奖走势</router-link>
        <span dentHoverclass="hoverclass">玩法说明</span>
      </div>
    </div>

    <!-- 开奖情况 -->
    <div class="reward-wrap flex justify-sa flow-col mt20 bgc-fff">
      <div class="flex justify-sb">
        <span>广东11选5</span>
        <span>{{open.expect}} 期开奖</span>
        <p class="reward-num-wrap">
          <template v-if="open.opencode">
            <span v-for="(item,index) in open.opencode.split(',')"
                  :key="index"
                  class="reward-num">{{item}}</span>
          </template>
        </p>
      </div>
      <div class="flex justify-sb">
        <span>{{open.expect*1 + 1}} 期开奖</span>
        <span>距封盘:
          <span class="font-theme">{{timeToClose}}</span>
        </span>
        <span>距开奖:
          <span class="font-theme">{{timeToOpen}}</span>
        </span>
      </div>
    </div>

    <!-- 下注信息 -->
    <template v-if="deskInfo">
      <div class="bets-wrap mt20 bgc-fff">
        <!-- 下注的类型 -->
        <div @click="switchType($event,'currentType')"
             data-index=0
             class="bets-type flex com-bd">
          <a v-for="(value, key, index) in deskInfo"
             v-if="value instanceof Object || value instanceof Array"
             :key="index"
             :data-index="index"
             :data-key="key"
             :class="currentType === key ? 'currentType' : ''"
             class="com-bd">{{key | keyToCharacter}}</a>
        </div>

        <!-- 两面盘 double-->
        <transition tag="div"
                    mode="out-in"
                    :name="currentTypeTransitionName">
          <div v-show="currentType === 'lmp'">

            <div class="double-wrap ovhd mt20 bgc-fff com-bd">

              <label v-for="(value, key, index) in deskInfo.lmp"
                     v-if="!(value instanceof Object || value instanceof Array)"
                     :key="index"
                     :data-key="key"
                     class="com-bd double-item">
                <input class="dent-hide-input"
                       type="checkbox"
                       :value="key"
                       v-model="shuoldCommitBets.lmp.alls">
                <span>{{key | keyToCharacter}}</span>
                <span class="font-theme">{{value}}</span>
              </label>

            </div>

            <div class="ovhd mt20 bgc-fff com-bd bdrn">
              <div v-for="(value, key, index) in deskInfo.lmp"
                   v-if="(value instanceof Object || value instanceof Array)"
                   :key="index"
                   class="col-wrap">
                <div class="col-header com-bd">{{key | keyToCharacter}}</div>
                <div v-for="(subvalue, subkey, subindex) in value"
                     :key="subindex"
                     class="double-col-item col-item com-bd">
                  <label class="col-type">
                    <span>{{subkey | keyToCharacter}}</span>
                    <input class="dent-hide-input"
                           type="checkbox"
                           :value="subkey"
                           v-model="shuoldCommitBets.lmp[key]">
                  </label>
                  <span class="col-num font-theme">{{subvalue}}</span>
                </div>
              </div>
            </div>

          </div>
        </transition>

        <!-- 单码 single-->
        <transition tag="div"
                    mode="out-in"
                    :name="currentTypeTransitionName">
          <div v-show="currentType === 'dm'"
               class="single-wrap double mt20 bgc-fff com-bd bdrn">
            <div v-for="(value, key, index) in deskInfo.dm"
                 :key="index"
                 class="col-wrap">
              <div class="col-header com-bd">{{key | keyToCharacter}}</div>
              <span v-for="(subvalue, subkey, subindex) in value"
                    :key="subindex"
                    class="col-item com-bd">
                <label class="col-type">
                  <input class="dent-hide-input"
                         type="checkbox"
                         :value="subkey"
                         v-model="shuoldCommitBets.dm[key]">
                  <span class="reward-num single-num">{{subkey}}</span>
                </label>
                <span class="col-num font-theme">{{subvalue}}</span>
              </span>
            </div>
          </div>
        </transition>

        <!-- 任选 pick-->
        <transition tag="div"
                    mode="out-in"
                    :name="currentTypeTransitionName">
          <div v-show="currentType === 'rx'"
               class="ovhd mt20 bgc-fff">
            <!-- 几中几 -->
            <div @click="switchType($event,'currentRXType')"
                 data-index=0
                 class="pick-wrap com-bd">
              <div v-for="(value, key, index) in deskInfo.rx"
                   :key="index"
                   :data-index="index"
                   :data-key="key"
                   :class="currentRXType === key?'col-header':''"
                   class="pick-row">{{key | keyToCharacter}}</div>
            </div>
            <!-- 号码赔率 -->
            <div class="pick-rate">

              <div class="pick-rate-head flex ovhd">
                <div class="col-item com-bd fl">
                  <span class="col-type">号码</span>
                  <span class="col-num">赔率</span>
                </div>
                <div class="col-item com-bd fl">
                  <span class="col-type">号码</span>
                  <span class="col-num">赔率</span>
                </div>
              </div>

              <div class="pick-rate-num ovhd">
                <div v-for="item in 11"
                     :key="item"
                     class="pick-item col-item com-bd">
                  <label class="col-type">
                    <input class="dent-hide-input"
                           type="checkbox"
                           :value="item"
                           v-model="shuoldCommitBets.rx[currentRXType]">
                    <span class="reward-num single-num">{{item}}</span>
                  </label>
                  <span class="col-num font-theme">{{deskInfo.rx[currentRXType][1]}}</span>
                </div>
              </div>

              <div class="bets-tips">
                <span class="font-theme">*</span>至少要选<span class="font-theme">{{currentRXType | keyToNum}}</span>个号码，注单才生效。
              </div>

            </div>

          </div>
        </transition>

        <!-- 组选 group -->
        <transition tag="div"
                    mode="out-in"
                    :name="currentTypeTransitionName">
          <div v-show="currentType === 'zux'"
               class="ovhd mt20 bgc-fff">
            <!-- 前二 前三-->
            <div @click="switchType($event,'currentGroupIndex')"
                 data-index=0
                 class="group-wrap flex com-bd">
              <div v-for="(value, key, index) in deskInfo.zux"
                   :key="index"
                   :data-index="index"
                   :data-key="key"
                   :class=" currentGroupIndex === key ?'col-header':''"
                   class="group-row">{{key | keyToCharacter}}</div>
            </div>

            <!-- 号码赔率 -->
            <div class="group-rate"
                 style="margin-top: -1px;">

              <!-- 三个球的赔率 -->
              <div class="group-rate-wrap fontc-6">
                <div class="group-rate-head flex ovhd">
                  <div class="col-item com-bd fl">
                    <span class="col-type">号码</span>
                    <span class="col-num">赔率</span>
                  </div>
                  <div class="col-item com-bd fl">
                    <span class="col-type">号码</span>
                    <span class="col-num">赔率</span>
                  </div>
                  <div class="col-item com-bd fl">
                    <span class="col-type">号码</span>
                    <span class="col-num">赔率</span>
                  </div>
                </div>

                <div class="group-item-wrap ovhd">
                  <div v-for="item in 11"
                       :key="item"
                       class="group-item col-item com-bd">
                    <label class="col-type">
                      <input class="dent-hide-input"
                             type="checkbox"
                             :value="item"
                             v-model="shuoldCommitBets.zux[currentGroupIndex]">
                      <span class="reward-num single-num">{{item}}</span>
                    </label>
                    <span class="col-num font-theme">{{deskInfo.zux[currentGroupIndex][1]}}</span>
                  </div>
                </div>

              </div>

            </div>

          </div>
        </transition>

        <!-- 直选 direct -->
        <transition tag="div"
                    mode="out-in"
                    :name="currentTypeTransitionName">
          <div v-show="currentType === 'zhix'"
               class="ovhd mt20 bgc-fff">
            <!-- 前二 前三-->
            <div @click="switchType($event,'currentDirectIndex')"
                 data-index=0
                 class="group-wrap flex com-bd">
              <div v-for="(value, key, index) in deskInfo.zhix"
                   :key="index"
                   :data-index="index"
                   :data-key="key"
                   :class=" currentDirectIndex === key ?'col-header':''"
                   class="group-row">{{key | keyToCharacter}}</div>
            </div>

            <!-- 号码赔率 -->
            <div class="group-rate">

              <!-- 三个球的赔率 -->

              <div v-for="(item,index) in ['firstball','secondball']"
                   v-show="currentDirectIndex === 'firstTwo'"
                   :key="index"
                   class="group-rate-wrap fontc-6">
                <p class="group-rate-title com-bd">{{item | keyToCharacter}}</p>
                <div class="group-rate-head flex ovhd">
                  <div class="col-item com-bd fl">
                    <span class="col-type">号码</span>
                    <span class="col-num">赔率</span>
                  </div>
                  <div class="col-item com-bd fl">
                    <span class="col-type">号码</span>
                    <span class="col-num">赔率</span>
                  </div>
                  <div class="col-item com-bd fl">
                    <span class="col-type">号码</span>
                    <span class="col-num">赔率</span>
                  </div>
                </div>

                <div class="group-item-wrap ovhd">
                  <div v-for="subitem in 11"
                       :key="subitem"
                       class="group-item col-item com-bd">
                    <label class="col-type">
                      <input class="dent-hide-input"
                             type="checkbox"
                             :value="subitem"
                             v-model="shuoldCommitBets.zhix[currentDirectIndex][item]">
                      <span class="reward-num single-num">{{subitem}}</span>
                    </label>
                    <span class="col-num font-theme">{{deskInfo.zhix[currentDirectIndex][1]}}</span>
                  </div>
                </div>
              </div>

              <div v-for="(item,index) in ['firstball','secondball','thirdball']"
                   v-show="currentDirectIndex === 'firstThree'"
                   :key="index"
                   class="group-rate-wrap fontc-6">
                <p class="group-rate-title com-bd">{{item | keyToCharacter}}</p>
                <div class="group-rate-head flex ovhd">
                  <div class="col-item com-bd fl">
                    <span class="col-type">号码</span>
                    <span class="col-num">赔率</span>
                  </div>
                  <div class="col-item com-bd fl">
                    <span class="col-type">号码</span>
                    <span class="col-num">赔率</span>
                  </div>
                  <div class="col-item com-bd fl">
                    <span class="col-type">号码</span>
                    <span class="col-num">赔率</span>
                  </div>
                </div>

                <div class="group-item-wrap ovhd">
                  <div v-for="subitem in 11"
                       :key="subitem"
                       class="group-item col-item com-bd">
                    <label class="col-type">
                      <input class="dent-hide-input"
                             type="checkbox"
                             :value="subitem"
                             v-model="shuoldCommitBets.zhix[currentDirectIndex][item]">
                      <span class="reward-num single-num">{{subitem}}</span>
                    </label>
                    <span class="col-num font-theme">{{deskInfo.zhix[currentDirectIndex][1]}}</span>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </transition>

      </div>
    </template>

    <!-- 下注按钮区 -->
    <div class="bets-btn-wrap flex">

      <div class="bets-total bets-btn flex flow-col font-theme com-bd">
        <span>共{{totalBets}}注</span>
        <span>{{totalBets*default_gold}}</span>
      </div>

      <div @click="toggleModal('betsSubmitModal')"
           dentHoverclass="hoverclass"
           class="bets-btn font-theme com-bd">
        每注金额
      </div>

      <div @click="handleCommit"
           dentHoverclass="hoverclass-btn"
           :class="timeToClose?'':'bets-btn-disable'"
           class="bets-submit bets-btn">
        下注
      </div>

      <div @click="handleReset"
           dentHoverclass="hoverclass-btn"
           class="bets-reset bets-btn">
        重置
      </div>

    </div>

    <!-- 注单金额弹窗 -->
    <transition name="fadein">
      <div @click.self="toggleModal('betsSubmitModal')"
           class="modal-mask"
           v-show="betsSubmitModal">
        <div class="modal">
          <div class="modal-title">
            注单金额
          </div>
          <div class="modal-body">
            <p class="gold-input-wrap flex">
              <span class="fontc-3">请输入金额:</span>
              <input class="gold-input font-theme"
                     type="number"
                     v-model.number="default_gold">
            </p>
            <div class="gold-wrap ovhd">
              <label v-for="(item,index) in [10,100,1000,5000,10000,50000]"
                     :key="index"
                     class="gold-item com-bd">
                <input class="dent-hide-input"
                       type="radio"
                       :value="item"
                       v-model="default_gold">
                <span>{{item}}元</span>
              </label>
            </div>
            <p class="fontc-9">注数：{{totalBets}}注</p>
            <p><span class="fontc-9">总额：</span>{{totalBets*default_gold}}元</p>
          </div>
          <div class="modal-btn flex">
            <a @click="toggleModal('betsSubmitModal')"
               dentHoverclass="hoverclass"
               class="fontc-9">取消</a>
            <a @click="toggleModal('betsSubmitModal')"
               dentHoverclass="hoverclass"
               class="modal-btn-confrim">确认</a>
          </div>
        </div>
      </div>
    </transition>

    <!-- 下注提示弹窗 -->
    <div @click.self="toggleModal('betsTipsModal')"
         v-show="betsTipsModal"
         class="modal-mask">
      <div class="sm-modal flex flow-col justify-ct">
        <div class="sm-modal-title">
          下注成功!
        </div>
        <div class="sm-modal-body fontc-9">
          本次注单金额为：60000元<br />扣除本次注单，您的余额为：12566
        </div>
        <div class="sm-modal-btn">
          <a @click="toggleModal('betsTipsModal')"
             dentHoverclass="hoverclass"
             class="sm-modal-btn-confrim">确认</a>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
console.log("GAME_SANGONG_11xuan5_VUE");
import { keyToCharacter,keyToNum } from '@/js/11xuan5'

export default {
  filters: {
    // 键值转成中文
    keyToCharacter: function (value) {
      let character = '';
      for( let key in keyToCharacter){
        if( key === value ) {
          character = keyToCharacter[key]
          break;
        }else {
          continue;
        }
      }

      return character;
    },
    // 键值转成数字
    keyToNum: function (value) {
      let character = '';
      for( let key in keyToNum){
        if( key === value ) {
          character = keyToNum[key]
          break;
        }else {
          continue;
        }
      }

      return character;
    }
  },
  data() {
    return {
      deskInfo: false,
      //常用操作弹窗
      basicOpr:false,
      //当前浏览的下注类型
      currentType:'lmp',
      // 切换 下注类型 的动画
      currentTypeTransitionName:'',
      //下注类型的索引
      currentTypeIndex:0,
      //几中几 的索引
      currentRXType:'oneToOne',
      //组选 前二 前三 的索引
      currentGroupIndex:'firstTwo',
      //直选 前二 前三 的索引
      currentDirectIndex:'firstThree',
      //下单弹窗
      betsSubmitModal:false,
      //下注操作提示弹窗
      betsTipsModal:false,

      //每单的金额
      default_gold: 0,
      //总注数
      totalBets:0,

      //当期开奖信息
      open:{},
      //倒计时
      timer:{},
      timeToOpen:'',
      timeToClose:'',

      // 用户基本信息
      shuoldCommit:{
        userInfo:{
          user_id:1,
          nickname:'Motley'
        },
        "gold":50,
        "issue":20180925,
        "time":"下注时间"
      },
      //用户下单信息
      shuoldCommitBets:{
        lmp:{
          "alls":[],
          "firstball":[],
          "secondball":[],
          "thirdball":[],
          "thouthball":[],
          "fifthball":[]
        },
        dm:{
          "firstball": [],
          "secondball": [],
          "thirdball": [],
          "thouthball": [],
          "fifthball": []
        },
        rx: {
          "oneToOne": [],
          "twoToTwo": [],
          "threeToThree": [],
          "fourTofour": [],
          "fiveToFive": [],
          "sixToFive": [],
          "sevenToFive": [],
          "eightToFive": []
        },
        zux: {
          "firstTwo": [],
          "firstThree": []
        },
        zhix: {
          "firstTwo": {
            "firstball":[],
            "secondball":[]
          },
          "firstThree": {
            "firstball":[],
            "secondball":[],
            "thirdball":[]
          }
        }
      },
      copyOfShuoldCommitBets:{}
    }
  },
  methods: {

      //请求 赔率 数据
      getOddsData() {
        this.deskInfo = this.getLocalCache('11xuan5-odds');
        return
        let first = true;
        let sendDataToServer = () => {
          this.senddata({type:"deskInfo",game:'GD',first});
        }

        try{
          let odds = this.getLocalCache('11xuan5-odds');
          first = false;
        }catch(e){
          first = true;
        }

        //请求数据
        if (!this.GLOBAL.gameSocketHand) {
          let weijie = 'ws://192.168.1.133:2310'
          let wanglangDent = 'ws://192.168.0.136:2310'
          this.createSocket(wanglangDent, () => {
            this.dealOnMessage(); //创建连接成功之后 监听返回的信息
            sendDataToServer();
          });
        } else {
          sendDataToServer();
        }

      },

      //提交数据
      handleCommit() {
        // if( !timeToClose ){return};
        // this.senddata({type:"open",game:'GD'}) 请求开奖信息

        //TODO 验证数据
        let data = {type:"create",
                    game:'GD',
                    ...this.shuoldCommit,
                    ...this.shuoldCommitBets,
                    bet:this.totalBets,
                    issue:this.open.expect+1,
                    goldCount:this.totalBets*this.default_gold,
                    gold:this.default_gold
                    };
        this.senddata(data);
      },

      //重置订单信息
      handleReset() {
        this.shuoldCommitBets = JSON.parse(JSON.stringify(this.copyOfShuoldCommitBets));
      },

      //处理返回的数据
      dealOnMessage() {
        this.onmessage( (res) => {
          res = JSON.parse(res);
          debugger
          let checkRes =  res instanceof Object || res instanceof Array; //验证返回的数据格式
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

      //倒计时
      countDown(timeStamp,dataStr){
        function setTimer(){

          let hh,mm,ss;
          let time = timeStamp - new Date().getTime();

          if(time<=0){
            return ''
            clearInterval(this.timer[ dataStr + 'timer']);
          }else{
            hh = Math.floor((time / 60 / 60) % 24);
            mm = Math.floor((time / 60) % 60);
            ss = Math.floor(time  % 60);
            debugger
            this[dataStr] = mm + ':' + ss ;
          }

        }
        setTimer();
        this.timer[ dataStr + 'timer'] = setInterval(setTimer,1000);
      },

      //显示隐藏 弹窗
      toggleModal(modalStr) {
        this[modalStr] = !this[modalStr];
      },

      //切换 各种 面板
      switchType(e,dataStr){
        let key = e.target.dataset.key;
        let preTypeIndex = e.currentTarget.dataset.index;
        let currentTypeIndex = e.target.dataset.index;
        //比较索引值大小 设置动画
        this.setAnimate( currentTypeIndex - preTypeIndex );
        // 把 索引值 储存到data 和 dom上的dataset
        key && (this[dataStr] = key);
        e.currentTarget.dataset.index = currentTypeIndex;
        //每次 切换不同的下注方式 ，清空未下注的信息
        // this.shuoldCommitBets = JSON.parse(JSON.stringify(this.copyOfShuoldCommitBets));
      },

      //左右切换 切换动画 -下注面板
      setAnimate(boolean) {
        this.currentTypeTransitionName = boolean > 0 ? 'slide-left' : 'slide-right';
      },

      //阶乘计算注数函数
      /**
       * 比如 5中5， 选择6个球的时候， 这样调用函数 calcBets(6,5)
       * 比如 2中2， 选择10个球的时候， 这样调用函数 calcBets(10,2)
       */
      calcBets( bets , n ) {
        if(bets < n){return 0}
        function Factorial(num) {
          return num <= 1 ? 1 : num*Factorial(num-1);
        }

        let totalBets = Factorial(bets) / (Factorial (bets - n) * Factorial(n))
        return totalBets;
      }
  },
  watch: {
    //watch 用户下注的信息，计算总注数
    shuoldCommitBets: {
      handler: function () {
        let totalBets = 0;
        for( let key in this.shuoldCommitBets) {

          // 不同的下注类型 不同的计算注数计算方式
          switch( key ) {
            case "lmp":
            case "dm":
                totalBets += calcBets_lmp_dm.call(this,this.shuoldCommitBets[key] );
              break;
            case "rx":
                totalBets += calc_rx.call(this);
              break;
            case "zux":
                totalBets += calc_zux.call(this);
              break;
            case "zhix":
                if (this.currentDirectIndex === "firstTwo"){
                  totalBets += calc_zhix_qianer.call(this);
                }else if(this.currentDirectIndex === "firstThree") {
                  totalBets += calc_zhix_qiansan.call(this);
                }
              break;
          }

        }

        this.totalBets = totalBets;

        //双面盘 单码
        function calcBets_lmp_dm(obj) {
          let bets = 0;
          for( let key in obj ) {
            bets += obj[key].length;
          }

          return bets;
        }
        //组选
        function calc_zux() {
          let zux_bets = 0;

          for( let key in this.shuoldCommitBets['zux'] ) {
            let n;
            key === "firstTwo" && (n = 2);
            key === "firstThree" && (n = 3);

            let bets = this.shuoldCommitBets['zux'][key].length;

            zux_bets += this.calcBets( bets , n );
          }

          return zux_bets;
        }
        //任选
        function calc_rx() {
          let rx_bets = 0;

          for( let key in this.shuoldCommitBets['rx'] ) {
            //当前 下注 选中的球数
            let bets = this.shuoldCommitBets['rx'][key].length;

            //当前 下注 的是几中几
            let n;
            switch( key ){
              case 'oneToOne':
                  n = 1;
                break;
              case 'twoToTwo':
                  n = 2;
                break;
              case 'threeToThree':
                  n = 3;
                break;
              case 'fourTofour':
                  n = 4;
                break;
              case 'fiveToFive':
                  n = 5;
                break;
              case 'sixToFive':
                  n = 6;
                break;
              case 'sevenToFive':
                  n = 7;
                break;
              case 'eightToFive':
                  n = 8;
                break;
            }

            rx_bets += this.calcBets( bets , n );
          }

          return rx_bets;
        }
        //直选前二
        function calc_zhix_qianer() {
          let n = this.shuoldCommitBets.zhix.firstTwo.firstball;
          let m = this.shuoldCommitBets.zhix.firstTwo.secondball;

          let unp =  Array.from(new Set([...n,...m]));
 	        return n.length * m.length - ( n.length + m.length -  unp.length  );
        }
        //直选前三
        function calc_zhix_qiansan() {
          let a = this.shuoldCommitBets.zhix.firstThree.firstball;
          let b = this.shuoldCommitBets.zhix.firstThree.secondball;
          let c = this.shuoldCommitBets.zhix.firstThree.thirdball;

          let al = a.length, bl = b.length, cl = c.length;
          let total = al*bl*cl;

          a.forEach( (item,index) => {

            if( b.find( ( bitem ) => bitem === item ) ) {
              total -= cl;
            }

            if( c.find( ( citem ) => citem === item ) ) {
              total -= bl;
            }

            if( b.find( ( bitem ) => bitem === item ) && c.find( ( citem ) => citem === item ) ) {
              total += 2;
            }

          })

          b.forEach( (item,index) => {

            if( c.find( ( citem ) => citem === item ) ) {
              total -= al;
            }

          })

          return total;
        }
      },
      deep: true//对象内部的属性监听，也叫深度监听
    },
    //watch 开奖信息 ，启动倒计时
    // open(val){
    //   debugger
    //   //开奖倒计时
    //   this.countDown(new Date(val.opentime).getTime(),'timeToOpen');
    //   //封盘倒计时
    //   this.countDown(new Date(val.opentime).getTime() - 2 * 60 *1000,'timeToClose');
    // }
  },
  mounted: function() {
    this.$parent.tabbarShow = false; //
    this.getOddsData();

    //初始化保存一份空的下注信息，用来还原清空下注信息
    this.copyOfShuoldCommitBets = JSON.parse(JSON.stringify(this.shuoldCommitBets));
  },
  updated: function(){

  },
  beforeDestroy: function() {

  }


};

</script>

<style scoped>
/* 动画 */
.fadein-enter-active,
.fadein-leave-active {
  will-change: opacity;
  transition: opacity 500ms;
}
.fadein-enter,
.fadein-leave-to {
  opacity: 0;
}

.dent-hide-input {
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  appearance: none;
}
.dent-hide-input:checked {
  background: #cccccc;
}
/* 导航栏 */
.mint-header {
  background-color: #57d6dd !important;
}
.reward-router {
  float: right;
  display: flex;
}
.reward {
  position: relative;
  padding: 10px;
}
.reward img {
  width: 18px;
  height: 18px;
}

.modal-basic-opr {
  opacity: 0;
  visibility: hidden;
  transition: 0.5s;
}
.modal-basic-opr-show {
  opacity: 1;
  visibility: visible;
}
.basic-opr {
  position: absolute;
  top: calc(10vw + 10px);
  right: 10px;
  background: #fff;
  transform: scale(0.5);
  transform-origin: top right;
  transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.basic-opr-show {
  transform: scale(1);
}
.basic-opr::after {
  content: "";
  position: absolute;
  top: -16px;
  right: 8px;
  border: 8px solid transparent;
  border-bottom-color: #fff;
}

.basic-opr span {
  display: block;
  width: 118px;
  height: 54px;
  text-align: center;
  line-height: 54px;
}

.basic-opr span + span {
  border-top: 1px solid #c1c1c1;
}

/* 开奖情况 */
.reward-wrap {
  padding: 1vh 2vw;
  height: 10.2vh;
}
.reward-wrap > div {
  flex: 1;
  width: 100%;
}
.reward-num {
  display: inline-block;
  margin-left: 6px;
  width: 6vw;
  height: 6vw;
  background: #adc1c2;
  line-height: 6vw;
  text-align: center;
  color: #fff;
  border-radius: 60%;
}

/* 下注界面 */
.bets-wrap {
  box-sizing: border-box;
  /* padding: 1vh 2vw; */
  min-height: 78vh;
}
.bets-wrap > div {
  padding-right: 2vw;
  padding-left: 2vw;
}
.bets-type {
  margin: 0 2vw;
  padding: 0 !important;
  font-size: 16px;
}
.bets-type > a {
  flex: 1;
  border-width: 0 1px 0 0;
  line-height: 30px;
  text-align: center;
}
.bets-type > a:last-child {
  border: none;
}
.currentType {
  background: #ec9582;
}

.bdrn {
  border-right: 0;
}

.col-wrap {
  float: left;
  width: 20%;
  line-height: 34px;
  text-align: center;
}
.col-header {
  background: #c9e0e1;
  border-width: 0 1px 1px 0;
}
.col-header:last-child {
  border-right-width: 0;
}
.col-item {
  display: flex;
  border-width: 0 1px 1px 1px;
}
.col-item:last-child {
  border-bottom-width: 0;
}

.col-type {
  position: relative;
  width: 40%;
  border-right: 1px solid #a3a3a3;
}
.col-type span {
  position: relative;
  z-index: 1;
}
.col-num {
  flex: 1;
}

/* 双面盘 */
.ovhd {
  overflow: hidden;
}
.double-wrap {
  border-bottom: none;
}
.double-item {
  position: relative;
  box-sizing: border-box;
  float: left;
  display: flex;
  flex-flow: column;
  height: 7.5vh;
  width: 20%;
  border-width: 0 1px 1px 0;
  justify-content: center;
}
.double-item:nth-child(5n) {
  border-width: 0 0 1px 0;
}
.double-item span {
  position: relative;
  z-index: 1;
}
.double-col-item {
  border-left-width: 0;
}

/* 单码 */
.single-wrap .col-wrap + .col-wrap .col-item {
  border-left-width: 0;
}
.single-num {
  color: #333;
  margin: 0;
}
.single-wrap .col-item:last-child {
  border-bottom-width: 1px;
}

/* 任选 */
.pick-wrap {
  float: left;
  width: 22.4vw;
}
.pick-row {
  line-height: 40px;
  text-align: center;
  border-bottom: 1px solid #a3a3a3;
}
.pick-row:last-child {
  border-bottom: none;
}
.pick-item {
  width: 50%;
  float: left;
  box-sizing: border-box;
}
.pick-item:nth-child(2n) {
  border-left-width: 0;
}
.pick-item:last-child {
  border-bottom-width: 1px;
}
.pick-rate {
  margin-left: 24.4vw;
}
.pick-rate {
  line-height: 40px;
}
.pick-rate-head .col-item {
  flex: 1;
  border-width: 1px;
}
.pick-rate-head .col-item:last-child {
  border-bottom-width: 1px;
  border-left-width: 0;
}

/* 组选 */
.group-wrap {
  line-height: 32px;
}
.group-row {
  flex: 1;
}
.group-row + .group-row {
  border-left: 1px solid #a3a3a3;
}
.group-rate-wrap {
  line-height: 32px;
}
.group-rate-head .col-item {
  flex: 1;
  border-width: 1px;
}
.group-rate-head .col-item + .col-item {
  border-left-width: 0;
}
.group-item-wrap .col-item {
  border-right-width: 0;
}
.group-item-wrap .col-item:nth-child(3n) {
  border-right-width: 1px;
}
.group-item-wrap .col-item:last-child {
  border-width: 0 1px 1px 1px;
}
.group-rate-title {
  border-width: 0 1px;
}
.group-rate-wrap + .group-rate-wrap .group-rate-title {
  margin-top: -1px;
  border-top-width: 1px;
}
.group-item {
  float: left;
  box-sizing: border-box;
  width: 33.3333%;
}

/* 下注按钮区 */
.bets-btn-wrap {
  position: fixed;
  z-index: 1;
  left: 0;
  bottom: 0;
  width: 100%;
  height: 50px;
  line-height: 50px;
  background: #fff;
  box-shadow: 0 0 8px rgba(114, 114, 114, 0.4);
}
.bets-btn {
  box-sizing: border-box;
  margin-left: 2vw;
  width: 23.5vw;
  line-height: 38px;
  text-align: center;
  font-size: 15px;
}
.bets-total {
  line-height: 19px;
}
.bets-submit {
  background: #e64100;
  border: 1px solid #e64100;
  color: #fff;
}
.bets-btn-disable {
  background: #eeeeee;
  border: 1px solid #eeeeee;
  color: #b5b5b5;
}
.bets-reset {
  width: 20vw;
  background: #adc1c2;
  border: 1px solid #adc1c2;
}
</style>
<style scope>
.fadein-enter .modal {
  transform: translate3d(0, 50%, 0);
}
.fadein-leave-active .modal {
  transform: translate3d(0, 50%, 0);
}
.modal-btn a {
  flex: 1;
  border-top: 1px solid #a3a3a3;
  text-align: center;
  line-height: 60px;
}
.modal-btn a + a {
  border-left: 1px solid #a3a3a3;
}
.modal-btn .modal-btn-confrim {
  color: #e62b00;
}

/* 小弹窗 */
.sm-modal {
  padding: 16px 0;
  width: 70%;
  background: #fff;
  font-size: 16px;
  text-align: left;
  border-radius: 6px;
  backface-visibility: hidden;
  user-select: none;
  transition: 0.2s;
  overflow: hidden;
}
.sm-modal-title {
  height: 34px;
  line-height: 34px;
}
.sm-modal-body {
  padding: 10px 0;
  line-height: 20px;
  text-align: center;
  font-size: 13px;
}
.contanier .sm-modal-btn-confrim {
  display: block;
  margin: 10px 0;
  width: 25.5vw;
  height: 28px;
  background: #e64100;
  color: #fff;
  font-size: 14px;
  line-height: 28px;
  text-align: center;
  border-radius: 6px;
}

/* 弹窗内样式 */
.gold-input-wrap,
.gold-input {
  line-height: 32px;
}
.gold-input {
  margin-left: 2vw;
  width: 26vw;
  border: 1px solid #a3a3a3;
  border-radius: 6px;
  text-align: left;
  text-indent: 10px;
  transition: 0.5s;
  font-size: 16px;
}
.gold-input:focus {
  outline: none;
  border-color: #e64600;
}
.gold-wrap {
  padding: 23px 0;
}
.gold-item {
  position: relative;
  float: left;
  margin-right: 2vw;
  margin-bottom: 2vw;
  width: 20vw;
  height: 32px;
  background: #e7e7e7;
  color: #333;
  line-height: 32px;
  text-align: center;
}
.gold-item span {
  position: relative;
  z-index: 1;
}
.gold-wrap .dent-hide-input:checked {
  box-sizing: content-box;
  margin: -1px;
  padding: 1px;
  background: #e64600;
}
.gold-wrap .dent-hide-input:checked + span {
  color: #fff;
}
</style>
