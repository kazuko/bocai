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
// // 阿里云服务器
// const Host = "https://bc.gd-dent.com";  //图片网址前缀
// const logoHost = "https://bc.gd-dent.com/bcweb/public/";  //logo网址前缀
// const socketHost = "wss://bc.gd-dent.com:8686";   //聊天websocket服务器
// const gameSocketHost = "wss://bc.gd-dent.com:2525";  //游戏websocket服务器
// const uploadHref = Host+'/lib/uploadDriver.php'; //发送语音和发送图片的上传路径

// // const socketHost = "wss://39.108.109.157:8686";   //聊天websocket服务器
// const gameSocketHost = "wss://39.108.109.157:2525";  //游戏websocket服务器

// const Host = "http://bc.gd-dent.com";  //图片网址前缀
// const logoHost = "http://bc.gd-dent.com/bcweb/public/";  //logo网址前缀
// const socketHost = "ws://39.108.109.157:8686";   //聊天websocket服务器
// const gameSocketHost = "ws://39.108.109.157:2525";  //游戏websocket服务器
// 本地服务器
const Host = "http://localhost"; //图片网址前缀
const logoHost = "http://localhost/bcweb/public/"; //logo网址前缀
const socketHost = "ws://192.168.2.100:8080";   //聊天websocket服务器
const uploadHref = Host+'/bcweb/lib/uploadDriver.php'; //发送语音和发送图片的上传路径
const gameSocketHost = "ws://192.168.2.100:2525";  //游戏websocket服务器
// websocket句柄
const socketHand = "";
// 游戏socket句柄
const gameSocketHand = "";
//
// const socketStatus = false;
// 消息列表
const msgList = [];
// 未读消息总条数
const msgLength = 0;
// 好友列表
const friendLists = {
  friends: {}, //好友
  strange: {}, //陌生人
  system: {} //系统消息
};
// 当前聊天的好友信息
const friendInfo = {};
// 记录每个好友的未读消息条数
const friendMsgNum = [];
// 三公牌桌信息
const sanGongInfo = {};

//应该缓存的游戏组件列表
//用法请看 mixins 的 toggleShouldKeepAlive
//请不要修改的缓存数组
const shouldKeepAlive = ["6hecai", "jsks", "bjpk10", "11xuan5"];

//6合彩
const sixImg = "static/gameIcon/liuhecai.png";
//江苏快三
const jsksImg = "static/gameIcon/jsk3.png";
//北京pk10
const bjpk10Img = "static/gameIcon/pk10.png";
//广东11选5
const gdImg = "static/gameIcon/11x5.png";

let bjpk10, jsks, six, gd;
let temp = {
  nextIssue: null,
  countdown: 0,
  interval: 60,
  order: {},
  orderList: []
};
six = { ...temp };
jsks = { ...temp };
bjpk10 = { ...temp };
gd = { ...temp };

// 将对象或者数组转换为json格式的字符串
const ObjToString = function(data) {
  return JSON.stringify(data);
};
const getObjLenght = function(data) {
  return Object.keys(data).length;
};
// 广播消息
const radioTextList = [];
// 聊天室消息队列
const chatRoomMsgList = [];
const chatRoomPage = 0;
const systemNoticePage = 0;

// 需要更新的容器
const updateFile = {
  faces: [] //表情包
};
export default {
  updateFile,
  systemNoticePage,
  getObjLenght,
  radioTextList,
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
  gameSocketHost,
  connectionId,
  shouldKeepAlive,
  six,
  sixImg,
  jsks,
  jsksImg,
  bjpk10,
  bjpk10Img,
  gd,
  gdImg,
  ObjToString,
  gameOrNot,
  gameSocketHand,
  uploadHref,
  chatRoomMsgList,
  chatRoomPage
};
</script>
