<template>
  <div class="contanier">
    <!-- 头部 -->
    <header class="dent-header flex justify-sb">

      <span dentHoverclass="hoverclass"
            @click="$router.go(-1)"
            class="flex iconfont icon-left click-icon"></span>

      <div class="hecai flex justify-sb">

        <div class="brief flex">
          <div class="avatar">

          </div>
          <div class="brief-info flex flow-col justify-sa">
            <span class="fz-18 font-theme">
              香港⑥合彩
            </span>
            <span class="fz-12 fontc-6">第2018117期</span>
          </div>
        </div>

        <div class="time-left flex flow-col justify-sa">
          <span class="fz-18 font-theme">36:33:40</span>
          <span class="fz-12 fontc-3">即将开奖......</span>
        </div>

      </div>

    </header>

    <!-- 开奖列表 -->
    <div class="hecai-list bgc-fff">

      <div v-for="n in 3"
           class="hecai-item">
        <div class="hecai-item-info flex justify-sb fontc-6 fz-12">
          <span>第2018116期</span>
          <span>2018-10-11 21:36:51</span>
        </div>
        <openNum :arr=[42,19,29,7,39,9,41]
                 :showAnimals="false" />
      </div>

    </div>

  </div>
</template>

<script>
import openNum from "./compoent-openNum";

export default {
  components:{
    openNum
  },
  mounted(){
    this.$parent.tabbarShow = false;
  }
}
</script>

<style scoped>
.fz-12 {
  font-size: 12px;
}
.fz-18 {
  font-size: 18px;
}
.hoverclass {
  background: rgba(0, 0, 0, 0.1);
}
.contanier {
  padding: 0;
}
/* 头部 */
.dent-header {
  height: 18vw;
  background: #57d6dd;
}
.click-icon {
  flex: 1;
  padding-left: 2vw;
  height: 100%;
  color: #fff;
  font-size: 20px;
}
.hecai {
  width: 87.5vw;
  height: 14vw;
  background: white;
  border-top-left-radius: 7vw;
  border-bottom-left-radius: 7vw;
}
.avatar {
  margin-left: 1vw;
  margin-right: 2vw;
  width: 12vw;
  height: 12vw;
  border-radius: 50%;
  background: #e3e5e8;
  overflow: hidden;
}
.brief {
  height: 100%;
}
.brief-info {
  align-items: flex-start;
  height: 100%;
}
/* 开奖列表 */
.hecai-item {
  padding: 0 2vw 2vw;
  border-bottom: 1px solid #e5e5e5;
}
.hecai-item-info {
  line-height: 10vw;
}
</style>
