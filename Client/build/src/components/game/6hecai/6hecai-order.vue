<template>
  <div class="contanier flex flow-col">
    <!-- 头部 -->
    <header class="dent-header flex justify-sb fz-16">

      <div dentHoverclass="hoverclass"
           @click="$router.go(-1)"
           class="isleft flex iconfont icon-left click-icon"></div>

      <div class="iscenter flex justify-ct">数字彩票</div>

      <div class="isright flex iconfont icon-filter click-icon"></div>

    </header>

    <switchTab :tabObj='tabObj'
               flex="true"
               v-model="activeTab"></switchTab>

    <mt-tab-container swipeable="true"
                      v-model="activeTab">

      <mt-tab-container-item v-for="(value,key,index) in tabObj"
                             :id="index"
                             :key="index">
        <div v-for="n in 4"
             class="order-item flex flow-col fontc-6">
          <div class="game-info flex">
            <div class="avatar"></div>
            <span class="fz-14">香港⑤合彩2018117期</span>
          </div>
          <div class="order-status flex justify-sb fz-14">
            <span>投注2.000元</span>
            <span class="flex font-theme"><b class="iconfont icon-zhongjiang"></b>投注2.000元</span>
          </div>
        </div>
      </mt-tab-container-item>

    </mt-tab-container>

  </div>
</template>

<script>
import switchTab from "./compoent-switch";

export default {
  components: {
    switchTab
  },
  data(){
    return{
      activeTab:0,
      tabObj:{"全部注单":"全部注单", "待开奖":"待开奖", "已中奖":"已中奖", "已撤单":"已撤单"}
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
.fz-14 {
  font-size: 14px;
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
.contanier {
  padding-top: 13.8vw;
  height: 100vh;
  background: #fff;
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
.isright {
  justify-content: flex-end;
}
.click-icon {
  font-size: 20px;
}
.mint-tab-container {
  width: 100%;
}
.mt-tab-container-wrap {
  flex: 1;
}
.order-item {
  padding: 2vw 0;
  line-height: 2;
  border-bottom: 1px solid #f1f1f1;
}
.game-info {
  align-self: flex-start;
  padding: 0 2vw;
}
.avatar {
  margin-right: 2vw;
  width: 8vw;
  height: 8vw;
  background: #f1f1f1;
  border-radius: 50%;
}
.order-status {
  margin-top: 2vw;
  padding: 0 2vw;
  width: 100%;
}
</style>
