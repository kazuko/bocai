<script>
console.log("GLOBAL_VUE");
// socket链接成功返回的唯一标识符
const connectionId = "";
//用户登录状态
const loginStatus = false;
// 标志是否为游戏socket
const gameOrNot = false;
// 保存用户信息
const userInfo = {};
// 保存系统广播消息
const sysNews = {};
// 保存app信息
const appInfo = {
  logo_src: "", //appLOGO
  app_name: "", //app名称
  error_str: "" //飞手机端登陆提示信息
};
// logo网址前缀
const logoHost = "http://localhost/bcweb/public/";
// const logoHost = "http://bc.gd-dent.com/bcweb/public/";
// 图片网址前缀
// const Host = "http://bc.gd-dent.com";
const Host = "http://localhost";
// websocket服务器地址
// const socketHost = "ws://39.108.109.157:8080";
// const sanGongHost = "ws://39.108.109.157:2525";
const socketHost = "ws://192.168.0.134:8080";
const sanGongHost = "ws://192.168.0.134:2525";
// websocket句柄
const socketHand = "";
// 游戏socket句柄
const gameSocketHand = "";
// 
const socketStatus = false;
// 消息列表
const msgList = [];
// 未读消息总条数
const msgLength = 0;
// 好友列表
const friendLists = {
  friends: {},//好友
  strange: {},//陌生人
  system: {}//系统消息
};
// 当前聊天的好友信息
const friendInfo = {};
// 记录每个好友的未读消息条数
const friendMsgNum = [];
// 三公牌桌信息
const sanGongInfo = {};
// 将对象或者数组转换为json格式的字符串
const ObjToString = function(data){
  return JSON.stringify(data);
}

export default {
  userInfo, 
  sysNews, 
  appInfo,
  logoHost,
  Host,
  socketHand,
  socketHost,
  msgList,
  msgLength,
  friendLists,
  friendMsgNum,
  friendInfo,
  sanGongInfo,
  sanGongHost,
  connectionId,
  ObjToString,
  gameOrNot,
  gameSocketHand,
};
</script>
