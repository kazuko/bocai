<template>
  <div>
    <mt-header title="论坛首页" style="background:#57D6DD" fixed>
      <!-- <router-link to="/" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link> -->
    </mt-header>
    <div class="pageContent" id="zone-list-box">
      <div class="box" v-for="(item,index) in forum" :key="index" @click="toTheme(item,index)">
        <div class="list">
          <div class="left-box" style="display:flex;">
            <div class="icon">
              <img :src="host + item.img">
            </div>
            <div class="text">
              <div class="title">
                <p>{{item.title}}</p>
              </div>
              <div class="tips">帖数：{{item.COUNT}}</div>
            </div>
          </div>
          <div class="tag">{{item.name}}</div>
        </div>
        <!-- <div class="display">
        <div class="new"><p>{{item.post_title}}</p></div>
        <div class="time">{{item.ADDTIME}}分钟前</div>
        </div> -->
      </div>
    </div>
  </div>
</template>

<script>
console.log("FORUM_FORUM_VUE");
// import root from "@/config/root.js";
export default {
  data() {
    return {
      forum: [],
      page: 0,
      host: this.GLOBAL.Host,
      refreshFlag: true
    };
  },
  mounted: function() {
    this.$parent.tabbarShow = true;
    this.$parent.tabbarWhic = true;
    let that = this;
    this.checkSocket(function() {
      that.getThemeList();
    });
    this.onmessage();
    this.refresh({
      elementId: "zone-list-box",
      type: "down",
      callback: function(obj) {
        if (that.refreshFlag) {
          that.getThemeList();
        }
      }
    });
  },
  methods: {
    getThemeList() {
      console.log('ss');
      let data = {
        type: "getThemeList",
        page: this.page,
        send_id: this.GLOBAL.userInfo.id,
        con_id: this.GLOBAL.connectionId
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(response) {
          if (Object.keys(response.zoneList).length) {
            that.forum = that.forum.concat(response.zoneList);
            that.page++;
            if(response.end){
              that.refreshFlag = false;
            }
          } else {
            that.refreshFlag = false;
          }
        },
        callbackFlag: "ResponeGetThemeList",
        handType: "user"
      });
    },
    toTheme: function(item, index) {
      if (item.door) {
        if (this.GLOBAL.userInfo.id) {
          if (this.GLOBAL.userInfo.vip_time >= this.showDate()) {
            this.$router.push({
              path: "/theme",
              query: { zone_id: item.id }
            });
          } else {
            this.$messagebox
              .confirm("该区仅会员可以查看，现在购买VIP?")
              .then(action => {
                this.$router.push({
                  path: "/recharge",
                  query: { fromForum: true }
                });
              });
          }
        } else {
          this.$messagebox
            .confirm("该区仅会员可以查看，现在去登陆？")
            .then(action => {
              this.$router.push("/login");
            });
        }
      } else {
        this.$router.push({
          path: "/theme",
          query: { zone_id: item.id }
        });
      }
    }
  }
};
</script>

<style scoped>
.box {
  display: block;
  border-bottom: 1px solid #eaeaea;
  padding: 10px 0;
}
.list {
  display: flex;
  justify-content: space-between;
  padding: 5px 0;
}
.icon {
  padding: 1vw;
  border: 1px solid #e8e8e8;
  width: 12vw;
  height: 12vw;
  display: flex;
  justify-content: center;
  justify-items: center;
  align-content: center;
  align-items: center;
}
.icon img {
  /* width: 40px;
    height: 40px; */
  max-width: 100%;
  max-height: 100%;
}
.text {
  /* border:1px solid red; */
  /* padding: 2vw 0px; */
  /* line-height: 8vw; */
  /* height: 15vw; */
  /* width: 52vw; */
  text-align: left;
}
.title {
  font-size: 4vw;
  color: #000;
  height: 6vw;
  line-height: 9vw;
}
.tips {
  font-size: 3vw;
  color: #999;
}
.tag {
  min-width: 22vw;
  height: 9vw;
  line-height: 9vw;
  color: #f8c02d;
  border: 0.4vw solid #f8c02d;
  border-radius: 2vw;
  margin-top: 1.6vw;
  font-size: 4vw;
  overflow: hidden;
}
.display {
  padding: 0 10px;
  display: flex;
  justify-content: space-between;
  font-size: 12px;
}
.new {
  padding-left: 10px;
}
p {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
}
.time {
  color: #999;
  min-width: 60px;
}
.pageContent{
  height: 100vh;
  overflow-y: auto;
  box-sizing: border-box;
  padding-top: 10vw;
}
</style>