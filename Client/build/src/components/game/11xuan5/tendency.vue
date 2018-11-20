<template>
  <div class="contanier">
    <!-- 导航栏 -->
    <mt-header title="开奖走势"
               fixed="true">
      <router-link to="/11xuan5"
                   slot="left">
        <mt-button icon="back"></mt-button>
      </router-link>

      <router-link slot="right"
                   style="color:#047278;"
                   tag="span"
                   denthoverclass="hoverclass"
                   to="/11xuan5/openHistory">
        开奖历史
      </router-link>
    </mt-header>

    <!-- 可选项 -->
    <div class="option flex justify-sb">
      <p class="flex">显示遗漏分层<span class="checkbox"></span></p>
      <p class="flex">显示折线<span class="checkbox"></span></p>
      <p class="flex">不带遗漏<span class="checkbox"></span></p>
    </div>

    <!-- 万位 千位 -->
    <div class="option digit flex justify-sb">
      <span v-for="(item,index) in ['万位','千位','百位','十位','个位']"
            :class="index===1?'currentDigit':''"
            denthoverclass="_hoverclass">{{item}}</span>
    </div>

    <!-- 固定头部 -->
    <table class="dent-table">
      <COLGROUP>
        <col width="15.38%">
        <col v-for="n in  11"
             width="7.69%">
      </COLGROUP>
      <thead>
        <tr>
          <th>期号</th>
          <th v-for="n in  11">{{n}}</th>
        </tr>
      </thead>
    </table>

    <!-- 表格主体  -->
    <div class="table-main-wrap">
      <table id="table"
             class="dent-table">
        <COLGROUP>
          <col width="15.38%">
          <col v-for="n in  11"
               width="7.69%">
        </COLGROUP>
        <tbody>
          <tr v-for="n in 20">
            <td>{{ n.toString().length === 1? '0'+n : n }}</td>
            <td v-for="m in  11">
              <div :class=" m === n + Math.floor(Math.random()*5) ?'openball':''">{{Math.floor(Math.random()*20)}}
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 固定底部 -->
    <table class="dent-table">
      <COLGROUP>
        <col width="15.38%">
        <col v-for="n in  11"
             width="7.69%">
      </COLGROUP>
      <thead>
        <tr>
          <th>期号</th>
          <th v-for="n in  11">{{n}}</th>
        </tr>
      </thead>
    </table>
    <!-- 万位 千位 -->
    <div class="option digit flex justify-sb">
      <span v-for="(item,index) in ['万位','千位','百位','十位','个位']"
            :class="index===1?'currentDigit':''"
            denthoverclass="_hoverclass">{{item}}</span>
    </div>

  </div>
</template>

<script>
export default {
  data(){
    return{
      pickerDate:'',
      UnformatPickerDate:'',
      openHistory:[]
    }
  },
  methods:{
    //初始化请求数据
    getPingData() {
      const _sendData = () => {
        this.senddata({type:"trend",game:'GD',});
      }

      //请求数据
      if (!this.GLOBAL.gameSocketHand) {
        this.createSocket("ws://192.168.0.136:2310", () => {
          _sendData();
        });
      } else {
        _sendData();
      }

    },
    //处理返回的数据
    dealOnMessage() {
      this.onmessage( (res) => {
        res = JSON.parse(res);
        debugger
        let checkRes =  res instanceof Object || res instanceof Array;
        if(checkRes) {
          this.openHistory = res
        };
      })
    },
    drawline(){
      //初始化
      let table = document.getElementById("table");
      let canvas = `<canvas id="dentTableCanvas" width="${table.offsetWidth}" height="${table.offsetHeight}" style="position: absolute;left: 0;top: 0; z-index: 1;"></canvas>`;
      table.insertAdjacentHTML('beforebegin',canvas);
      let ctx = document.getElementById("dentTableCanvas").getContext("2d");
      //获取所有开奖的球
      let balls = table.querySelectorAll('.openball');
      let ballWidth = balls[0].parentNode.offsetWidth / 2;
      let ballHeight = balls[0].parentNode.offsetHeight / 2;
      //获取相对于 class= table-main-wrap  的坐标值
      let poinst = [];
      balls.forEach(element => {
        poinst.push({
          left: element.parentNode.offsetLeft,
          top: element.parentNode.offsetTop
        })
      });
      //开始画线
      ctx.lineWidth = 1;//线条的宽度
      ctx.strokeStyle = "#e64600";//线条的颜色
      ctx.beginPath();
      ctx.moveTo(poinst[0].left + ballWidth, poinst[0].top + ballHeight);//起始位置
      for(let i=1;i<poinst.length;i++){ //画点
        ctx.lineTo(poinst[i].left + ballWidth, poinst[i].top + ballHeight);
      }
      ctx.stroke()
    },
  },
  mounted (){
    this.$parent.tabbarShow = false;
    this.getPingData();
    this.dealOnMessage();

    this.drawline();
  },
}
</script>

<style scoped>
.contanier {
  padding: 10vw 10px 0;
  background: #fff;
  color: #333;
  font-size: 13px;
  font-family: pingfang sc regular;
}

._hoverclass {
  background: rgba(87, 214, 221, 0.1);
}

.option {
  height: 13.8vw;
  line-height: 13.8vw;
  border-bottom: 1px solid #e7e7e7;
}
.digit {
  padding: 2px 0;
  height: 10vw;
  line-height: 10vw;
  border-bottom: 1px solid #e7e7e7;
}
.digit span {
  position: relative;
  flex: 1;
  margin: 0 1vw;
  font-size: 14px;
}
.checkbox {
  display: inline-block;
  margin-left: 4px;
  width: 20px;
  height: 20px;
  border: 1px solid #a3a3a3;
  border-radius: 2px;
}
.currentDigit {
  color: #57d6dd;
  border-bottom: 2px solid #57d6dd;
}

.dent-table {
  margin-top: -1px;
  width: 100%;
  border-spacing: 0;
  border-collapse: collapse;
  font-size: 12px;
}
.dent-table th,
.dent-table td {
  border: 1px solid #ddd;
  font-weight: normal;
}
.dent-table th,
.dent-table td {
  line-height: 6vw;
}
/* 表格主体 */
#table,
.table-main-wrap {
  position: relative;
}
.table-main-wrap {
  height: calc(100vh - 12vw - 20vw - 13.8vw - 10vw - 4vw - 14px);
  overflow: auto;
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
</style>
