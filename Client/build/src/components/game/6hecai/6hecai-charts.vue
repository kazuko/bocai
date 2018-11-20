<template>
  <div class="container">
    <!-- 导航栏 -->
    <dentGameHeader v-on:onselect="onselect"
                    v-on:onClickRightIcon="onClickRightIcon"
                    :obj="{'基本走势':'','生肖':'','色波':''}"
                    rightCharts="true">
    </dentGameHeader>

    <!-- 主体 -->
    <div class="ex-header flex flow-col">
      <!-- 固定表头 -->
      <table class="fixheader">
        <COLGROUP>
          <col width="21.6%">
          <col v-for="item in  11"
               :width="(100 - 21.6)/11+'%'">
        </COLGROUP>
        <thead>
          <tr>
            <th>期号</th>
            <th v-for="item in  11">{{ item.toString().length === 1? '0'+item : item }}</th>
          </tr>
        </thead>
      </table>

      <!-- 表格主体 -->

      <!-- 表格主体 -->
      <div class="table-main flex flow-col">
        <!-- 表格 -->
        <div class="table-main-wrap">
          <table id="table">
            <COLGROUP>
              <col width="21.6%">
              <col v-for="item in  11"
                   :width="(100 - 21.6)/11+'%'">
            </COLGROUP>
            <tbody>
              <tr v-for="tr in  20"
                  :class="tr%2 === 0?'bg-grey':''">
                <td>{{tr}}期</td>
                <td v-for="subtr in  11">
                  <div :class=" subtr === tr + Math.floor(Math.random()*5) ?'openball':''">
                    {{Math.floor(Math.random()*20)}}
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- 统计数据 -->
        <table class="count">
          <COLGROUP>
            <col width="21.6%">
            <col v-for="item in  11"
                 :width="(100 - 21.6)/11+'%'">
          </COLGROUP>
          <tbody>
            <tr>
              <td>出现次数</td>
              <td v-for="tr in  11">{{Math.floor(Math.random()*20)}}</td>
            </tr>
            <tr>
              <td>平均遗漏</td>
              <td v-for="tr in  11">{{Math.floor(Math.random()*20)}}</td>
            </tr>
            <tr>
              <td>最大遗漏</td>
              <td v-for="tr in  11">{{Math.floor(Math.random()*20)}}</td>
            </tr>
            <tr>
              <td>最大连出</td>
              <td v-for="tr in  11">{{Math.floor(Math.random()*20)}}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 球数 -->
      <div class="flex ball">
        <span v-for="(item,index) in ['一','二','三','四','五']"
              :class="index===0?'current-ball':''"
              class="ball-item">
          第{{item}}球
        </span>
      </div>

      <!-- 距离下期开奖 -->
      <div class="time-to-open">
        • <span class="font-grey">距20181005010期截止 : </span><span class="font-theme">00 : 06 : 14</span>
      </div>

    </div>

    <!-- 走势图设置弹窗 -->
    <transition name="fadein">
      <div @click.self="toggleModal('chartsModal')"
           class="charts-mask"
           v-show="chartsModal">
        <div :class="chartsModal?'modal-show':''"
             class="modal">
          <div class="modal-title">
            走势图设置
          </div>

          <div class="modal-body">
            <!-- 选择期数 -->
            <div class="setup">
              <p class="fontc-6">期数：</p>
              <div class="flex justify-sa">
                <label v-for="item in [30,50,100]">
                  <input type="radio"
                         v-model="issueNum"
                         :value="item">{{item}}期</label>
              </div>
            </div>
            <!-- 选择排序 -->
            <div class="setup">
              <p class="fontc-6">排序：</p>
              <div class="flex justify-sa">
                <label v-for="item in [{text:'顺序显示',order:'asc'},{text:'倒序显示',order:'desc'}]">
                  <input type="radio"
                         v-model="order"
                         :value="item.order">{{item.text}}</label>
              </div>
            </div>

          </div>

          <div class="modal-btn flex">
            <a @click="toggleModal('chartsModal')"
               dentHoverclass="hoverclass"
               class="modal-btn-cancle fontc-9">取消</a>
            <a @click="toggleModal('chartsModal')"
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

export default {
  components: {
    dentGameHeader
  },
  data () {
    return {
      chartsModal: false,
      issueNum:30,
      order:'desc'
    }
  },
  mounted: function() {
    this.$parent.tabbarShow = false;
    this.drawline();
  },
  methods: {
    onselect(val){
    },

    onClickRightIcon(){
      this.toggleModal('chartsModal');
    },

    //显示隐藏 弹窗
    toggleModal(modalStr) {
      this[modalStr] = !this[modalStr];
    },

    drawline(){
      //初始化 获取表格的宽高，生成一个 canvas 定位到 table上(z-index)
      let table = document.getElementById("table");
      let canvas = `<canvas id="dentTableCanvas" width="${table.offsetWidth}" height="${table.offsetHeight}" style="position: absolute;left: 0;top: 0; z-index: 1;"></canvas>`;
      table.insertAdjacentHTML('beforebegin',canvas);
      //获取所有开奖的球
      let balls = table.querySelectorAll('.openball');
      let ballWidth = balls[0].parentNode.offsetWidth / 2;
      let ballHeight = balls[0].parentNode.offsetHeight / 2;
      //获取开奖球相对于  table-main-wrap(position: relative;)  的坐标值
      let poinst = [];
      balls.forEach(element => {
        poinst.push({
          left: element.parentNode.offsetLeft,
          top: element.parentNode.offsetTop
        })
      });
      //开始画线
      let ctx = document.getElementById("dentTableCanvas").getContext("2d");
      ctx.lineWidth = 1;//线条的宽度
      ctx.strokeStyle = "#e64600";//线条的颜色
      ctx.beginPath();
      ctx.moveTo(poinst[0].left + ballWidth, poinst[0].top + ballHeight);//起始位置
      for(let i=1;i<poinst.length;i++){ //画点
        ctx.lineTo(poinst[i].left + ballWidth, poinst[i].top + ballHeight);
      }
      ctx.stroke()
    },
  }
}
</script>

<style scoped>
.container {
  padding-top: 13.8vw;
  font-family: pingfang sc normal;
  font-size: 12px;
}
.ex-header {
  height: calc(100vh - 13.8vw);
}
table {
  width: 100%;
  border-spacing: 0;
  border-collapse: collapse;
  font-size: 12px;
}
th,
td {
  border: solid #e9e9e9;
  border-width: 0 1px;
  font-weight: normal;
  color: #858da3;
  line-height: 7.8vw;
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
/* 统计表格 */
.count {
  background: #edffd1;
}
.count th,
.count td {
  border-width: 1px;
}
/* 球数 */
.ball-item {
  flex: 1;
  background: #e5e5e5;
  line-height: 8vw;
  color: #4e4e4e;
}
.current-ball {
  background: #e64600;
  color: #fff;
}
/* 下期开奖 */
.ball {
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
.modal-show {
  transform: translate3d(0, 0, 0);
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
  background: #cc0000;
  color: #ffffff;
}
.setup {
  font-size: 14px;
  line-height: 8vw;
}
</style>
