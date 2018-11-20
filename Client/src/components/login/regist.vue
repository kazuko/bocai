<template>
  <div>
    <mt-header title="" fixed="true">
      <router-link to="/" slot="left">
        <mt-button icon="back">退出注册</mt-button>
      </router-link>
    </mt-header>
    <div class="pageContent" id="registPage">
      <div class="title">用户注册</div>
      <input id="username-input-box" type="text" class="username-box" :placeholder="placeholder" v-model="username" />
      <div class="yzm" v-show="phoneCheck">
        <input class="username-box yzm-box" placeholder="验证码" v-model="yzm" />
        <button v-if="time" class="yzm-btn">{{time}}</button>
        <mt-button v-else class="yzm-btn" @click="getYam">获取验证码</mt-button>
      </div>
      <input class="username-box" placeholder="密码" type="password" v-model="password" align="left" />
      <input class="username-box" placeholder="邀请码" type="password" v-model="invitedKey" align="left" />
      <mt-button class="register-btn" size="large" @click="regist">注册</mt-button>
      <router-link class="tips-box" to="/login">已有帐号？马上登录</router-link>
    </div>
  </div>
</template>

<script>
console.log("LOGIN_REGIST_VUE");

// import root from "@/config/root.js";
export default {
  data() {
    return {
      username: "", //用户账号或手机号
      password: "", //登陆密码
      invitedKey: "", //（用户填）邀请码
      yzm: "", //验证码
      mode: "", //注册模式（0：禁止注册；1：用户名注册；2：手机注册，不需验证；3：手机验证注册）

      placeholder: "用户名", //提示语
      phoneCheck: false, //是否需要短信验证
      maxLenght: 0, //用户名最大长度
      invitedCode: "", //（数据库）邀请码
      keyword: "", //关键词（用户名不可出现）
      prefix: "", //用户前缀
      handle: "", //验证码倒计时句柄
      time: 0
    };
  },
  watch: {},
  methods: {
    regist() {
      // 检测邀请码是否一直
      if (this.invitedKey == this.invitedCode) {
        // 对用户名或手机进行初步检测
        if (this.mode == 1) {
          // 用户名注册
          if (this.keyword.test(this.username)) {
            // 用户名存在非法字眼
            let result = this.keyword.exec(this.username);
            console.log(result);
            this.$messagebox.alert("用户名存在非法字眼：“" + result[1] + "”!");
            return false;
          } else if (this.username.length > this.maxLenght) {
            // 用户名长度超出最大限制长度
            this.$messagebox.alert(
              "用户名超出最大长度" + this.maxLenght + "了！"
            );
            return false;
          }
        } else if (this.mode == 2 || this.mode == 3) {
          this.placeholder = "手机号";
          // 手机注册
          let reg = new RegExp(/[^0-9]+/);
          if (this.username.length != 11 || reg.test(this.username)) {
            // 手机号长度不等于11或者存在非数字的字符表示手机号不正确
            this.$messagebox.alert("请输入正确的手机号！");
            return false;
          } else if (this.mode == 3 && !this.yzm) {
            this.$messagebox.alert("请输入验证码！");
            return false;
          }
        } else {
          // 未开发注册功能，跳回首页
          this.$messagebox.alert("抱歉，当前未开发注册功能！", "提示", {
            confirmButtonText: "知道了",
            callback: action => {
              this.$router.push("/");
            }
          });
          return false;
        }
        // 提交注册信息
        let data = {
          type: "registerMember",
          username: this.username,
          password: this.password,
          code: this.yzm,
          invitedCode: this.invitedKey,
          mode: this.mode,
          con_id: this.GLOBAL.connectionId
        };
        let that = this;
        this.senddata({
          data: data,
          callback: function(respone) {
            if (respone.status) {
              // 返回注册成功提示语则输出后台设置的提示语
              if (respone.wellcome) {
                that.$messagebox.alert(respone.wellcome, "提示", {
                  confirmButtonText: "确定",
                  callback: action => {
                    that.$router.push("/login");
                  }
                });
              } else {
                // 返回默认的注册成功提示语
                that.$messagebox
                  .confirm("注册成功！现在去登陆？")
                  .then(action => {
                    that.$router.push("/login");
                  })
                  .catch(err => {
                    that.$router.push("/");
                  });
              }
            } else {
              if (respone.msg) {
                that.$messagebox.alert(respone.msg);
              } else {
                that.$messagebox.alert("网络堵塞，请稍后再试！");
              }
            }
          },
          handType: "user"
        });
      } else {
        // 邀请码不正确
        this.$messagebox.alert("邀请码不正确！");
      }
    },
    /**
     * 获取验证码
     */
    getYam() {
      // 手机号的基本检测
      if (!this.username || this.username.length != 11) {
        this.$messagebox.alert("请输入正确的手机号！", "提示", {
          confirmButtonText: "好的",
          callback: action => {
            document.getElementById("username-input-box").focus();
            this.$messagebox.close();
          }
        });
        return false;
      }
      // 发送手机号
      let data = {
        type: "getYanZhengMa",
        phone: this.username,
        con_id: this.GLOBAL.connectionId
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(respone) {
          if (respone.status) {
            // 验证码发送成功
            that.time = 30;
            that.computedTime();
          } else {
            // 验证码发送失败
            if (respone.msg) {
              that.$messagebox.alert(respone.msg);
            } else {
              that.$messagebox.alert("网络堵塞，请稍后再试！");
            }
          }
        },
        callbackFlag: "responeGetYanZhengMa",
        handType: "user"
      });
    },
    /**
     * 倒计时
     */
    computedTime() {
      let that = this;
      // 创建倒计时对象
      this.handle = setInterval(function() {
        if (that.time) {
          that.time--;
        } else {
          // 清楚倒计时对象
          clearInterval(that.handle);
        }
      }, 1000);
    }
  },
  mounted: function() {
    this.$parent.tabbarShow = false;
    let that = this;
    this.checkSocket(function() {
      // 获取注册规则
      let data = {
        type: "getRegisterRules",
        con_id: that.GLOBAL.connectionId
      };
      that.senddata({
        data: data,
        callback: function(respone) {
          switch (respone.rules.register_mod) {
            case 0:
              // 禁止注册
              that.$messagebox
                .alert("抱歉！当前未开发注册功能，请留意官方通知!")
                .then(action => {
                  that.$router.push("/");
                });
              return false;
              break;
            case 1:
              // 用户名注册
              that.placeholder = "用户名";
              break;
            case 2:
              // 手机号注册
              that.placeholder = "手机号";
              break;
            case 3:
              // 手机号验证注册
              that.placeholder = "手机号";
              that.phoneCheck = true;
              break;
          }
          // 用户名最大长度
          that.maxLenght = respone.rules.register_max_length;
          // 邀请码
          that.invitedCode = respone.rules.register_check_key;
          // 用户名禁止的关键词
          that.keyword = new RegExp("(" + respone.rules.register_keyword + ")");
          // 默认昵称的前缀
          that.prefix = respone.rules.register_prefix;
          // 起始编号（貌似没用到）
          that.startCode = respone.rules.register_start_number;
          // 注册模式
          that.mode = respone.rules.register_mod;
        },
        callbackFlag: "responeGetRegisterRules",
        handType: "user"
      });
    });
    this.onmessage();
  }
};
</script>

<style scoped>
#registPage {
  background: linear-gradient(135deg, #59d2e0, #9982df);
  padding: 0 5.5vw;
  height: 100vh;
}
.title {
  padding: 16.5vw 0 5.5vw 0;
  color: #fff;
  font-size: 5.5vw;
  text-align: center;
}
.yzm {
  display: flex;
  justify-content: space-between;
}
.yzm-btn {
  margin-top: 5.5vw;
  width: 45%;
  height: 13vw;
  border: 0.5vw solid #fff;
  background-color: transparent;
  font-size: 3.85vw;
  color: #f4de43;
  text-decoration: underline;
  text-decoration-color: #f4de43;
  margin-left: 5.5vw;
  border-radius: 1.5vw;
}
.register-btn {
  font-size: 5vw;
  background-color: #57d6dd !important;
  color: #fff;
  text-align: center;
  margin: 5.5vw auto 0px;
  width: 100%;
  height: 13vw;
  /* font-weight: bold; */
  letter-spacing: 2px;
  outline: none;

  margin-bottom: 5.5vw !important;
}
.username-box {
  width: 100%;
  height: 13vw;
  border-radius: 1.5vw;
  border: 0px;
  box-sizing: border-box;
  padding: 0vw 3vw;
  outline: none;
  font-size: 4vw;
  margin-top: 5.5vw;
}
.yzm-box {
  /* height: 13vw;
  font-size: 4vw;
  margin-top: 5.5vw;
  border: 0px;
  box-sizing: border-box;
  padding: 0vw 3vw;
  border-radius: 1.5vw; */
  width: 60%;
  /* outline: none; */
}
.tips-box {
  color: blue;
  font-size: 4vw;
  text-decoration: underline;
}
</style>