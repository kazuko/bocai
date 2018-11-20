console.log("ROOTER_INDEX.JS");
// 注册模块
// 引入Vue
import Vue from "vue";
import Router from "vue-router";
// 大厅
// import Hall from '@/components/hall'
const Hall = () => import("@/components/hall");
// 用户中心
// import User from '@/components/user/index'
const User = () => import("@/components/user/index");
// 好友
// import Friend from '@/components/user/pageFriend/friend'
const Friend = () => import("@/components/user/pageFriend/friend");
// 添加好友
// import AddFriend from '@/components/user/pageFriend/addFriend'
const AddFriend = () => import("@/components/user/pageFriend/addFriend");
// 聊天室
// import Chat from '@/components/common/chat'
const Chat = () => import("@/components/common/chat");
// 系统通知
const systemNotice = () => import("@/components/common/systemNotice");
// 查找朋友
// import SearchFriend from '@/components/user/pageFriend/searchFriend'
const SearchFriend = () => import("@/components/user/pageFriend/searchFriend");
// 银行社区
// import Bank from '@/components/user/bank/bank'
const Bank = () => import("@/components/user/bank/bank");
// import recharge from '@/components/user/bank/recharge'
const recharge = () => import("@/components/user/bank/recharge");
//
// import MySystem from '@/components/user/mySystem/mySystem'
const MySystem = () => import("@/components/user/mySystem/mySystem");
//
// import Personal from '@/components/user/mySystem/personal'
const Personal = () => import("@/components/user/mySystem/personal");
//
// import TransPsd from '@/components/user/mySystem/transPsd'
const TransPsd = () => import("@/components/user/mySystem/transPsd");
// 用户勋章
// import Medal from '@/components/user/medal/medal'
const Medal = () => import("@/components/user/medal/medal");
// 我的红包
// import Redbag from '@/components/user/redbag/redbag'
const Redbag = () => import("@/components/user/redbag/redbag");
// 红包详情
// import redbagdetail from '@/components/user/redbag/redbagdetail'
const redbagdetail = () => import("@/components/user/redbag/redbagdetail");
// 发红包
// import sendredbag from '@/components/user/redbag/sendredbag'
const sendredbag = () => import("@/components/user/redbag/sendredbag");
// 转账
// import transacount from '@/components/user/redbag/transacount'
const transacount = () => import("@/components/user/redbag/transacount");
// import transerfersuccess from '@/components/user/redbag/transerfersuccess'
const transerfersuccess = () =>
  import("@/components/user/redbag/transerfersuccess");
// import transferdetail from '@/components/user/redbag/transferdetail'
const transferdetail = () => import("@/components/user/redbag/transferdetail");
// 社区广播
// import Radio from '@/components/user/radio/radio'
const Radio = () => import("@/components/user/radio/radio");
// 登陆
// import Login from '@/components/login/login'
const Login = () => import("@/components/login/login");
//
// import PutForward from '@/components/user/cash/putForward'
const PutForward = () => import("@/components/user/cash/putForward");
//
// import BankCard from '@/components/user/cash/bankCard'
const BankCard = () => import("@/components/user/cash/bankCard");
// 绑定银行卡
// import bindBankCard from '@/components/user/cash/bindBankCard'
const bindBankCard = () => import("@/components/user/cash/bindBankCard");
// 提现申请
// import Alipay from '@/components/user/cash/alipay'
const Alipay = () => import("@/components/user/cash/alipay");
// import bindAlipay from '@/components/user/cash/bindAlipay'
const bindAlipay = () => import("@/components/user/cash/bindAlipay");
// import Wechat from '@/components/user/cash/wechat'
const Wechat = () => import("@/components/user/cash/wechat");
// import bindWechat from '@/components/user/cash/bindWechat'
const bindWechat = () => import("@/components/user/cash/bindWechat");
// import Regist from '@/components/login/regist'
const Regist = () => import("@/components/login/regist");
// import Chatroom from '@/components/common/chatroom'
const Chatroom = () => import("@/components/common/chatroom");
// import error from '@/components/common/error'
const error = () => import("@/components/common/error");
//论坛
// import Forum from '@/components/forum/forum'
const Forum = () => import("@/components/forum/forum");
// import theme from '@/components/forum/theme'
const theme = () => import("@/components/forum/theme");
// import post from '@/components/forum/post'
const post = () => import("@/components/forum/post");
// import mytheme from '@/components/forum/mytheme'
const mytheme = () => import("@/components/forum/mytheme");
// import themeDetail from '@/components/forum/themeDetail'
const themeDetail = () => import("@/components/forum/themeDetail");

//三公
// import sgHall from '@/components/game/sangong/sgHall'
const sgHall = () => import("@/components/game/sangong/sgHall");
// import sangong from '@/components/game/sangong/sangong'
const sangong = () => import("@/components/game/sangong/sangong");

//六合彩
const _6hecai = () => import("@/components/game/6hecai/6hecai");
const _6hecaicharts = () => import("@/components/game/6hecai/6hecai-charts");
const _6hecaiopen = () => import("@/components/game/6hecai/6hecai-open");
const _6hecaiwaiForPay = () =>
  import("@/components/game/6hecai/6hecai-waiForPay");
const _6hecaiorder = () => import("@/components/game/6hecai/6hecai-order");

//广东11选5
const _11xuan5 = () => import("@/components/game/11xuan5/11xuan5");
const _11xuan5charts = () => import("@/components/game/11xuan5/11xuan5-charts");

//江苏快三
const jsks = () => import("@/components/game/jsks/jsks");
const jskscharts = () => import("@/components/game/jsks/jsks-charts");

//北京PK拾
const bjpk10 = () => import("@/components/game/bjpk10/bjpk10");
const bjpk10charts = () => import("@/components/game/bjpk10/bjpk10-charts");

//6合彩江苏快三 订单生成页
const dentGameWaiForPay = () => import("@/components/common/dentGameWaiForPay");
//6合彩江苏快三 购彩记录页
const dentGameOrder = () => import("@/components/common/dentGameOrder");
//6合彩江苏快三 近期开奖
const dentGameOpenLately = () =>
  import("@/components/common/dentGameOpenLately");

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
      path: "/forum",
      name: "froum",
      meta: {
        index: 3
      },
      component: Forum
    },
    {
      path: "/user",
      name: "User",
      meta: {
        index: 4
      },
      component: User
    },
    {
      path: "/friend",
      name: "Friend",
      meta: {
        index: 5
      },
      component: Friend
    },
    {
      path: "/bank",
      name: "Bank",
      meta: {
        index: 5
      },
      component: Bank
    },
    {
      path: "/mySystem",
      name: "MySystem",
      meta: {
        index: 5
      },
      component: MySystem
    },
    {
      path: "/redbag",
      name: "Redbag",
      meta: {
        index: 5
      },
      component: Redbag
    },
    {
      path: "/mytheme",
      name: "Mytheme",
      meta: {
        index: 5
      },
      component: mytheme
    },
    {
      path: "/radio",
      name: "Radio",
      meta: {
        index: 5
      },
      component: Radio
    },
    {
      path: "/recharge",
      name: "Recharge",
      meta: {
        index: 5
      },
      component: recharge
    },
    {
      path: "/putForward",
      name: "PutForward",
      meta: {
        index: 5
      },
      component: PutForward
    },
    {
      path: "/medal",
      name: "Medal",
      meta: {
        index: 5
      },
      component: Medal
    },
    {
      path: "/addFriend",
      name: "AddFriend",
      meta: {
        index: 6
      },
      component: AddFriend
    },
    {
      path: "/chat",
      name: "Chat",
      meta: {
        index: 7
      },
      component: Chat
    },
    {
      path: "/systemNotice",
      name: "SystemNotice",
      meta: {
        index: 7
      },
      component: systemNotice
    },
    {
      path: "/sendredbag",
      name: "Sendredbag",
      meta: {
        index: 8
      },
      component: sendredbag
    },
    {
      path: "/transacount",
      name: "Transacount",
      meta: {
        index: 8
      },
      component: transacount
    },
    {
      path: "/login",
      name: "Login",
      meta: {
        index: 5
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
      path: "/redbagdetail",
      name: "redbagdetail",
      meta: {
        index: 8
      },
      component: redbagdetail
    },
    {
      path: "/transerfersuccess",
      name: "transerfersuccess",
      meta: {
        index: 6
      },
      component: transerfersuccess
    },
    {
      path: "/transferdetail",
      name: "transferdetail",
      meta: {
        index: 6
      },
      component: transferdetail
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
      path: "/personal",
      name: "Personal",
      meta: {
        index: 6
      },
      component: Personal
    },
    {
      path: "/transPsd",
      name: "TransPsd",
      meta: {
        index: 6
      },
      component: TransPsd
    },
    {
      path: "/bankCard",
      name: "BankCard",
      meta: {
        index: 6
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
        index: 6
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
        index: 6
      },
      component: bindWechat
    },
    {
      path: "/theme",
      name: "theme",
      meta: {
        index: 4
      },
      component: theme
    },
    {
      path: "/themeDetail",
      name: "themeDetail",
      meta: {
        index: 6
      },
      component: themeDetail
    },
    {
      path: "/post",
      name: "post",
      meta: {
        index: 5
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
    // 游戏公共组件 开始
    {
      path: "/dentGameWaiForPay/",
      name: "dentGameWaiForPay",
      meta: {
        index: 3
      },
      component: dentGameWaiForPay
    },
    {
      path: "/dentGameOrder/",
      name: "dentGameOrder",
      meta: {
        index: 3
      },
      component: dentGameOrder
    },
    {
      path: "/dentGameOpenLately/",
      name: "dentGameOpenLately",
      meta: {
        index: 3
      },
      component: dentGameOpenLately
    },
    // 游戏公共组件 结束
    {
      path: "/6hecai/",
      name: "6hecai",
      meta: {
        index: 1,
        keepAlive: true
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
    },
    {
      path: "/11xuan5",
      name: "11xuan5",
      meta: {
        index: 1,
        keepAlive: true
      },
      component: _11xuan5
    },
    {
      path: "/11xuan5/11xuan5-charts",
      name: "11xuan5charts",
      meta: {
        index: 2
      },
      component: _11xuan5charts
    },
    {
      path: "/bjpk10/",
      name: "bjpk10",
      meta: {
        index: 1,
        keepAlive: true
      },
      component: bjpk10
    },
    {
      path: "/bjpk10/bjpk10-charts",
      name: "bjpk10charts",
      meta: {
        index: 2
      },
      component: bjpk10charts
    },
    {
      path: "/jsks/",
      name: "jsks",
      meta: {
        index: 1,
        keepAlive: true
      },
      component: jsks
    },
    {
      path: "/jsks/jsks-charts",
      name: "jskscharts",
      meta: {
        index: 2
      },
      component: jskscharts
    }
  ]
});
