<template>
  <div class="container">
    <!-- 导航栏 -->
    <dentGameHeader v-on:onselect="onselect"
                    v-on:onClickRightIcon="chartsModal=true"
                    :obj="select"
                    subType="normal"
                    rightCharts="true">
    </dentGameHeader>

    <div class="ex-header flex flow-col">
      <!-- 基本走势 -->
      <template v-if="currentTable === '基本走势'">
        <template v-if="basicTableData.length">
          <!-- 基本走势 -->
          <div key="基本走势"
               class="total flex flow-col">
            <!-- 固定表头 -->
            <table class="fixheader">
              <COLGROUP>
                <col width="20%">
                <col width="50%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
              </COLGROUP>
              <thead>
                <tr>
                  <th>期号</th>
                  <th>开奖号码</th>
                  <th>和值</th>
                  <th>大小</th>
                  <th>单双</th>
                </tr>
              </thead>
            </table>
            <!-- 表格主体 -->
            <div class="table-main-wrap">
              <table style="margin-top: -1px;">
                <COLGROUP>
                  <col width="20%">
                  <col width="50%">
                  <col width="10%">
                  <col width="10%">
                  <col width="10%">
                </COLGROUP>
                <tbody>
                  <tr v-for="(tr,index) in  basicTableData"
                      :class="index%2 === 1?'bg-grey':''">
                    <td>{{tr.expect}}</td>
                    <td>
                      <div class="flex justify-sa">
                        <span v-for="n in tr.open_code">{{n}}</span>
                      </div>
                    </td>
                    <td>{{tr.sum.sum}}</td>
                    <td>{{tr.sum.daxiao | keyToCharacter}}</td>
                    <td>{{tr.sum.danshuang | keyToCharacter}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </template>
        <div v-else
             style="flex: 1;"
             class="flex justify-ct">
          <mt-spinner color="#e62b00"
                      type="fading-circle"></mt-spinner>
        </div>
      </template>

      <!-- 定位胆 -->
      <template v-if="currentTable=== '定位胆'">
        <div key="定位胆"
             class="total flex justify-ct flow-col">
          <!-- 表格主体 -->
          <!--  -->
          <template v-if="dwdTableData.table && Object.keys(dwdTableData.table).length">
            <!-- 固定表头 -->
            <table class="fixheader"
                   style="position: sticky;top: 0;z-index: 2;">
              <colgroup>
                <col width="75px">
                <col v-for="n in 11"
                     width="30px">
              </colgroup>
              <thead>
                <tr>
                  <th>期号</th>
                  <th v-for="n in 11">{{n}}</th>
                </tr>
              </thead>
            </table>
            <div class="table-main-wrap">
              <!-- 固定表格主体 -->
              <table class="canvas-table"
                     style="margin-top: -1px;">
                <colgroup>
                  <col width="75px">
                  <col v-for="n in 11"
                       width="30px">
                </colgroup>
                <tbody>
                  <tr v-for="(tr,index) in dwdTableData.table"
                      :class="index%2 === 1?'bg-grey':''">
                    <td>{{dwdTableData.issue[index]}}</td>
                    <td v-for="(n,subindex) in 11">
                      <div :class="tr['num'+(subindex+1)] === 0 ? 'openball' : ''">
                        {{ (tr['num'+(subindex+1)] === 0 ? subindex+1 : tr['num'+(subindex+1)]) }}
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <!-- 固定表脚 -->
              <table style="position: sticky;bottom: 0;z-index: 1;">
                <colgroup>
                  <col width="75px">
                  <col v-for="n in 11"
                       width="30px">
                </colgroup>
                <tfoot class="bg-yellow">
                  <tr>
                    <td>出现次数</td>
                    <td v-for="(value,key,index) in  dwdTableData.analyse.chuxiancishu">
                      {{ dwdTableData.analyse.chuxiancishu['num'+(index+1)] }}
                    </td>
                  </tr>
                  <tr>
                    <td>平均遗漏</td>
                    <td v-for="(value,key,index) in  dwdTableData.analyse.pinjunyilou">
                      {{ dwdTableData.analyse.pinjunyilou['num'+(index+1)] }}
                    </td>
                  </tr>
                  <tr>
                    <td>最大遗漏</td>
                    <td v-for="(value,key,index) in  dwdTableData.analyse.zuidayilou">
                      {{ dwdTableData.analyse.zuidayilou['num'+(index+1)] }}
                    </td>
                  </tr>
                  <tr>
                    <td>最大连出</td>
                    <td v-for="(value,key,index) in  dwdTableData.analyse.zuidaliangchu">
                      {{ dwdTableData.analyse.zuidaliangchu['num'+(index+1)] }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </template>
          <mt-spinner v-else
                      color="#e62b00"
                      type="fading-circle"></mt-spinner>
        </div>
        <!-- 球数 -->
        <div class="ball-wrap flex">
          <label v-for="(value,key,index) in switchBall"
                 :class="site===value?'current-ball':''"
                 class="ball-item">
            <input type="radio"
                   :value="value"
                   v-model="site"
                   class="dent-hide-input">
            第{{key}}球
          </label>
        </div>
      </template>

      <!-- 距离下期开奖 -->
      <div class="time-to-open">
        • <span class="font-grey">距{{nextIssue}}期截止 : </span><span class="font-theme">{{timer.timeToOpen}}</span>
      </div>
    </div>

    <!-- 走势图设置弹窗 -->
    <transition name="fadein">
      <div @click.self="chartsModal=false"
           class="charts-mask"
           v-show="chartsModal">
        <div class="modal">
          <div class="modal-title">
            走势图设置
          </div>

          <div class="modal-body">
            <!-- 选择期数 -->
            <div class="setup">
              <p class="fontc-6">期数：</p>
              <div class="flex justify-sa">
                <label v-for="item in [30,50,100]"
                       class="flex">
                  <input type="radio"
                         class="dent-radio"
                         v-model="issueNum"
                         :value="item">{{item}}期</label>
              </div>
            </div>
            <!-- 选择排序 -->
            <div class="setup">
              <p class="fontc-6">排序：</p>
              <div class="flex justify-sa">
                <label v-for="item in [{text:'顺序显示',order:'asc'},{text:'倒序显示',order:'desc'}]"
                       class="flex">
                  <input type="radio"
                         class="dent-radio"
                         v-model="order"
                         :value="item.order">{{item.text}}</label>
              </div>
            </div>

          </div>

          <div class="modal-btn flex">
            <a @click="chartsModal=false"
               dentHoverclass="hoverclass"
               class="modal-btn-cancle fontc-9">取消</a>
            <a @click="chartsModal=false"
               dentHoverclass="hoverclass"
               class="modal-btn-confrim">确认</a>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script>
import dentGameHeader from "@/components/common/dentGameHeader";
import {countDownFunc} from '@/mixins/mixins'

export default {
  mixins: [countDownFunc],
  components: {
    dentGameHeader
  },
  filters: {
    // 键值转成中文
    keyToCharacter: function(value) {
      let temp = { da:'大',xiao:'小',dan:'单',shuang:'双' };
      return temp[value];
    }
  },
  data() {
    return {
      gameKey:'gd',
      /* 数据相关 */
      select:{'基本走势':'basic_Gd','定位胆':'dingDanWei_Gd'},
      basicTableData:[],  //基本 走势图数据
      dwdTableData:{},  //定位胆 走势图数据

      /* 状态相关 */
      currentTable:'基本走势',  //当前走势图
      chartsModal: false, //走势图设置弹窗
      issueNum: 30, //当前期数
      order: "desc", //正反序
      site:'ball_1',  //第几球
    };
  },
  mounted: function() {
    this.$parent.tabbarShow = false;
    this.nextIssue = this.GLOBAL.bjpk10.nextIssue;
    //从全局变量读取并且启动倒计时
    let {countdown,interval} = this.GLOBAL[this.gameKey];
    countdown && this.mixins_countTime(countdown,interval,'timeToOpen');

    this.getOddsData();
  },
  computed:{
    switchBall(){
      return {'一':'ball_1','二':'ball_2', '三':'ball_3', '四':'ball_4', '五':'ball_5'}
    }
  },
  methods: {
    onselect(index) {
      this.currentTable = index;
      let type;
      let yeshu = '';
      if(index === '定位胆') type = 'dingDanWei_Gd', yeshu='ball_1';
      if(index === '基本走势') type = 'basic_Gd';
      this.getData(type, this.issueNum, yeshu);
    },

    getData(key, size, yeshu) {
      let data = {
        type: key,
        size,
        game: this.gameKey,
        yeshu:yeshu
      }
      this.senddata({ data });
    },

    //请求 赔率 数据
    getOddsData() {
      this.checkGameSocket(() => {
        this.dealOnMessage(); //已经创建连接 监听返回的信息
      });
    },

    //处理返回的数据
    dealOnMessage() {
      this.onmessage(res => {
        res = JSON.parse(res);
        //保持心跳
        if (res.code === "pong") {
          this.senddata({ type: "ping" });
        }
        //基本走势
        if( this.$check(res,'array') ){
          this.basicTableData = res;
        }
        //定位胆
         if( this.$check(res,'object') ){
          this.dwdTableData = res;
        }

      })
    }

  },
  watch:{

    //倒序数组
    order(){
      if(this.currentTable === '基本走势'){
        this.basicTableData.reverse();
      };
      if(this.currentTable === '定位胆'){
        this.dwdTableData.table.reverse();
      }
    },

    // 更改期数
    issueNum(){
      this.onselect(this.currentTable);
    },

    //改变球数的时候 发起新的请求
    site(site){
      this.getData('dingDanWei_Gd', this.issueNum, site);
    },

    //表格渲染完成连线
    dwdTableData:{
      handler(){
        this.$nextTick(() => {
          this.$drawline({selector:'.canvas-table'});
        })
      },
      deep:true
    }
  },
}
</script>

<style scoped>
.bg-hong {
  background: #ec0909;
}
.bg-lan {
  background: #0e86e3;
}
.bg-lv {
  background: #12c231;
}
.bg-yellow {
  background: #edffd1;
}
.fontc-hong {
  color: #ec0909;
}
.fontc-lan {
  color: #0e86e3;
}
.fontc-lv {
  color: #12c231;
}
.container {
  padding-top: 13.8vw;
  background: #fff;
  font-family: pingfang sc normal;
  font-size: 12px;
}
.ex-header {
  height: calc(100vh - 13.8vw);
}
table {
  table-layout: fixed;
  width: 100%;
  border-spacing: 0;
  border-collapse: collapse;
  font-size: 12px;
}
th,
td {
  padding: 1.6vw 0;
  border: solid #dcdcdc;
  border-width: 1px;
  font-weight: normal;
  color: #858da3;
}
.dent-radio {
  margin-right: 1vw;
  appearance: none;
  outline: none;
}
.dent-radio::after {
  content: "";
  position: relative;
  box-sizing: border-box;
  display: block;
  width: 4vw;
  height: 4vw;
  background-color: #eeeeee;
  border: 1px solid #dedee2;
  border-radius: 50%;
  text-align: center;
  transition: background 0.5s;
}

.dent-radio:checked:after {
  content: "";
  background: #e64600;
  border: 2px solid #fff;
  box-shadow: 0 0 0 1px #dedee2;
}

.total {
  flex: 1;
  width: 100%;
  overflow: auto;
}

/* 表头 */
.fixheader {
  background: #e5e5e5;
}
/* 表格主体 */
#table,
.table-main-wrap {
  flex: 1;
  position: relative;
}
.charts {
  flex: 1;
}
.table-main {
  flex: 1;
  margin-top: -1px;
  background: #ffffff;
}
.table-main-wrap {
  flex: 1;
  overflow: auto;
}
.animalTfootWrap {
  height: 32vw;
}
.fontc-3 {
  color: #333;
}
.bg-grey {
  background: #f8f8f8;
}
.openball {
  position: relative;
  z-index: 1;
  width: 6.4vw;
  height: 6.4vw;
  background: #e64600;
  line-height: 6.4vw;
  color: #fff;
  border-radius: 50%;
}
/* tab */
.charts-tab-item {
  flex: 1;
  background: #e5e5e5;
  line-height: 6vw;
  color: #4e4e4e;
}
.current-tab {
  background: #e64600;
  color: #fff;
}
/* 下期开奖 */
.charts-tab {
  width: 100%;
}
.time-to-open {
  width: 100%;
  height: 14vw;
  background: #fff;
  text-align: left;
  line-height: 14vw;
  font-size: 14px;
  text-indent: 20px;
}
.font-grey {
  color: #606060;
}

/* 球数 */
.ball-wrap {
  width: 100%;
  overflow: auto;
}
.ball-item {
  flex: none;
  width: 75px;
  background: #e5e5e5;
  line-height: 8vw;
  color: #4e4e4e;
}
.current-ball {
  background: #e64600;
  color: #fff;
}
.dent-hide-input {
  display: none;
}

/* 走势图弹窗 */
.charts-mask {
  position: fixed;
  z-index: 2;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.1);
  color: #333;
}
.modal {
  background-color: #fff;
  width: 84%;
  border-radius: 6px;
  font-size: 16px;
  text-align: left;
  user-select: none;
  backface-visibility: hidden;
  transform: translate3d(0, 0, 0);
  transition: 0.35s;
  overflow: hidden;
}
.fadein-enter-active,
.fadein-leave-active {
  will-change: opacity;
  transition: opacity 500ms;
}
.fadein-enter,
.fadein-leave-to {
  opacity: 0;
}
.fadein-enter .modal {
  transform: translate3d(0, 50%, 0);
}
.fadein-leave-active .modal {
  transform: translate3d(0, 50%, 0);
}
.modal-title {
  border-bottom: 1px solid #e7e7e7;
  line-height: 50px;
  text-align: center;
}
.modal-body {
  padding: 10px;
}
.modal-btn {
  border-top: 1px solid #dedede;
  padding: 10px;
}
.modal-btn a {
  flex: 1;
  margin: 0 2vw;
  text-align: center;
  line-height: 10vw;
  border-radius: 4px;
}
.modal-btn .modal-btn-cancle {
  background: #e5e5e5;
  color: #9c9c9c;
}
.modal-btn .modal-btn-confrim {
  background: #e64600;
  color: #ffffff;
}
.setup {
  font-size: 14px;
  line-height: 8vw;
}
</style>
