<template>
  <div>
    <mt-header title="发红包" fixed='true' id="redbagbox">
      <router-link slot="left" :to="this.$route.query.rType?'/chatroom':'/chat'">
        <mt-button icon='back'>返回</mt-button>
      </router-link>
    </mt-header>
    <section class="bodybox">
      <div class="jinebox redbox">
        <span>金额</span>
        <input type="number" placeholder="0.00" v-model="money">
        <span>元</span>
      </div>
      <div class="liuyanbox redbox">
        <span>留言</span>
        <input type="text" style="width:86%;font-weight:bold;" placeholder="恭喜发财，大吉大利" v-model="remark">
      </div>
      <div class="jinebox redbox" v-if="this.$route.query.rType">
        <span>红包个数</span>
        <input type="number" placeholder="0.00" v-model="redNum">
        <span>元</span>
      </div>
      <div class="money">
        <span>￥</span>
        <span id="moneyNum" v-if="money">{{money}}</span>
        <span id="moneyNum" v-else>0.00</span>
      </div>
      <div class="btnbox" :class="(money ? 'active' : '')" @click="sendredbag">塞钱进红包</div>
    </section>

    <section v-show="show">
      <div id="backbox" class="backbox"></div>
      <div id="input-password-box" class="input-password-box">
        <div class="title-box">
          <img :src="this.GLOBAL.Host + this.GLOBAL.userInfo.head" alt="">
          <span class="title-t">请输入交易密码</span>
          <span class="canclebtn" @click="cancelTrading">x</span>
        </div>
        <div>
          <p class="redbag-name">红包</p>
          <p class="redbag-money">￥
            <span>{{money}}</span>
          </p>
        </div>
        <div class="inputbox">
          <input id="tradingbox" type="password" v-model="trading">
        </div>
      </div>
    </section>
  </div>
</template>
<script>
export default {
  data() {
    return {
      money: "", //红包金额
      remark: "恭喜发财，大吉大利", //红包留言
      trading: "", //交易密码
      show: false,
      redNum: 1
    };
  },
  watch: {
    money: function() {
      this.money = Math.round(this.money * 100) / 100;
    },
    trading: function() {
      if (this.trading.length == 6) {
        this.show = false;
        let that = this;
        if (this.trading == this.GLOBAL.userInfo.trading) {
          console.log(this.GLOBAL.friendInfo);
          let data = {
            send_id: this.GLOBAL.userInfo.id, //本人id
            get_id: this.GLOBAL.friendInfo.info
              ? this.GLOBAL.friendInfo.info.id
              : "", //接收方id
            type: "sendRedBagToFriend", //请求类型
            gold: this.money, //红包金额
            remark: this.remark, //红包说明备注
            trading: this.trading, //交易密码
            send_time: this.showDate(), //红包发送时间
            status: this.GLOBAL.friendInfo.status
              ? this.GLOBAL.friendInfo.status
              : "", //好友的类型：1表示好朋友；2表示陌生人
            con_id: this.GLOBAL.connectionId, //本人的链接资源标识符
            fcon_id: this.GLOBAL.friendInfo.info
              ? this.GLOBAL.friendInfo.info.connectionID
              : "", //接收方的链接资源标识符
            redType: this.$route.query.rType ? 2 : 1, //红包类型：1表示个人红包，2表示群发红包
            recive_num: this.$route.query.rType ? this.redNum : 1 //红包接收人数
          };
          this.senddata({
            data: data,
            callback: function(respone) {
              if (respone.status) {
                // 更新本地存储信息
                that.GLOBAL.userInfo.gold -= data.gold;
                localStorage.setItem(
                  "bc_userInfo",
                  JSON.stringify(that.GLOBAL.userInfo)
                );
                if (that.$route.query.rType) {
                  var len = Object.keys(that.GLOBAL.chatRoomMsgList).length;
                  that.$set(that.GLOBAL.chatRoomMsgList, len, {});
                  respone.msg["head"] = that.GLOBAL.userInfo.head;
                  respone.msg["nickname"] = that.GLOBAL.userInfo.nickname;
                  that.setGlobalAttribute(
                    that.GLOBAL.chatRoomMsgList[len],
                    respone.msg
                  );
                  that.$router.push("/chatroom");
                } else {
                  console.log(data);
                  if (!that.GLOBAL.msgList[data.get_id]) {
                    that.$set(that.GLOBAL.msgList, data.get_id, {});
                  }
                  var len = Object.keys(that.GLOBAL.msgList[data.get_id])
                    .length;
                  that.$set(that.GLOBAL.msgList[data.get_id], len, {});
                  // console.log(respone.msg);
                  that.setGlobalAttribute(
                    that.GLOBAL.msgList[data.get_id][len],
                    respone.msg
                  );
                  // console.log(that.GLOBAL.msgList)
                  that.$router.push("/chat");
                }
              } else {
                console.log(respone);
                that.$messagebox.alert(respone.errMsg).then(action => {});
              }
            },
            handType: "user"
          });
        } else {
          this.$messagebox.alert("密码错误！").then(action => {
            that.show = true;
            that.trading = "";
            that.$nextTick(() => {
              document.getElementById("tradingbox").focus();
            });
          });
        }
      } else {
        this.trading = this.trading.substring(0, 6);
      }
      console.log(this.trading);
    }
  },
  beforeCreate: function() {
    console.log(this.GLOBAL.userInfo);
    if (!this.GLOBAL.userInfo.trading) {
      this.$messagebox
        .confirm("您目前还没有设置交易密码，请前往“我的设置”设置交易密码")
        .then(action => {
          this.$router.push("/transPsd");
        })
        .catch(err => {
          this.$router.push("/chat");
        });
    }
  },
  mounted: function() {
    this.checkLogin();
    this.onmessage();
    this.$parent.tabbarShow = false;
  },
  methods: {
    sendredbag: function() {
      if (this.money) {
        if (this.$route.query.rType) {
          if (this.money < this.redNum) {
            this.$messagebox.alert("红包金额不得小于红包个数！");
            return false;
          }
        }
        if (this.GLOBAL.userInfo.gold >= this.money) {
          this.show = true;
          this.$nextTick(() => {
            document.getElementById("tradingbox").focus();
          });
        } else {
          this.$messagebox.alert(
            "您的余额为" + this.GLOBAL.userInfo.gold + ",不足以发红包！"
          );
        }
      }
    },
    cancelTrading: function() {
      this.show = false;
    }
  }
};
</script>
<style scoped>
.inputbox input {
  height: 10vw;
  width: 50vw;
  margin: auto;
  font-size: 5vw;
  border: 0.3vw solid #ccc;
  outline: none;
}
.redbag-money {
  height: 13vw;
  line-height: 13vw;
  font-weight: bold;
  font-size: 5vw;
}
.redbag-money span {
  font-size: 7vw;
}
.redbag-name {
  height: 10vw;
  line-height: 14vw;
  font-size: 4.5vw;
  color: black;
  font-family: Cambria, Cochin, Georgia, Times, "Times New Roman", serif;
}
.backbox {
  background: black;
  opacity: 0.5;
  filter: opacity(5);
  position: fixed;
  top: 0vh;
  left: 0px;
  z-index: 99;
  width: 100vw;
  height: 100vh;
}
.input-password-box {
  width: 80vw;
  height: 50vw;
  position: fixed;
  left: 10vw;
  top: 15vw;
  z-index: 100;
  background: white;
}
.title-box {
  border-bottom: 0.1px solid rgb(144, 247, 144);
  display: flex;
  padding: 0 1vw;
  box-sizing: border-box;
  height: 10vw;
  line-height: 10vw;
  color: #999;
  position: relative;
}
.title-box img {
  width: auto;
  height: 9vw;
}
.title-box span {
  margin-left: 2vw;
  font-size: 4.5vw;
}
.canclebtn {
  display: inline-block;
  /* border: 1px solid red; */
  position: absolute;
  right: 0px;
  width: 7vw;
}
#redbagbox {
  background: #57d6dd !important;
}
.bodybox {
  margin: 14vw auto 0px;
  width: 95%;
}
.redbox {
  background: white;
  height: 15vw;
  line-height: 15vw;
  box-sizing: border-box;
  padding-left: 3vw;
  margin-top: 0.4vw;
  text-align: left;
}
.redbox span {
  font-size: 4vw;
  font-weight: bold;
  color: #666;
}
.redbox input {
  width: 80%;
  height: 10vw;
  margin-top: 2.5vw;
  border: 0px;
  outline: none;
  text-align: right;
  color: #666;
  font-size: 4.5vw;
}
.money {
  height: 20vw;
  line-height: 20vw;
  font-size: 10vw;
  font-weight: bold;
}
.money span {
  font-size: 5vw;
}

#moneyNum {
  font-size: 10vw;
}
.btnbox {
  height: 15vw;
  line-height: 15vw;
  background: #d9a59c;
  font-size: 5vw;
  font-weight: 400;
  color: white;
  font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
  letter-spacing: 0.7vw;
}
.active {
  background: #b24b38;
}
</style>
