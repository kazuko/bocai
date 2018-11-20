<template>
  <div style="border: 1px solid green;">
    <!-- <mt-header :title="this.GLOBAL.obj.val.nickname"> -->
    <mt-header id="header" v-bind:title="this.friendInfo.info.nickname" fixed="true" style="height:10vw;">
      <router-link to="/friend" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
      <!-- <router-link to="/addFriend" slot="right">
        <mt-button>
          <i class="iconfont icon-yonghu" style="font-size:30px"></i>
        </mt-button>
      </router-link> -->
    </mt-header>
    <div class="pageContent chat" id="centerbox">
      <div id="chat">
        <div v-for="(item,index) in this.GLOBAL.msgList[this.friendInfo.info.id]" :key="index">
          <!-- 时间 -->
          <p style="text-align:center;font-size:3vw;color:#999;margin-top:10px;" v-if="showchatRecordsDate(index)">{{showchatRecordsDate(index)}}</p>
          <!-- 聊条消息 -->
          <div v-if="item.status" style="display: flex;" :class="item.send_id==self_id?('myList list'):('list')">
            <!-- 我方头像 -->
            <div class="head" v-if="item.send_id==self_id">
              <img v-bind:src="host + selfHead">
            </div>
            <!-- 对方头像 -->
            <div v-else class="head">
              <img v-bind:src="host + head">
            </div>
            <div class="msgText">
              <p v-if="checkRedBag(item.content,true, index)" @click="lookRedbag(checkRedBag(item.content, true, index), index)" style="display:flex;">
                <img :src="redbagsrc[index]" alt="" style="height:15vw;width:auto;">
                <span>
                  <p style="color:red;font-size:4vw;">{{checkRedBag(item.content,false, index)}}</p>
                  <p style="font-size:4vw;">{{redbagtips[index]}}</p>
                </span>
              </p>
              <p v-else v-html="item.content">{{item.content}}</p>
              <div :class="item.send_id==self_id?('myboo'):('boo')"></div>
              <!-- 我方发送失败 -->
              <span class="send_fail_tips" v-if="item.send_id==self_id&&!item.send_status">!</span>
            </div>
          </div>
          <!-- 系统提示 -->
          <div v-else class="addTips">
            <p style="text-align:center;color:#999;font-size:4vw;">{{item.content}}</p>
          </div>
        </div>
      </div>

    </div>
    <section class="user">
      <!-- 发送信息 -->
      <div id="footer">
        <div class="voiceBtn sendBox" style="width: 12vw;">
          <img v-if="voice" src="../../assets/语音2.svg" alt="" @click="sendVoice(true)">
          <img v-else src="../../assets/语音1.svg" alt="" @click="sendVoice(false)">
        </div>
        <div class="content sendBox">
          <button v-if="voice" class="voiceStartBtn">按住开始说话</button>
          <div v-else class="contentBox" @focus="foucuson" id="MSGCONTENT" name="" cols="30" rows="10" contenteditable="true" @keyup="msgContent" @blur="loseFocus">
          </div>
        </div>
        <div class="BTNBOX sendBox">
          <img v-if="!faceshow" src="../../assets/表情1.svg" alt="" @click="showface">
          <img v-else src="../../assets/表情2.svg" alt="" @click="showface">
          <button v-if="myMsg&&!funboxshow" @click="send">发送</button>
          <img v-else-if="!myMsg&&!funboxshow" src="../../assets/chatadd.svg" alt="" style="margin-left:1.5vw;" @click="showFun">
          <img v-else src="../../assets/加.svg" alt="" style="margin-left:1.5vw;" @click="showFun">
        </div>
      </div>
      <!-- 扩展功能 -->
      <div v-if="funboxshow">
        <ul class="proccessbox">
          <li>
            <router-link to="/sendredbag">
              <img src="../../assets/红包.svg" alt="">
              <span>发红包</span>
            </router-link>
          </li>
          <li>
            <router-link to="/transacount">
              <img src="../../assets/转账.svg" alt="">
              <span>转账</span>
            </router-link>
          </li>
        </ul>
      </div>
      <!-- 表情包 -->
      <div v-if="faceshow">
        <ul class="facesBox">
          <li v-for="(item,index) in faces" v-bind:key="index" v-if="item">
            <img :src="item" alt="" @click="addFaceToContent(index)">
          </li>
        </ul>
        <div style="clear:both;margin-bottom:2vw;"></div>
      </div>
    </section>
    <!-- 领红包 -->
    <section v-show="redbagShow" class="open-redbag-box-background"></section>
    <div v-show="redbagShow" class="open-redbag-box">
      <div class="userInfo-box">
        <div class="user-head-box">
          <img :src="this.GLOBAL.Host + this.GLOBAL.friendInfo.info.head" alt="">
        </div>
        <p class="user-name-box">{{this.GLOBAL.friendInfo.info.nickname}}</p>
        <p class="user-title-box">给您发了一个红包</p>
        <p class="user-remark-box">恭喜发财，大吉大利</p>
      </div>
      <span class="close-btn" @click="closeRedbagBox">x</span>
      <div class="max-circle-box"></div>
      <span class="min-circle-box-back"></span>
      <span class="min-circle-box" @click="reciveRedbag">拆红包</span>
    </div>
  </div>
</template>

<script>
console.log("COMMON_CHAT_VUE");
export default {
  data() {
    return {
      redbagsrc: [],
      redbagtips: [],
      red_ids: [],
      red_index: "", //拆红包的消息索引
      red_id: "", //拆红包的id
      redbagShow: false, //显示拆红包
      myMsg: "", // 发送的消息
      head: this.GLOBAL.friendInfo.info.head, //朋友投降
      friendInfo: this.GLOBAL.friendInfo, //朋友信息
      host: this.GLOBAL.Host, //主机或者域名
      selfHead: this.GLOBAL.userInfo.head, //本人头像
      voice: false, //是否为语音模式
      funboxshow: false, //是否显示附加功能
      faceshow: false, //是否显示表情包
      // 表情包
      faces: [
        "static/faces/分类.svg",
        "static/faces/发呆.svg",
        "static/faces/可爱.svg",
        "static/faces/大笑.svg",
        "static/faces/失败.svg",
        "static/faces/板脸.svg",
        "static/faces/白眼.svg",
        "static/faces/笑口常开.svg",
        "static/faces/等待.svg",
        "static/faces/衰.svg",
        "static/faces/调皮.svg",
        "static/faces/难过.svg",
        "static/faces/面无表情.svg",
        "static/faces/鬼脸.svg"
      ],
      self_id: this.GLOBAL.userInfo.id
    };
  },
  beforeCreate: function() {
    console.log("chat->beforecreate....");
  },
  mounted: function() {
    // console.log(this.GLOBAL.friendLists);
    // console.log(this.GLOBAL.friendInfo);
    // 隐藏底部导航栏
    this.$parent.tabbarShow = false;
    if (!this.GLOBAL.friendInfo.info) {
      this.$router.push("/friend");
    }
    // 检测是否登陆
    this.checkLogin();
    this.onmessage();
    this.changeReadStatus();
  },
  updated: function() {
    if (this.message != "") {
      this.$nextTick(() => {
        let lastChat = document.getElementById("chat").lastChild;
        if (lastChat) {
          lastChat.scrollIntoView();
        }
      });
    }
    this.changeReadStatus();
  },
  computed: {},
  methods: {
    /**
     * 关闭红包弹窗
     */
    closeRedbagBox: function() {
      this.redbagShow = false;
      this.red_id = "";
      this.red_index = "";
    },
    /**
     * 拆红包
     */
    reciveRedbag: function() {
      let data = {
        type: "reciveRedBagFromFriend",
        send_id: this.GLOBAL.userInfo.id,
        red_id: this.red_id,
        red_send_id: this.GLOBAL.friendInfo.info.id,
        con_id: this.GLOBAL.connectionId
      };
      let that = this;
      // 发送拆红包请求
      this.senddata(data, function(resopne) {
        if (resopne.status) {
          // 更新本地金币池
          that.GLOBAL.userInfo.gold = resopne.gold;
          // 更新本地用户信息
          localStorage.setItem(
            "bc_userInfo",
            JSON.stringify(that.GLOBAL.userInfo)
          );
          // 更新本地红包信息的收取状态
          that.GLOBAL.msgList[that.GLOBAL.friendInfo.info.id][that.red_index][
            "content"
          ] =
            "false:" +
            that.GLOBAL.msgList[that.GLOBAL.friendInfo.info.id][that.red_index][
              "content"
            ];
          // 隐藏红包弹窗
          that.redbagShow = false;
          // 跳转红包详情
          that.$router.push({
            path: "/redbagdetail",
            query: { red_id: that.red_id }
          });
        } else {
          // 输出提示
          that.$messagebox.alert(resopne.errMsg).then(action => {
            that.redbagShow = false;
          });
        }
      });
    },
    /**
     * 查看红包详情或者显示拆红包弹窗
     */
    lookRedbag: function(red_id, index) {
      if (
        this.GLOBAL.userInfo.id ==
        this.GLOBAL.msgList[this.GLOBAL.friendInfo.info.id][index]["send_id"]
      ) {
        // 发红包方查看红包详情
        this.$messagebox.alert("查看红包：" + red_id);
        this.$router.push({
          path: "/redbagdetail",
          query: { red_id: red_id }
        });
      } else {
        // 收红包方点击红包信息
        if (red_id > 0) {
          // 显示拆开红包的弹窗
          this.red_id = red_id;
          this.red_index = index;
          this.redbagShow = true;
        } else {
          // 已经领取红包的情况下，跳转红包详情
          this.$router.push({
            path: "/redbagdetail",
            query: { red_id: -red_id }
          });
        }
      }
    },
    /**
     * 检测消息是否为红包信息
     * content: 消息内容
     * check: 为真时返回布尔类型，否则返回检测后的内容
     */
    checkRedBag: function(content, check = false, index = "") {
      if (check) {
        // 检测是否为红包信息
        // 初始化红包图片为未打开状态
        this.redbagsrc[index] = "static/open.png";
        // 初始化提示语为“点击领取”
        this.redbagtips[index] = "点击领取";
        // 初始化匹配规则
        let RegE = new RegExp(/^(false:|)\(@redbag([0-9]+)redbag@\)/);
        if (RegE.test(content)) {
          // 匹配并返回红包id
          let redid = RegE.exec(content);
          if (redid[1]) {
            // 修改红包图片为打开状态
            this.redbagsrc[index] = "static/红包.png";
            // 修改提示语为“已领取”
            this.redbagtips[index] = "已领取";
            return -redid[2];
          } else {
            return redid[2];
          }
        } else {
          // 非红包消息
          return false;
        }
      } else {
        // 格式化消息内容
        let RegE = new RegExp(/(^(false:|)\(@redbag[0-9]+redbag@\))(.*)/);
        let result = RegE.exec(content);
        // 判断是否为红包发送方;
        if (
          this.GLOBAL.userInfo.id ==
          this.GLOBAL.msgList[this.GLOBAL.friendInfo.info.id][index]["send_id"]
        ) {
          // 若是则修改提示语为“点击查看”
          this.redbagtips[index] = "点击查看";
        }
        // 返回信息内容
        return result[3];
      }
    },
    foucuson: function() {
      this.funboxshow = false;
      this.faceshow = false;
    },
    /**
     * 添加表情到内容中
     */
    addFaceToContent: function(src) {
      let obj = document.getElementById("MSGCONTENT");
      let htmlStr = obj.innerHTML;
      console.log(htmlStr);
      htmlStr +=
        "<img src='" +
        this.faces[src] +
        "' style='width:8vw;height:auto;display:inline-block;margin:0px 1vw;' align='absmiddle'>";
      obj.innerHTML = htmlStr;
      this.myMsg = htmlStr;
      this.faceshow = false;
    },
    /**
     * 表情显示或收起
     */
    showface: function() {
      this.faceshow = !this.faceshow;
      this.funboxshow = false;
    },
    // 附加功能显示或收起
    showFun: function() {
      this.funboxshow = !this.funboxshow;
      this.faceshow = false;
    },
    /**
     * 将发送消息绑定到变量中
     */
    msgContent: function() {
      this.myMsg = document.getElementById("MSGCONTENT").innerHTML;
    },
    loseFocus: function() {
      this.myMsg = document.getElementById("MSGCONTENT").innerHTML;
    },
    /**
     * 发送语音
     */
    sendVoice: function(status) {
      this.voice = !this.voice;
    },
    /**
     * 显示聊天日期
     */
    showchatRecordsDate: function(index) {
      // 判断是否存在
      if (index - 1 >= 0) {
        return this.showDate(
          this.GLOBAL.msgList[this.friendInfo.info.id][index - 1].time,
          this.GLOBAL.msgList[this.friendInfo.info.id][index].time
        );
      } else {
        if (this.GLOBAL.msgList[this.friendInfo.info.id][index].status) {
          // 聊条消息
          if (this.GLOBAL.msgList[this.friendInfo.info.id][index].chatType) {
            // 当前聊天消息，第一条
            return this.showDate(
              this.GLOBAL.msgList[this.friendInfo.info.id][index].time
            );
          } else {
            // 历史聊天消息,第一条
            return this.showDate(
              "",
              this.GLOBAL.msgList[this.friendInfo.info.id][index].time
            );
          }
        } else {
          // 系统好友提醒消息
          return this.showDate(
            "",
            "",
            this.GLOBAL.msgList[this.friendInfo.info.id][index].time
          );
        }
      }
    },
    /**
     * 发送消息
     */
    send: function() {
      if (this.myMsg) {
        // 更新本地的消息队列
        let time = this.updateMsgList(this.myMsg);
        // 发送给对方的消息体
        let msgtext = {
          send_id: this.GLOBAL.userInfo.id, // 发送方
          // nickname: this.GLOBAL.userInfo.nickname, // 发送方昵称
          get_id: this.friendInfo.info.id, // 接收方
          // fname: this.GLOBAL.obj.nickname,
          content: this.myMsg.replace(/"/g, "&quot;"), // 消息内容
          time: time, // 消息发送时间
          type: "text", // 消息发送类型
          rType: 1, // 房间类型：1对1表示个人， 0表示聊天室
          status: this.friendInfo.status, // 和user_message表的status对应,1：好友消息，2：陌生人消息
          con_id: this.GLOBAL.connectionId,
          fcon_id: this.GLOBAL.friendInfo.info.connectionID
        };
        let that = this;
        this.senddata(msgtext, function(result) {
          console.log("===============chattest===============");
          console.log("sendStatus:" + result.status);
          if (!result.status) {
            that.GLOBAL.msgList[msgtext.get_id][
              Object.keys(that.GLOBAL.msgList[msgtext.get_id]).length - 1
            ].send_status = false;
          } else {
            console.log(that.GLOBAL.friendInfo.status);
            if (that.GLOBAL.friendInfo.status == 2) {
              console.log(that.GLOBAL.friendLists);
              if (
                !that.GLOBAL.friendLists["strange"][
                  that.GLOBAL.friendInfo.info.id
                ]
              ) {
                // 如何陌生人队列中不存在该用户信息，则将该用户保存到陌生人队列中
                that.GLOBAL.friendLists["strange"][
                  that.GLOBAL.friendInfo.info.id
                ] =
                  that.GLOBAL.friendInfo.info;
              }
            }
            console.log("===============chattest===============");
          }
        });
        // 清空消息框
        this.myMsg = "";
        document.getElementById("MSGCONTENT").innerHTML = "";
        this.faceshow = false;
        this.funboxshow = false;
      }
    },
    /**
     * 修改消息的阅读状态
     */
    changeReadStatus: function() {
      // 判断是否存在未读消息
      if (this.GLOBAL.friendMsgNum[this.friendInfo.info.id]) {
        console.log("changeStatus");
        // 发送修改消息阅读状态信号给服务器
        var data = {
          send_id: this.friendInfo.info.id,
          nickname: "",
          get_id: this.GLOBAL.userInfo.id,
          fname: "",
          msg: "change the readStatus",
          type: "readStatus",
          rType: 1,
          fType: this.friendInfo.status,
          con_id: this.GLOBAL.connectionId
        };
        this.senddata(data, function(result) {
          console.log("<chatChangeStatus>");
          console.log(result);
          console.log("</chatChangeStatus>");
          that.GLOBAL.msgLength -=
            that.GLOBAL.friendMsgNum[this.friendInfo.info.id];
          that.GLOBAL.friendMsgNum[this.friendInfo.info.id] = 0;
          console.log("reader ok");
        });
      } else {
        console.log("no messages");
      }
      // 总未读消息条数 - 查阅的消息条数
      this.GLOBAL.msgLength -= this.GLOBAL.friendMsgNum[
        this.friendInfo.info.id
      ];
      // 清空当前好友未读消息条数
      this.GLOBAL.friendMsgNum[this.friendInfo.info.id] = 0;
    }
  }
};
</script>

<style scoped>
.user-name-box {
  color: #fdd496;
  font-size: 6vw;
}
.user-title-box {
  color: #cf6e51;
  font-size: 4.5vw;
  font-weight: bold;
  line-height: 9vw;
}
.user-remark-box {
  color: #fdd496;
  font-size: 6vw;
  line-height: 15vw;
}
.userInfo-box {
  position: relative;
  z-index: 99999;
}
.user-head-box {
  display: inline-block;
}
.user-head-box img {
  width: auto;
  height: 16vw;
  border-radius: 16vw;
  margin-top: 10vw;
}
.close-btn {
  position: absolute;
  z-index: 10000000;
  left: 4vw;
  top: 1vw;
  font-size: 7vw;
}
.open-redbag-box-background {
  position: fixed;
  top: 0px;
  left: 0px;
  width: 100vw;
  height: 100vh;
  background: black;
  opacity: 0.2;
  filter: opacity(8);
  z-index: 1000;
}
.open-redbag-box {
  width: 80vw;
  height: 100vw;
  margin: auto;
  position: fixed;
  top: 15vw;
  left: 10vw;
  z-index: 1001;
  background: #aa4132;
  opacity: 1;
  filter: opacity(1);
  border-radius: 4vw;
  overflow: hidden;
}
.max-circle-box {
  position: absolute;
  left: -17vw;
  top: -17vw;
  z-index: 1005;
  width: 110vw;
  height: 110vw;
  border-radius: 0vw 0vw 100vw 0vw;
  background: #c44d38;
  transform: rotate(43deg);
  -ms-transform: rotate(43deg);
  -moz-transform: rotate(43deg);
  -webkit-transform: rotate(43deg);
  -o-transform: rotate(43deg);
  border: 2vw solid #a13826;
}
.min-circle-box-back {
  display: block;
  width: 26vw;
  height: 26vw;
  border-radius: 24vw;
  position: absolute;
  top: 65vw;
  left: 28.002vw;
  z-index: 1003;
  background: #a13826;
  border: 0px;
}
.min-circle-box {
  display: block;
  width: 22vw;
  height: 22vw;
  border-radius: 22vw;
  line-height: 22vw;
  background: #fdb852;
  margin: 2vw auto;
  text-align: center;
  color: #cd7241;
  font-weight: bold;
  font-size: 5vw;
  position: absolute;
  top: 65vw;
  left: 30.002vw;
  z-index: 1006;
}
.facesBox {
  width: 100vw;
  border-top: 1px solid #ccc;
}
.facesBox li {
  float: left;
  margin-left: 2vw;
  height: auto;
}
.facesBox li img {
  width: 7vw;
}
.proccessbox {
  display: flex;
  width: 100vw;
  border-top: 1px solid #ccc;
}
.proccessbox li {
  width: 16vw;
  /* border: 1px solid red; */
  text-align: center;
  height: 17vw;
}
.proccessbox li img {
  height: 10vw;
  width: auto;
  margin: auto;
}
.proccessbox li span {
  font-size: 4vw;
}
#footer {
  height: 15vw;
}
.voiceStartBtn {
  width: 100%;
  height: 10vw;
  margin-top: 2.5vw;
  background: #eef;
  border-radius: 10vw;
  outline: none;
  border: 1.5px solid #ccc;
  font-size: 4vw;
}
.send_fail_tips {
  color: red;
  position: absolute;
  top: 4vw;
  left: -10px;
  font-size: 6vw;
  font-weight: bold;
}
.user {
  /* height: 15vw; */
  position: fixed;
  bottom: 0px;
  width: 100%;
  font-size: 5vw;
  background: white;
}
.user .sendBox {
  float: left;
}
.user .voiceBtn {
  width: 12vw;
  height: 15vw;
  padding: 0px;
}
.user .voiceBtn img {
  width: auto;
  height: 9vw;
  margin: 3vw auto;
}
.user .content {
  width: 63.5vw;
  height: 15vw;
  margin-right: 1.5vw;
}
.user .content .contentBox {
  width: 100%;
  box-sizing: border-box;
  height: 10vw;
  margin-top: 2.5vw;
  border: 1.5px solid #ccc;
  border-radius: 10px;
  outline: none;
  resize: none;
  padding: 5px;
  font-size: 4vw;
  text-align: left;
  overflow: hidden;
  overflow-y: auto;
}
.user .BTNBOX {
  width: 22vw;
  height: 15vw;
}
.user .BTNBOX img {
  float: left;
  width: 8vw;
  height: auto;
  margin-top: 3.5vw;
  margin-left: 0.6vw;
}
.user .BTNBOX button {
  width: 12vw;
  height: 7vw;
  margin: 4vw 0.5vw;
  font-size: 3.5vw;
}

.userContent {
  height: 100%;
  border-top: 1px solid #999;
  display: flex;
  justify-content: space-between;
}
#sendText {
  width: 80%;
}
.send {
  width: 20%;
}

.chat {
  overflow: hidden;
  overflow-y: auto;
  box-sizing: border-box;
  height: 100vh;
  text-align: left;
  position: fixed;
  left: 0px;
  top: 0px;
  width: 100vw;
  padding: 5px 10px;
}
#chat {
  margin-top: 10vw;
  margin-bottom: 15vw;
}
.list {
  padding: 10px 0;
  flex-wrap: nowrap;
}
.head {
  width: 11vw;
  height: 11vw;
  border-radius: 100%;
  overflow: hidden;
  text-align: center;
}
.head img {
  width: auto;
  height: 100%;
  margin: auto;
}
.msgText {
  font-size: 4vw;
  padding: 1.5vw 2.5vw;
  max-width: 60%;
  background-color: #fff;
  border-radius: 2vw;
  position: relative;
  margin-left: 3vw;
  line-height: 8vw;
  word-wrap: break-word;
  word-break: break-all;
}
.msgText p,
.msgText p * {
  /* padding-top: 5px; */
  word-wrap: break-word;
  word-break: break-all;
}
.msgText p * {
  width: 100%;
  /* border: 1px solid red; */
}
.boo {
  position: absolute;
  width: 0;
  height: 0;
  border-top: 5px solid transparent;
  border-right: 10px solid #fff;
  border-bottom: 5px solid transparent;
  border-left: 10px solid transparent;
  top: 4.5vw;
  left: -20px;
}
.myList {
  flex-direction: row-reverse;
}
.myList .msgText {
  margin-left: 0;
  margin-right: 20px;
  background-color: #57d6dd;
  color: #fff;
}
.myList .myboo {
  position: absolute;
  width: 0;
  height: 0;
  border-top: 5px solid transparent;
  border-right: 10px solid transparent;
  border-bottom: 5px solid transparent;
  border-left: 10px solid #57d6dd;
  top: 4.5vw;
  right: -20px;
}
</style>


