<template>
  <div>
    <mt-header v-bind:title="themeInfo.name" style="background:#57D6DD;height:10vw;" fixed>
      <router-link to="/forum" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
    </mt-header>
    <div id="list-box" class="pageContent" style="margin-top:10vw;">
      <div v-for="(item,index) in postList" :key="index">
        <div class="list">
          <div class="title">
            <div class="head"><img :src="host + item.head"></div>
            <div class="themeUserInfo">
              <div class="nickname">{{item.user_name}}</div>
              <div class="info">{{showTime(item.addtime)}}</div>
            </div>
            <div class="icon"><img src="./../../assets/箭头 右.png"></div>
          </div>
          <div class="text-title" @click="toThemeDetail(item, index)">
            <p>{{item.title}}</p>
          </div>
          <div class="text" @click="toThemeDetail(item, index)">
            <div class="contentText" v-html="item.content"></div>
          </div>
          <div class="image" v-show="item.imgs">
            <div class="theme-one-image" v-for="(src, i) in item.imgs" :key="i">
              <img :src="imgHost + src">
            </div>
          </div>
          <div class="bottom">
            <div class="star like" @click="lile(item,index)"><i class="iconfont icon-xihuan"></i> {{item.collect}}</div>
            <div class="star comment"><i class="iconfont icon-pinglun"></i> {{item.reply}}</div>
            <div class="star see"><i class="iconfont icon-yanjing"></i> {{item.visitor}}</div>
          </div>
        </div>
      </div>

    </div>
    <div v-show="!themeInfo.limit_tie" class="btn" @click="post">
      <div class="btn-img">
        <img src="./../../assets/发帖@2x.png">
      </div>
    </div>
  </div>
</template>

<script>
// console.log("FORUM_THEME_VUE");
// import root from "@/config/root";
// import radioText from "@/components/common/radioText";
export default {
  // components: {
  //   radioText
  // },
  data() {
    return {
      likeStatus: true,
      postList: [],
      themeInfo: {
        name: ""
      },
      page: 0,
      host: this.GLOBAL.Host,
      imgHost: this.GLOBAL.logoHost + "themeImgs/",
      refreshFlag: true
    };
  },

  mounted: function() {
    this.$parent.tabbarShow = false;
    let that = this;
    this.checkSocket(function() {
      that.getPostList();
    });
    this.onmessage();
    this.refresh({
      elementId: "list-box",
      type: "down",
      callback: function() {
        console.log("已经到底了，正在加载中");
        console.log(that.refreshFlag);
        if (that.refreshFlag) {
          that.getPostList();
        }
      }
    });
  },
  watch: {},
  methods: {
    toThemeDetail(item, index) {
      // console.log(item);return false;
      // if (item.readers) {
      //   item.readers = JSON.parse(item.readers);
      // }
      if (
        this.themeInfo.gold &&
        item.user_id != this.GLOBAL.userInfo.id &&
        (!item.read_status)
      ) {
        this.$messagebox
          .confirm("您确定要花" + this.themeInfo.gold + "查看该帖吗？")
          .then(action => {
            this.$router.push({
              path: "/themeDetail",
              query: {
                zone_id: this.themeInfo.id,
                post_id: item.id
              }
            });
          })
          .catch(err => {
            // console.log("取消查看");
          });
      } else {
        this.$router.push({
          path: "/themeDetail",
          query: {
            zone_id: this.themeInfo.id,
            post_id: item.id
          }
        });
      }
    },
    showTime(time) {
      return this.showDate(false, time);
    },
    post: function() {
      // console.log("fatie");
      // console.log(this.themeInfo);
      if (!this.GLOBAL.userInfo.id) {
        this.$messagebox.confirm("您还没登陆，现在去登陆？").then(action => {
          this.$router.push("/login");
        });
      } else {
        this.$router.push({
          path: "/post",
          query: {
            zone_id: this.themeInfo.id
          }
        });
      }
    },
    like: function(item, index) {
      this.$axios
        .post(root.forumLike, {
          post_id: item.id,
          user_id: 1
        })
        .then(response => {
          if (this.likeStatus) {
            item.like++;
          } else {
            item.like--;
          }
        });
    },
    getPostList() {
      let data = {
        type: "getPostList",
        page: this.page,
        zone_id: this.$route.query.zone_id,
        send_id: this.GLOBAL.userInfo.id,
        con_id: this.GLOBAL.connectionId
      };
      // console.log("getPostlist");
      let that = this;
      this.senddata({
        data: data,
        callback: function(response) {
          // console.log("{{{{{{{{{{{{{{{{postList}}}}}}}}}}}}}}}");
          // console.log(response);
          // console.log("{{{{{{{{{{{{{{{{postList}}}}}}}}}}}}}}}");
          if (response.zoneInfo) {
            that.themeInfo = response.zoneInfo;
          }
          if (Object.keys(response.postList).length) {
            that.postList = that.postList.concat(response.postList);
            that.page++;

            if (response.end) {
              that.refreshFlag = false;
            }
          } else {
            that.refreshFlag = false;
          }
        },
        callbackFlag: "resopneGetPostList",
        handType: "user"
      });
    }
  }
};
</script>
<style>
.contentText div {
  display: none !important;
}
.contentText img {
  display: none !important;
}
</style>
<style scoped>
a {
  display: block;
}
.list {
  border-bottom: 0.4vw solid #e9e9e9;
}
.title {
  padding: 2vw 0;
  display: flex;
  justify-content: space-between;
}
.head {
  /* border-radius: 50%;
  min-width: 50px;
  width: 50px;
  height: 50px; */
  display: flex;
  width: 13vw;
  height: 13vw;
  border-radius: 13vw;

  justify-content: center;
  justify-items: center;
  align-content: center;
  align-items: center;
  overflow: hidden;
  background-color: #7dcbf9;
}
.head img {
  max-width: 100%;
  max-height: 100%;
}
.themeUserInfo {
  width: 75vw;
  /* height: 13vw; */
  line-height: 7vw;
  text-align: left;
  padding: 0 2vw;
}
.icon {
  min-width: 3vw;
}
.icon img {
  width: 3vw;
  margin-top: 4vw;
}
.nickname {
  font-size: 4vw;
}
.info {
  font-size: 3vw;
  color: #a5a5a5;
}
.text-title {
  font-size: 4.5vw;
  /* margin-bottom: 2vw; */
  text-align: left;
}
.text-title p {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
}
.text {
  font-size: 4vw;
  line-height: 5vw;
  text-align: left;
}
.text .contentText {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  padding-top: 2vw;
}

.image {
  padding: 1vw 0;
  display: flex;
  flex-wrap: wrap;
  margin-left: -3.33%;
  /* max-height: 55vw; */
  /* overflow: hidden; */
}
.image .theme-one-image {
  /* display: block; */
  width: 30%;
  height: 30vw;
  margin-top: 1vw;
  margin-left: 3.33%;
}
.theme-one-image img {
  max-width: 100%;
  max-height: 100%;
  display: inline-block;
}
.bottom {
  padding: 2vw 0;
  display: flex;
  justify-content: space-between;
  font-size: 4vw;
  color: #afafaf;
  position: relative;
}
.bottom .iconfont {
  font-size: 5vw;
  position: relative;
  top: 2px;
}
.btn {
  position: fixed;
  transform-origin: center;
  /* bottom: 2vw; */
  top: 100vh;
  left: 37.5vw;
  z-index: 10;
  /* margin-bottom: 27vw; */
  /* margin-left: -35px; */
}
.btn-img {
  height: 25vw;
  width: 25vw;
  border-radius: 25vw;
  position: absolute;
  top: -27vw;
}
.btn-img img {
  width: 100%;
  height: 100%;
}
.pageContent {
  height: 100vh;
  overflow-y: auto;
}
</style>