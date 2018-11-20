<template>
  <div style="border: 0px solid green;background:#E7E7E7;min-height:100vh;">
    <mt-header id="header" title="聊天室" fixed="true" style="height:10vw;background:#57D6DD;">
      <router-link to="/" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
    </mt-header>
    <div class="pageContent chat" id="centerbox">
      <p v-if="!getMore&&this.GLOBAL.chatRoomPage" style="text-align:center;font-size:3vw;color:#888;">没有更多了</p>
      <div v-for="(item,index) in this.GLOBAL.chatRoomMsgList" :key="index">
        <!-- 时间 -->
        <p style="text-align:center;font-size:3vw;color:#999;margin-top:10px;" v-if="showchatRecordsDate(index)">{{showchatRecordsDate(index)}}</p>
        <!-- 聊条消息 -->
        <div v-if="item.status >= 0" style="display: flex;" :class="item.send_id==self_id?('myList list'):('list')">
          <!-- 我方头像 -->
          <!-- <div class="head" v-if="item.send_id==self_id">
              <img v-bind:src="host + selfHead">
            </div> -->
          <!-- 对方头像 -->
          <div>
            <div class="head">
              <img v-bind:src="host + item.head">
            </div>
          </div>
          <!-- 红包消息 -->
          <!-- <span class="usernickname-box">{{item.nickname}}</span> -->
          <div v-if="item.status == 1" class="msgText redbag-info-box">
            <p @click="lookRedbag(item, index)" id="transfer-account-box">
              <!-- {{item.id}} -->
              <span class="transfer-account-box-info" :style="checkRedBag(item.content, 0)?'background-color:#f5c97c;':''">
                <img :src="checkRedBag(item.content, 1)" alt="" style="width:9.5vw;height:12vw">
                <span style="width:auto;">
                  <p style="font-size:4vw;">{{checkRedBag(item.content, 2)}}</p>
                  <p style="font-size:3vw;">{{checkRedBag(item.content, 3)}}</p>
                </span>
              </span>
              <span class="transfer-account-box-tips">
                <p>红包</p>
              </span>
            </p>
            <div :class="item.send_id==self_id?('myboo transfer-myboo'):('boo transfer-boo')" :style="checkRedBag(item.content, 0)?item.send_id==self_id?'border-left-color:#f5c97c !important;':'border-right-color:#f5c97c !important;':''"></div>
            <!-- 我方发送失败 -->
            <span class="send_fail_tips" v-if="item.send_id==self_id && !item.send_status==2">!</span>
          </div>
          <!-- 语音消息 -->
          <div v-else-if="item.status == 2" class="msgText">
            <div @click="playVocie(item.content, index)" class="video-box" :class="item.send_id==self_id?'rightfloat':'leftfloat'">
              <audio @ended="endPlay()" :src="host + item.content" :id="'audioPlayer'+index" preload="preload"></audio>
              <span class="play-status-cirle play-status-cirle-background-stop" :id="'video-box-'+index"></span>
              <span class="status-show stop-status play-status1" :class="item.send_id==self_id?'status-show-self':'status-show-others'" :id="'video-box-'+index+'-1'"></span>
              <span class="status-show stop-status play-status2" :class="item.send_id==self_id?'status-show-self':'status-show-others'" :id="'video-box-'+index+'-2'"></span>
              <span class="status-show stop-status play-status3" :class="item.send_id==self_id?'status-show-self':'status-show-others'" :id="'video-box-'+index+'-3'"></span>
            </div>
            <div :class="item.send_id==self_id?('myboo'):('boo')"></div>
            <span class="send_fail_tips" v-if="item.send_id==self_id&&item.send_status == 2">!</span>
          </div>
          <!-- 照片消息 -->
          <div v-else-if="item.status == 3" class="msgText">
            <p class="msgImage" :class="item.send_id==self_id?'myMsgImg':'otherMsgImg'">
              <img v-if="item.send_status==3" :src="item.content" alt="有内涵哈！">
              <img v-else :src="host + item.content" alt="有内涵哈！">
            </p>
            <div :class="item.send_id==self_id?('myboo'):('boo')"></div>
            <!-- 我方发送失败 -->
            <span class="send_fail_tips" v-if="item.send_id==self_id&&item.send_status == 2">!</span>
            <span class="send_fail_tips" v-else-if="item.send_status == 3">上传中</span>
          </div>
          <!-- 视频消息 -->
          <div v-else-if="item.status == 4"></div>
          <!-- 普通消息(文字聊天) -->
          <div v-else class="msgText">
            <p v-html="item.content"></p>
            <div :class="item.send_id==self_id?('myboo'):('boo')"></div>
            <!-- 我方发送失败 -->
            <span class="send_fail_tips" v-if="item.send_id==self_id&&item.send_status == 2">!</span>
          </div>
        </div>
        <!-- 系统提示 -->
        <div v-else class="addTips">
          <p style="text-align:center;color:#999;font-size:4vw;">{{item.content}}</p>
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
          <input v-if="voice" class="voiceStartBtn" id="audioBtn" type="button" value="按住开始说话">
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
            <router-link :to="{path:'/sendredbag', query:{rType:true}}">
              <img src="static/icon-red@chat.png" alt="">
              <span>发红包</span>
            </router-link>
          </li>
          <!-- <li>
            <router-link to="/transacount">
              <img src="static/icon-transfer.png" alt="">
              <span>转账</span>
            </router-link>
          </li> -->
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
          <img :src="host + this.GLOBAL.userInfo.head" alt="">
        </div>
        <p class="user-name-box">{{this.GLOBAL.userInfo.nickname}}</p>
        <p class="user-title-box">给您发了一个红包</p>
        <p class="user-remark-box">恭喜发财，大吉大利</p>
      </div>
      <span class="close-btn" @click="closeRedbagBox">x</span>
      <div class="max-circle-box"></div>
      <span class="min-circle-box-back"></span>
      <span class="min-circle-box" @click="reciveRedbag">拆红包</span>
    </div>
    <section id="show-record-box"></section>
  </div>
</template>

<script>
console.log("COMMON_CHAT_VUE");
export default {
  data() {
    return {
      timer: null,
      // voiceSrc: "",
      voice_index: "", //展示
      audio_id: "",
      playN: 0,
      playF: false,
      redbagsrc: [], //保存红包领取状态图片的路径
      // redbagtips: [], //保存红包领取状体的提示
      // MsgList: [], //消息队列

      // transferSrc: [], //保存转账的图片路径
      // red_ids: [],
      red_index: "", //拆红包的消息索引
      red_id: "", //拆红包的id
      redbagShow: false, //显示拆红包
      myMsg: "", // 发送的消息
      // head: this.GLOBAL.friendInfo.info.head, //朋友投降
      // friendInfo: this.GLOBAL.friendInfo, //朋友信息
      host: this.GLOBAL.Host, //主机或者域名
      // selfHead: this.GLOBAL.userInfo.head, //本人头像
      voice: false, //是否为语音模式
      funboxshow: false, //是否显示附加功能
      faceshow: false, //是否显示表情包
      // 表情包
      faces: this.GLOBAL.updateFile.faces,
      self_id: this.GLOBAL.userInfo.id,
      page: 0,
      status: 0,
      msgIndex: null,
      refreshFlag: true,
      getMore: true,
      oldScrollTop: 0,
      robRedInfo: null
    };
  },
  mounted: function() {
    // 隐藏底部导航栏
    this.$parent.tabbarShow = false;
    // 检测是否登陆
    let that = this;
    this.checkLogin(function() {
      that.getMsgList(false);
    });
    this.onmessage();
    this.refresh({
      elementId: "centerbox",
      type: "up",
      callback: function(obj) {
        that.oldScrollTop = obj.scrollHeight;
        that.getMsgList();
      }
    });
    this.scroll({
      elementId: "centerbox",
      type: "bottom",
      scrollingCallback: function() {
        that.refreshFlag = false;
      },
      scrollEndCallback: function() {
        that.refreshFlag = true;
      }
    });
    this.$nextTick(() => {
      let obj = document.querySelector("#centerbox");
      obj.scrollTop = obj.scrollHeight;
    });
  },
  updated: function() {
    if (this.refreshFlag) {
      this.$nextTick(() => {
        let obj = document.getElementById("centerbox");
        obj.scrollTop = obj.scrollHeight;
      });
    }
  },
  beforeDestroy: function() {
    let data = {
      type: "leaveChatRoom",
      con_id: this.GLOBAL.connectionId
    };
    this.senddata({ data: data, handType: "user" });
  },
  computed: {},
  methods: {
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
      var reader = new FileReader();
      //将文件以Data URL形式读入页面
      reader.readAsDataURL(file);
      var previewSrc = "";
      reader.onload = function(e) {
        // var result = document.getElementById("result");
        // //显示文件
        // result.innerHTML = '<img src="' + this.result + '" alt="" />';
        previewSrc = this.result;
      };
      let msgData = {
        id: "",
        send_id: this.GLOBAL.userInfo.id,
        send_time: this.showDate(),
        status: 3,
        content: previewSrc,
        head: this.GLOBAL.userInfo.head,
        nickname: this.GLOBAL.userInfo.nickname,
        send_status: 3
      };
      var len = Object.keys(this.GLOBAL.chatRoomMsgList).length;
      this.msgIndex = len;
      this.$set(this.GLOBAL.chatRoomMsgList, len, msgData);
      // this.setGlobalAttribute(this.GLOBAL.chatRoomMsgList[len], msgData);
      // console.log(that.MsgList);
      // that.sendmsg(msgCon);
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
              that.$forceUpdate();
              that.GLOBAL.chatRoomMsgList[that.msgIndex].send_status = 1;
              that.GLOBAL.chatRoomMsgList[that.msgIndex].content = resp.src;
              msgData.content = resp.src;
              console.log(msgData);
              that.sendmsg(msgData);
            } else {
              that.GLOBAL.chatRoomMsgList[that.msgIndex].send_status = 2;
              // that.msgN++;
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
    // setPlay(index){
    //   this.play[index] = false;
    // },
    goto(topage) {
      this.$router.push(topage);
    },
    /**
     * 获取消息列表
     */
    getMsgList: function(pageF = true) {
      if (this.getMore) {
        if (!pageF) {
          var len = Object.keys(this.GLOBAL.chatRoomMsgList).length;
          if (len) {
            // this.msgN = len;.
            var notice = {
              type: "comimgToChatRoom",
              con_id: this.GLOBAL.connectionId
            };
            this.senddata({ data: notice, handType: "user" });
            return false;
          }
        }
        let data = {
          type: "getChatRoomMsgList",
          con_id: this.GLOBAL.connectionId,
          page: this.GLOBAL.chatRoomPage
        };
        let that = this;
        this.senddata({
          data: data,
          callback: Response => {
            if (Response.status) {
              that.$forceUpdate();
              if (Object.keys(Response.MsgList).length) {
                Response.MsgList.forEach(element => {
                  that.GLOBAL.chatRoomMsgList.unshift(element);
                });
                that.GLOBAL.chatRoomPage++;
                if(Response.end){
                  that.getMore = false;
                }
                that.$nextTick(function() {
                  var obj = document.querySelector("#centerbox");
                  obj.scrollTop = obj.scrollHeight - that.oldScrollTop;
                });
              } else {
                that.getMore = false;
              }
            } else if (Response.type == "responeGetChatRoomMsgList") {
              if (Response.msg) {
                that.$messagebox.alert(Response.msg);
              } else {
                that.$messagebox.alert("网络堵塞，请检查网络是否正常！");
              }
            }
          },
          callbackFlag: "responeGetChatRoomMsgList",
          handType: "user"
        });
      } else {
      }
    },
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
      // let data = {
      //   type: "reciveRedBagFromFriend",
      //   send_id: this.GLOBAL.userInfo.id,
      //   con_id: this.GLOBAL.connectionId,
      //   red_id: this.red_id,
      //   red_send_id: this.GLOBAL.friendInfo.info.id,
      //   red_send_con: this.GLOBAL.friendInfo.info.connectionID
      // };
      let that = this;
      // 发送拆红包请求
      this.senddata({
        data: this.robRedInfo,
        callback: function(response) {
          console.log();
          if (response.status) {
            // 更新本地金币池
            that.GLOBAL.userInfo.gold = response.gold;
            // 更新本地用户信息
            localStorage.setItem(
              "bc_userInfo",
              JSON.stringify(that.GLOBAL.userInfo)
            );
            // 更新本地红包信息的收取状态
            that.GLOBAL.chatRoomMsgList[that.red_index]["content"] =
              response.content;

            // 隐藏红包弹窗
            that.redbagShow = false;
            // 跳转红包详情
            that.$router.push({
              path: "/redbagdetail",
              query: { red_id: that.robRedInfo.red_id, chatRoom: true }
            });
          } else {
            // 输出提示
            that.$messagebox.alert(response.msg).then(action => {
              that.redbagShow = false;
            });
            try {
              let rege = /^(false:|true:|)\[(.*)\](\(@redbag[0-9]+redbag@\).*)/;
              let result = rege.exec(
                that.GLOBAL.chatRoomMsgList[that.red_index].content
              );
              if (response.code == 303) {
                if (
                  result[2].indexOf("-o-" + that.GLOBAL.userInfo.id + "-") ===
                  -1
                ) {
                  that.GLOBAL.chatRoomMsgList[that.red_index]["content"] =
                    result[1] +
                    "[" +
                    result[2] +
                    "o-" +
                    that.GLOBAL.userInfo.id +
                    "-]" +
                    result[3];
                }
              } else if (response.code != 505) {
                if (response.redStatus == 1) {
                  if (!result[1]) {
                    that.GLOBAL.chatRoomMsgList[that.red_index]["content"] =
                      "false:" +
                      that.GLOBAL.chatRoomMsgList[that.red_index]["content"];
                  }
                } else {
                  if (!result[1]) {
                    that.GLOBAL.chatRoomMsgList[that.red_index]["content"] =
                      "true:" +
                      that.GLOBAL.chatRoomMsgList[that.red_index]["content"];
                  }
                }
              }
              console.log("PPPPPPPPPPPPPPPPPPPPPPPPPPPPP");
              console.log(that.GLOBAL.chatRoomMsgList[that.red_index].content);
              console.log("PPPPPPPPPPPPPPPPPPPPPPPPPPPPP");
            } catch (e) {
              console.log(e);
            }
          }
        },
        callbackFlag: "responseChatRoomRedbag",
        handType: "user"
      });
    },
    /**
     * 查看红包详情或者显示拆红包弹窗
     */
    lookRedbag: function(item, index) {
      try {
        let rege = /^(false:|true:|)(\[.*\])\(@redbag([0-9]+)redbag@\)(.*)/;
        let result = rege.exec(item.content);
        if (result[1]) {
          // 显示已抢完
          // if (result[2].indexOf("-" + this.GLOBAL.userInfo.id + "-") === -1) {
          //   // 当前用户不在抢到红包的人员队列中
          //   this.$messagebox
          //     .confirm("当前红包已被领完或者已经过期！去看看谁手势最好？")
          //     .then(action => {
          //       this.$router.push({
          //         path: "/redbagdetail",
          //         query: { red_id: Math.abs(result[3]) }
          //       });
          //     })
          //     .catch(err => {});
          // } else {
          //   // 当前用户在抢到红包的人员队列中。跳转红包详情
          //   this.$router.push({
          //     path: "/redbagdetail",
          //     query: { red_id: Math.abs(result[3]) }
          //   });
          // }

          // 已过期或者已被领完则直接跳转红包详情页
          this.$router.push({
            path: "/redbagdetail",
            query: { red_id: Math.abs(result[3]), chatRoom: true }
          });
        } else {
          // 未显示已抢完或者过期，发送抢红包信息
          if (result[2].indexOf("-o-" + this.GLOBAL.userInfo.id + "-") === -1) {
            // 当前用户不在抢到红包的人员队列中，向后抬发送数据抢红包
            this.robRedInfo = {
              type: "chatRoomRedbag",
              user_id: this.GLOBAL.userInfo.id,
              // head: this.GLOBAL.userInfo.head,
              // nickname: this.GLOBAL.userInfo.nickname,
              msg_id: item.id,
              red_id: Math.abs(result[3]),
              con_id: this.GLOBAL.connectionId
            };
            this.red_index = index;
            // this.senddata(data, function(response) {}, "responseChatRoomRedbag");
            this.redbagShow = true;
          } else {
            // 当前用户在抢到红包的人员队列中。跳转红包详情
            this.$router.push({
              path: "/redbagdetail",
              query: { red_id: Math.abs(result[3]), chatRoom: true }
            });
          }
        }
      } catch (e) {
        console.log(e);
      }
    },
    /**
     * 检测消息是否为红包信息
     * content: 消息内容
     * check: 为真时返回布尔类型，否则返回检测后的内容
     */
    checkRedBag: function(content, flag) {
      // console.log(content);
      try {
        let rege = /^(false:|true:|)(\[.*\]|)\(@redbag([0-9]+)redbag@\)(.*)/;
        // console.log(rege);
        let result = rege.exec(content);
        // console.log(result);
        switch (flag) {
          case 0:
            // 检测改红包是否已被领取完或者已过期或者被当前用户领取过
            if (
              result[1] ||
              result[2].indexOf("-o-" + this.GLOBAL.userInfo.id + "-") !== -1
            ) {
              console.log("true");
              return true;
            } else {
              console.log("false");
              return false;
            }
            break;
          case 1:
            // 检测当前用户是否领取过
            if (
              result[2].indexOf("-o-" + this.GLOBAL.userInfo.id + "-") === -1
            ) {
              return "static/to_recive_redbag.png";
            } else {
              return "static/has_recived_redbag.png";
            }
            break;
          case 2:
            // 获取红包备注
            return result[4];
            break;
          case 3:
            // 显示红包提示语
            if (
              result[2].indexOf("-o-" + this.GLOBAL.userInfo.id + "-") === -1
            ) {
              if (result[1] == "false:") {
                return "已被抢完！";
              } else if (result[1] == "true:") {
                return "已过期！";
              } else {
                return "抢红包！";
              }
            } else {
              return "已领取！";
            }
            break;
          default:
        }
      } catch (e) {
        console.log(e);
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
      this.voice = false;
    },
    // 附加功能显示或收起
    showFun: function() {
      this.funboxshow = !this.funboxshow;
      this.faceshow = false;
      this.voice = false;
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
              console.log(respone);
              if (respone) {
                try {
                  respone = JSON.parse(respone);
                  console.log("reponeVoice=>{");
                  console.log(respone);
                  console.log("}");
                  if (respone.status) {
                    that.$forceUpdate();
                    // that.$messagebox.alert(respone.src);
                    let msgCon = {
                      id: "",
                      send_id: that.GLOBAL.userInfo.id,
                      send_time: that.showDate(),
                      status: 2,
                      content: respone.src,
                      head: that.GLOBAL.userInfo.head,
                      nickname: that.GLOBAL.userInfo.nickname,
                      send_status: 1
                    };
                    var len = Object.keys(that.GLOBAL.chatRoomMsgList).length;
                    that.msgIndex = len;
                    that.$set(that.GLOBAL.chatRoomMsgList, len, msgCon);
                    // that.setGlobalAttribute(
                    //   that.GLOBAL.chatRoomMsgList[len],
                    //   msgCon
                    // );
                    // console.log(that.MsgList);
                    that.sendmsg(msgCon);
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
            },
            audioFail: function(err) {
              console.log(err.name + ":" + err.message);
              that.$messagebox
                .alert("当前设备不支持语音输入！")
                .then(action => {
                  that.voice = !that.voice;
                });
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
          this.GLOBAL.chatRoomMsgList[index - 1].send_time,
          this.GLOBAL.chatRoomMsgList[index].send_time
        );
      } else {
        // if (this.MsgList[index].status) {
        // 聊条消息
        // if (this.MsgList[index].chatType) {
        //   // 当前聊天消息，第一条
        //   return this.showDate(
        //     this.MsgList[index].send_time
        //   );
        // } else {
        // 历史聊天消息,第一条
        return this.showDate("", this.GLOBAL.chatRoomMsgList[index].send_time);
        // }
        // } else {
        //   // 系统好友提醒消息
        //   return this.showDate(
        //     "",
        //     "",
        //     this.MsgList[index].send_time
        //   );
        // }
      }
    },
    /**
     * 发送消息
     */
    send: function() {
      if (this.myMsg) {
        // 更新本地的消息队列
        // let time = this.updateMsgList(this.myMsg);
        let msg = {
          id: "",
          send_id: this.GLOBAL.userInfo.id,
          send_time: this.showDate(),
          status: this.status,
          content: this.myMsg,
          head: this.GLOBAL.userInfo.head,
          nickname: this.GLOBAL.userInfo.nickname,
          send_status: 1
        };
        var len = Object.keys(this.GLOBAL.chatRoomMsgList).length;
        this.msgIndex = len;
        this.$set(this.GLOBAL.chatRoomMsgList, len, msg);
        this.$nextTick(function() {
          let obj = document.getElementById("centerbox");
          obj.scrollTop = obj.scrollHeight;
        });
        // this.MsgList.push(msg);
        this.sendmsg(msg);
        this.myMsg = "";
        document.getElementById("MSGCONTENT").innerHTML = "";
        this.faceshow = false;
        this.funboxshow = false;
      }
    },
    sendmsg(msg) {
      let data = {
        msg: msg,
        type: "ChatRoomMsg",
        con_id: this.GLOBAL.connectionId
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(respone) {
          if (respone.status) {
            that.GLOBAL.chatRoomMsgList[that.msgIndex].id = respone.id;
          } else {
            that.GLOBAL.chatRoomMsgList[that.msgIndex].send_status = 2;
          }
        },
        handType: "user"
      });
    }
  }
};
</script>

<style scoped>
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

/*转账或者红包css*/
#transfer-account-box {
  border-radius: 2vw !important;
  overflow: hidden;
}
#transfer-account-box .transfer-account-box-info {
  background: #fea405;
  display: flex;
  align-items: center;
  color: #ffffff;
  height: 15vw;
}
#transfer-account-box .transfer-account-box-info img {
  width: 9vw !important;
  height: 12vw !important;
  margin: 0vw 3vw;
}
#transfer-account-box .transfer-account-box-info span p:nth-child(1) {
  /* height: ; */
  font-size: 5vw;
  height: 7vw;
  /* line-height: 12vw; */
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
/*表情包css*/
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
/*扩展功能css*/
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
/*消息发送css*/
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
  left: -2vw;
  font-size: 6vw;
  font-weight: bold;
}
.user {
  /* height: 15vw; */
  position: fixed;
  bottom: 0px;
  /* top: 100vh; */
  left: 0px;
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
  position: absolute;
  /* position: fixed; */
  left: 0px;
  top: 0px;
  width: 100vw;
  /* padding: 5px 10px; */
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
  justify-items: center;
}
.head img {
  width: auto;
  height: 100%;
  margin: auto;
}
.usernickname-box {
  font-size: 2vw;
  width: 11vw;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
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
</style>


