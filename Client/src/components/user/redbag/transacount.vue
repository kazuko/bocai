<template>
  <div>
    <mt-header title="转账" style="" id="transacountheader">
      <router-link slot="left" to="/chat">
        <mt-button icon="back" style="color:#666;font-size:7vw;font-weight:600;"></mt-button>
      </router-link>
      <div slot="right" style="color: #666;font-size:4vw;font-weight:600;">记录</div>
    </mt-header>
    <section class="backbox">
      <div class="bodybox">
        <div class="headbox">
          <div class="head-img">
            <img :src="this.GLOBAL.Host+this.GLOBAL.friendInfo.info.head" alt="">
          </div>
          <p>{{this.GLOBAL.friendInfo.info.nickname}}</p>
        </div>
        <div class="moneybox">
          <p>输入金额</p>
          <div>
            <span>￥</span><input type="number" placeholder="0.00" v-model="gold">
          </div>
        </div>
        <div class="remarkbox">
          <input type="text" placeholder="转账备注（50字以内）" v-model="remark">
        </div>
        <div class="btnbox" @click="transGold">转账</div>
      </div>
    </section>
    <section v-show="isTransing">
      <div class="bg-box"></div>
      <div class="trading-box">
        <div class="trading-inline-box trading-first-box">
          <span @click="cancle" class="trading-cancel-btn">&#215;</span>
          <span class="trading-inline-box tranding-tips-box">
            <span>转账支付</span>
            <span>忘记密码?</span>
          </span>
        </div>
        <div class="trading-inline-box trading-second-box">
          <div class="trading-inline-box user-head-box">
            <img :src="this.GLOBAL.Host+this.GLOBAL.userInfo.head" alt="头像">
          </div>
          <span>转账给“{{this.GLOBAL.friendInfo.info.nickname}}”</span>
        </div>
        <div class="trading-inline-box trading-third-box">
          <span>￥</span>
          <span>{{gold}}{{gold_prefix}}</span>
        </div>
        <div class="trading-inline-box trading-fourth-box">
          <img src="static/64-金币.png" alt="金币">
          <div class="trading-inline-box gold-box">
            <span>金币</span>
            <img src="static/右箭头.png" alt="">
          </div>
        </div>
        <div class="trading-inline-box trading-fifth-box">
          <input type="password" id="tradding" v-model="trading">
        </div>
      </div>
    </section>
  </div>
</template>
<script>
export default {
  data() {
    return {
      gold: "",
      remark: "",
      trading: "",
      isTransing: false
    };
  },
  mounted: function() {
    console.log(this.GLOBAL.friendInfo);
    this.$parent.tabbarShow = false;
    if (!this.GLOBAL.friendInfo.info) {
      this.$router.push("/friend");
    }
    this.checkLogin();
    this.onmessage();
    // console.log(this.GLOBAL.userInfo.trading);
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
  methods: {
    transGold() {
      if (!this.gold) {
        this.$messagebox.alert("请输入转账金币！");
      } else {
        if (this.gold > this.GLOBAL.userInfo.gold) {
          this.$messagebox.alert(
            "您当前的金币为：" + this.GLOBAL.userInfo.gold + ",不足以转账。"
          );
        } else {
          // 向金币数目后面追加“.00”;
          if (this.gold.indexOf(".") == -1) {
            this.gold += ".00";
          } else {
            let floatN = this.gold.split(".");
            if (floatN[1].length < 2) {
              this.gold += "0";
            }
          }
          this.isTransing = true;
          this.$nextTick(function() {
            document.getElementById("tradding").focus();
          });
        }
      }
    },
    cancle() {
      this.isTransing = false;
      this.trading = "";
    }
  },
  watch: {
    /**
     * 检测金币小数点，只保留前两位
     */
    gold: function() {
      let reg = /[0-9]+\.[0-9]{0,2}/;
      let result = this.gold.match(reg);
      if (result) {
        this.gold = result[0];
      }
    },
    trading: function() {
      let reg = /[^0-9]/;
      let result = reg.test(this.trading);
      if (result) {
        this.$messagebox.alert("请输入六位数字的密码！").then(action => {
          this.trading = "";
          document.getElementById("tradding").focus();
        });
      } else if (this.trading.length >= 6) {
        this.trading = this.trading.substr(0, 6);
        if (this.trading == this.GLOBAL.userInfo.trading) {
          let data = {
            type: "transferAccounts",
            con_id: this.GLOBAL.connectionId,
            gold: this.gold,
            remark: this.remark
              ? this.remark
              : "转账给" + this.GLOBAL.friendInfo.info.nickname,
            trading: this.trading,
            recive_id: this.GLOBAL.friendInfo.info.id,
            recive_name: this.GLOBAL.friendInfo.info.nickname,
            recive_connectionID: this.GLOBAL.friendInfo.info.connectionID,
            status: this.GLOBAL.friendInfo.status,
            send_id: this.GLOBAL.userInfo.id,
            send_name: this.GLOBAL.userInfo.nickname
          };
          let that = this;
          this.senddata({
            data: data,
            callback: function(respone) {
              console.log(respone);
              if (that.GLOBAL.userInfo.gold != respone.gold) {
                // 金币数目不一致，更新本地用户信息
                that.GLOBAL.userInfo.gold = respone.gold;
                localStorage.setItem(
                  "bc_userInfo",
                  JSON.stringify(that.GLOBAL.userInfo)
                );
              }
              if (respone.status) {
                that.$forceUpdate();
                if (that.GLOBAL.msgList[that.GLOBAL.friendInfo.info.id]) {
                  that.updateMsgList(respone.msg.content);
                } else {
                  that.$set(
                    that.GLOBAL.msgList,
                    that.GLOBAL.friendInfo.info.id,
                    {}
                  );
                  respone.msg["send_status"] = true;
                  let newMsg = [respone.msg];
                  that.setGlobalAttribute(
                    that.GLOBAL.msgList[that.GLOBAL.friendInfo.info.id],
                    newMsg
                  );
                }
                if (respone.systemMsg) {
                  if (that.GLOBAL.msgList.systemNotice) {
                    that.$set(
                      that.GLOBAL.msgList,
                      Object.keys(that.GLOBAL.msgList.systemNotice).length,
                      respone.systemMsg
                    );
                  } else {
                    that.$set(that.GLOBAL.msgList, "systemNotice", {});
                    that.$set(
                      that.GLOBAL.msgList.systemNotice,
                      0,
                      respone.systemMsg
                    );
                  }
                  that.GLOBAL.msgLength++;
                }

                that.$router.push({
                  path: "/transerfersuccess",
                  query: { gold: data.gold, recive_user: data.recive_name }
                });
              } else {
                // 转账失败，输出提示信息
                this.$messagebox.alert(respone.msg);
              }
            },
            handType: "user"
          });
          console.log("发送转账请求");
        } else {
          this.$messagebox.alert("支付密码错误，请重试！").then(action => {
            this.trading = "";
            document.getElementById("tradding").focus();
          });
        }
      }
    }
  }
};
</script>
<style>
#transacountheader {
  font-size: 6vw;
  height: 10vw;
  background: white;
}
.backbox {
  width: 100vw;
  /* height: 100vh; */
  background: white;
  margin-bottom: -31vw;
}
.bodybox {
  padding-top: 0vw;
}
.headbox {
  height: 34vw;
}
.headbox .head-img {
  width: 20vw;
  height: 20vw;
  border-radius: 3vw;
  margin: 6vw auto 0px;
  background: #72aefa;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
}
.head-img img {
  max-height: 100%;
  max-width: 100%;
}
.headbox p {
  line-height: 9vw;
  font-size: 7vw;
}
.moneybox p {
  box-sizing: border-box;
  text-align: left;
  padding-left: 8vw;
  font-size: 5vw;
  font-weight: bold;
  font-family: 黑体;
  color: #667;
  line-height: 10vw;
}
.moneybox div,
.remarkbox {
  width: 84%;
  margin: auto;
  height: 15vw;
  border-bottom: 1px solid rgb(190, 231, 241);
}

.moneybox div input {
  height: 14vw;
  border: 0px;
  width: 90%;
  text-align: left;
  font-size: 9vw;
  font-weight: bold;
  box-sizing: border-box;
  padding-left: 2vw;
  color: #667;
  outline: none;
}
.moneybox div span {
  font-size: 6vw;
  font-weight: bold;
  color: #667;
}
.remarkbox input {
  width: 100%;
  height: 10vw;
  margin-top: 4vw;
  text-align: left;
  box-sizing: border-box;
  padding: 0 1vw;
  font-size: 5vw;
  border: 0px;
  /* border-bottom: 1px solid rgb(190, 231, 241); */
  outline: none;
  color: #667;
}
.btnbox {
  height: 15vw;
  background: #57d6dd;
  width: 84%;
  margin: 7vw auto 0px;
  border-radius: 1vw;
  border: 0px;
  color: white;
  font-size: 6vw;
  line-height: 15vw;
}
.bg-box {
  position: fixed;
  top: 0px;
  left: 0px;
  z-index: 1;
  width: 100vw;
  height: 100vh;
  background: #000;
  opacity: 0.5;
}
.trading-box {
  position: fixed;
  top: 12vh;
  left: 5vw;
  z-index: 2;
  width: 90vw;
  height: 70vw;
  background: #fff;
}
.trading-inline-box {
  display: flex;
}
.trading-first-box {
  border-bottom: 0.5vw solid #d7d7d7;
  height: 10vw;
  line-height: 10vw;
  font-size: 4.5vw;
}
.trading-first-box span {
  margin-left: 2vw;
}
.trading-first-box .trading-cancel-btn {
  font-size: 8vw;
  color: #a3a3a3;
  font-weight: 100;
  margin-right: 2vw;
}
.trading-first-box .tranding-tips-box {
  justify-content: space-between;
  flex: auto;
}
.trading-first-box .tranding-tips-box span:nth-child(2) {
  margin-right: 2vw;
}
.trading-second-box {
  width: 100%;
  /* background: yellow; */
  height: 17vw;
  align-items: flex-end;
}
.trading-second-box .user-head-box {
  width: 12vw;
  height: 12vw;
  justify-content: center;
  justify-items: center;
  align-content: center;
  align-items: center;
  background: #999;
  margin-left: 12vw;
}
.user-head-box img {
  max-width: 100%;
  max-height: 100%;
}
.trading-second-box span {
  display: inline-block;
  height: 17vw;
  line-height: 22vw;
  margin-left: 5vw;
  font-weight: bold;
  font-size: 5vw;
}
.trading-third-box {
  width: 100%;
  justify-content: center;
  /* justify-items: center; */
  align-items: center;
}
.trading-third-box span {
  font-weight: bold;
}
.trading-third-box span:nth-child(1) {
  font-size: 6vw;
}
.trading-third-box span:nth-child(2) {
  font-size: 9vw;
}
.trading-fourth-box {
  width: 90%;
  margin: auto;
  /* background: yellow; */
  border-bottom: 0.45vw solid #eaeaea;
}
.trading-fourth-box img {
  width: 10vw;
  height: 10vw;
}
.gold-box {
  justify-content: space-between;
  justify-items: space-between;
  align-content: center;
  align-items: center;
  flex: auto;
  margin-left: 2vw;
}
.gold-box span {
  font-size: 4.5vw;
  font-weight: 600;
}
.gold-box img {
  width: 6vw;
  height: 6vw;
}
.trading-fifth-box {
  /* background: yellow; */
  height: 20vw;
  display: flex;
  align-items: center;
}
.trading-fifth-box input {
  width: 90%;
  margin: auto;
  height: 10vw;
  line-height: 10vw;
  border: 0.4vw solid #cecece;
  outline: none;
  font-size: 13vw;
  letter-spacing: 4vw;
}
</style>


