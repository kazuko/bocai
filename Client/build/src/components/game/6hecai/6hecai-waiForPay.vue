<template>
  <div class="contanier flex flow-col">
    <!-- 头部 -->
    <header class="dent-header flex justify-sb fz-16">

      <div dentHoverclass="hoverclass"
           @click="$router.go(-1)"
           class="isleft flex iconfont icon-left click-icon"></div>

      <div class="iscenter flex justify-ct">投注单</div>

      <div class="isleft"></div>

    </header>

    <!-- 主体 -->
    <div class="bets flex flow-col">
      <p class="bets-time-left fontc-6">2018117期投注截止时间：<span class="font-theme">35:06:28</span></p>
      <!-- 出票口 -->
      <div class="bets-ticket"></div>
      <!-- 票据 -->
      <div class="ticket-wrap">

        <transition-group name="ticketout"
                          tag="div">
          <div v-for="(item,index) in ticket"
               :key="item"
               class="ticket-item flex justify-sb">
            <div class="ticket-item-info flex flow-col">
              <p class="fontc-6 fz-12">
                两面(两面)共<span class="font-theme">1</span>注，共<span class="font-theme">2</span>元
              </p>
              <span class="fz-16">{{item}}</span>
            </div>
            <span @click="remove(index)"
                  class="iconfont icon-guanbi cancel-ticket"></span>
          </div>
        </transition-group>

      </div>
    </div>

    <!-- 底部下注栏 -->
    <div class="hecai-bottom flex justify-sb bgc-fff">
      <div class="hecai-btn random-btn">清空</div>

      <div class="should-pay flex flow-col justify-sa fontc-3 fz-12">
        <p>合计<span class="font-theme">12.000元</span></p>
        <p>可用余额<span class="font-theme">12.000元</span></p>
      </div>

      <div v-if="totalBets"
           class="hecai-btn submit-btn cansubmit">投注</div>
      <div v-else
           @click="add"
           class="hecai-btn submit-btn">投注</div>
    </div>
  </div>
</template>

<script>
export default {
  data(){
    return{
      ticket:['特单0','特单1','特单2','特单3','特单4']
    }
  },
  methods:{
    add: function () {
      this.ticket.unshift('unshift插进来'+Math.random())
    },
    remove: function (index) {
      this.ticket.splice(index,1);
    },
  }
}
</script>

<style scoped>
.fz-12 {
  font-size: 12px;
}
.fz-16 {
  font-size: 16px;
}
.fz-18 {
  font-size: 18px;
}
.hoverclass {
  background: rgba(0, 0, 0, 0.1);
}

.ticketout-enter-active {
  transition: all 0.5s;
}
.ticketout-leave-active {
  transition: all 0.3s;
}
.ticketout-enter {
  opacity: 0;
  transform: translateY(-100%);
}
.ticketout-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
.ticketout-move {
  transition: transform 0.5s;
}

.contanier {
  height: 100vh;
}
/* 头部 */
.dent-header {
  position: fixed;
  top: 0;
  left: 0;
  box-sizing: border-box;
  padding: 0 2vw;
  width: 100%;
  height: 13.8vw;
  background: #57d6dd;
  color: #ffffff;
}
.iscenter {
  flex: 1;
  height: 100%;
}
.isleft,
.isright {
  flex: 0.5;
  height: 100%;
}
.click-icon {
  font-size: 20px;
}
/* 主体 */
.bets {
  flex: 1;
  width: 100%;
}
.bets-time-left {
  line-height: 10vw;
}

.bets-ticket {
  width: 94.6vw;
  height: 4.2vw;
  border: 1.4vw solid #fff;
  border-radius: 2.1vw;
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3), 0 0 3px 1px rgba(0, 0, 0, 0.2);
}
.ticket-wrap {
  position: relative;
  margin-top: -2.2vw;
  padding-top: 2.8vw;
  width: 89vw;
  background: #ffffff;
  border: 1px solid #ddd;
  filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0.3));
}
.ticket-wrap::after {
  content: "";
  position: absolute;
  left: 0;
  bottom: -4vw;
  width: 100%;
  height: 4vw;
  background: radial-gradient(#fff, #fff 2vw, transparent 2vw) repeat-x;
  background-size: 4vw 4vw;
  background-position: 0 -2vw;
}
.ticket-item + .ticket-item {
  border-top: 1px dashed #c8c8c8;
}
.ticket-item-info {
  flex: 1;
  align-items: flex-start;
  padding: 1vw 2vw;
  line-height: 1.6;
}
.cancel-ticket {
  align-self: flex-start;
  padding: 2vw;
  color: #dbdfe6;
}

/* 底部固定栏 */
.hecai-bottom {
  width: 100%;
  padding: 0 2vw;
  height: 10.5vw;
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
.should-pay {
  height: 100%;
  line-height: 1;
}
</style>
