<template>
  <div style="border: 0px solid green;background:#E7E7E7;min-height:100vh;">
    <!-- <mt-header :title="this.GLOBAL.obj.val.nickname"> -->
    <mt-header id="header" title="系统通知" fixed="true" style="height:10vw;background:#57D6DD;">
      <router-link to="/friend" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
    </mt-header>
    <div class="pageContent chat" id="centerbox">
      <p v-if="!this.GLOBAL.systemNoticePage" class="tips-box1"><span style="text-decoration:underline;" @click="getRecords()">获取更多记录</span></p>
      <p v-if="endRecord" class="tips-box2"><span>没有更多了</span></p>
      <div v-for="(item,index) in this.GLOBAL.msgList.systemNotice" :key="index">
        <!-- 聊条消息 -->
        <div class="list">
          <div class="msgText">
            <h2 style="text-align:center;font-weight:bold !important;">{{checkContent(item.content)}}</h2>
            <p v-html="checkContent(item.content,false)"></p>
            <p class="time-box">{{getDate(item)}}</p>
          </div>
        </div>
        <!-- 时间 -->
      </div>
    </div>
  </div>
</template>

<script>
console.log("COMMON_CHAT_VUE");
export default {
  data() {
    return {
      refreshStatus: true,
      allPage: 0, //聊天记录总页数
      endRecord: false,
      host: this.GLOBAL.Host, //主机或者域名

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
  mounted: function() {
    // 隐藏底部导航栏
    this.$parent.tabbarShow = false;
    // console.log(this.GLOBAL.systemNoticePage);
    var that = this;
    // // 检测是否登陆
    this.checkLogin(function() {
      that.changeReadStatus();
    });
    this.onmessage();
    this.refresh({
      elementId: "centerbox",
      type: "up",
      callback: function(obj) {
        if (that.refreshStatus && that.GLOBAL.systemNoticePage) {
          that.refreshStatus = false;
          that.oldScrollTop = obj.scrollHeight;
          that.getRecords();
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
    this.$nextTick(() => {
      let obj = document.getElementById("centerbox");
      obj.scrollTop = obj.scrollHeight;
    });
  },
  updated: function() {
    if (this.updateStatus) {
      this.$nextTick(() => {
        let obj = document.getElementById("centerbox");
        obj.scrollTop = obj.scrollHeight;
      });
    }
    this.changeReadStatus();
  },
  computed: {},
  methods: {
    // 获取通知记录
    getRecords() {
      if (!this.endRecord) {
        let data = {
          type: "getChatRecords",
          user_id: this.GLOBAL.userInfo.id,
          frined_id: -10086,
          con_id: this.GLOBAL.connectionId,
          page: this.GLOBAL.systemNoticePage
        };
        console.log(data);
        let that = this;
        this.senddata({
          data: data,
          callback: function(respone) {
            // console.log(respone);
            if (Object.keys(respone.recordsList).length) {
              console.log("sssss");
              that.$forceUpdate();
              that.refreshStatus = true;
              // 将聊天记录倒序
              // respone.recordsList.reverse();
              if (respone.total) {
                // 保存30天内的聊天记录总页数
                that.allPage = respone.total;
                // 重置当前好友的消息记录
                that.GLOBAL.msgList.systemNotice = new Array();
              }
              for (var i in respone.recordsList) {
                that.GLOBAL.msgList.systemNotice.unshift(
                  respone.recordsList[i]
                );
              }

              // 当前页数自增
              that.GLOBAL.systemNoticePage++;
              // 判断是否获取完30天内的所有聊天记录
              if (respone.end || that.GLOBAL.systemNoticePage > that.allPage) {
                that.endRecord = true;
              }
              that.$nextTick(function() {
                var obj = document.querySelector("#centerbox");
                obj.scrollTop = obj.scrollHeight - that.oldScrollTop;
              });
            } else {
              console.log("fff");
              that.endRecord = true;
            }
          },
          callbackFlag: "responeGetChatRecords",
          handType: "user"
        });
      }
    },
    /**
     * 将时间戳转换为字符串日期
     */
    getDate(item) {
      //   console.log(item);
      // console.log(item.time);
      return this.showDate(null, null, item.time);
    },
    checkContent(content, title = true) {
      // console.log(content);
      var reg = new RegExp(/\(@title:(.*)@\)(.*)/gm);
      var result = reg.exec(content);
      // console.log(result);
      if (title) {
        return result[1];
      } else {
        return result[2];
      }
    },
    /**
     * 检测消息的阅读状态
     */
    checkReadStatus() {
      var res = false;
      if (this.GLOBAL.msgList.systemNotice) {
        for (var index in this.GLOBAL.msgList.systemNotice) {
          if (
            this.GLOBAL.msgList.systemNotice[index] &&
            !this.GLOBAL.msgList.systemNotice[index].flag
          ) {
            this.GLOBAL.msgList.systemNotice[index].flag = 1;
            this.GLOBAL.msgLength--;
            if (!res) {
              res = true;
            }
          }
        }
      }
      return res;
    },
    /**
     * 修改消息的阅读状态
     */
    changeReadStatus: function() {
      // 判断是否存在未读消息
      if (this.checkReadStatus()) {
        // console.log("changeStatus");
        // 发送修改消息阅读状态信号给服务器
        var data = {
          send_id: -10086,
          get_id: this.GLOBAL.userInfo.id,
          // msg: "change the readStatus",
          type: "readStatus",
          con_id: this.GLOBAL.connectionId
        };
        this.senddata({ data: data, handType: "user" });
      }
    }
  }
};
</script>

<style scoped>
.time-box {
  text-align: center;
  font-size: 3vw;
  color: #999;
  /* margin-top: 0px; */
  /* margin-bottom: 5vw; */
}
.tips-box1 {
  text-align: center;
  font-size: 3vw;
  color: #888;
  margin-top: 1vw;
}
.tips-box2 {
  text-align: center;
  font-size: 3vw;
  color: #888;
  margin-top: 1vw;
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
  box-sizing: border-box;
  /* padding-bottom: 15vw; */
}
/* #chat {
  margin-top: 10vw;
  margin-bottom: 15vw;
} */
.list {
  padding: 10px 0;
  flex-wrap: nowrap;
  display: flex;
  justify-content: center;
  align-items: center;
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
  /* padding: 1.5vw 2.5vw; */
  /* max-width: 60%; */
  width: 90%;
  background-color: #fff;
  border-radius: 2vw;
  position: relative;
  /* margin-left: 3vw; */
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
  text-align: center;
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
</style>


