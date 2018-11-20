<template>
  <div>
    <mt-header title="我的帖子" style="background:#57D6DD;">
      <router-link to="/user" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
    </mt-header>
    <div class="button-box">
      <div @click="changeStatus(true)" class="fatie-btn" v-bind:class="(fatie ? 'active-box-btn' : '')">我的发帖</div>
      <div @click="changeStatus(false)" class="huitie-btn" v-bind:class="(fatie ? '':'active-box-btn')">我的回帖</div>
    </div>
    <div class="theme-list-box">
      <transition name="fatie-list">
        <ul v-show="fatie" class="theme-fatie-list-box">
          <li v-for="(item, index) in fatieList" :key="index" class="one-theme-info-box">
            <router-link :to="{path: '/themeDetail', query:{zone_id: item.zone_id,post_id:item.id, mytheme: true}}">
              <div class="theme-title-box">
                <div class="theme-title">{{item.title}}</div>
                <div class="theme-detail-btn">
                  <div v-if="item.toRead" class="theme-to-read-num">{{item.toRead}}</div>
                  <img src="static/右箭头.png" alt="友箭头" class="theme-flag">
                </div>
              </div>
              <div class="theme-time-reader-looker">
                <div class="theme-time">{{item.addtime}}</div>
                <div class="theme-reader">
                  <img src="static/评论.png" alt="评论">
                  <span>{{item.reply}}</span>
                </div>
                <div class="theme-looker">
                  <img src="static/眼睛.png" alt="查看">
                  <span>{{item.visitor}}</span>
                </div>
              </div>
            </router-link>
          </li>
        </ul>
      </transition>
      <transition name='huitie-list'>
        <ul v-show="!fatie" class="theme-huitie-list-box">
          <li v-for="(item, index) in huitieList" :key="index" class="one-theme-info-box">
            <router-link :to="{path: '/themeDetail', query:{zone_id: item.zone_id,post_id:item.id, mytheme: true}}">
              <div class="theme-title-box">
                <div class="theme-title">{{item.title}}</div>
                <div class="theme-detail-btn">
                  <div v-if="item.toRead" class="theme-to-read-num">{{item.toRead}}</div>
                  <img src="static/右箭头.png" alt="友箭头" class="theme-flag">
                </div>
              </div>
              <div class="theme-time-reader-looker">
                <div class="theme-time">{{item.addtime}}</div>
                <div class="theme-reader">
                  <img src="static/评论.png" alt="评论">
                  <span>{{item.reply}}</span>
                </div>
                <div class="theme-looker">
                  <img src="static/眼睛.png" alt="查看">
                  <span>{{item.visitor}}</span>
                </div>
              </div>
            </router-link>
          </li>
        </ul>
      </transition>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      fatie: true,
      fatieList: [],
      fatieListPage: 0,
      huitieList: [],
      huitieListPage: 0
    };
  },
  methods: {
    changeStatus(status) {
      this.fatie = status;
      if (!this.fatie && !this.huitieList.length) {
        this.getMyReplyList();
      }
    },
    getMyThemeList() {
      let data = {
        type: "getMyThemeList",
        con_id: this.GLOBAL.connectionId,
        user_id: this.GLOBAL.userInfo.id,
        page: this.fatieListPage
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(respone) {
          console.log(respone);
          if (respone.status) {
            that.fatieList = respone.list;
          } else {
            that.$messagebox.alert("请检查网络是否正常！");
          }
        },
        callbackFlag: "responeGetMyThemeList",
        handType: "user"
      });
    },
    getMyReplyList() {
      let data = {
        type: "getMyReplyList",
        con_id: this.GLOBAL.connectionId,
        user_id: this.GLOBAL.userInfo.id,
        page: this.huitieListPage
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(respone) {
          console.log(respone);
          if (respone.status) {
            that.huitieList = respone.list;
          } else {
            that.$messagebox.alert("请检查网络是否正常！");
          }
        },
        handType: "user"
      });
    }
  },
  mounted: function() {
    this.$parent.tabbarShow = false;
    let that = this;
    this.checkSocket(function() {
      that.getMyThemeList();
    });
    this.onmessage();
  }
};
</script>
<style scoped>
.fatie-list-enter-active,
.fatie-list-leave-active,
.huitie-list-enter-active,
.huitie-list-leave-active {
  will-change: transform;
  transition: all 500ms;
  position: absolute;
}
.fatie-list-enter {
  opacity: 0;
  transform: translate(-100%, 0);
}
.fatie-list-leave-active {
  opacity: 0;
  transform: translate(-100%, 0);
}
.huitie-list-enter {
  opacity: 0;
  transform: translate(100%, 0);
}
.huitie-list-leave-active {
  opacity: 0;
  transform: translate(100%, 0);
}
.button-box {
  display: flex;
  justify-content: center;
  height: 13vw;
  border-bottom: 0.5vw solid #efefef;
}
.button-box .fatie-btn,
.button-box .huitie-btn {
  width: 45%;
  text-align: center;
  height: 90%;
  line-height: 13vw;
  /* border: 0px; */
  font-size: 4vw;
  font-weight: 600;
}
.fatie-btn {
  border-right: 0.2vw solid #ebebeb;
}
.active-box-btn {
  color: #f7cf56;
  border-bottom: 0.6vw solid #f6c43c;
  box-shadow: 0vw 0.5vw 0.5vw 0vw rgb(243, 226, 178);
}
.theme-list-box {
  width: 100%;
  margin: auto;
}
.theme-list-box ul {
  width: 100%;
  padding: 2vw 5%;
  box-sizing: border-box;
}
.theme-list-box ul li {
  height: 20vw;
  border-bottom: 0.6vw solid #eaeaea;
}
.theme-title-box,
.theme-time-reader-looker,
.theme-detail-btn,
.theme-reader,
.theme-looker {
  display: flex;
  font-size: 4vw;
  color: #333;
  /* align-content: center; */
  align-items: center;
  justify-content: space-between;
}
/* .theme-title-box {
  justify-content: space-between;
} */
.theme-title {
  overflow: hidden;
  text-overflow: ellipsis;
  -ms-text-overflow: ellipsis;
  -o-text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  font-weight: 600;
}
.theme-title,
.theme-detail-btn {
  height: 11vw;
  line-height: 11vw;
  max-width: 80%;
  font-size: 4.5vw;
}
.theme-to-read-num {
  width: 5vw;
  height: 5vw;
  border-radius: 5vw;
  font-size: 4vw;
  text-align: center;
  line-height: 5vw;
  background: red;
  color: white;
  /* margin-right: 0vw; */
}
.theme-flag {
  width: 6vw;
  height: auto;
}

.theme-time-reader-looker {
  color: #adadad;
}
.theme-time,
.theme-reader,
.theme-looker {
  height: 9vw;
  line-height: 7vw;
  color: #adadad;
  /* background: green; */
}
.theme-reader img,
.theme-looker img {
  width: 5vw;
  height: auto;
}
.theme-reader span,
.theme-looker span {
  margin-left: 1vw;
}
</style>

