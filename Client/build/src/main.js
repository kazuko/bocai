// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from "vue";
import App from "./App";
import router from "./router";
import "mint-ui/lib/style.css";
// import './mockjs/mock'

import Mint from "mint-ui";
Vue.use(Mint);

import Axios from "axios";
Vue.prototype.$axios = Axios;

import animated from "animate.css";
Vue.use(animated);

import base from "./js/base.js";
Vue.use(base);

import sanGong from "./js/sanGong.js";
Vue.use(sanGong);

import sangong_ from "./components/sanGong";
Vue.prototype.SANGONG = sangong_;

import global_ from "./components/global";
Vue.prototype.GLOBAL = global_;

// import vuex from 'vuex'
// Vue.use(vuex);

// Vue.config.productionTip = false;
Vue.config.devtools = true;
/* eslint-disable no-new */

/**
 *设置本地缓存
 * key  要保存的键值
 * value  要保存的内容
 * expire 保存期限，不设置时默认为永久
 */
Vue.prototype.setLocalCache = function(key, value, expire) {
  let timeStamp = new Date() / 1000;
  let iFSucess = true;
  let data = {
    expire: expire ? timeStamp + parseInt(expire) : 0,
    value
  };

  try {
    window.localStorage[key] = JSON.stringify(data);
    console.log("设置缓存成功:\n" + JSON.stringify(data));
  } catch (e) {
    iFSucess = "设置缓存失败:" + e;
    console.log(iFSucess);
  }

  return iFSucess;
};

//读取缓存
Vue.prototype.getLocalCache = function(key) {
  var timeStamp = new Date() / 1000;
  let data = "";

  try {
    //判断缓存是否过期 或者 有没有设置缓存期限，没过期或者没有期限 返回数据
    //有期限并且过期了，清空缓存，返回空值
    let data = window.localStorage[key] && JSON.parse(window.localStorage[key]);

    if (data.expire > timeStamp || data.expire == 0) {
      data = data.value;
      console.log("读取缓存成功:\n" + JSON.stringify(data));
      return data;
    } else {
      data = "缓存已过期，请重新设置";
      this.removeLocalCache(key);
      console.log(data);
      return data;
    }
  } catch (e) {
    data = "读取缓存失败:\n" + e;
    console.log(data);
    return data;
  }
};

//移除缓存
Vue.prototype.removeLocalCache = function(key) {
  let iFSucess = true;
  try {
    console.log("移除缓存成功:" + key);
    window.localStorage.removeItem(key);
  } catch (e) {
    iFSucess = "移除缓存失败:" + e;
    console.log(iFSucess);
  }

  return iFSucess;
};

//清空缓存
Vue.prototype.clearLocalCache = function() {
  let iFSucess = true;
  try {
    window.localStorage.clear();
    console.log("清空缓存成功");
  } catch (e) {
    iFSucess = "清空缓存失败:" + e;
    console.log(iFSucess);
  }

  return iFSucess;
};

new Vue({
  el: "#app",
  router,
  components: { App },
  template: "<App/>",
  beforeDestroy: function() {},
  created: function() {
    if (!this.isMobile()) {
      if (!this.GLOBAL.appInfo.error_str) {
        this.GLOBAL.appInfo.error_str = "对不起，请通过手机访问！";
      }
      this.$messagebox.alert(this.GLOBAL.appInfo.error_str);
      this.$router.push("/error");
    }
    // 判断本地缓存是否存在
    if (localStorage.getItem("bc_appInfo") != null) {
      // 将缓存数据赋值到全局属性中
      this.setGlobalAttribute(
        this.GLOBAL.appInfo,
        JSON.parse(localStorage.getItem("bc_appInfo"))
      );
    }
    if (localStorage.getItem("bc_userInfo") != null) {
      // 将缓存用户的信息保存到全局变量中
      this.setGlobalAttribute(
        this.GLOBAL.userInfo,
        JSON.parse(localStorage.getItem("bc_userInfo"))
      );
    }
    if (localStorage.getItem("bc_friendInfo") != null) {
      // 将缓存的正在聊天的好友信息赋值到全局变量中；防止刷新聊天页面丢失好友信息
      this.setGlobalAttribute(
        this.GLOBAL.friendInfo,
        JSON.parse(localStorage.getItem("bc_friendInfo"))
      );
    }
  },
  watch: {}
});
/* 10/07 宁柏龙 */
// 注册 全局自定义指令
/**
 * hoverclass
 * 点击dom时 添加一个class类名，短暂延时后删除类名。
 */
/* 9/29 考虑到每个元素绑定一个click事件浪费性能 改用body代理 */
// Vue.directive("hoverclass", {
//   inserted: function(dom, binding) {
//     const hoverclass = binding.expression;
//     const clickHandel = function() {
//       clearTimeout(dom.timer);
//       // classList 安卓3.0开始支持
//       dom.classList.add(hoverclass);
//       dom.timer = setTimeout(() => {
//         dom.classList.remove(hoverclass);
//         clearTimeout(dom.timer);
//       }, 500);
//     };
//     dom.addEventListener("click", clickHandel);
//   }
// });

// classList 安卓3.0开始支持 ; getAttribute chrome 1支持
window.addEventListener("click", function(e) {
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
//时间格式化
// https://www.cnblogs.com/sexintercourse/p/6490921.html author: meizz
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

console.log("MAIN");
