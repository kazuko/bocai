<template>
    <div>
        <mt-header title="我的好友">
            <router-link to="/user" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
            <router-link to="/addFriend" slot="right">
                <mt-button>
                    <i class="iconfont icon-tianjiahaoyou" style="font-size:20px"></i>
                </mt-button>
            </router-link>
        </mt-header>

        <div class="module">
            <div class="f-title">全部好友 (100/100)
                <i class="iconfont" :class="iconDown" @click="friendSlide"></i>
            </div>
            <div v-for="(value,index) in friends" v-bind:key="index">
                <div class="f-list">
                    <router-link class="f-info" :to="{path: '/chat', query: {val: value}}">
                        <div class="f-info">
                            <div class="f-pic">
                                <!-- {{value.head}} -->
                                <img v-bind:src="host + value.head">
                                <!-- <mt-badge type="error" size="small" class="badge friend-badge" v-show="value.message.length">{{value.message.length}}</mt-badge> -->
                            </div>
                            <div class="f-main">
                                <div class="f-username">{{value.nickname}}
                                    <span v-show="value.on_status">[在线]</span>
                                </div>
                                <div class="f-medal" v-for="(medal,mIndex) in value.medal" v-bind:key="mIndex">
                                    <!-- {{medal}} -->
                                    <img v-bind:src="host + medal">
                                </div>
                            </div>
                        </div>
                    </router-link>
                    <div class="f-btn">
                        <button class="del" @click="friendDel(value,index)">删除好友</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="module">
            <div class="f-title">陌生人
                <i class="iconfont" :class="strangeShow?iconDown:iconUp" @click="strangerSlide"></i>
            </div>
            <div v-for="(value,index) in strange" v-bind:key="index">
                <div class="f-list">
                    <div class="f-info">
                        <div class="f-pic">
                            <img v-bind:src="this.GLOBAL.Host+value.head">
                        </div>
                        <div class="f-main">
                            <div class="f-username">{{value.nickname}}</div>
                            <div class="f-medal">
                                请求添加您为好友！
                            </div>
                        </div>
                    </div>
                    <div class="f-btn">
                        <button class="accept" @click="agreen(value,index)">接受</button>
                        <button class="del" @click="refuse(value,index)">拒绝</button>
                    </div>
                </div>
            </div> -->
        <!-- <div v-for="(value,key,index) in this.$store.state.songInfo.strange" :key="index" v-show="strangerShow">
                <div class="f-list">
                    <div class="f-info">
                        <div class="f-pic">
                            <img :src="'http://192.168.0.133'+value.head">
                            <mt-badge type="error" size="small" class="badge friend-badge" v-show="value.message.length">{{value.message.length}}</mt-badge>
                        </div>
                        <div class="f-main">
                            <div class="f-username">{{value.nickname}}
                                <span v-show="value.ips">[在线]</span>
                            </div>
                            <div class="f-medal" v-for="(medal,mIndex) in value.medal" :key="mIndex">
                                <img :src="'http://192.168.0.133'+medal">
                            </div>
                        </div>
                    </div>
                    <div class="f-btn">
                        <button class="accept" @click="sendAdd(value,index)">添加好友</button>
                    </div>
                </div>
            </div> -->
        <!-- </div>

        <div class="module">
            <div class="f-title">系统消息
                <i class="iconfont" :class="systemShow?iconDown:iconUp" @click="systemSlide"></i>
            </div>
            <div v-for="(value,key,index) in this.GLOBAL.friendLists.system" :key="index" v-show="systemShow">
                <div class="f-list">
                    <div class="f-info">
                        <div class="f-pic">
                            <img v-bind:src="this.GLOBAL.Host+value.head">
                        </div>
                        <div class="f-main">
                            <div class="f-username">{{value.nickname}}</div>
                            <div class="f-medal">
                                {{value.content}}
                            </div>
                        </div>
                    </div>
                    <div class="f-btn">
                        <button class="del">删除</button>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</template>

<script>
console.log("USER_PAGEFRIEND_FRIEND_VUE");
import pic1 from "./../../../assets/pic1.png";
import root from "@/config/root.js";
export default {
  data() {
    return {
      friends: this.GLOBAL.friendLists.friends,
      //   strange: this.GLOBAL.friendLists.strange,
      //   system: this.GLOBAL.friendLists.system,
      friendsShow: false,
      strangeShow: true,
      systemShow: true,
      host: this.GLOBAL.Host,
      iconDown: "icon-jiantouxia",
      iconUp: "icon-jiantoushang",
    };
  },
  methods: {
    goto: function(pagePath) {
      this.$router.push(pagePath);
    },
    friendSlide: function() {
        friendsShow = !friendsShow;
    },
    strangerSlide: function() {
      this.strangerShow = !this.strangerShow;
      this.strangerList = !this.strangerList;
    },
    systemSlide: function() {
      this.systemShow = !this.systemShow;
      this.systemList = !this.systemList;
    },
    friendDel: function(value, index) {
      this.$messagebox
        .confirm("确定要删除好友" + value.nickname + "吗？")
        .then(action => {
          this.$axios
            .post(root.friend, {
              id: value.id
            })
            .then(response => {
              // this.$store.state.songInfo.friends = response.data;
            });
        });
    },
    agreen: function(value, index) {
      this.$axios
        .post(root.strange, {
          id: value.id,
          code: 1
        })
        .then(response => {
          console.log(response);
          // this.$store.state.songInfo.friends = response.data.friends;
          // console.log(this.$store.state.songInfo.addfriends)
          // this.$store.state.songInfo.addfriends.splice(index);
        });
    },
    refuse: function(value, index) {
      console.log(value);
      this.$axios
        .post(root.strange, {
          id: value.id,
          code: 0
        })
        .then(response => {
          if (response.data.code) {
            this.$messagebox.alert("拒绝添加好友成功").then(action => {
              // this.$store.state.songInfo.addfriends.splice(index);
            });
          }
        });
    },
    sendAdd: function(value, index) {
      this.$axios
        .post(root.sendAdd, {
          id: value.id
        })
        .then(response => {
          console.log(response);
          // if(value.id == this.$store.state.songInfo.user.id){
          // this.$messagebox.alert('不能添加自己为好友！')
          // }
          if (response.data.code == 2) {
            this.$messagebox.alert("已经是好友了！");
          }
          if (response.data.code == 0) {
            this.$messagebox.alert("发送添加好友请求失败！");
          }
          if (response.data.code == 1) {
            this.$messagebox.alert("发送添加好友请求成功！");
          }
          if (response.data.code == 3) {
            this.$messagebox.alert("请不要多次点击添加！");
          }
          if (response.data.code == 4) {
            this.$messagebox.alert("添加好友成功！");
            // this.$store.state.songInfo.friends = response.data.friends;
            // this.$store.state.songInfo.strange.splice(index);
          }
        });
    }
  },
  created: function() {},
  beforeCreate: function() {
    //   判断是否登陆
    if (this.GLOBAL.userInfo.loginStatus) {
      // 判断是否创建socket
      if (!this.GLOBAL.socketHand) {
        this.createSocket();
      }
      //   发送获取好友列表的请求
      var data = {
        send_id: this.GLOBAL.userInfo.id,
        nickname: "",
        get_id: "",
        fname: "",
        msg: "I want to know who are my friends",
        type: "friendList",
        rType: 1,
        fType: 0
      };
      this.senddata(data);
      console.log("<friendLists>");
      console.log(this.GLOBAL.friendLists);
      console.log("</friendLists>");
    } else {
      this.$router.push("/login");
    }
  }
};
</script>

<style scoped>
a {
  display: block;
}
.module {
  padding: 0 10px;
  margin: 10px 0;
  background-color: #fff;
}
.f-title {
  text-align: left;
  height: 40px;
  line-height: 40px;
  font-size: 14px;
}
.f-title .iconfont {
  font-size: 14px;
  color: #999;
}
.f-list {
  border-top: 1px solid #e7e7e7;
  height: 45px;
  line-height: 45px;
  padding: 10px 0;
  display: flex;
  justify-content: space-between;
}
.friend-badge {
  display: block;
  width: 16px;
  height: 16px;
  font-size: 0.75rem;
  padding: 1px 1px;
  line-height: normal;
  position: absolute;
  left: 30px;
  top: -5px;
}
.f-pic {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  overflow: hidden;
  margin-right: 10px;
}
.f-pic img {
  width: 100%;
  height: 100%;
}
.f-info {
  display: block;
  width: 60%;
  display: flex;
  flex-wrap: nowrap;
  position: relative;
}
.f-medal {
  display: flex;
  flex-wrap: nowrap;
  font-size: 12px;
  color: #999;
  line-height: 20px;
}
.f-medal img {
  width: 15px;
  height: 20px;
}
.f-username {
  height: 25px;
  line-height: 25px;
  text-align: left;
}
.f-username span {
  font-size: 12px;
  color: #64d7e3;
}
.f-btn {
  line-height: 45px;
  height: 45px;
  width: 40%;
  text-align: end;
}
.del {
  background-color: #e7e7e7;
  color: #999;
  font-size: 12px;
  border: none;
  height: 30px;
  line-height: 30px;
  padding: 0 15px;
}
.accept {
  background-color: #64d7e3;
  color: #fff;
  font-size: 12px;
  border: none;
  height: 30px;
  line-height: 30px;
  padding: 0 15px;
}
</style>