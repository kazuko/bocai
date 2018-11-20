// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from "vue";
import App from "./App";
import router from "./router";
import "mint-ui/lib/style.css";
// import './mockjs/mock'
//"animate": "^1.0.0",
// "animate.css": "^3.7.0",
import Mint from "mint-ui";
Vue.use(Mint);
// import {Button, Header, MessageBox, Field, Cell, Switch, Badge, } from 'mint-ui'
// // Vue.use(MessageBox);
// Vue.prototype.$messagebox=MessageBox;
// Vue.component(Button.name, Button);
// Vue.component(Header.name, Header);
// // Vue.component(MessageBox.name, MessageBox);
// Vue.component(Field.name, Field);
// Vue.component(Cell.name, Cell);
// Vue.component(Switch.name, Switch);
// Vue.component(Badge.name, Badge);

import Axios from "axios";
Vue.prototype.$axios = Axios;

import animated from "animate.css";
Vue.use(animated);

import base from "./js/base.js";
Vue.use(base);

//<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
// import yuyinSdk from "http://res.wx.qq.com/open/js/jweixin-1.0.0.js";
// Vue.use(yuyinSdk);

import sanGong from "./js/sanGong.js";
Vue.use(sanGong);

import game from "./components/game";
Vue.prototype.Game = game;

import global_ from "./components/global";
Vue.prototype.GLOBAL = global_;

import yuyintongxun from "./components/yuyintongxun";
Vue.prototype.VoiceJSDK = yuyintongxun;

// 注册常用过滤器
import * as filters from "./filters/filters";
Object.keys(filters).forEach(key => {
  Vue.filter(key, filters[key]);
});

// import vuex from 'vuex'
// Vue.use(vuex);
Vue.config.devtools = true;
// Vue.config.productionTip = false
/* eslint-disable no-new */
new Vue({
  el: "#app",
  router,
  components: { App },
  template: "<App/>",
  created: function () {
    if (!this.isMobile()) {
      if (!this.GLOBAL.appInfo.error_str) {
        this.GLOBAL.appInfo.error_str = "对不起，请通过手机访问！";
      }
      this.$messagebox.alert(this.GLOBAL.appInfo.error_str);
      this.$router.push("/error");
    }

    var data = localStorage.getItem('bc_appInfo');
    // 判断本地缓存是否存在
    if (data != null && data != undefined) {
      // 将缓存数据赋值到全局属性中
      this.setGlobalAttribute(this.GLOBAL.appInfo, JSON.parse(data));
    }
    if (this.GLOBAL.appInfo) {
      let link = document.createElement("link");
      link.rel = "shortcut icon";
      link.href = this.GLOBAL.logoHost + this.GLOBAL.appInfo.logo_src;
      document.head.appendChild(link);
    }

    data = localStorage.getItem("bc_userInfo");
    if (data != null && data != undefined) {
      // 将缓存用户的信息保存到全局变量中
      this.setGlobalAttribute(this.GLOBAL.userInfo, JSON.parse(data));
    }
    data = localStorage.getItem("bc_friendInfo");
    if (data != null && data != undefined) {
      // 将缓存的正在聊天的好友信息赋值到全局变量中；防止刷新聊天页面丢失好友信息
      this.setGlobalAttribute(this.GLOBAL.friendInfo, JSON.parse(data));
    }
    this.checkUpdate();

    //判断参数的类型 this.$check( [1,2,3] , 'array' ) === true
    Vue.prototype.$check = function(needCheck, type) {
      type =
        type.substring(0, 1).toUpperCase() + type.substring(1).toLowerCase();

      return Object.prototype.toString.call(needCheck) === `[object ${type}]`;
    };

    //设置本地缓存
    Vue.prototype.$setLocalCache = function (key, value) {
      try {
        window.localStorage[key] = JSON.stringify(value);
        console.log(
          "%c设置 " + key + " 缓存成功:\n",
          "color:red;font-size:13px;"
        );
        console.dir(value);
      } catch (e) {
        console.log("%c设置缓存失败:", "color:red;font-size:13px;", e);
      }
    };

    //读取缓存
    Vue.prototype.$getLocalCache = function (key) {
      try {
        let data =
          window.localStorage[key] && JSON.parse(window.localStorage[key]);
        if (data !== undefined) {
          console.log(
            "%c读取 " + key + " 缓存成功:\n",
            "color:red;font-size:13px;"
          );
          console.dir(data);
          return data;
        } else {
          console.log(
            "%c 未读取到 " + key + " 缓存信息，可能没有设置",
            "color:red;font-size:13px;"
          );
          return false;
        }
      } catch (e) {
        console.log("%c 读取缓存失败:\n", "color:red;font-size:13px;", e);
      }
    };

    //移除缓存
    Vue.prototype.$removeLocalCache = function (key) {
      try {
        window.localStorage.removeItem(key);
        console.log(
          "%c 移除 " + key + " 缓存成功:",
          "color:red;font-size:13px;"
        );
      } catch (e) {
        console.log(
          "%c 移除 " + key + " 缓存失败:",
          "color:red;font-size:13px;",
          e
        );
      }
    };

    //清空缓存
    Vue.prototype.$clearLocalCache = function () {
      try {
        console.log("%c 清空缓存成功", "color:red;font-size:13px;");
        window.localStorage.clear();
      } catch (e) {
        console.log("%c 清空缓存失败:", "color:red;font-size:13px;", e);
      }
    };

    //走势图canvas连线
    Vue.prototype.$drawline = function ({ selector, ballClass = ".openball" }) {
      console.log("表格连线");
      let dom = document.querySelector(selector);
      if (dom === null) return;

      init();
      let param = getPos();
      stroke(param);

      function init() {
        let canvas = dom.parentNode.querySelector("canvas");

        if (!canvas) {
          let canvas = `<canvas width="${dom.offsetWidth}" height="${
            dom.offsetHeight
            }" style="position: absolute;left: 0;top: 0; z-index: 1;"></canvas>`;

          dom.insertAdjacentHTML("beforebegin", canvas);
        }
      }

      //获取所有开奖的球
      function getPos() {
        let balls = dom.querySelectorAll(ballClass);
        let ballWidth = balls[0].offsetWidth / 2;
        let ballHeight = balls[0].offsetHeight / 2;
        //获取开奖球相对于  父元素(position: relative;)  的坐标值
        let points = [];
        balls.forEach(element => {
          points.push({
            left: element.parentNode.offsetLeft,
            top: element.parentNode.offsetTop
          });
        });

        return { points, ballWidth, ballHeight };
      }

      //开始画线
      function stroke(param) {
        let { points, ballWidth, ballHeight } = param;
        let canvas = dom.parentNode.querySelector("canvas");
        let ctx = canvas.getContext("2d");
        canvas.height = canvas.height;
        ctx.lineWidth = 1; //线条的宽度
        ctx.strokeStyle = "#e64600"; //线条的颜色
        ctx.beginPath();
        ctx.moveTo(points[0].left + ballWidth, points[0].top + ballHeight); //起始位置
        for (let i = 1; i < points.length; i++) {
          //画点
          ctx.lineTo(points[i].left + ballWidth, points[i].top + ballHeight);
        }
        ctx.stroke();
      }
    };
  },
  watch: {}
});

// classList 安卓3.0开始支持 ; getAttribute chrome 1支持
window.addEventListener("click", function (e) {
  // debugger;
  let dom = e.target;
  let hoverclass = dom.getAttribute && dom.getAttribute("dentHoverclass");
  //可能点击的是子元素 往上找一层
  if (!hoverclass) {
    dom = dom.parentNode;
    dom.getAttribute && (hoverclass = dom.getAttribute("dentHoverclass"));
  }
  if (hoverclass) {
    clearTimeout(dom.timer);
    dom.classList.add(hoverclass);
    dom.timer = setTimeout(() => {
      dom.classList.remove(hoverclass);
      clearTimeout(dom.timer);
    }, 500);
  }
});
//时间格式化 (new Date()).Format("yyyy-MM-dd hh:mm:ss")
// https://www.cnblogs.com/sexintercourse/p/6490921.html author: meizz
Date.prototype.Format = function (fmt) {
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

console.log("MAIN");
