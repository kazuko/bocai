<template>
  <div>
    <div class="pageContent loginPage" id="loginPage">
      <div class="title">
        用户登录
      </div>
      <mt-field placeholder="请输入用户名" type="tel" v-model="username" align="left"></mt-field>
      <mt-field placeholder="请输入密码" type="password" v-model="password"></mt-field>
      <div class="text">
        <div class="regist text-link" @click="toRegist">我要注册</div>
        <div class="forget text-link">忘记密码？</div>
      </div>
      <mt-button size="large" class="login-btn" @click="login">登录</mt-button>
    </div>
  </div>
</template>

<script>
console.log("LOGIN_LOGIN_VUE");
import root from "@/config/root.js";
export default {
  data() {
    return {
      username: "", // 账号
      password: "" // 密码
      // status: ""
    };
  },
  methods: {
    login: function() {
      if (!this.username) {
        this.$messagebox.alert("账号不能为空！");
      } else if (!this.password) {
        this.$messagebox.alert("密码不能为空！");
      } else {
        let data = {
          type: "logining",
          con_id: this.GLOBAL.connectionId,
          username: this.username,
          password: this.password
        };
        let that = this;
        this.senddata(data, function(Response) {
          console.log("============LOGIN============");
          console.log(Response);
          console.log("============LOGIN============");
          if (Response.code) {
            that.$messagebox.alert(Response.msg);
          } else {
            // 显示底部导航栏;
            that.$parent.tabbarShow = true;
            // 将用户信息赋值给全局变量
            that.setGlobalAttribute(that.GLOBAL.userInfo, Response.userInfo);
            localStorage.setItem(
              "bc_userInfo",
              JSON.stringify(that.GLOBAL.userInfo)
            );
            // 跳转到app主界面
            that.$router.push("/");
          }
        });
      }

      // this.$axios
      //   .post(root.login, {
      //     username: this.username,
      //     password: this.password
      //   })
      //   .then(Response => {
      //     if (!Response.data.code) {
      //       // 将用户信息赋值给全局变量
      //       this.setGlobalAttribute(this.GLOBAL.userInfo, Response.data.userInfo);
      //       // 显示底部导航栏
      //       this.$parent.tabbarShow = true;
      //       // 保存用户登录状态
      //       this.GLOBAL.userInfo.loginStatus = true;
      //       localStorage.setItem('bc_userInfo',JSON.stringify(this.GLOBAL.userInfo));
      //       // 跳转到app主界面
      //       this.$router.push("/user");
      //     } else {
      //       // 输出错误提示
      //       this.$messagebox.alert(Response.data.msg, "提示");
      //     }
      //   });
    },
    toRegist: function() {
      // 跳转到注册页面
      this.$router.push("/regist");
    }
  },
  mounted: function() {
    if (!this.GLOBAL.socketHand || !this.GLOBAL.socketStatus) {
      let that = this;
      this.createSocket();
      this.onmessage();
    }
  },
  created: function() {
    //   隐藏导航栏
    this.$parent.tabbarShow = false;
  },
  destroyed: function() {
    // this.$store.state.isPageLogin = false;
  },
  beforeCreate: function() {
    document.getElementsByTagName("body")[0].className = "mg0";
    document.getElementsByTagName("html")[0].className = "mg0";
  },
  beforeDestroy: function() {
    document.getElementsByTagName("html")[0].removeAttribute("class", "mg0");
    document.body.removeAttribute("class", "mg0");
  }
};
</script>

<style scoped>
.loginPage {
  background: url("./../../assets/图层3@2x.png") no-repeat;
  background-size: 100% 100%;
  padding: 0 20px;
}
.title {
  margin: 15vw 0;
  color: #fff;
  font-size: 5vw;
}
.loginPage {
  padding-top: 30px;
  height: 100vh;
  box-sizing: border-box;
}
input {
  text-align: left;
}
.text {
  display: flex;
  justify-content: space-between;
  font-size: 4vw;
  color: #fff;
  padding: 4vw 0;
}
.login-btn {
  font-size: 4vw;
  background-color: #57d6dd;
  color: #fff;
  margin-top: 10px;
}
</style>