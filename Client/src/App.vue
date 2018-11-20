<template>
  <div id="app">
    <div class="main">
      <!-- $route.meta.keepAlive是否需要缓存组件状态 -->
      <transition :name="transitionName">
        <keep-alive :include="shouldKeepAlive">
          <router-view v-if="$route.meta.keepAlive">
          </router-view>
        </keep-alive>
      </transition>
      <transition :name="transitionName">
        <router-view v-if="!$route.meta.keepAlive">
        </router-view>
      </transition>
    </div>
    <mainnav v-show="tabbarShow"
             v-if="tabbarWhic"></mainnav>
    <usernav v-show="tabbarShow"
             v-if="!tabbarWhic"></usernav>
    <radioText :list="radioTextList"></radioText>
  </div>
</template>

<script>
console.log("APP_VUE");
import usernav from "@/components/common/usernav";
import mainnav from "@/components/common/mainnav";
import radioText from "@/components/common/radioText";

export default {
  name: "App",
  data() {
    return {
      transitionName: "",
      tabbarShow: true,
      tabbarWhic: true,
      radioTextList: this.GLOBAL.radioTextList,
      shouldKeepAlive:[], //应该被缓存的组件
    };
  },
  components: {
    usernav,
    mainnav,
    radioText
  },
  created(){
    //复制一份缓存组件数组，请不要修改 global的缓存数组
    this.shouldKeepAlive = [...this.GLOBAL.shouldKeepAlive];
  },
  watch: {
    $route(to, from) {
      let ooo = false;
      ooo && console.log(
        "to-meta-index: " +
          to.meta.index +
          "; from-meta-index: " +
          from.meta.index
      );
      if (to.meta.index > from.meta.index) {
        ooo &&  console.log("left");
        this.transitionName = "slide-left";
      } else {
        ooo && console.log("right");
        this.transitionName = "slide-right";
      }
    }
  }

};
</script>

<style>
#app {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}
body,
div,
ol,
ul,
li,
dl,
dt,
dd,
h1,
h2,
h3,
h4,
h5,
h6,
p,
form,
fieldset,
legend,
input {
  margin: 0;
  padding: 0;
  font-size: 3.46vw;
}
h1,
h2,
h3,
h4,
h5,
h6 {
  font-size: 100%;
  font-weight: normal;
}
ol,
ul {
  list-style: none;
}
img,
fieldset {
  border: 0 none;
  display: block;
}
input {
  text-align: center;
}
textarea {
  font-family: inherit;
}
/* html { */
/* margin-bottom: 55px; */
/* overflow: auto; */
/* } */
li {
  list-style: none;
  /* height: 10vw; */
  /* margin-top: 2vw; */
}
.mg0 {
  margin: 0;
}
/* body { */
/* background: white; */
/* background-color: #e7e7e7; */
/* margin-bottom: 15px; */
/* } */
/* .mint-header { */
/* background-color: #57d6dd !important; */
/* } */
.slide-right-enter-active,
.slide-right-leave-active {
  will-change: transform;
  transition: all 500ms;
  position: absolute;
  /* width: 100%; */
  width: 100vw;
  height: 100vh;
  /* opacity: 1; */
}
.slide-right-enter {
  opacity: 0;
  transform: translate(-100%, 0);
}
.slide-right-leave-active {
  opacity: 0;
  transform: translate(100%, 0);
}

.slide-left-enter-active,
.slide-left-leave-active {
  will-change: transform;
  transition: all 500ms;
  position: absolute;
  width: 100vw;
  height: 100vh;
}
.slide-left-enter {
  opacity: 0;
  transform: translate(100%, 0);
}
.slide-left-leave-active {
  opacity: 0;
  transform: translate(-100%, 0);
}

.pageContent {
  padding: 0 4vw;
}
a {
  text-decoration: none;
  color: #000;
}
.mint-cell,
.mint-cell-wrapper {
  /* background: none!important; */
  background-image: none !important;
}
.loginPage input {
  text-align: left;
}

.mint-cell-wrapper {
  background-color: #fff;
}
.transPsd1 .mint-field .mint-cell-value,
.transPsd2 .mint-field .mint-cell-value,
.transPsd .mint-field .mint-cell-value {
  border: 0.3vw solid #b9b9b9;
  height: 10vw;
  line-height: 10vw;
}
.bgc-fff {
  background-color: #fff;
}
.bgc-e7e7e7 {
  background-color: #e7e7e7;
}
.mint-field .mint-cell-title {
  text-align: left;
}
input[disabled] {
  background-color: #fff;
}
.interval {
  height: 3vw;
  background-color: #e7e7e7;
}
.password input {
  text-align: left;
  padding: 0 10px;
  color: #b3b3b3;
}
.password .mint-field .mint-cell-value {
  border: 0.3vw solid #b3b3b3;
  height: 10vw;
  line-height: 10vw;
}
.mint-switch-input:checked + .mint-switch-core {
  border-color: #57d6dd;
  background-color: #57d6dd;
}
.mint-switch-core::before {
  background-color: #c3c3c3;
}
.mt10 {
  margin: 0;
}
.mt30 {
  margin-top: 30px;
}
.mt20 {
  margin-top: 3vw;
}

.mySystem .mint-cell-text {
  margin-left: 10px;
}
.bank input {
  text-align: left;
  font-size: 3vw;
  height: 10vw;
  line-height: 10vw;
}
.bank .mint-cell-wrapper {
  border-top: 0.5vw solid #e7e7e7;
}
.radio #radioText:focus {
  outline: none;
}
/* .findBox */
.findBox .mint-field .mint-cell-value {
  border: 1px solid #e7e7e7;
}
.findBox .mint-field-core {
  line-height: 30px;
}
.findBox .mint-radiolist-label {
  padding: 0;
  line-height: 35px;
}
.loginPage .mint-cell {
  border-radius: 5px;
  margin-top: 20px;
}
#registPage input {
  text-align: left;
}
#registPage .mint-cell {
  border-radius: 5px;
  margin-top: 20px;
}
.clearfix::after {
  content: "";
  height: 0;
  line-height: 0;
  display: block;
  visibility: hidden;
  clear: both;
}
.clearfix {
  zoom: 1;
}

/* 一些全局样式 */
.hidden {
  display: none;
}
.iconfont {
  font-size: 4.26vw;
}
.mint-toast.is-placemiddle {
  letter-spacing: 0.02em;
  opacity: 0.9;
  box-shadow: 0 0 6px rgba(0, 0, 0, 0.4);
}
.mint-spinner-fading-circle {
  width: 7.5vw !important;
  height: 7.5vw !important;
}
div,
label {
  box-sizing: border-box;
}
.flex {
  display: flex;
  align-items: center;
}
.flow-col {
  flex-flow: column;
}
.justify-ct {
  justify-content: center;
}
.justify-sa {
  justify-content: space-around;
}
.justify-sb {
  justify-content: space-between;
}
.justify-se {
  justify-content: space-evenly;
}
.fl {
  float: left;
}

.font-theme {
  color: #e62b00;
}
.fontc-3 {
  color: #333;
}
.fontc-6 {
  color: #666;
}
.fontc-9 {
  color: #999;
}
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
  background: #f7f9fa;
}
.hoverclass-btn {
  box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
}
.hoverclass-reward {
  background: rgba(0, 0, 0, 0.2);
}

.contanier {
  padding-top: 10vw;
  color: #333;
  font-size: 3.46vw;
  font-family: pingfang sc regular;
}
.userSelect {
  background: #cccccc;
}

.com-bd {
  border: 1px solid #a3a3a3;
}
/* 弹窗样式 */
.modal-mask {
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
}
.modal {
  background-color: #fff;
  width: 84%;
  border-radius: 6px;
  font-size: 4.26vw;
  text-align: left;
  user-select: none;
  backface-visibility: hidden;
  transition: 0.5s;
  transform: translate3d(0, 0, 0);
  overflow: hidden;
}
.modal-title {
  border-bottom: 1px solid #e7e7e7;
  line-height: 50px;
  text-align: center;
}
.modal-body {
  padding: 25px;
}

/* .mint-header.is-fixed{
  z-index: 20;
} */
</style>
