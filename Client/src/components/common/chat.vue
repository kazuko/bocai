<template>
  <div style="border: 0px solid green;background:#E7E7E7;min-height:100vh;">
    <!-- <mt-header :title="this.GLOBAL.obj.val.nickname"> -->
    <mt-header id="header" v-bind:title="this.friendInfo.info.nickname" fixed="true" style="height:10vw;background:#57D6DD;">
      <router-link to="/friend" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
    </mt-header>
    <!-- <mt-loadmore :top-method="loadTop" @top-status-change="handleTopChage" auto-fill="false" ref="loadmore"> -->
    <div class="pageContent chat" id="centerbox">
      <p v-if="!record_page" style="text-align:center;font-size:3vw;color:#888;margin-top:1vw;"><span style="text-decoration:underline;" @click="getChatRecords()">聊天记录</span></p>
      <p v-if="endRecord" style="text-align:center;font-size:3vw;color:#888;margin-top:1vw;"><span>没有更多了</span></p>
      <!-- <div id="chat"> -->
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
          <!-- 红包 -->
          <div v-if="checkRedBag(item.content,0, index)" class="msgText redbag-info-box">
            <p @click="lookRedbag(checkRedBag(item.content, 1, index), index, item.id)" id="transfer-account-box">
              <span class="transfer-account-box-info" :style="checkRedBag(item.content,1, index)>0?'':'background-color:#f5c97c;'">
                <img :src="checkRedBag(item.content, 4, index)?'static/to_recive_redbag.png':'static/has_recived_redbag.png'" alt="">
                <span style="width:auto;">
                  <p style="font-size:4vw;">{{checkRedBag(item.content,2, index)}}</p>
                  <!-- <p v-if="item.send_id==self_id" style="font-size:3vw;">{{checkRedBag(item.content,3, index)>0?'点击查看':'已被领取'}}</p> -->
                  <p style="font-size:3vw;">{{checkRedBag(item.content,3, index)}}</p>
                </span>
              </span>
              <span class="transfer-account-box-tips">
                <p>红包</p>
              </span>
            </p>
            <div :class="item.send_id==self_id?('myboo transfer-myboo'):('boo transfer-boo')" :style="checkRedBag(item.content,1, index)>0?'':item.send_id==self_id?'border-left-color:#f5c97c !important;':'border-right-color:#f5c97c !important;'"></div>
            <!-- 我方发送失败 -->
            <span class="send_fail_tips" v-if="item.send_id==self_id&&item.send_status==2">!</span>
          </div>
          <!-- 转账 -->
          <div v-else-if="checkTransfer(item.content, 0, index)" class="msgText transfer-info-box">
            <p id="transfer-account-box" @click="lookTransfer(checkTransfer(item.content, 0, index), item.id, index)">
              <span class="transfer-account-box-info" :style="checkTransfer(item.content, 0, index)<0?'background-color:#f5c97c;':''">
                <img :src="checkTransfer(item.content, 3, index)" alt="">
                <span style="width:auto;">
                  <p>{{checkTransfer(item.content, 1, index)}}</p>
                  <p>{{checkTransfer(item.content, 2, index)}}</p>
                </span>
              </span>
              <span class="transfer-account-box-tips">
                <p>转账</p>
              </span>
            </p>
            <div :class="item.send_id==self_id?('myboo transfer-myboo'):('boo transfer-boo')" :style="checkTransfer(item.content, 0, index)>0?'':item.send_id==self_id?'border-left-color:#f5c97c !important;':'border-right-color:#f5c97c !important;'"></div>
            <!-- 我方发送失败 -->
            <span class="send_fail_tips" v-if="item.send_id==self_id&&item.send_status==2">!</span>
          </div>
          <!-- 语音消息 -->
          <div v-else-if="checkVoice(item.content, 0, index)" class="msgText">
            <div @click="playVocie(item.content, index)" class="video-box" :class="item.send_id==self_id?'rightfloat':'leftfloat'">
              <audio @ended="endPlay()" :src="host + checkVoice(item.content, 1, index)" :id="'audioPlayer'+index" preload="preload"></audio>
              <span class="play-status-cirle play-status-cirle-background-stop" :id="'video-box-'+index"></span>
              <span class="status-show stop-status play-status1" :class="item.send_id==self_id?'status-show-self':'status-show-others'" :id="'video-box-'+index+'-1'"></span>
              <span class="status-show stop-status play-status2" :class="item.send_id==self_id?'status-show-self':'status-show-others'" :id="'video-box-'+index+'-2'"></span>
              <span class="status-show stop-status play-status3" :class="item.send_id==self_id?'status-show-self':'status-show-others'" :id="'video-box-'+index+'-3'"></span>
            </div>
            <div :class="item.send_id==self_id?('myboo'):('boo')"></div>
            <span class="send_fail_tips" v-if="item.send_id==self_id&&item.send_status == 2">!</span>
          </div>
          <!-- 照片消息 -->
          <div v-else-if="checkImage(item.content, 0, index)" class="msgText">
            <p class="msgImage" :class="item.send_id==self_id?'myMsgImg':'otherMsgImg'">
              <!-- <img v-if="item.send_status" :src="checkImage(item.content, 1, index)" alt="有内涵哈！"> -->
              <img :src="host + checkImage(item.content, 1, index)" alt="有内涵哈！">
            </p>
            <div :class="item.send_id==self_id?('myboo'):('boo')"></div>
            <!-- 我方发送失败 -->
            <span class="send_fail_tips" v-if="item.send_id==self_id&&item.send_status==2">!</span>
            <!-- <span class="send_fail_tips" v-else-if="item.send_status == 3">上传中</span> -->
          </div>
          <!-- 文字聊天 -->
          <div v-else class="msgText">
            <p v-html="item.content"></p>
            <div :class="item.send_id==self_id?('myboo'):('boo')"></div>
            <!-- 我方发送失败 -->
            <span class="send_fail_tips" v-if="item.send_id==self_id&&item.send_status==2">!</span>
          </div>
        </div>
        <!-- 系统提示 -->
        <div v-else class="addTips">
          <p style="text-align:center;color:#999;font-size:4vw;">{{item.content}}</p>
        </div>
      </div>
      <!-- </div> -->
    </div>
    <!-- </mt-loadmore> -->

    <section class="user">
      <!-- 发送信息 -->
      <div id="footer">
        <div class="voiceBtn sendBox" style="width: 12vw;">
          <img v-if="voice" src="../../assets/语音2.svg" alt="" @click="sendVoice(true)">
          <img v-else src="../../assets/语音1.svg" alt="" @click="sendVoice(false)">
        </div>
        <div class="content sendBox">
          <button v-if="voice" class="voiceStartBtn" id="audioBtn">按住开始说话</button>
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
              <img src="static/icon-red@chat.png" alt="">
              <span>发红包</span>
            </router-link>
          </li>
          <li>
            <router-link to="/transacount">
              <img src="static/icon-transfer.png" alt="">
              <span>转账</span>
            </router-link>
          </li>
          <li style="position:relative;">
            <input id="imageUpload" type="file" accept="image/gif,image/jpeg,image/jpg,image/png" @change="sendImages">
            <img src="static/icon-photo.png" alt="">
            <span>图片</span>
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
    <div id="show-record-box"></div>
  </div>
</template>

<script>
console.log("COMMON_CHAT_VUE");
export default {
  data() {
    return {
      refreshStatus: true,
      // redbagsrc: [], //保存红包领取状态图片的路径
      // redbagtips: [], //保存红包领取状体的提示
      record_page: 0, //当前页数
      allPage: 0, //聊天记录总页数
      endRecord: false,
      transferSrc: [], //保存转账的图片路径
      // red_ids: [],
      red_index: null, //拆红包的消息索引
      red_id: null, //拆红包的id
      mid: null, //红包小消息的id
      redbagShow: false, //显示拆红包
      myMsg: null, // 发送的消息
      head: this.GLOBAL.friendInfo.info.head, //朋友头像
      friendInfo: this.GLOBAL.friendInfo, //朋友信息
      host: this.GLOBAL.Host, //主机或者域名
      selfHead: this.GLOBAL.userInfo.head, //本人头像
      voice: false, //是否为语音模式
      funboxshow: false, //是否显示附加功能
      faceshow: false, //是否显示表情包
      // 表情包
      faces: this.GLOBAL.updateFile.faces,
      self_id: this.GLOBAL.userInfo.id,
      oldScrollTop: 0,
      updateStatus: true,

      voice_index: null,
      audio_id: null,
      playF: false,
      playN: 0,
      timer: null,
      send_time: null
    };
  },
  beforeCreate: function() {
    // this.GLOBAL.redbagsrc = [];
  },
  mounted: function() {
    // console.log(this.GLOBAL.msgList[this.friendInfo.info.id]);
    // console.log(this.GLOBAL.friendLists);
    // console.log(this.GLOBAL.friendInfo);
    // 隐藏底部导航栏
    var that = this;
    this.$parent.tabbarShow = false;
    if (!this.GLOBAL.friendInfo.info) {
      this.$router.push("/friend");
    }
    // 检测是否登陆
    this.checkLogin(function() {
      that.changeReadStatus();
    });
    this.onmessage();
    this.record_page =
      this.GLOBAL.friendInfo.status == 2
        ? this.GLOBAL.friendLists.strange[this.GLOBAL.friendInfo.info.id]
          ? this.GLOBAL.friendLists.strange[this.GLOBAL.friendInfo.info.id]
              .record_page
            ? this.GLOBAL.friendLists.strange[this.GLOBAL.friendInfo.info.id]
                .record_page
            : 0
          : 0
        : this.GLOBAL.friendLists.friends[this.GLOBAL.friendInfo.info.id]
          ? this.GLOBAL.friendLists.friends[this.GLOBAL.friendInfo.info.id]
              .record_page
            ? this.GLOBAL.friendLists.friends[this.GLOBAL.friendInfo.info.id]
                .record_page
            : 0
          : 0;
    this.refresh({
      elementId: "centerbox",
      type: "up",
      callback: function(obj) {
        if (that.refreshStatus && that.record_page) {
          that.oldScrollTop = obj.scrollHeight;
          console.log("oldscrolltop=" + that.oldScrollTop);
          that.refreshStatus = false;
          that.getChatRecords();
        }
      }
    });
    this.scroll({
      elementId: "centerbox",
      type: "bottom",
      scrollingCallback: function() {
        that.updateStatus = false;
      },
      scrollEndCallback: function() {
        that.updateStatus = true;
      }
    });
  },
  updated: function() {
    console.log("updateStatus=" + this.updateStatus);
    if (this.updateStatus) {
      this.$nextTick(() => {
        let obj = document.getElementById("centerbox");
        obj.scrollTop = obj.scrollHeight;
      });
      this.changeReadStatus();
    }
  },
  computed: {},
  methods: {
    checkImage(content, flag, index) {
      var reg = new RegExp(/\(#image-image#\)(.*)/);
      switch (flag) {
        case 0:
          if (reg.test(content)) {
            return true;
          } else {
            return false;
          }
          break;
        case 1:
          var result = reg.exec(content);
          console.log(result);
          return result[1];
          break;
        default:
          console.log("不是图片消息");
      }
    },

    /**
     * 发送图片
     */
    sendImages() {
      this.funboxshow = false;
      var file = document.querySelector("#imageUpload").files[0];
      if (!/image\/\w+/.test(file.type)) {
        this.$messagebox.alert("请上传图片！");
        return false;
      }
      var formdata = new FormData();
      formdata.append("image", file);
      formdata.append("type", "sendImage");
      var xhr = new XMLHttpRequest();
      this.$forceUpdate();
      let that = this;
      xhr.onreadystatechange = function(respone) {
        if (xhr.readyState == 4 && xhr.status == 200) {
          try {
            var resp = JSON.parse(respone.currentTarget.response);
            // console.log(resp);
            if (resp.status) {
              that.myMsg = "(#image-image#)" + resp.src;
              that.send();
            } else {
              that.$messagebox.alert("图片上传失败！");
            }
          } catch (e) {
            console.log(e);
          }
        } else if (xhr.readyState == 4 && xhr.status != 200) {
          console.log("上传失败！");
        }
      };
      xhr.open("POST", this.GLOBAL.uploadHref);
      xhr.send(formdata);
    },
    /**
     * 播放完毕回调事件
     */
    endPlay() {
      if (this.audio_id) {
        let audioObj = document.getElementById(this.audio_id);
        if (audioObj) {
          audioObj.pause();
          try {
            audioObj.currentTime = 0;
          } catch (e) {
            audioObj.fastSeek(0);
          }
        }
      }
      let obj = document.getElementById(this.voice_index);
      let obj1 = document.getElementById(this.voice_index + "-1");
      let obj2 = document.getElementById(this.voice_index + "-2");
      let obj3 = document.getElementById(this.voice_index + "-3");
      obj.classList.remove("play-status-cirle-background-play");
      obj.classList.add("play-status-cirle-background-stop");

      obj1.classList.remove("play-status", "hhide");
      obj1.classList.add("stop-status", "sshow");

      obj2.classList.remove("play-status", "hhide");
      obj2.classList.add("stop-status", "sshow");

      obj3.classList.remove("play-status", "hhide");
      obj3.classList.add("stop-status", "sshow");
      // this.voiceSrc = "";
      this.voice_index = "";
      this.audio_id = "";
      this.playF = false;
      this.playN = 1;
    },
    /**
     * 播放声音
     */
    playVocie(src, index) {
      // this.voiceSrc = src;
      // this.$forceUpdate();
      if (this.voice_index || this.audio_id) {
        this.endPlay();
      }
      let that = this;
      // this.$nextTick(function() {
      this.audio_id = "audioPlayer" + index;
      let audioObj = document.getElementById(this.audio_id);
      if (audioObj) {
        audioObj.play();
        // document.getElementById("video-box-" + index).innerHTML =
        //   "正在播放...";
        that.voice_index = "video-box-" + index;
        let obj = document.getElementById(that.voice_index);
        // console.log(obj.classList);
        let obj1 = document.getElementById(that.voice_index + "-1");
        let obj2 = document.getElementById(that.voice_index + "-2");
        let obj3 = document.getElementById(that.voice_index + "-3");
        obj.classList.remove("play-status-cirle-background-stop");
        obj.classList.add("play-status-cirle-background-play");

        obj1.classList.remove("stop-status", "hhide");
        obj1.classList.add("play-status", "sshow");

        obj2.classList.remove("stop-status", "sshow");
        obj2.classList.add("play-status", "hhide");

        obj3.classList.remove("stop-status", "sshow");
        obj3.classList.add("play-status", "hhide");

        that.playF = true;
        if (that.timer) {
          clearInterval(that.timer);
          that.timer = null;
        }
        that.timer = setInterval(function() {
          // console.log("playF=" + that.playF);
          if (that.playF) {
            that.playN++;
            // console.log("playN=" + that.playN);
            if (that.playN > 3) {
              that.playN = 0;
            }
            switch (that.playN) {
              case 1:
                // console.log("case1->obj2->hidden");
                // console.log("case1->obj3->hidden");
                obj2.classList.remove("sshow");
                obj2.classList.add("hhide");

                obj3.classList.remove("sshow");
                obj3.classList.add("hhide");
                break;
              case 2:
                // console.log("case2->obj2->show");
                // console.log("case2->obj3->hidden");
                obj2.classList.remove("hhide");
                obj2.classList.add("sshow");

                obj3.classList.remove("sshow");
                obj3.classList.add("hhide");
                break;
              case 3:
                // console.log("case3->obj2->show");
                // console.log("case3->obj3->show");
                obj2.classList.remove("hhide");
                obj2.classList.add("sshow");

                obj3.classList.remove("hhide");
                obj3.classList.add("sshow");
                break;
              default:
              // console.log("case->default:s");
            }
          } else {
            // console.log("clearInterval");
            clearInterval(that.timer);
            that.timer = null;
          }
        }, 200);
      }
      // });
    },
    /**
     * 检测是否为音频信息
     */
    checkVoice(content, flag, index) {
      var reg = new RegExp(/\(#voice-voice#\)(.*)/);
      switch (flag) {
        case 0:
          if (reg.test(content)) {
            return true;
          } else {
            return false;
          }
          break;
        case 1:
          var result = reg.exec(content);
          // console.log(result);
          return result[1];
          break;
        default:
          console.log("该信息不是音频");
      }
    },
    /**
     * 获取聊天记录
     */
    getChatRecords() {
      if (!this.endRecord) {
        let data = {
          type: "getChatRecords",
          user_id: this.GLOBAL.userInfo.id,
          frined_id: this.GLOBAL.friendInfo.info.id,
          con_id: this.GLOBAL.connectionId,
          page: this.record_page
        };
        let that = this;
        this.senddata({
          data: data,
          callback: function(respone) {
            if (Object.keys(respone.recordsList).length) {
              // that.$forceUpdate();
              that.refreshStatus = true;
              // 将聊天记录倒序
              // respone.recordsList.reverse();
              if (respone.total) {
                // 保存30天内的聊天记录总页数
                that.allPage = respone.total;
                // 重置当前好友的消息记录
                that.GLOBAL.msgList[that.friendInfo.info.id] = new Array();
              }
              for (var i in respone.recordsList) {
                that.GLOBAL.msgList[that.friendInfo.info.id].unshift(
                  respone.recordsList[i]
                );
              }

              // 当前页数自增
              that.record_page++;
              // 保存当前好友的聊天记录当前的页数到全局变量
              if (that.GLOBAL.friendInfo.status == 2) {
                that.$set(
                  that.GLOBAL.friendLists.strange[
                    that.GLOBAL.friendInfo.info.id
                  ],
                  "record_page",
                  that.record_page
                );
              } else {
                that.$set(
                  that.GLOBAL.friendLists.friends[
                    that.GLOBAL.friendInfo.info.id
                  ],
                  "record_page",
                  that.record_page
                );
              }
              // 判断是否获取完30天内的所有聊天记录
              if (respone.end || that.record_page > that.allPage) {
                that.endRecord = true;
              }
              that.$nextTick(function() {
                var obj = document.querySelector("#centerbox");
                obj.scrollTop = obj.scrollHeight - that.oldScrollTop;
              });
            } else {
              that.endRecord = true;
            }
          },
          callbackFlag: "responeGetChatRecords",
          handType: "user"
        });
      }
    },
    /**
     *
     */
    checkTransfer(content, flag, index) {
      // console.log("transferAccountCheck => {");
      // console.log(content);
      // console.log(flag);
      // console.log(index);
      // console.log("}");
      let rege = new RegExp(
        /(false:|true:|)\(@transfer#([0-9]+)-([0-9]+|[0-9]+\.[0-9]{0,2})#transfer@\)(.*)/
      );
      if (rege.test(content)) {
        let result = rege.exec(content);
        // console.log("result => {");
        // console.log(result);
        // console.log("}");
        switch (flag) {
          case 0:
            // 检测是否为转账信息，返回转账信息id或者-id
            if (result[1]) {
              // 已被领取或者已过期，返回-id
              //   // this.transferSrc[index] = "static/icon@2x.png";
              return -result[2];
            } else {
              // 待领取，返回id
              //   // this.transferSrc[index] = "static/icon-@2x.png";
              return result[2];
            }
            break;
          case 1:
            if (result[1] == "false:") {
              // if (
              //   result[4].indexOf("转账给" + this.GLOBAL.userInfo.nickname) ===
              //   -1
              // ) {
              //   return "已被领取";
              // } else {
              //   return "已收钱";
              // }
              return "已收钱";
            } else if (result[1] == "true:") {
              // if (
              //   result[4].indexOf("转账给" + this.GLOBAL.userInfo.nickname) ===
              //   -1
              // ) {
              //   return "已退还至我的账户";
              // } else {
              //   return "已过期";
              // }
              return "已过期";
            } else {
              // 检测，返回金额
              return result[3];
            }
            break;
          case 2:
            if (result[1]) {
              return result[3];
            } else {
              // 检测，返回备注内容
              result[4] = result[4].replace(
                "转账给" + this.GLOBAL.userInfo.nickname,
                "转账给你"
              );
              return result[4];
            }
            break;
          case 3:
            if (result[1]) {
              return "static/icon@2x.png";
            } else {
              return "static/icon-@2x.png";
            }
            break;
          default:
            this.$messagebox.alert("发生数据丢失，请谨慎操作！");
            break;
        }
      } else {
        // console.log("不是转账:" + content);
        return false;
      }
    },
    /**
     *
     */
    lookTransfer(transfer_id, msg_id, index) {
      // console.log("transfer_id:" + transfer_id);
      // console.log("index:" + index);
      let content = this.GLOBAL.msgList[this.GLOBAL.friendInfo.info.id][index]
        .content;
      if (
        content.indexOf("转账给" + this.GLOBAL.userInfo.nickname) === -1 &&
        content.indexOf("转账给") !== -1
      ) {
        // console.log("我传的帐");
        this.$router.push({
          path: "/transferdetail",
          query: { transfer_id: Math.abs(transfer_id) }
        });
      } else {
        // console.log("我要收账");
        // console.log("transfer_id:" + transfer_id);
        if (transfer_id > 0) {
          let data = {
            type: "reciveTransfer",
            transfer_id: transfer_id,
            msg_id: msg_id,
            con_id: this.GLOBAL.connectionId,
            user_id: this.GLOBAL.userInfo.id,
            send_id: this.GLOBAL.friendInfo.info.id,
            send_con: this.GLOBAL.friendInfo.info.connectionID
          };
          let that = this;
          this.senddata({
            data: data,
            callback: function(Response) {
              // console.log(Response);
              if (Response.status) {
                // 更新用户金币信息
                that.GLOBAL.userInfo.gold = Response.gold;
                localStorage.setItem(
                  "bc_userInfo",
                  JSON.stringify(that.GLOBAL.userInfo)
                );
                that.GLOBAL.msgList[that.GLOBAL.friendInfo.info.id][
                  index
                ].content = "false:" + content;
                that.$router.push({
                  path: "/transferdetail",
                  query: { transfer_id: transfer_id }
                });
              } else {
                that.$messagebox.alert(Response.msg);
              }
            },
            callbackFlag: "responseRecivedTransfer",
            handType: "user"
          });
        } else {
          this.$router.push({
            path: "/transferdetail",
            query: { transfer_id: Math.abs(transfer_id) }
          });
        }
      }
    },
    /**
     * 关闭红包弹窗
     */
    closeRedbagBox: function() {
      this.redbagShow = false;
      this.red_id = null;
      this.red_index = null;
      this.mid = null;
    },
    /**
     * 拆红包
     */
    reciveRedbag: function() {
      let data = {
        type: "reciveRedBagFromFriend",
        send_id: this.GLOBAL.userInfo.id,
        con_id: this.GLOBAL.connectionId,
        red_id: this.red_id,
        mid: this.mid,
        red_send_id: this.GLOBAL.friendInfo.info.id,
        red_send_con: this.GLOBAL.friendInfo.info.connectionID
      };
      let that = this;
      // 发送拆红包请求
      this.senddata({
        data: data,
        callback: function(resopne) {
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
              that.GLOBAL.msgList[that.GLOBAL.friendInfo.info.id][
                that.red_index
              ]["content"];
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
            // console.log(resopne);
            if (resopne.code == 1) {
              // 更新本地红包信息的收取状态
              that.GLOBAL.msgList[that.GLOBAL.friendInfo.info.id][
                that.red_index
              ]["content"] =
                "true:" +
                that.GLOBAL.msgList[that.GLOBAL.friendInfo.info.id][
                  that.red_index
                ]["content"];
            } else if (resopne.code == 3) {
              that.GLOBAL.msgList[that.GLOBAL.friendInfo.info.id][
                that.red_index
              ]["content"] =
                "false:" +
                that.GLOBAL.msgList[that.GLOBAL.friendInfo.info.id][
                  that.red_index
                ]["content"];
            }
          }
        },
        callbackFlag: "responseReciveRedBag",
        handType: "user"
      });
    },
    /**
     * 查看红包详情或者显示拆红包弹窗
     */
    lookRedbag: function(red_id, index, msg_id) {
      console.log("msg_id=" + msg_id);
      // return false;
      // console.log("red_id:"+red_id);
      if (
        this.GLOBAL.userInfo.id ==
        this.GLOBAL.msgList[this.GLOBAL.friendInfo.info.id][index]["send_id"]
      ) {
        // 发红包方查看红包详情
        // this.$messagebox.alert("查看红包：" + red_id);
        // this.GLOBAL.msgList[this.GLOBAL.friendInfo.info.id][index].content = "true:"+this.GLOBAL.msgList[this.GLOBAL.friendInfo.info.id][index].content;
        this.$router.push({
          path: "/redbagdetail",
          query: { red_id: Math.abs(red_id), index: index }
        });
      } else {
        // 收红包方点击红包信息
        if (red_id > 0) {
          // 显示拆开红包的弹窗
          this.red_id = red_id;
          this.red_index = index;
          this.redbagShow = true;
          this.mid = msg_id;
        } else if (red_id == 0) {
          this.$messagebox.alert("红包已过期！");
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
    checkRedBag: function(content, flag, index) {
      let rege = /^(false:|true:|)\(@redbag([0-9]+)redbag@\)(.*)/;
      if (rege.test(content)) {
        let result = rege.exec(content);
        switch (flag) {
          case 0:
            // 只是检测是否为红包信息，是返回true
            // if (result[1]) {
            //   // 初始化红包图片为打开状态
            //   this.redbagsrc[result[2]] = "static/红包.png";
            //   this.redbagtips[index] = "已领取";
            // } else {
            //   // 初始化红包图片为未打开状态
            //   this.redbagsrc[result[2]] = "static/open.png";
            //   // 初始化提示语为“点击领取”
            //   this.redbagtips[index] = "点击领取";
            // }
            return true;
            break;
          case 1:
            // 返回红包的ID
            if (result[1]) {
              // 已经领取返回负数
              return -result[2];
            } else {
              // 未领取返回正数
              return result[2];
            }
            break;
          case 2:
            // 返回信息内容
            return result[3];
            break;
          case 3:
            //  判断是否为红包发送方;

            if (result[1] == "true:") {
              return "已过期";
            } else if (result[1] == "false:") {
              if (
                this.GLOBAL.userInfo.id ==
                this.GLOBAL.msgList[this.GLOBAL.friendInfo.info.id][index][
                  "send_id"
                ]
              ) {
                return "已被领取";
              } else {
                return "已领取";
              }
            } else {
              if (
                this.GLOBAL.userInfo.id ==
                this.GLOBAL.msgList[this.GLOBAL.friendInfo.info.id][index][
                  "send_id"
                ]
              ) {
                return "点击查看";
              } else {
                return "点击领取";
              }
            }
            break;
          case 4:
            if (result[1] == "false:") {
              return false;
            } else {
              return true;
            }
            break;
          default:
            this.$messagebox.alert("发生数据丢失，请谨慎操作！");
            break;
        }
      } else {
        return false;
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
      // console.log(htmlStr);
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
      this.funboxshow = false;
      this.faceshow = false;
      let that = this;
      if (this.voice) {
        this.$nextTick(function() {
          that.VoiceJSDK.readyRecord({
            that: that,
            elementId: "audioBtn",
            showId: "show-record-box",
            url: that.GLOBAL.uploadHref,
            success: function(respone) {
              // console.log(respone);
              if (respone) {
                try {
                  respone = JSON.parse(respone);
                  console.log("reponeVoice=>{");
                  console.log(respone);
                  console.log("}");
                  if (respone.status) {
                    that.myMsg = "(#voice-voice#)" + respone.src;
                    that.send();
                  } else {
                    that.$messagebox.alert(respone.msg);
                  }
                } catch (e) {
                  console.log(e);
                }
              }
            },
            fail: function(error) {
              console.log("error=>{");
              console.log(error);
              console.log("}");
            }
          });
        });
      }
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

        this.$nextTick(function() {
          let obj = document.getElementById("centerbox");
          obj.scrollTop = obj.scrollHeight;
        });
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
        this.senddata({
          data: msgtext,
          callback: function(result) {
            // console.log("===============chattest===============");
            // console.log("sendStatus:" + result.status);
            if (!result.status) {
              that.GLOBAL.msgList[msgtext.get_id][
                Object.keys(that.GLOBAL.msgList[msgtext.get_id]).length - 1
              ].send_status = 2;
            } else {
              // console.log(that.GLOBAL.friendInfo.status);
              if (that.GLOBAL.friendInfo.status == 2) {
                // console.log(that.GLOBAL.friendLists);
                if (
                  !that.GLOBAL.friendLists["strange"][
                    that.GLOBAL.friendInfo.info.id
                  ]
                ) {
                  // 如何陌生人队列中不存在该用户信息，则将该用户保存到陌生人队列中
                  that.GLOBAL.friendLists["strange"][
                    that.GLOBAL.friendInfo.info.id
                  ] = that.GLOBAL.friendInfo.info;
                }
              }
              // console.log("===============chattest===============");
            }
          },
          callbackFlag: "responseTextMsg",
          handType: "user"
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
        // console.log("changeStatus");
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
        this.senddata({ data: data, handType: "user" });
        // 总未读消息条数 - 查阅的消息条数
        if (
          this.GLOBAL.msgLength >=
            this.GLOBAL.friendMsgNum[this.friendInfo.info.id] &&
          this.GLOBAL.msgLength > 0
        ) {
          this.GLOBAL.msgLength -= this.GLOBAL.friendMsgNum[
            this.friendInfo.info.id
          ];
        }
        // 清空当前好友未读消息条数
        this.GLOBAL.friendMsgNum[this.friendInfo.info.id] = 0;
      } else {
        // console.log("no messages");
      }
    }
  }
};
</script>

<style scoped>
#transfer-account-box {
  border-radius: 2vw !important;
  overflow: hidden;
}
#transfer-account-box .transfer-account-box-info {
  background: #fea405;
  display: flex;
  align-items: center;
  color: #ffffff;
}
#transfer-account-box .transfer-account-box-info img {
  width: 10vw !important;
  height: 10vw !important;
  margin: 0vw 3vw;
}
#transfer-account-box .transfer-account-box-info span p:nth-child(1) {
  /* height: ; */
  font-size: 5vw;
  height: 7vw;
  line-height: 12vw;
}
#transfer-account-box .transfer-account-box-info span p:nth-child(2) {
  font-weight: 400;
  font-size: 4vw;
}
#transfer-account-box .transfer-account-box-tips p {
  background: #ffffff !important;
  height: 5vw;
  font-size: 3vw;
  line-height: 5vw;
  color: #333333;
  box-sizing: border-box;
  padding-left: 2vw;
}

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
  left: -13vw;
  top: -20vw;
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
  top: 60vw;
  left: 27.002vw;
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
  top: 60vw;
  left: 29.002vw;
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
  margin-top: 1vw;
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
  /* height: 17vw; */
}
.proccessbox li img {
  height: 10vw;
  width: auto;
  margin: 1vw auto 0px;
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
  z-index: 20;
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
  padding-top: 10vw;
  padding-bottom: 15vw;
}
/* #chat {
  margin-top: 10vw;
  margin-bottom: 15vw;
} */
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
  display: flex;
  justify-content: center;
  align-items: center;
  background: #99b9f7;
}
.head img {
  /* width: auto;
  height: 100%;
  margin: auto; */
  max-width: 100%;
  max-height: 100%;
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
  /* overflow: hidden; */
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
  border-top: 1.5vw solid transparent;
  border-right: 3vw solid #fff;
  border-bottom: 1.5vw solid transparent;
  border-left: 3vw solid transparent;
  top: 4.5vw;
  left: -6vw;
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
  border-top: 1.5vw solid transparent;
  border-right: 3vw solid transparent;
  border-bottom: 1.5vw solid transparent;
  border-left: 3vw solid #57d6dd;
  top: 4.5vw;
  right: -6vw;
}
.transfer-info-box,
.redbag-info-box {
  padding: 0px;
  width: 100% !important;
}
.transfer-boo,
.redbagboo {
  border-right: 3vw solid #fea405 !important;
}
.redbagmyboo,
.transfer-myboo {
  border-left: 3vw solid #fea405 !important;
}

/*音频css*/
.video-box {
  /* border: 1px solid red; */
  height: 10vw;
  width: 25vw;
  background: #57d6dd;
  border-radius: 2vw;
  position: relative;
  display: flex;
  align-items: center;
}
.play-status-cirle {
  display: inline-block;
  width: 3vw;
  height: 3vw;
  border-radius: 3vw;
}
.play-status-cirle-background-stop {
  background: green;
}
.play-status-cirle-background-play {
  background: red;
}

.status-show {
  /* -moz-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  -o-transform: rotate(45deg);
  transform: rotate(45deg);
  -webkit-transform: rotate(45deg); */
  display: inline-block;
  border-radius: 100%;
}
.stop-status {
  border-color: green;
}
.play-status {
  border-color: red;
}
.play-status1 {
  width: 2vw;
  height: 2vw;
}
.play-status2 {
  width: 3vw;
  height: 3vw;
}
.play-status3 {
  width: 4vw;
  height: 4vw;
}

.rightfloat {
  /* float: right; */
  flex-direction: row-reverse;
  padding-right: 2vw;
}
.leftfloat {
  padding-left: 2vw;
}
.status-show-self {
  border-left: 0.5vw solid;
  /* border-bottom: 0.5vw solid; */
}
.status-show-others {
  border-right: 0.5vw solid;
  /* border-top: 0.5vw solid; */
}

.sshow {
  display: block !important;
}
.hhide {
  display: none !important;
}
/*照片消息css*/
.msgImage {
  max-width: 60vw;
  overflow: hidden;
  /* border: 1px solid red; */
  box-sizing: border-box;
}
/* .myMsgImg{
  padding-right: 10vw;
}
.otherMsgImg{
  padding-left: 10vw;
} */
.msgImage img {
  max-width: 100%;
}
/*图片发送框input-css*/
#imageUpload {
  position: absolute;
  left: 0px;
  top: 0px;
  width: 100%;
  height: 100%;
  border: 1px solid red;
  opacity: 0;
}
</style>


