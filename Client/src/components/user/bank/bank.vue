<template>
  <div style="background:#E7E7E7;min-height:93vh;">
    <mt-header title="社区银行" fixed='true' style="height:10vw;font-size:4vw;background:#57D6DD;">
      <router-link to="/user" slot="left">
        <mt-button style="height:10vw;" icon="back" class="BACKBTN">返回</mt-button>
      </router-link>
    </mt-header>
    <div class="pageContent bank">
      <div class="mt10 bankMoney">
        已存金币： {{this.GLOBAL.userInfo.bank}}
      </div>
      <div class="mt10 option">
        <!-- <mt-field label="当前金币" type="number" v-model="this.GLOBAL.userInfo.gold" autocomplete="off"></mt-field> -->
        <div class="nowgold">
          <span>当前金币</span>
          <span>{{this.GLOBAL.userInfo.gold}}</span>
        </div>
      </div>
      <div class="mt10 option bankbox">
        <mt-field type="number" placeholder="请输入金额" v-model="storage_gold" autocomplete="off" :disableClear="true">
          <mt-button @click="operationStorage">存金币</mt-button>
        </mt-field>
        <mt-field type="number" placeholder="请输入金额" v-model="get_gold" autocomplete="off" :disableClear="true">
          <mt-button @click="operationTakeout">取金币</mt-button>
        </mt-field>
      </div>
    </div>
    <section v-show="trading">
      <div class="trading-bgbox"></div>
      <div class="password-box">
        <span @click="cancleTrading">X</span>
        <div class="tips-box">请输入交易密码</div>
        <div class="psd-box">
          <input id="passwordbox" type="password" v-model="tradingPsd" style="font-size:10vw;">
        </div>
      </div>
    </section>
  </div>
</template>

<script>
console.log("USER_BANK_BANK_VUE");
export default {
  data() {
    return {
      trading: false,
      storage_gold: "",
      get_gold: "",
      tradingPsd: "",
      operation: ""
    };
  },
  watch: {
    tradingPsd: function() {
      console.log(this.tradingPsd.length);
      if (this.tradingPsd.length >= 6) {
        this.tradingPsd = this.tradingPsd.slice(0, 6);
        if (this.tradingPsd == this.GLOBAL.userInfo.trading) {
          if (this.operation == 1) {
            this.calculateStorage();
          } else {
            this.calculateTakeout();
          }
          this.operation = "";
          this.storage_gold = "";
          this.tradingPsd = "";
          this.trading = false;
        } else {
          this.$messagebox.alert("交易密码不正确！");
          this.tradingPsd = "";
        }
      }
    }
  },
  methods: {
    cancleTrading: function() {
      this.trading = false;
      this.tradingPsd = "";
    },
    operationStorage: function() {
      if (!this.storage_gold) {
        this.$messagebox.alert("请输入存储金币");
        return false;
      }
      this.trading = true;
      this.operation = 1;
      this.$nextTick(function() {
        document.getElementById("passwordbox").focus();
      });
    },
    calculateStorage: function() {
      if (this.storage_gold) {
        if (this.storage_gold > this.GLOBAL.userInfo.gold) {
          this.$messagebox.alert("当前金额不足以支付！");
        } else {
          let data = {
            type: "storageGold",
            gold: this.storage_gold,
            tradingPsd: this.tradingPsd,
            send_id: this.GLOBAL.userInfo.id,
            con_id: this.GLOBAL.connectionId
          };
          let that = this;
          this.senddata({
            data: data,
            callback: function(response) {
              console.log("<++++++++++++++++++++++++++++++++>");
              console.log(response);
              console.log("<++++++++++++++++++++++++++++++++>");
              if (response.status) {
                that.$messagebox.alert("存储成功！");
                that.GLOBAL.userInfo.bank = response.goldInfo.bank;
                that.GLOBAL.userInfo.gold = response.goldInfo.gold;
                that.$forceUpdate();
                localStorage.setItem(
                  "bc_userInfo",
                  JSON.stringify(that.GLOBAL.userInfo)
                );
              } else {
                that.$messagebox.aelrt(response.msg);
              }
            },
            handType: "user"
          });
        }
      } else {
        this.$messagebox.alert("请输入存放金额！");
      }
    },
    operationTakeout: function() {
      if (!this.get_gold) {
        this.$messagebox.alert("请输入取出金币");
        return false;
      }
      this.operation = 2;
      this.trading = true;
      this.$nextTick(function() {
        document.getElementById("passwordbox").focus();
      });
    },
    calculateTakeout: function() {
      if (this.get_gold > this.GLOBAL.userInfo.bank) {
        this.$messagebox.alert("银行金币不足！");
      } else if (!this.get_gold) {
        this.$messagebox.alert("请输入金币个数！");
      } else {
        let data = {
          type: "getGoldFromBank",
          gold: this.get_gold,
          tradingPsd: this.tradingPsd,
          send_id: this.GLOBAL.userInfo.id,
          con_id: this.GLOBAL.connectionId
        };
        let that = this;
        this.senddata({
          data: data,
          callback: function(response) {
            console.log("《=================================》");
            console.log(response);
            console.log("《=================================》");
            if (response.status) {
              that.GLOBAL.userInfo.bank = response.goldInfo.bank;
              that.GLOBAL.userInfo.gold = response.goldInfo.gold;
              that.$messagebox.alert("成功取出金币：" + that.get_gold + "个");
              that.get_gold = "";
              that.$forceUpdate();
              localStorage.setItem(
                "bc_userInfo",
                JSON.stringify(that.GLOBAL.userInfo)
              );
            } else {
              that.$messagebox.alert(response.msg);
            }
          },
          handType: "user"
        });
      }
    }
  },
  beforeCreate: function() {},
  created: function() {},
  beforeMount: function() {},
  mounted: function() {
    this.$parent.tabbarShow = false;
    this.$parent.tabbarWhic = false;
    if (this.GLOBAL.userInfo.trading) {
      this.checkLogin();
      this.onmessage();
    } else {
      this.$messagebox
        .confirm("您还没设置交易密码，现在去设置？")
        .then(action => {
          this.$router.push("/transPsd");
        })
        .catch(err => {
          this.$router.push("/user");
        });
    }
  },
  beforeUpdate: function() {},
  updated: function() {
    // this.$parent.tabbarShow = false;
    // this.$parent.tabbarWhic = false;
  },
  beforeDestroy: function() {},
  destroyed: function() {}
};
</script>

<style scoped>
.password-box {
  width: 80vw;
  height: 50vw;
  background: white;
  position: fixed;
  top: 26vh;
  left: 10vw;
  z-index: 101;
  border-radius: 2vw;
}
.password-box span {
  position: absolute;
  top: 2vw;
  left: 4vw;
  z-index: 102;
  color: #999;
  font-variant: all-small-caps;
  font-size: 7vw;
}
.password-box .tips-box {
  height: 26vw;
  line-height: 32vw;
  font-size: 5vw;
  color: #5dd7dd;
  letter-spacing: 1px;
}
.password-box .psd-box input {
  height: 10vw;
  width: 70vw;
  border: 1px solid #c5c5c5;
  outline: none;
}
.trading-bgbox {
  width: 100vw;
  height: 100vh;
  position: fixed;
  top: 0px;
  left: 0px;
  z-index: 100;
  background: black;
  opacity: 0.3;
}
.bankMoney {
  padding: 0 10px;
  color: #ffba00;
  text-align: left;
  font-weight: 600;
  font-family: "黑体";
  font-size: 4vw;
  height: 10vw;
  line-height: 10vw;
}
.option button {
  background-color: #57d6dd;
  color: #fff;
  font-size: 14px;
}
.nowgold {
  background: white;
  text-align: left;
  height: 15vw;
  line-height: 15vw;
  box-sizing: border-box;
  padding: 0px 2vw;
  font-size: 4.5vw;
  display: flex;
  justify-content: space-between;
}

.nowgold span:nth-child(2) {
  font-weight: 600;
  font-size: 5vw;
  text-align: right;
}
.bank {
  margin-top: 10vw;
}
.bankbox .mint-button {
  font-size: 3vw;
  height: 6vw;
  line-height: 6vw;
}
.bankbox .mint-cell-value {
  height: 10vw;
  font-size: 3.5vw;
}
.mint-button-icon .mintui,
.mintui-back {
  font-size: 4vw !important;
}
</style>