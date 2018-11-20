<template>
  <div>
    <mt-header title="发红包" fixed='true' id="redbagbox">
      <router-link slot="left" to="/chat">
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
      show: false
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
            send_id: this.GLOBAL.userInfo.id,
            get_id: this.GLOBAL.friendInfo.info.id,
            type: "sendRedBagToFriend",
            gold: this.money,
            remark: this.remark,
            trading: this.trading,
            send_time: this.showDate(),
            status: this.GLOBAL.friendInfo.status,
            con_id: this.GLOBAL.connectionId,
            fcon_id: this.GLOBAL.friendInfo.info.connectionID,
          };
          this.senddata(data, function(respone) {
            if (!respone) {
              that.trading = "";
              that.$messagebox.alert("网络堵塞，请检查网络是否正常！");
              return false;
            }
            console.log("------------respone-------------");
            console.log(respone);
            console.log("------------respone-------------");
            if (respone.status) {
              // let content =
                // '(@redbag'+respone.red_id+'redbag@)<span style="display:flex;"><img style="height:15vw;width:auto;" src="static/红包.png" /><span><p style="color:red;font-size:4vw;">' +
                // that.remark +
                // '</p><p style="font-size:4vw;">点击查看</p></span></span>';
              let content = '(@redbag'+respone.red_id+'redbag@)'+that.remark;
              // 更新本地的消息队列
              let time = that.updateMsgList(content);
              that.GLOBAL.userInfo.gold -= data.gold;
              localStorage.setItem(
                "bc_userInfo",
                JSON.stringify(that.GLOBAL.userInfo)
              );
              that.$router.push("/chat");
            } else {
              that.$messagebox.alert(respone.errMsg).then(action => {});
            }
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
  created: function() {},
  beforeMount: function() {},
  mounted: function() {
    this.checkLogin();
    this.onmessage();
    this.$parent.tabbarShow = false;
  },
  beforeUpdate: function() {},
  updated: function() {
    this.$parent.tabbarShow = false;
  },
  beforeDestroy: function() {},
  destroyed: function() {},
  methods: {
    sendredbag: function() {
      if (this.money) {
        if (this.GLOBAL.userInfo.gold >= this.money) {
          this.show = true;
          this.$nextTick(() => {
            document.getElementById("tradingbox").focus();
          });
        } else {
          this.$messagebox.alert('您的余额为'+this.GLOBAL.userInfo.gold+',不足以发红包！');
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
  border: 1px solid #ccc;
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
