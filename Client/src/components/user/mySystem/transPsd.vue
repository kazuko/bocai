<template>
  <div>
    <mt-header title="交易密码" style="background:#57D6DD;">
      <router-link to="/mySystem" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
    </mt-header>
    <div class="pageContent mt10">
      <div class="transAttention">
        <p>注意：交易密码必须为6位数的数字</p>
      </div>
      <div class="transPsd" v-show="oldpsd">
        <mt-field label="原始交易密码" type="password" placeholder="请输入交易密码" v-model="transPsd"></mt-field>
        <div v-if="transError" class="transError">*密码错误</div>
      </div>
      <div class="transPsd1">
        <mt-field label="设置交易密码" type="password" placeholder="请输入交易密码" v-model="transPsd1"></mt-field>
        <div v-if="transError1" class="transError">*密码必须为6位数字</div>
      </div>
      <div class="transPsd2">
        <mt-field label="确认交易密码" type="password" placeholder="请确认交易密码" v-model="transPsd2"></mt-field>
        <div v-if="transError2" class="transError">*两次输入的密码不一致</div>
      </div>
      <mt-button size="large" class="transBtn" @click="transChange">确定修改</mt-button>
      <div class="transInfo">
        <p>交易密码说明：</p>
        <p>交易密码用于好友之间转账和在社区银行里面取金币的时候使用，若遗忘请联系管理员核对资料后修改。</p>
      </div>
    </div>
  </div>
</template>

<script>
console.log("USER_MYSYSTEM_TRANSPSD_VUE");
import root from "@/config/root.js";
export default {
  data() {
    return {
      transPsd: "",
      transPsd1: "",
      transPsd2: "",
      transError: false,
      transError1: false,
      transError2: false,
      oldpsd: this.GLOBAL.userInfo.trading
    };
  },
  watch: {
    transPsd: function() {
      if (this.transPsd) {
        if (this.transPsd != this.GLOBAL.userInfo.trading) {
          this.transError = true;
        } else {
          this.transError = false;
        }
      } else {
        this.transError = false;
      }
    },
    transPsd1: function() {
      if (this.transPsd1.length > 6 || this.transPsd1.length < 6) {
        if (this.transPsd1) {
          this.transError1 = true;
        } else {
          this.transError1 = false;
        }
      } else {
        let transReg = new RegExp("[0-9]{6}");
        if (!transReg.test(this.transPsd1)) {
          this.transError1 = true;
        } else {
          this.transError1 = false;
        }
      }
      this.transError2 = false;
      this.transPsd2 = "";
    },
    transPsd2: function() {
      if (this.transPsd2) {
        if (this.transPsd2 != this.transPsd1) {
          this.transError2 = true;
        } else {
          this.transError2 = false;
        }
      }
    }
  },
  beforeCreate: function() {
    document.getElementsByTagName("body")[0].className = "bgc-fff";
  },
  mounted: function() {
    this.checkLogin();
    this.onmessage();
    this.$parent.tabbarShow = false;
  },
  beforeDestroy: function() {
    document.body.removeAttribute("class", "bgc-fff");
  },
  methods: {
    transChange: function() {
      console.log(this.oldpsd + ":" + this.transPsd1);
      if (this.transError) {
        this.$messagebox.alert("原密码错误！");
      } else if (this.transError1) {
        this.$messagebox.alert("新密码格式不正确！");
      } else if (this.transError2) {
        this.$messagebox.alert("两次输入不一致！");
      } else if (this.oldpsd == this.transPsd1) {
        this.$messagebox.alert("新密码不可和原密码相同！");
      } else {
        let data = {
          type: "changeTrading",
          send_id: this.GLOBAL.userInfo.id,
          oldPsd: this.transPsd,
          newPsd: this.transPsd1,
          newPsd1: this.transPsd2,
          con_id: this.GLOBAL.connectionId,
          signature: "" //验证字符串
        };
        let that = this;
        this.senddata({
          data: data,
          callback: function(resopne) {
            console.log(resopne);
            if (resopne.status) {
              that.GLOBAL.userInfo.trading = data.newPsd;
              that.$forceUpdate();
              console.log(that.GLOBAL.userInfo);
              localStorage.setItem(
                "bc_userInfo",
                JSON.stringify(that.GLOBAL.userInfo)
              );
              that.$messagebox.alert("修改成功！").then(action => {
                that.$router.push("/mySystem");
              });
            } else {
              that.$messagebox.alert(resopne.msg);
            }
          },
          handType: "user"
        });
      }
    }
  }
};
</script>

<style scoped>
body {
  background-color: #fff;
}

.transBtn {
  margin-top: 7vw;
  font-size: 4vw;
  background-color: #57d6dd;
  color: #fff;
}
.transInfo {
  padding: 15px;
  position: fixed;
  top: 80vh;
}
.transInfo p {
  font-size: 3.5vw;
  line-height: 6vw;
  text-align: start;
  color: #7d7d7d;
  font-weight: 600;
  letter-spacing: 1px;
}
.transInfo p:last-child {
  text-indent: 2em;
  color: #bdbdbd;
}
.transError {
  color: #e84120;
  font-size: 3vw;
  height: 5vw;
}
/* .transErrorActive {
  opacity: 1;
} */
.transPsd,
.transPsd1,
.transPsd2 {
  background-color: #fff;
}
.transAttention {
  font-size: 3vw;
  color: #e84120;
  text-align: left;
  padding: 2vw;
}
</style>