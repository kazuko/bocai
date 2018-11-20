<template>
  <div>
    <mt-header title="个人资料" style="background:#57D6DD;">
      <router-link to="/mySystem" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
    </mt-header>
    <div class="pageContent personal">
      <div class="purikura">
        <div class="purimg">
          <img id="purpic" :src="this.GLOBAL.Host + this.GLOBAL.userInfo.head">
        </div>
        <div class="uppurBtn">
          <input id="uppic" type="file" @change="previewPic" style="opacity: 0; position: absolute; top: 0; left: 0; width: 100%; height: 100%">
          <i class="iconfont icon-weibiaoti1"></i>
        </div>
      </div>
      <div class="userId">
        <!-- <mt-field label="用户ID：" disabled v-model="this.$store.state.songInfo.user.id" :disableClear="disableClear"></mt-field> -->
      </div>
      <div class="userName">
        <div class="username-box">
          <span>昵称：</span>
          <input type="text" v-model="nickname" placeholder="昵称">
          <i class="iconfont icon-xiugaiziliao" @click="changeUsername"></i>
        </div>
      </div>
      <div class="userName">
        <div class="username-box">
          <span>签名：</span>
          <input type="text" v-model="signature" placeholder="写点什么吧...">
          <i class="iconfont icon-xiugaiziliao" @click="changeSign"></i>
        </div>
      </div>
    </div>
    <div class="interval"></div>
    <div class="pageContent">
      <div class="password">
        <div class="psdTitle">登录密码修改</div>
        <div class="original-psd">
          <mt-field type="password" placeholder="请输入原始密码" v-model="originalPsd" autocomplete="off"></mt-field>
        </div>
        <div class="psdError" :class="{ psdErrorActive:oriActive }">*密码不正确</div>
        <div class="new-psd1">
          <mt-field type="password" placeholder="请输入新密码" v-model="newPsd1"></mt-field>
        </div>
        <div class="psdError"></div>
        <div class="new-psd2">
          <mt-field type="password" placeholder="请确认新密码" v-model="newPsd2"></mt-field>
        </div>
        <div class="psdError" :class=" { psdErrorActive:newActive } ">*新密码不一致请再次输入</div>
        <mt-button size="large" class="psdBtn" @click="psdVerify">确定修改</mt-button>
      </div>

    </div>
  </div>
</template>

<script>
console.log("USER_MYSYSTEM_PERSONAL_VUE");
// import pic1 from "./../../../assets/pic1.png";
// import root from "@/config/root.js";
export default {
  data() {
    return {
      disableClear: false,
      nickname: this.GLOBAL.userInfo.nickname,
      signature: this.GLOBAL.userInfo.signature,

      originalPsd: "",
      newPsd1: "",
      newPsd2: "",
      // originalPsdGet: this.$store.state.songInfo.user.password,
      oriActive: false,
      newActive: false,
      // purimg: [ this.pic2 = 'http://192.168.0.133'  + this.$store.state.songInfo.user.head],
      imgData: {
        accept: "image/gif, image/jpeg, image/jpg, image/png"
      }
    };
  },
  watch: {
    originalPsd: function() {
      //    console.log(this.originalPsd);
    }
  },
  methods: {
    psdVerify: function() {
      if (this.originalPsd != this.GLOBAL.userInfo.password) {
        this.oriActive = true;
        this.newActive = false;
      } else if (this.newPsd1 != this.newPsd2) {
        this.newActive = true;
        this.oriActive = false;
      } else {
        this.oriActive = false;
        this.newActive = false;
        let data = {
          type: "changeLoginPassword",
          signature: "",
          originalPsd: this.originalPsd,
          newPsd1: this.newPsd1,
          newPsd2: this.newPsd2,
          send_id: this.GLOBAL.userInfo.id,
          con_id: this.GLOBAL.connectionId
        };
        this.newPsd1 = "";
        this.newPsd2 = "";
        this.originalPsd = "";
        let that = this;
        this.senddata({
          data: data,
          callback: function(response) {
            console.log("(******************************)");
            console.log(response);
            console.log("(******************************)");
            if (response.status) {
              that.$messagebox.alert("修改成功！");
              that.GLOBAL.userInfo.password = data.newPsd1;
              localStorage.setItem(
                "bc_userInfo",
                JSON.stringify(that.GLOBAL.userInfo)
              );
            } else {
              if (response.msg) {
                that.$messagebox.alert(response.msg);
              } else {
                that.$messagebox.alert("修改失败，请检查网络是否正常！");
              }
            }
          },
          handType: "user"
        });
      }
    },
    //上传图片
    previewPic: function(event) {
      // let render = new FileReader();
      let img1 = event.target.files[0];
      let type = img1.type; //文件的类型，判断是否是图片
      let size = img1.size; //文件的大小，判断图片的大小
      if (this.imgData.accept.indexOf(type) == -1) {
        this.$messagebox.alert("请选择gif|jpeg|jpg|png格式的图片！");
        return false;
      }
      if (size > 3145728) {
        this.$messagebox.alert("请选择3M以内的图片！");
        return false;
      }
      var url = this.GLOBAL.Host + "/bcweb/index.php/Home/Uploads/uploadHead";
      let form = new FormData();
      form.append("file", img1, img1.name);
      form.append("uid", this.GLOBAL.userInfo.id);
      let that = this;
      this.$axios
        .post(url, form, {
          headers: { "Content-Type": "multipart/form-data" }
        })
        .then(response => {
          console.log("++++++++++++++++++++++++++++++");
          console.log("++++++++++++++++++++++++++++++");
          console.log(response.data);
          console.log("++++++++++++++++++++++++++++++");
          console.log("++++++++++++++++++++++++++++++");
          // response = JSON.parse(response.data);
          console.log(response);
          if (response.data.status) {
            (that.GLOBAL.userInfo.head = response.data.head),
              that.$forceUpdate();
            localStorage.setItem(
              "bc_userInfo",
              JSON.stringify(that.GLOBAL.userInfo)
            );
          } else {
            if (response.data.msg != "null") {
              that.$messagebox.alert(response.data.msg);
            } else {
              that.$messagebox.alert("图像不能为空！");
            }
          }
        });
    },
    //修改昵称
    changeUsername: function() {
      if (!this.nickname) {
        this.$messagebox.alert("请输入昵称！");
      } else {
        if (this.nickname == this.GLOBAL.userInfo.nickname) {
          this.$messagebox.alert("新昵称不可以和旧昵称相同！");
        } else {
          let data = {
            type: "changeNickName",
            newname: this.nickname,
            signature: "",
            send_id: this.GLOBAL.userInfo.id,
            con_id: this.GLOBAL.connectionId
          };
          let that = this;
          this.senddata({
            data: data,
            callback: function(response) {
              console.log(response);
              if (response.status) {
                that.GLOBAL.userInfo.nickname = that.nickname;
                localStorage.setItem(
                  "bc_userInfo",
                  JSON.stringify(that.GLOBAL.userInfo)
                );
                that.$messagebox.alert("修改成功！");
              } else {
                that.nickname = that.GLOBAL.userInfo.nickname;
                if (response.msg) {
                  that.$messagebox.alert(response.msg);
                } else {
                  that.$messagebox.alert("修改失败，请检查网络是否正常！");
                }
              }
            },
            handType: "user"
          });
        }
      }
    },
    //修改个性签名
    changeSign: function() {
      if (!this.signature) {
        this.$messagebox.alert("请输入签名...");
      } else {
        if (this.signature == this.GLOBAL.userInfo.signature) {
          this.$messagebox.alert("签名还没有发生变化哦！");
        } else {
          let data = {
            type: "changeSignature",
            newsignature: this.signature,
            signatureStr: "",
            send_id: this.GLOBAL.userInfo.id,
            con_id: this.GLOBAL.connectionId
          };
          let that = this;
          this.senddata({
            data: data,
            callback: function(response) {
              console.log("<+++++++++++++++++++>");
              console.log(response);
              console.log("<+++++++++++++++++++>");
              if (response.status) {
                that.$messagebox.alert("修改成功！");
                that.GLOBAL.userInfo.signature = that.signature;
                localStorage.setItem(
                  "bc_userInfo",
                  JSON.stringify(that.GLOBAL.userInfo)
                );
              } else {
                that.signature = that.GLOBAL.userInfo.signature;
                if (response.msg) {
                  that.$messagebox.alert(response.msg);
                } else {
                  that.$messagebox.alert("修改失败，请检查网络是否正常！");
                }
              }
            },
            handType: "user"
          });
        }
      }
    }
  },
  beforeCreate: function() {
    // document.getElementsByTagName("body")[0].className = "bgc-fff";
  },
  mounted: function() {
    this.checkLogin();
    this.onmessage();
    this.$parent.tabbarShow = false;
    this.$parent.tabbarWhic = false;
  },
  beforeDestroy: function() {
    // document.body.removeAttribute("class", "bgc-fff");
  }
};
</script>

<style scoped>
.username-box {
  background: white;
  border-bottom: 0.3vw solid #e7e7e7;
  padding: 0px 3.5vw;
  display: flex;
  font-size: 4.5vw;
  height: 14vw;
  line-height: 14vw;
}
.username-box span {
  display: inline-block;
  width: 13.5vw;
  /* background: red; */
  font-weight: bold;
}
.username-box input {
  width: 65.4vw;
  height: 100%;
  border: 0px;
  outline: none;
  text-align: right;
  box-sizing: border-box;
  padding: 2vw;
  font-size: 4.5vw;
  color: #666;
  /* background: #57d6dd; */
}
.username-box i {
  margin-left: 0.5vw;
}
.purikura {
  position: relative;
}
.purimg {
  width: 18vw;
  height: 18vw;
  border-radius: 18vw;
  overflow: hidden;
  margin: 5vw auto;
  border: 0.5vw solid #e7e7e7;
  display: flex;
  align-items: center;
  align-content: center;
  justify-content: center;
}
.purimg img {
  /* width: auto; */
  /* height: 100%; */
  /* margin: auto; */
  max-width: 100%;
  max-height: 100%;
}
.original-psd,
.new-psd1,
.new-psd2 {
  position: relative;
}
.uppurBtn {
  background-color: #fff;
  width: 7vw;
  height: 7vw;
  line-height: 7vw;
  border-radius: 7vw;
  overflow: hidden;
  position: absolute;
  top: 11vw;
  left: 55%;
  border: 0.5vw solid #e7e7e7;
}
.icon-weibiaoti1:before {
  font-size: 5vw;
}
.uppurBtn .iconfont {
  color: #57d6dd;
  font-size: 20px;
}
.userName .iconfont,
.perSign .iconfont {
  color: #a3a3a3;
  font-size: 20px;
}
.psdTitle {
  text-align: left;
  padding: 0 2vw;
  font-size: 4.5vw;
  line-height: 10vw;
  font-weight: bold;
}
.userName,
.perSign {
  border-top: 0.3vw solid #e7e7e7;
}
.psdBtn {
  background-color: #57d6dd;
  color: #fff;
  font-size: 4.5vw;
  margin: 5vw auto;
}
.psdError {
  height: 4vw;
  color: #e84120;
  font-size: 3vw;
  text-align: left;
  padding: 0 20px;
  opacity: 0;
}
.psdErrorActive {
  opacity: 1;
}
.icon-xiugaiziliao:before {
  font-size: 5vw;
}
</style>