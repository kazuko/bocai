<template>
  <div>
    <mt-header title="社区广播" style="background:#57D6DD;">
      <router-link to="/user" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
    </mt-header>
    <div class="pageContent mt20 radio">
      <div class="title">
        <img src="./../../../assets/广播图标@2x.png">
        世界喇叭
      </div>
      <div class="radio-text">
        <div contenteditable id="radioText" placeholder="输入内容，全世界能看到" rows="6"></div>
      </div>
      <div class="radio-btn">
        <div class="radioIntro" disabled>花费xx金币全世界都能看到</div>
        <mt-button class="radioBtn" @click="sendRadio">发送</mt-button>
      </div>
      <div class="attention">
        <p>说明:</p>
        <p>1.不能发送违法语言；</p>
        <p>2.不能发送与本游戏无关的内容；</p>
        <p>3.世界喇叭持续xx主题，不会被顶掉</p>
      </div>
    </div>
  </div>
</template>

<script>
console.log("USER_RADIO_RADIO_VUE");
export default {
  data() {
    return {
      userRadio: ""
    };
  },
  mounted: function() {
    this.$parent.tabbarShow = false;
    this.checkLogin();
    this.onmessage();
  },
  methods: {
    sendRadio() {
      // this.$messagebox
      //   .confirm("发表该言论将话费5金币，您确定发表吗？")
      //   .then(action => {
      let data = {
        type: "sendRadio",
        content: document.getElementById("radioText").innerText,
        con_id: this.GLOBAL.connectionId,
        send_name: this.GLOBAL.userInfo.nickname,
        send_id: this.GLOBAL.userInfo.id
      };
      console.log("sendContent => {");
      console.log(data.content);
      console.log("}");
      let that = this;
      this.senddata({
        data: data,
        callback: function(respone) {
          if (respone.status) {
            // that.$router
            that.radioText(respone.radiotext);
          } else {
            that.$messagebox.alert("发表失败，请检查网络是否正常！");
          }
        },
        handType: "user"
      });
      //     }).catch(err => {
      //       console.log("已取消");
      //     });
    }
  }
};
</script>

<style scoped>
.title {
  height: 10vw;
  line-height: 10vw;
  font-size: 4.5vw;
  text-align: left;
  /* padding: 0 2vw; */
  display: flex;
  flex-wrap: nowrap;
}
.title img {
  width: 5vw;
  height: 5vw;
  margin-top: 2.5vw;
  margin-right: 2vw;
}
.radio-text {
  border: 0.4vw solid #d8d8d8;
  padding: 2vw;
}
#radioText {
  width: 100%;
  border: none;
  height: 40vw;
  font-size: 3.5vw;
  color: #666;
  line-height: 5vw;
  letter-spacing: 1px;
  text-align: left;
}
#radioText:focus {
  border: none;
  outline: none;
}
#radioText:empty::before {
  content: attr(placeholder);
  font-size: 4vw;
  color: #999;
}
#radioText::-webkit-input-placeholder {
  text-align: center;
  line-height: 30vw;
}
/* Mozilla Firefox 4 to 18 */
#radioText:-moz-placeholder {
  text-align: center;
  line-height: 30vw;
}
/* Mozilla Firefox 19+ */
#radioText::-moz-placeholder {
  text-align: center;
  line-height: 30vw;
}
/* Internet Explorer 10+ */
#radioText:-ms-input-placeholder {
  text-align: center;
  line-height: 30vw;
}
.radio-btn {
  display: flex;
  flex-wrap: nowrap;
  justify-content: space-between;
  margin-top: 5vw;
  height: 10vw;
}
.radioIntro {
  background-color: #ffba00;
  color: #fff;
  font-size: 4.3vw;
  width: 67%;
  height: 11.5vw;
  line-height: 11.5vw;
  border-radius: 2vw;
  text-align: center;
}
.radioBtn {
  background-color: #57d6dd;
  color: #fff;
  font-size: 4.3vw;
  width: 30%;
  height: 11.5vw;
  line-height: 11.5vw;
}
.attention {
  text-align: left;
  padding: 5vw;
  /* margin-top: 100px; */
  font-size: 4vw;
  color: #797979;
  position: fixed;
  top: 75vh;
}
.attention p:first-child {
  font-weight: 600;
}
</style>