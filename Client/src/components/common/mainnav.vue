<template>
    <div>
        <div class="tabbar">
            <router-link to="/" class="tabbar-box">
                <div class="tabbar-icon">
                    <img src="./../../assets/iconhome.png">
                </div>
                <div class="tabbar-label">
                    主页
                </div>
            </router-link>
            <div @click="goto('/chatroom')" class="tabbar-box">
                <div class="tabbar-icon">
                    <img src="./../../assets/iconforum.png">
                </div>
                <div class="tabbar-label">
                    聊天室
                </div>
            </div>
            <router-link to="/forum" class="tabbar-box">
                <div class="tabbar-icon">
                    <img src="./../../assets/iconchat.png">
                </div>
                <div class="tabbar-label">
                    论坛
                </div>
            </router-link>
            <div @click="goto('/user')" class="tabbar-box">
                <div class="tabbar-icon">
                    <img src="./../../assets/iconmy.png">
                </div>
                <div class="tabbar-label">
                    我的
                </div>
            </div>

        </div>
    </div>
</template>

<script>
console.log("COMMON_TABBAR1_VUE");

export default {
  name: "tabbar1",
  data() {
    return {};
  },
  methods: {
    goto: function(topage) {
      if (this.GLOBAL.userInfo.id) {
        this.$router.push(topage);
      } else {
        this.$router.push("/login");
      }
    },
  }
};
</script>

<style scoped>
.tabbar {
  height: 15vw;
  position: fixed;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: 15;
  background-color: #fff;
  display: flex;
  flex-wrap: nowrap;
  border-top: 2px solid #e7e7e7;
}
.tabbar-box {
  width: 25%;
  height: 100%;
  padding: 2.5vw 0;
  display: block;
}
.tabbar-icon {
  width: 6vw;
  height: auto;
  margin: 0 auto 1vw;
}
.tabbar-icon img {
  width: 100%;
  height: 100%;
}
.tabbar-label {
  font-size: 4vw;
  line-height: 1;
  /* margin-top: 1vw; */
  color: #64d7e3;
}
</style>