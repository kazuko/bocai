<template>
  <div>
    <mt-header title="三公大厅" fixed="true">
      <router-link to="/" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
    </mt-header>
    <div class="pageContent" id="sgHall">
      <div class="sgHall">
        <div class="box-container">
          <div v-for="(value, index) in room" :key="index" class="box" @click="enterDesk(value.desk_id)">
            <div class="user">
              <div class="head">
                <img src="./../../../assets/图层15@2x.png">
              </div>
              <div class="info">
                <div class="nickname">
                  <p>{{value.name}}</p>
                </div>
                <div class="tag">倍率:{{value.multiple}}</div>
              </div>
            </div>
            <div class="online">
              <p>在线人数：</p>
              <p>{{value.online_setting}}</p>
            </div>
            <div class="gold">
              <p>金币：</p>
              <p>{{value.gold_min}}-{{value.gold_max}}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
console.log("GAME_SANGONG_SGHALL_VUE");
// import root from "@/config/root.js";
export default {
  data() {
    return {
      room: {}
    };
  },
  methods: {
    enterDesk: function(desk) {
      console.log("client" + this.GLOBAL.userInfo.id + " click in " + desk);
      //进入牌桌前的检测
      let data = {
        type: "enterDesk",
        desk: desk,
        id: this.GLOBAL.userInfo.id,
        game: "SanGong"
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(respone) {
          // if(respone.code){
          that.$router.push({ path: "/sangong", query: { desk: desk } });
          // }else{
          //     that.$messagebox.alert("金币不足！");
          // }
        }
      });
    },
    reciveData: function(respone) {
      if (respone) {
        let data = JSON.parse(respone);
        console.log("==============sgHall==============");
        console.log(data);
        console.log("==============sgHall==============");
        if (data.code != 2) {
          this.setGlobalAttribute(this.GLOBAL.sanGongInfo, data);
        } else {
          this.room = data.roomInfo;
        }
      }
    },
    getRoomInfo: function() {
      let data = {
        type: "enterRoom",
        game: "SanGong"
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(respone) {
          let type = Object.prototype.toString.call(respone);
          if (type == "[object Object]" || type == "[object Array]") {
            console.log("==============roomInfo==============");
            that.room = respone.roomInfo;
            console.log("==============roomInfo==============");
          } else {
          }
        }
      });
    },
    checkGameSocket: function() {
      if (!this.GLOBAL.userInfo.id) {
        this.$router.push("/login");
      } else {
        if (!this.GLOBAL.gameSocketHand) {
          let that = this;
          this.createSocket(this.GLOBAL.gameGongHost, function() {
            that.onmessage(that.reciveData);
            that.getRoomInfo();
          });
        } else {
          this.onmessage(this.reciveData);
          this.getRoomInfo();
        }
      }
    }
  },
  // mounted: function() {
  //   // document.getElementById("sgHall").style.height =
  //   //   window.screen.height - 40 + "px";
  // },
  mounted: function() {
    console.log("mouted....................");
    this.$parent.tabbarShow = false;
    this.GLOBAL.gameOrNot = true;
    this.checkGameSocket();
    document.getElementsByTagName("body")[0].className = "mg0";
    document.getElementsByTagName("html")[0].className = "mg0";
  },
  updated: function() {
    // this.checkGameSocket();
  },
  beforeDestroy: function() {
    // this.SANGONG.socketHand.close();//关闭页面关闭socket
    document.getElementsByTagName("html")[0].removeAttribute("class", "mg0");
    document.body.removeAttribute("class", "mg0");
  }
};
// ws.onmessage = function(e){
//     console.log(e.data);
// }
</script>

<style scoped>
#sgHall {
  background: url("./../../../assets/色彩平衡1@2x.png") no-repeat;
  background-size: 100% 100%;
  overflow: hidden;
  overflow-y: auto;
}
.sgHall {
  padding: 20px;
}
.mint-header {
  background-color: #222222 !important;
}
.box-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
  margin-left: -4%;
}
.box {
  display: block;
  width: 95%;
  border: 2px solid #fff;
  border-radius: 10px;
  height: 150px;
  margin-left: 4%;
  margin-top: 10px;
  overflow: hidden;
}
p {
  float: left;
}
.user {
  display: flex;
  flex-wrap: nowrap;
  margin: 10px 5px 0;
}
.head {
  width: 50px;
  height: 50px;
  min-width: 50px;
}
.head img {
  width: 100%;
  height: 100%;
}
.info {
  margin: 5px;
  line-height: 20px;
  color: #fff;
  text-align: left;
  width: 100%;
}
.nickname {
  font-size: 12px;
}
.nickname p {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
}
.tag {
  font-size: 16px;
}
.online {
  padding: 10px 15px;
  color: #f8e00d;
  text-align: left;
  line-height: 24px;
  float: left;
}
.gold {
  padding: 10px 15px;
  color: #f8e00d;
  text-align: left;
  line-height: 24px;
  float: left;
}
.gold p:first-child {
  font-size: 14px;
}
.gold p:last-child {
  font-size: 18px;
}
.box-container .box:nth-child(6n + 1) {
  background-color: #7e07a9;
}
.box-container .box:nth-child(6n + 2) {
  background-color: #462b83;
}
.box-container .box:nth-child(6n + 3) {
  background-color: #3c52a0;
}
.box-container .box:nth-child(6n + 4) {
  background-color: #2c3e82;
}
.box-container .box:nth-child(6n + 5) {
  background-color: #a68e00;
}
.box-container .box:nth-child(6n + 6) {
  background-color: #4212af;
}
</style>