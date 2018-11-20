<template>
  <div v-cloak
       class="container">
    <!-- 导航栏 -->
    <dentGameHeader v-on:onselect="onselect"
                    v-on:onClickRightIcon="chartsModal=true"
                    :obj="select"
                    subType="normal"
                    rightCharts="true">
    </dentGameHeader>

    <!-- 基本走势 -->
    <template v-if="currentTable === '基本走势'">
      <div class="ex-header flex flow-col">
        <template v-if="trendTableData.length">
          <!-- 总和 -->
          <div v-show="currenShow === 0"
               class="total flex flow-col">
            <!-- 固定表头 -->
            <table class="fixheader">
              <COLGROUP>
                <col width="26%">
                <col width="26%">
                <col width="16%">
                <col width="16%">
                <col width="16%">
              </COLGROUP>
              <thead>
                <tr>
                  <th>期号</th>
                  <th>总数</th>
                  <th>单双</th>
                  <th>大小</th>
                  <th>七色波</th>
                </tr>
              </thead>
            </table>
            <!-- 表格主体 -->
            <div class="table-main-wrap">
              <table id="table"
                     style="margin-top: -1px;">
                <COLGROUP>
                  <col width="26%">
                  <col width="26%">
                  <col width="16%">
                  <col width="16%">
                  <col width="16%">
                </COLGROUP>
                <tbody>
                  <tr v-for="tr in  trendTableData"
                      :class="tr%2 === 0?'bg-grey':''">
                    <td>{{tr.issue}}期</td>
                    <td>{{tr.sum}}</td>
                    <td>{{tr.dsh | keyToCharacter}}</td>
                    <td>{{tr.dax | keyToCharacter}}</td>
                    <td>{{tr.qsb | keyToCharacter}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 特码 -->
          <div v-show="currenShow === 1"
               class="total flex flow-col">
            <!-- 固定表头 -->

            <!-- 表格主体 -->
            <div class="table-main-wrap">
              <table class="fixheader"
                     style="position: sticky;top: 0;z-index: 1;">
                <COLGROUP>
                  <col width="100px">
                  <col width="100px">
                  <col width="100px">
                  <col width="100px">
                  <col width="100px">
                  <col width="100px">
                  <col width="100px">
                  <col width="100px">
                </COLGROUP>
                <thead>
                  <tr>
                    <th>期号</th>
                    <th>特别号</th>
                    <th>特码大小单双</th>
                    <th>合数大小单双</th>
                    <th>尾大小</th>
                    <th>生肖</th>
                    <th>色波</th>
                    <th>五行</th>
                  </tr>
                </thead>
              </table>
              <table id="table"
                     style="margin-top: -1px;">
                <COLGROUP>
                  <col width="100px">
                  <col width="100px">
                  <col width="100px">
                  <col width="100px">
                  <col width="100px">
                  <col width="100px">
                  <col width="100px">
                  <col width="100px">
                </COLGROUP>
                <tbody>
                  <tr v-for="tr in  trendTableData"
                      :class="tr%2 === 0?'bg-grey':''">
                    <td>{{tr.issue}}期</td>
                    <td>{{tr.tm.toString().length === 1 ?'0'+tr.tm:tr.tm}}期</td>
                    <td>{{tr.tmdxdsh | keyToCharacter}}</td>
                    <td>{{tr.hshdxdsh | keyToCharacter}}</td>
                    <td>{{tr.tmwdx | keyToCharacter}}</td>
                    <td>{{tr.tmshx | keyToCharacter}}</td>
                    <td>{{tr.tmsb | keyToCharacter}}</td>
                    <td>{{tr.tmwx | keyToCharacter}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- tab -->
          <div class="flex charts-tab">
            <span v-for="(item,index) in ['总和','特码']"
                  @click="currenShow = index"
                  :class="currenShow===index?'current-tab':''"
                  class="charts-tab-item">
              {{item}}
            </span>
          </div>
        </template>
        <div v-else
             style="flex: 1;"
             class="flex justify-ct">
          <mt-spinner color="#e62b00"
                      type="fading-circle"></mt-spinner>
        </div>

        <!-- 距离下期开奖 -->
        <div class="time-to-open">
          • <span class="font-grey">距{{nextIssue}}期截止 : </span><span class="font-theme">{{timer.timeToOpen}}</span>
        </div>

      </div>
    </template>
    <!-- 色波 -->
    <template v-if="currentTable=== '色波'">
      <div class="ex-header flex flow-col">

        <div class="total flex justify-ct flow-col">
          <!-- 表格主体 -->
          <template v-if="colorTableData.color && Object.keys(colorTableData.color).length">
            <div class="table-main-wrap">
              <table class="fixheader"
                     style="position: sticky;top: 0;z-index: 1;">
                <colgroup>
                  <col width="76px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="70px">
                </colgroup>
                <thead>
                  <tr>
                    <th rowspan="2">期号</th>
                    <th colspan="7">开奖号码</th>
                    <th rowspan="2">波色比</th>
                  </tr>
                  <tr>
                    <th>正一</th>
                    <th>正二</th>
                    <th>正三</th>
                    <th>正四</th>
                    <th>正五</th>
                    <th>正六</th>
                    <th>特码</th>
                  </tr>
                </thead>
              </table>
              <table id="table"
                     style="margin-top: -1px;">
                <colgroup>
                  <col width="76px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="70px">
                </colgroup>
                <tbody>
                  <tr v-for="(tr,index) in colorTableData.color"
                      :class="index%1 === 0?'bg-grey':''">
                    <td>{{tr.issue}}期</td>
                    <td>
                      <span :class="'bg-' + tr.zh1.bs"
                            class="ball">{{tr.zh1.shuZ}}</span>
                      <span class="fz-14">{{tr.zh1.shX|keyToCharacter}}</span>
                    </td>
                    <td>
                      <span :class="'bg-' + tr.zh2.bs"
                            class="ball">{{tr.zh2.shuZ}}</span>
                      <span class="fz-14">{{tr.zh2.shX|keyToCharacter}}</span>
                    </td>
                    <td>
                      <span :class="'bg-' + tr.zh3.bs"
                            class="ball">{{tr.zh3.shuZ}}</span>
                      <span class="fz-14">{{tr.zh3.shX|keyToCharacter}}</span>
                    </td>
                    <td>
                      <span :class="'bg-' + tr.zh4.bs"
                            class="ball">{{tr.zh4.shuZ}}</span>
                      <span class="fz-14">{{tr.zh4.shX|keyToCharacter}}</span>
                    </td>
                    <td>
                      <span :class="'bg-' + tr.zh5.bs"
                            class="ball">{{tr.zh5.shuZ}}</span>
                      <span class="fz-14">{{tr.zh5.shX|keyToCharacter}}</span>
                    </td>
                    <td>
                      <span :class="'bg-' + tr.zh6.bs"
                            class="ball">{{tr.zh6.shuZ}}</span>
                      <span class="fz-14">{{tr.zh6.shX|keyToCharacter}}</span>
                    </td>
                    <td>
                      <span :class="'bg-' + tr.tm.bs"
                            class="ball">{{tr.tm.shuZ}}</span>
                      <span class="fz-14">{{tr.tm.shX|keyToCharacter}}</span>
                    </td>
                    <td>
                      <span class="fontc-hong">{{tr.sbb.hong}} : </span>
                      <span class="fontc-lan">{{tr.sbb.lan}} : </span>
                      <span class="fontc-lv">{{tr.sbb.lv}}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
              <table style="position: sticky;bottom: 0;z-index: 1;">
                <colgroup>
                  <col width="76px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="60px">
                  <col width="70px">
                </colgroup>
                <tfoot class="bg-yellow">
                  <td class="fontc-6">统计</td>
                  <td v-for="item in [1,2,3,4,5,6]">
                    <span class="fontc-hong">{{colorTableData.analyse['zh'+item].hong}} : </span>
                    <span class="fontc-lan">{{colorTableData.analyse['zh'+item].lan}} : </span>
                    <span class="fontc-lv">{{colorTableData.analyse['zh'+item].lv}}</span>
                  </td>
                  <td>
                    <span class="fontc-hong">{{colorTableData.analyse.tm.hong}} : </span>
                    <span class="fontc-lan">{{colorTableData.analyse.tm.lan}} : </span>
                    <span class="fontc-lv">{{colorTableData.analyse.tm.lv}}</span>
                  </td>
                  <td>
                    <span class="fontc-hong">{{colorTableData.analyse.sbb.hong}} : </span>
                    <span class="fontc-lan">{{colorTableData.analyse.sbb.lan}} : </span>
                    <span class="fontc-lv">{{colorTableData.analyse.sbb.lv}}</span>
                  </td>
                </tfoot>
              </table>
            </div>
          </template>
          <mt-spinner v-else
                      color="#e62b00"
                      type="fading-circle"></mt-spinner>
        </div>

        <!-- 距离下期开奖 -->
        <div class="time-to-open">
          • <span class="font-grey">距{{nextIssue}}期截止 : </span><span class="font-theme">{{timer.timeToOpen}}</span>
        </div>
      </div>
    </template>
    <!-- 生肖 -->
    <template v-if="currentTable === '生肖'">
      <div class="ex-header flex flow-col">

        <div class="total flex justify-ct flow-col">
          <!-- 表格主体 -->
          <template v-if="animalTableData && Object.keys(animalTableData).length">
            <div class="table-main-wrap">
              <table class="fixheader"
                     style="position: sticky;top: 0;z-index: 1;">
                <colgroup>
                  <col width="76px">
                  <col v-for="n in 19"
                       width="40px">
                </colgroup>
                <thead>
                  <tr>
                    <th rowspan="2">期号</th>
                    <th colspan="7">开奖号码</th>
                    <th rowspan="2">鼠</th>
                    <th rowspan="2">牛</th>
                    <th rowspan="2">虎</th>
                    <th rowspan="2">兔</th>
                    <th rowspan="2">龙</th>
                    <th rowspan="2">蛇</th>
                    <th rowspan="2">马</th>
                    <th rowspan="2">羊</th>
                    <th rowspan="2">猴</th>
                    <th rowspan="2">鸡</th>
                    <th rowspan="2">狗</th>
                    <th rowspan="2">猪</th>
                  </tr>
                  <tr>
                    <th>正一</th>
                    <th>正二</th>
                    <th>正三</th>
                    <th>正四</th>
                    <th>正五</th>
                    <th>正六</th>
                    <th>特码</th>
                  </tr>
                </thead>
              </table>
              <table id="table"
                     style="margin-top: -1px;">
                <colgroup>
                  <col width="76px">
                  <col v-for="n in 19"
                       width="40px">
                </colgroup>
                <tbody>
                  <tr v-for="(tr,index) in animalTableData.animal"
                      :class="index%2 !== 0?'bg-grey':''">
                    <td>{{tr.issue}}期</td>
                    <td>
                      <span :class="'bg-' + tr.zh1.bs"
                            class="ball">{{tr.zh1.shuZ}}</span>
                      <span class="fz-14">{{tr.zh1.shX|keyToCharacter}}</span>
                    </td>
                    <td>
                      <span :class="'bg-' + tr.zh2.bs"
                            class="ball">{{tr.zh2.shuZ}}</span>
                      <span class="fz-14">{{tr.zh2.shX|keyToCharacter}}</span>
                    </td>
                    <td>
                      <span :class="'bg-' + tr.zh3.bs"
                            class="ball">{{tr.zh3.shuZ}}</span>
                      <span class="fz-14">{{tr.zh3.shX|keyToCharacter}}</span>
                    </td>
                    <td>
                      <span :class="'bg-' + tr.zh4.bs"
                            class="ball">{{tr.zh4.shuZ}}</span>
                      <span class="fz-14">{{tr.zh4.shX|keyToCharacter}}</span>
                    </td>
                    <td>
                      <span :class="'bg-' + tr.zh5.bs"
                            class="ball">{{tr.zh5.shuZ}}</span>
                      <span class="fz-14">{{tr.zh5.shX|keyToCharacter}}</span>
                    </td>
                    <td>
                      <span :class="'bg-' + tr.zh6.bs"
                            class="ball">{{tr.zh6.shuZ}}</span>
                      <span class="fz-14">{{tr.zh6.shX|keyToCharacter}}</span>
                    </td>
                    <td>
                      <span :class="'bg-' + tr.tm.bs"
                            class="ball">{{tr.tm.shuZ}}</span>
                      <span class="fz-14">{{tr.tm.shX|keyToCharacter}}</span>
                    </td>
                    <td>{{tr.shu}}</td>
                    <td>{{tr.niu}}</td>
                    <td>{{tr.hu}}</td>
                    <td>{{tr.tu}}</td>
                    <td>{{tr.long}}</td>
                    <td>{{tr.she}}</td>
                    <td>{{tr.ma}}</td>
                    <td>{{tr.yang}}</td>
                    <td>{{tr.hou}}</td>
                    <td>{{tr.ji}}</td>
                    <td>{{tr.gou}}</td>
                    <td>{{tr.zhu}}</td>
                  </tr>
                </tbody>
              </table>
              <div class="animalTfootWrap"
                   style="position: sticky;bottom: 0;z-index: 1;">
                <table>
                  <colgroup>
                    <col width="76px">
                    <col v-for="n in 19"
                         width="40px">
                  </colgroup>
                  <tfoot class="bg-yellow">
                    <tr v-for="(value,key) in animalTableData.analyse">
                      <td class="fontc-3">{{key | keyToCharacter}}</td>
                      <td>{{value.zh1}}</td>
                      <td>{{value.zh2}}</td>
                      <td>{{value.zh3}}</td>
                      <td>{{value.zh4}}</td>
                      <td>{{value.zh5}}</td>
                      <td>{{value.zh6}}</td>
                      <td>{{value.tm}}</td>
                      <td>{{value.shu}}</td>
                      <td>{{value.niu}}</td>
                      <td>{{value.hu}}</td>
                      <td>{{value.tu}}</td>
                      <td>{{value.long}}</td>
                      <td>{{value.she}}</td>
                      <td>{{value.ma}}</td>
                      <td>{{value.yang}}</td>
                      <td>{{value.hou}}</td>
                      <td>{{value.ji}}</td>
                      <td>{{value.gou}}</td>
                      <td>{{value.zhu}}</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </template>
          <mt-spinner v-else
                      color="#e62b00"
                      type="fading-circle"></mt-spinner>
        </div>

        <!-- 距离下期开奖 -->
        <div class="time-to-open">
          • <span class="font-grey">距{{nextIssue}}期截止 : </span><span class="font-theme">{{timer.timeToOpen}}</span>
        </div>
      </div>
    </template>

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
import { six as keyToCharacter } from "@/js/keyToCharacter";
import { Spinner } from "mint-ui";

export default {
  components: {
    dentGameHeader
  },
  filters: {
    // 键值转成中文
    keyToCharacter: function(value) {
      let character = "";
      value = value.toLowerCase();
      if (value.match(/_/)) value = value.split("_")[1];
      for (let key in keyToCharacter) {
        if (key === value) {
          character = keyToCharacter[key];
          break;
        }
      }
      !character && (character = value);
      return character;
    }
  },
  data() {
    return {
      //数据相关
      select: { 基本走势: "", 生肖: "", 色波: "" },
      trendTableData: [], //基本走势图数据
      colorTableData: {}, //色波走势图数据
      animalTableData: {}, //色波走势图数据
      //状态相关
      nextIssue: 0,
      currentTable: "基本走势", //当前走势图
      currenShow: 0, //切换 基本走势的 总和 和 特码
      chartsModal: false, //走势图设置弹窗
      issueNum: 30, //当前期数
      order: "desc", //正反序
      timer: {} //倒计时
    };
  },
  mounted: function() {
    this.nextIssue = this.GLOBAL.six.issue;
    //从全局变量读取并且启动倒计时
    let countdown = this.GLOBAL.six.countdown;
    this.countTime(countdown, "timeToOpen");

    this.$parent.tabbarShow = false;
    this.getOddsData();
  },
  computed: {},
  watch: {
    //倒序数组
    order() {
      if (this.currentTable === "基本走势") {
        this.trendTableData.reverse();
      }
      if (this.currentTable === "色波") {
        this.colorTableData.color.reverse();
      }
      if (this.currentTable === "生肖") {
        this.animalTableData.animal.reverse();
      }
    },
    //更改期数
    issueNum() {
      this.onselect(this.currentTable);
    }
  },
  methods: {
    onselect(index) {
      this.currentTable = index;
      let key;
      if (index === "色波") key = "color";
      if (index === "生肖") key = "animal";
      if (index === "基本走势") key = "trend";
      this.getData(key, this.issueNum);
    },

    getData(key, size) {
      //type:trend：走势图，type:animal:生肖，type:color:色波  game:Six, size:30,50或者100
      this.senddata({ data: { type: key, game: "six", size } });
    },

    //请求 赔率 数据
    getOddsData() {
      let gameGongHost = this.GLOBAL.gameGongHost;
      //请求数据
      if (!this.GLOBAL.gameSocketHand) {
        this.createSocket(gameGongHost, () => {
          this.dealOnMessage(); //创建连接成功之后 监听返回的信息
          this.getData("trend", 30);
        });
      } else {
        this.dealOnMessage(); //已经创建连接 监听返回的信息
        this.getData("trend", 30);
      }
    },

    //处理返回的数据
    dealOnMessage() {
      this.onmessage(res => {
        res = JSON.parse(res);
        if (res.code === "pong") return;
        //基本走势
        if (res.trend) {
          this.trendTableData = res.trend;
        }
        //色波数据
        if (res.color) {
          res.color.map(arrobj => {
            ["sbb", "tm", "zh1", "zh2", "zh3", "zh4", "zh5", "zh6"].forEach(
              key => {
                arrobj[key] = JSON.parse(arrobj[key]);
              }
            );
          });

          this.colorTableData = res;
        }
        //生肖数据
        if (res.animal) {
          res.animal.map(arrobj => {
            ["tm", "zh1", "zh2", "zh3", "zh4", "zh5", "zh6"].forEach(key => {
              arrobj[key] = JSON.parse(arrobj[key]);
            });
          });

          this.animalTableData = res;
        }
      });
    },

    //倒计时 endTimeStamp时间戳 dataStr...
    countTime(endTimeStamp, dataStr) {
      let setTimer = () => {
        let leftTime = endTimeStamp - new Date().getTime(); //时间差
        //定义变量 d,h,m,s保存倒计时的时间
        let d, h, m, s;
        function toFix(str) {
          return str < 10 ? "0" + str : str;
        }
        if (leftTime >= 0) {
          // d = Math.floor(leftTime/1000/60/60/24);
          h = toFix(Math.floor((leftTime / 1000 / 60 / 60) % 24));
          m = toFix(Math.floor((leftTime / 1000 / 60) % 60));
          s = toFix(Math.floor((leftTime / 1000) % 60));

          this.$set(this.timer, dataStr, `${h}:${m}:${s}`);
        } else {
          clearInterval(this.timer[dataStr + "timer"]);
        }
      };

      this.timer[dataStr + 'Interval'] = setInterval(setTimer,1000);
    }

  }

}
</script>

<style scoped>
.ball {
  display: inline-block;
  width: 6vw;
  height: 6vw;
  background: #e64600;
  line-height: 6vw;
  color: #fff;
  border-radius: 50%;
}
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
  width: 100%;
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
