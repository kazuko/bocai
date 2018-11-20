<template>
  <div>
    <mt-header fixed="true" style="height:12vw;background:white !important;">
      <router-link to="/friend" slot="left">
        <mt-button icon="back" style="color:#666;"></mt-button>
      </router-link>
      <div slot="right" class="textbox">
        <input type="text" v-model="user" placeholder="请输入好友手机号｜账号｜昵称">
      </div>
    </mt-header>
    <div class="resultbox" v-show="user">
      <div class="resultTitle">搜索结果（共{{total}}条结果）
        <button v-if="pageNum>1" @click="nextPage">下一页</button>
      </div>
      <div class="resultList">
        <div class="oneInfo" v-for="(value,index) in searchResult" v-bind:key="index">
          <div class="headBox">
            <img :src="host + value.head" alt="头像丢失">
          </div>
          <div class="infobox">
            <div>
              <span>{{value.username}}</span>
              <span>
                <img v-for="(v,k) in value.medals" :src="host + v" alt="" v-bind:key="k">
              </span>
            </div>
            <div>
              ID:{{value.nickname}}
            </div>
          </div>
          <div class="btnBox">
            <button v-if="value.status==3" style="background:#ccc;border:1px solid #ccc;">等待验证</button>
            <button v-else @click="addFriend(value, $event)">添加好友</button>
            <button @click="tochat(value)">发送消息</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
console.log("USER_PAGEFRIEND_ADDFRIEND_VUE");
// import root from "@/config/root.js";
export default {
  data() {
    return {
      user: "",
      searchResult: [],
      total: 0,
      page: 0,
      pageNum: 0,
      host: this.GLOBAL.Host
    };
  },
  watch: {
    user: function() {
      this.page = 0;
      this.searchFriend();
    }
  },
  methods: {
    nextPage: function() {
      if (this.page < this.pageNum) {
        this.searchFriend();
      }
    },
    searchFriend: function() {
      this.total = 0;
      this.searchResult = [];
      var data = {
        send_id: this.GLOBAL.userInfo.id,
        nickname: this.GLOBAL.userInfo.nickname,
        fname: this.user,
        msg: "I am finding friends!",
        type: "searchFriends",
        page: this.page,
        con_id: this.GLOBAL.connectionId
      };
      var that = this;
      this.senddata({
        data: data,
        callback: function(result) {
          that.searchResult = result.result.rearchResult;
          that.total = result.result.totalNum;
          that.pageNum = Math.ceil(that.total / result.result.listRows);
          that.page++;
          if (that.page == that.pageNum) {
            that.pageNum = 0;
          }
        },
        handType: "user"
      });
    },
    tochat: function(value) {
      // this.$router.push({
      //   path: "/chat",
      //   query: {
      //     fid: value.id,
      //     nickname: value.username,
      //     head: value.head,
      //     status: 2
      //   }
      // });
      if (!value.stmessage) {
        this.$messagebox.alert("对方设置了拒收陌生消息！");
        return false;
      } else if (!this.GLOBAL.userInfo.stmessage) {
        this.$messagebox
          .confirm(
            "你设置了拒收陌生人消息，对方无法向你发送消息，是否现在是修改设置？"
          )
          .then(action => {
            this.$router.push("/mySystem");
          })
          .catch(err => {});
        return false;
      }
      this.GLOBAL.friendInfo = {
        info: {
          head: value.head,
          id: value.id,
          nickname: value.nickname,
          username: value.username,
          connectionID: value.connectionID,
          on_status: value.on_status
        },
        status: 2
      };
      this.$router.push("/chat");
    },
    addFriend: function(value, event) {
      var data = {
        send_id: this.GLOBAL.userInfo.id,
        get_id: value.id,
        status: 3,
        content: "我想添加你为好友！",
        type: "addFriendRequest",
        con_id: this.GLOBAL.connectionId,
        fcon_id: value.connectionID
      };
      var that = this;
      this.senddata({
        data: data,
        callback: function(result) {
          console.log("<result>");
          console.log(result);
          console.log("</result>");
          if (!result.msg) {
            that.$messagebox.alert("请求发送失败！");
          } else {
            if (result.fInfo) {
              // 更新好友列表
              that.$set(that.GLOBAL.friendLists.friends, result.fInfo.id, []);
              that.setGlobalAttribute(
                that.GLOBAL.friendLists.friends[result.fInfo.id],
                result.fInfo
              );
              // 更新消息列表
              that.$set(that.GLOBAL.msgList, result.fInfo.id, []);
              that.GLOBAL.msgList[result.fInfo.id].push(result.msg);
              // 更新好友未读消息条数
              if (that.GLOBAL.friendMsgNum[result.fInfo.id]) {
                that.GLOBAL.friendMsgNum[result.fInfo.id]++;
              } else {
                that.GLOBAL.friendMsgNum[result.fInfo.id] = 1;
              }
              that.$forceUpdate();
              let data = {
                info: result.fInfo,
                status: 1
              };
              that.setGlobalAttribute(that.GLOBAL.friendInfo, data);
              localStorage.setItem("bc_friendInfo", JSON.stringify(data));
              // 跳转到聊天页面
              that.$router.push("/chat");
            } else {
              value.status = 3;
              // 输出提示
              that.$messagebox.alert(result.msg.content);
            }
          }
        },
        handType: "user"
      });
    }
  },

  beforeCreate: function() {
    console.log("addFriend->beforeCreate...");
  },
  created: function() {
    console.log("addFriend->created...");
  },
  beforeMount: function() {
    console.log("addFriend->beforeMount...");
  },
  mounted: function() {
    console.log("addFriend->mounted...");
    this.$parent.tabbarShow = false;
  },
  beforeUpdate: function() {
    console.log("addFriend->beforeUpdate...");
    this.$parent.tabbarShow = false;
    // 检测是否登陆
    this.checkLogin();
    this.onmessage();
  },
  updated: function() {
    console.log("addFriend->updated...");
    //   判断是否登录
    this.checkLogin();
    this.onmessage();
    document.getElementsByTagName("body")[0].className = "bgc-fff";
  },
  beforeDestroy: function() {
    console.log("addFriend->beforeDestroy...");
    document.body.removeAttribute("class", "bgc-fff");
  },
  destroyed: function() {
    console.log("addFriend->destroyed...");
  }
};
</script>

<style scoped>
.textbox {
  position: absolute;
  top: 0px;
  left: 7.5vw;
  width: 85vw;
  height: 12vw;
  text-align: center;
}
.textbox input {
  width: 73vw;
  height: 10vw;
  border: 0px;
  outline: none;
  font-size: 4vw;
  color: #666;
  border-bottom: 1px solid #1ae6e6;
}
.resultbox {
  margin-top: 12.5vw;
}
.resultTitle {
  text-align: left;
  height: 10vw;
  line-height: 10vw;
  box-sizing: border-box;
  padding: 0px 15px;
  font-family: fantasy, 仿宋;
  position: fixed;
  top: 12vw;
  width: 100vw;
  background-color: white;
  font-size: 3.5vw;
}
.resultTitle button {
  border: 0px;
  background: white;
  float: right;
  height: 5vw;
  margin-top: 2.5vw;
  font-size: 3.5vw;
  color: #666;
  outline: none;
  font-family: 宋体;
}
.resultList {
  margin-top: 22vw;
}
.oneInfo {
  height: 20vw;
  box-sizing: border-box;
  padding: 0px 15px;
  border-top: 1px dashed #ccc;
}
.headBox {
  float: left;
  width: 15.5vw;
  height: 15.5vw;
  border-radius: 15.5vw;
  background: #afaffb;
  margin-top: 2.25vw;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}
.headBox img {
  width: 15vw;
  height: 15vw;
  border-radius: 15vw;
  margin-top: 2.5vw;
}
.infobox {
  float: left;
  width: 50vw;
  height: 20vw;
  text-align: left;
  font-size: 4.5vw;
}
.infobox div {
  width: 100%;
  height: 10vw;
  font-size: 4.5vw;
  color: #666;
}
.infobox div span {
  height: 10vw;
  display: block;
  float: left;
  line-height: 14vw;
}
.infobox div span img {
  width: 5vw;
  margin-top: 4vw;
  margin-left: 3px;
  float: left;
}
.btnBox {
  float: left;
  width: 26vw;
  height: 20vw;
  text-align: right;
  font-size: 4.5vw;
}
.btnBox button {
  font-size: 3.7vw;
  background-color: #1ae6e6;
  border: 1px solid #1ae6e6;
  height: 7vw;
  color: white;
  font-weight: bold;
  margin-top: 2vw;
  outline: none;
}
</style>