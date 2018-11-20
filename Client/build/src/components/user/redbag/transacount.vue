<template>
    <div>
        <mt-header title="转账" fixed='true' id="transacountheader">
            <router-link slot="left" to="/chat">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <section class="backbox">
            <div class="bodybox">
                <div class="headbox">
                    <img :src="this.GLOBAL.Host+this.GLOBAL.friendInfo.info.head" alt="">
                    <p>marrr</p>
                </div>
                <div class="moneybox">
                    <p>输入金额</p>
                    <div>
                        <span>￥</span><input type="number" placeholder="0.00">
                    </div>
                </div>
                <div class="remarkbox">
                    <input type="text" placeholder="转账备注（50字以内）">
                </div>
                <div class="btnbox">转账</div>
            </div>
        </section>
    </div>
</template>
<script>
export default {
  data() {
    return {};
  },
  beforeCreate: function() {},
  created: function() {},
  beforeMount: function() {},
  mounted: function() {
    if (!this.GLOBAL.friendInfo.info) {
      this.$router.push("/friend");
    }
    this.checkLogin();
    this.onmessage();
    this.$parent.tabbarShow = false;
  },
  beforeDestroy: function() {}
};
</script>
<style>
#transacountheader {
  font-size: 4vw;
  height: 10vw;
  background: white;
}
.backbox {
  width: 100vw;
  height: 100vh;
  background: white;
  margin-bottom: -31vw;
}
.bodybox {
  padding-top: 14vw;
}
.headbox {
  height: 34vw;
}
.headbox img {
  width: 20vw;
  height: 20vw;
  border-radius: 3vw;
  margin: 6vw auto 0px;
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
.moneybox div,.remarkbox {
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
.remarkbox input{
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
.btnbox{
  height: 15vw;
  background: #57D6DD;
  width: 84%;
  margin: 7vw auto 0px;
  border-radius: 1vw;
  border:0px;
  color: white;
  font-size: 6vw;
  line-height: 15vw;
}
</style>


