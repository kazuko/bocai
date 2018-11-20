console.log("ROOTER_INDEX.JS");
// 注册模块
// 引入Vue
import Vue from "vue";
import Router from "vue-router";
// 大厅
import Hall from "@/components/hall";
// 用户中心
import User from "@/components/user/index";
// 好友
import Friend from "@/components/user/pageFriend/friend";
// 添加好友
import AddFriend from "@/components/user/pageFriend/addFriend";
// 聊天室
import Chat from "@/components/common/chat";
// 查找朋友
import SearchFriend from "@/components/user/pageFriend/searchFriend";
// 银行社区
import Bank from "@/components/user/bank/bank";
import recharge from "@/components/user/bank/recharge";
//
import MySystem from "@/components/user/mySystem/mySystem";
//
import Personal from "@/components/user/mySystem/personal";
//
import TransPsd from "@/components/user/mySystem/transPsd";
// 用户勋章
import Medal from "@/components/user/medal/medal";
// 我的红包
import Redbag from "@/components/user/redbag/redbag";
// 红包详情
import redbagdetail from "@/components/user/redbag/redbagdetail";
// 发红包
import sendredbag from "@/components/user/redbag/sendredbag";
// 转账
import transacount from "@/components/user/redbag/transacount";
// 社区广播
import Radio from "@/components/user/radio/radio";
// 登陆
import Login from "@/components/login/login";
//
import PutForward from "@/components/user/cash/putForward";
//
import BankCard from "@/components/user/cash/bankCard";
// 绑定银行卡
import bindBankCard from "@/components/user/cash/bindBankCard";
// 提现申请
import Alipay from "@/components/user/cash/alipay";
import bindAlipay from "@/components/user/cash/bindAlipay";
import Wechat from "@/components/user/cash/wechat";
import bindWechat from "@/components/user/cash/bindWechat";
import Regist from "@/components/login/regist";
import Chatroom from "@/components/common/chatroom";
import error from "@/components/common/error";
//论坛
import Forum from "@/components/forum/forum";
import theme from "@/components/forum/theme";
import post from "@/components/forum/post";
import mytheme from "@/components/forum/mytheme";

//三公
import sgHall from "@/components/game/sangong/sgHall";
import sangong from "@/components/game/sangong/sangong";

//广东11选5
import _11xuan5 from "@/components/game/11xuan5/11xuan5";
import _11xuan5openHistory from "@/components/game/11xuan5/openHistory";
import _11xuan5currentBets from "@/components/game/11xuan5/currentBets";
import _11xuan5tendency from "@/components/game/11xuan5/tendency";

//六合彩
import _6hecai from "@/components/game/6hecai/6hecai";
import _6hecaicharts from "@/components/game/6hecai/6hecai-charts";
import _6hecaiopen from "@/components/game/6hecai/6hecai-open";
import _6hecaiwaiForPay from "@/components/game/6hecai/6hecai-waiForPay";
import _6hecaiorder from "@/components/game/6hecai/6hecai-order";

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: "/",
      name: "Hall",
      meta: {
        index: 0
      },
      component: Hall
    },
    {
      path: "/chatroom",
      name: "Chatroom",
      meta: {
        index: 1
      },
      component: Chatroom
    },
    {
      path: "/login",
      name: "Login",
      meta: {
        index: 1
      },
      component: Login
    },
    {
      path: "/error",
      name: "error",
      meta: {
        index: 2
      },
      component: error
    },
    {
      path: "/regist",
      name: "Regist",
      meta: {
        index: 2
      },
      component: Regist
    },
    {
      path: "/user",
      name: "User",
      meta: {
        index: 2
      },
      component: User
    },
    {
      path: "/mytheme",
      name: "Mytheme",
      meta: {
        index: 3
      },
      component: mytheme
    },
    {
      path: "/recharge",
      name: "Recharge",
      meta: {
        index: 3
      },
      component: recharge
    },
    {
      path: "/friend",
      name: "Friend",
      meta: {
        index: 3
      },
      component: Friend
    },
    {
      path: "/addFriend",
      name: "AddFriend",
      meta: {
        index: 4
      },
      component: AddFriend
    },
    {
      path: "/chat",
      name: "Chat",
      meta: {
        index: 4
      },
      component: Chat
    },
    {
      path: "/redbagdetail",
      name: "redbagdetail",
      meta: {
        index: 5
      },
      component: redbagdetail
    },
    {
      path: "/sendredbag",
      name: "Sendredbag",
      meta: {
        index: 5
      },
      component: sendredbag
    },
    {
      path: "/transacount",
      name: "Transacount",
      meta: {
        index: 5
      },
      component: transacount
    },
    {
      path: "/searchFriend",
      name: "SearchFriend",
      meta: {
        index: 5
      },
      component: SearchFriend
    },
    {
      path: "/bank",
      name: "Bank",
      meta: {
        index: 3
      },
      component: Bank
    },
    {
      path: "/mySystem",
      name: "MySystem",
      meta: {
        index: 3
      },
      component: MySystem
    },
    {
      path: "/personal",
      name: "Personal",
      meta: {
        index: 4
      },
      component: Personal
    },
    {
      path: "/transPsd",
      name: "TransPsd",
      meta: {
        index: 4
      },
      component: TransPsd
    },
    {
      path: "/medal",
      name: "Medal",
      meta: {
        index: 3
      },
      component: Medal
    },
    {
      path: "/redbag",
      name: "Redbag",
      meta: {
        index: 3
      },
      component: Redbag
    },
    {
      path: "/radio",
      name: "Radio",
      meta: {
        index: 3
      },
      component: Radio
    },
    {
      path: "/putForward",
      name: "PutForward",
      meta: {
        index: 3
      },
      component: PutForward
    },
    {
      path: "/bankCard",
      name: "BankCard",
      meta: {
        index: 4
      },
      component: BankCard
    },
    {
      path: "/bindBankCard",
      name: "bindBankCard",
      meta: {
        index: 4
      },
      component: bindBankCard
    },
    {
      path: "/alipay",
      name: "Alipay",
      meta: {
        index: 4
      },
      component: Alipay
    },
    {
      path: "/bindAlipay",
      name: "bindAlipay",
      meta: {
        index: 4
      },
      component: bindAlipay
    },
    {
      path: "/wechat",
      name: "Wechat",
      meta: {
        index: 4
      },
      component: Wechat
    },
    {
      path: "/bindWechat",
      name: "bindWechat",
      meta: {
        index: 4
      },
      component: bindWechat
    },
    {
      path: "/forum",
      name: "froum",
      meta: {
        index: 1
      },
      component: Forum
    },
    {
      path: "/theme",
      name: "theme",
      meta: {
        index: 2
      },
      component: theme
    },
    {
      path: "/post",
      name: "post",
      meta: {
        index: 4
      },
      component: post
    },
    {
      path: "/sgHall",
      name: "sgHall",
      meta: {
        index: 1
      },
      component: sgHall
    },
    {
      path: "/sangong",
      name: "sangong",
      meta: {
        index: 2
      },
      component: sangong
    },
    {
      path: "/11xuan5",
      name: "11xuan5",
      meta: {
        index: 2
      },
      component: _11xuan5
    },
    {
      path: "/11xuan5/openHistory",
      name: "openHistory",
      meta: {
        index: 3
      },
      component: _11xuan5openHistory
    },
    {
      path: "/11xuan5/currentBets",
      name: "currentBets",
      meta: {
        index: 3
      },
      component: _11xuan5currentBets
    },
    {
      path: "/11xuan5/tendency",
      name: "tendency",
      meta: {
        index: 3
      },
      component: _11xuan5tendency
    },
    {
      path: "/6hecai/",
      name: "charts",
      meta: {
        index: 2
      },
      component: _6hecai
    },
    {
      path: "/6hecai/6hecai-charts",
      name: "6hecaicharts",
      meta: {
        index: 3
      },
      component: _6hecaicharts
    },
    {
      path: "/6hecai/6hecai-open",
      name: "6hecaiopen",
      meta: {
        index: 3
      },
      component: _6hecaiopen
    },
    {
      path: "/6hecai/6hecai-waiForPay",
      name: "6hecaiwaiForPay",
      meta: {
        index: 3
      },
      component: _6hecaiwaiForPay
    },
    {
      path: "/6hecai/6hecai-order",
      name: "6hecaiorder",
      meta: {
        index: 3
      },
      component: _6hecaiorder
    }
  ]
});
