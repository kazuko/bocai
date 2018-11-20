<template>
  <div id="app">
    <div class="main">
      <transition :name="transitionName">
        <router-view />
      </transition>
    </div>
    <mainnav v-show="tabbarShow"
             v-if="tabbarWhic"></mainnav>
    <usernav v-show="tabbarShow"
             v-if="!tabbarWhic"></usernav>
  </div>
</template>

<script>
console.log("APP_VUE");
import usernav from "@/components/common/usernav";
import mainnav from "@/components/common/mainnav";
import root from "@/config/root.js";

export default {
  name: "App",
  data() {
    return {
      transitionName: "",
      tabbarShow: true,
      tabbarWhic: true
    };
  },
  components: {
    usernav,
    mainnav
  },
  watch: {
    $route(to, from) {
      if (to.meta.index > from.meta.index) {
        this.transitionName = "slide-left";
      } else {
        this.transitionName = "slide-right";
      }
    }
  },
  methods: {},
  beforeCreate: function(){
    console.log("app->beforeCreate...");
  },
  created: function(){
    console.log("app->created...");
  },
  beforeMount: function(){
    console.log('app->beforeMount...');
  },
  mounted: function(){
    console.log("app->mouted...");
  },
  beforeUpdate: function() {
    console.log("app->beforeUpdate...");
  },
  updated: function(){
    console.log("app->updated...");
  },
  beforeDestroy: function(){
    console.log("app->beforeDestroy...");
  },
  destroyed: function() {
    console.log("app->destroyed...");
    // this.$axios
    //   .post(root.logout, {
    //     userid: this.GLOBAL.userInfo.id
    //   })
    //   .then(Response => {
    //     console.log(Response);
    //   });
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
html {
  overflow: auto;
}
li {
  list-style: none;
  height: 10vw;
  margin-top: 2vw;
}
.mg0 {
  margin: 0;
}
body {
  background-color: #e7e7e7;
  /* margin-bottom: 15px; */
}
input:focus {
  outline: none;
}
.mint-header {
  background-color: #57d6dd !important;
}
.slide-right-enter-active,
.slide-left-enter-active,
.slide-right-leave-active,
.slide-left-enter-active {
  will-change: transform;
  transition: all 500ms;
  position: absolute;
  width: 100%;
}
.slide-right-enter,
.slide-left-leave-active {
  /* opacity: 0; */
  transform: translate(-100%, 0);
}
.slide-right-leave-active,
.slide-left-enter {
  /* opacity: 0; */
  transform: translate(100%, 0);
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
  border: 1px solid #b9b9b9;
  height: 30px;
  line-height: 30px;
}
.bgc-fff {
  background-color: #fff;
}
.mint-field .mint-cell-title {
  text-align: left;
}
input[disabled] {
  background-color: #fff;
}
.interval {
  height: 10px;
  background-color: #e7e7e7;
}
.password input {
  text-align: left;
  padding: 0 10px;
  color: #b3b3b3;
}
.password .mint-field .mint-cell-value {
  border: 1px solid #b3b3b3;
  height: 40px;
  line-height: 40px;
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

.mintui-back {
  color: #fff;
}
/* 一些全局样式 */
div,
label {
  box-sizing: border-box;
}
.hidden {
  display: none;
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
  font-size: 13px;
  font-family: pingfang sc regular;
}
.contanier a {
  color: #333;
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
  font-size: 16px;
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
</style>
