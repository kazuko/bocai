<template>
  <div class="contanier">
    <!-- 导航栏 -->
    <mt-header title="开奖历史"
               fixed="true">
      <router-link to="/11xuan5"
                   slot="left">
        <mt-button icon="back"></mt-button>
      </router-link>

      <router-link slot="right"
                   tag="span"
                   style="color:#333;"
                   v-hoverclass="hoverclass"
                   to="/11xuan5/tendency">走势图</router-link>
    </mt-header>

    <!-- 当前查看的日期 -->
    <div class="date flex justify-sb bgc-fff com-bb">
      <span class="game">广东11选5</span>
      <div class="date-input-wrap">
        <input @focus="openDatePicker($event)"
               type="text"
               class="date-input"
               :value="pickerDate">
        <img class="input-icon-bg"
             src="@/assets/input-dropDown.png">
        <img class="input-icon"
             src="@/assets/input-dropDownArrow.png">
      </div>
    </div>

    <!-- 开奖历史 -->
    <div v-for="item in openHistory"
         class="history-item bgc-fff com-bb">
      <p class="history-item-date flex justify-sb">
        <span>{{item.expect}}</span>
        <span>{{item.opentime.split(' ')[0]}}</span>
        <span>{{item.opentime.split(' ')[1]}}</span>
      </p>
      <p class="open-num">
        <span v-for="n in item.opencode.split(',')"
              class="open-num-item">{{n}}</span>
      </p>
    </div>

    <mt-datetime-picker type="date"
                        ref="picker"
                        :startDate="startDate"
                        :endDate="endDate"
                        v-model="UnformatPickerDate"
                        @confirm="handleConfirm">
    </mt-datetime-picker>

    <!-- 下注按钮 -->
    <div class="btn-container">
      <mt-button type="default"
                 size="large">去下注</mt-button>
    </div>
  </div>
</template>

<script>

Date.prototype.Format = function(fmt) {
  var o = {
    "M+": this.getMonth() + 1, //月份
    "d+": this.getDate(), //日
    "h+": this.getHours(), //小时
    "m+": this.getMinutes(), //分
    "s+": this.getSeconds(), //秒
    "q+": Math.floor((this.getMonth() + 3) / 3), //季度
    S: this.getMilliseconds() //毫秒
  };
  if (/(y+)/.test(fmt))
    fmt = fmt.replace(
      RegExp.$1,
      (this.getFullYear() + "").substr(4 - RegExp.$1.length)
    );
  for (var k in o)
    if (new RegExp("(" + k + ")").test(fmt))
      fmt = fmt.replace(
        RegExp.$1,
        RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length)
      );
  return fmt;
};

export default {
  data(){
    return{
      pickerDate:'',
      UnformatPickerDate:'',
      openHistory:[]
    }
  },
  methods:{
    openDatePicker(e) {
      e.currentTarget.blur();
      this.$refs.picker.open();
    },
    //初始化请求数据
    getPingData() {
      const _sendData = () => {
        this.senddata({type:"history",game:'GD',});
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
    //时间相关
    time (){
      //input框的日期
      this.pickerDate = new Date().Format("yyyy-MM-dd");
      // 时间选择组件可以选择的日期
      this.startDate = new Date('2017 01 01');
      this.endDate = new Date();
    }
  },
  watch:{
    UnformatPickerDate(val){
      let a = val.Format("yyyy-MM-dd");
    }
  },
  mounted (){
    this.$parent.tabbarShow = false;
    this.getPingData();
    this.dealOnMessage();

    this.time();
  },
}
</script>

<style scoped>
/* 组件微调 */
.mint-datetime-confirm {
  color: #57d6dd;
}
.mint-datetime-cancel {
  color: #999;
}
.picker-item.picker-selected {
  color: #57d6dd;
}
.com-bb {
  border-bottom: 1px solid #e7e7e7;
}
.contanier {
  padding-top: 10vw;
  color: #333;
  font-size: 13px;
  font-family: pingfang sc regular;
}
/* 当前查看日期 */
.date {
  padding: 0 12px;
  height: 10vh;
  line-height: 10vh;
}
.game {
  font-size: 14px;
}
.date-input {
  text-align: left;
  width: 37vw;
  height: 8vw;
  border: 1px solid #a3a3a3;
  line-height: 4vh;
  text-indent: 10px;
  letter-spacing: 0.05em;
  border-radius: 6px;
  transition: 0.5s;
}
.date-input:focus {
  border-color: #e64100;
}
.date-input-wrap {
  position: relative;
}
.input-icon {
  position: absolute;
  top: 7.5vw;
  right: 2.2vw;
  height: 2.5vw;
}
.input-icon-bg {
  position: absolute;
  top: 4.8vw;
  right: 0;
  height: 8vw;
}
/* 历史开奖号码 */
.history-item {
  padding: 10px;
}
.history-item-date {
  width: 68vw;
  line-height: 28px;
}
.open-num {
  padding: 5px 0;
  overflow: hidden;
}
.open-num-item {
  float: left;
  margin-right: 1vw;
  width: 24px;
  height: 24px;
  background: #e62b00;
  color: #fff;
  text-align: center;
  line-height: 24px;
  font-size: 12px;
  border-radius: 50%;
}

/* 下注按钮 */
.btn-container {
  position: fixed;
  left: 0;
  bottom: 0;
  width: 100%;
}
.btn-container .mint-button--default {
  height: 12vw;
  background: #e64100;
  color: #fff;
  font-weight: normal;
  letter-spacing: 0.05em;
  font-family: inherit;
}
</style>
