<template>
  <div>
    <mt-header title="我的勋章" style="background:#57D6DD;height:10vw;" fixed>
      <router-link to="/user" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
    </mt-header>
    <div class="pageContent">
      <div class="medalList">
        <section v-for="(oneItem, iindex) in medalList" :key="iindex">
          <div class="medalType">{{oneItem.title}}</div>
          <div class="medal-box">
            <div class="medalBox" v-for="(item,index) in oneItem.list" :key="index">
              <div class="status" @click="changeStatus(item.id, iindex, index)">
                <img src="./../../../assets/未获得@2x.png" v-if="checkMedal(item.id)==1">
                <img src="./../../../assets/已获得@2x.png" v-else-if="checkMedal(item.id)==2">
                <img src="./../../../assets/medal@chosed.png" v-else>
              </div>
              <div class="medalImg" @click="changeStatus(item.id, iindex, index)">
                <img :src="host+item.src" :alt="'充值金币到达'+item.min+'即可获得'">
              </div>
              <div class="medalTitle" @click="introduce('gold', index)">
                <p>
                  {{item.name}}
                </p>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
console.log("USER_MEDAL_MEDAL_VUE");
import root from "@/config/root.js";
import Vue from "vue";
export default {
  data() {
    return {
      medalStatus: true,
      medalCount: 0,

      medalList: [], //所有勋章列表【积分、金币、特殊】
      userMedalList: "", // 用户拥有的勋章字符串
      host: this.GLOBAL.Host,
      status: true // 标志是否可以修改状态，防止点击（操作）过快，服务器未反应过来就点击下一个，倒是数据出错
    };
  },
  mounted: function() {
    this.$parent.tabbarShow = false;
    let that = this;
    this.checkLogin(function() {
      that.getMedalList();
    });
    this.onmessage();
  },
  methods: {
    checkMedal(id) {
      if (this.userMedalList.indexOf("[" + id + "-") === -1) {
        return 1;
      } else {
        if (this.userMedalList.indexOf("[" + id + "-0]") === -1) {
          return 0;
        } else {
          return 2;
        }
      }
    },
    getMedalList() {
      let data = {
        type: "getMedalList",
        con_id: this.GLOBAL.connectionId,
        user_id: this.GLOBAL.userInfo.id
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(response) {
          that.medalList = response.medalList;
          that.userMedalList = response.userMedalList;
        },
        callbackFlag: "resopneGetMedalList",
        handType: "user"
      });
    },
    changeStatus: function(id, iindex, index) {
      console.log(this.status);
      if (this.status) {
        this.status = false;
        console.log(this.status);
        if (this.userMedalList.indexOf("[" + id + "-") === -1) {
          // 判断是否拥有当前勋章
          this.$messagebox.alert("您还没拥有该勋章！");
        } else {
          // 默认勾选
          let status = 1;
          if (this.userMedalList.indexOf("[" + id + "-0]") === -1) {
            // 已经存在则改为取消
            status = 0;
          }
          if (status) {
            let rege = /\[[0-9]+-1+\]/gm;
            let result = this.userMedalList.match(rege);
            // 勾选的情况下，判断是否已经超出5个
            if (result && result.length >= 5) {
              this.$messagebox.alert("你已经选择了5个，请取消后再选！");
              return false;
            }
          }
          let data = {
            type: "changeMedalStatus",
            medal_id: id,
            user_id: this.GLOBAL.userInfo.id,
            con_id: this.GLOBAL.connectionId,
            status: status
          };
          console.log(data);
          let that = this;
          this.senddata({
            data: data,
            callback: function(response) {
              if (response.status) {
                that.$forceUpdate();
                if (status) {
                  // console.log();
                  that.userMedalList = that.userMedalList.replace(
                    "[" + id + "-0]",
                    "[" + id + "-1]"
                  );
                  that.$set(
                    that.GLOBAL.userInfo.medals,
                    Object.keys(that.GLOBAL.userInfo.medals).length,
                    that.medalList[iindex].list[index].src
                  );
                  // that.GLOBAL.userInfo.medals.push(
                  //   that.medalList[iindex].list[index].src
                  // );
                } else {
                  that.userMedalList = that.userMedalList.replace(
                    "[" + id + "-1]",
                    "[" + id + "-0]"
                  );
                  for (var i in that.GLOBAL.userInfo.medals) {
                    if (
                      that.GLOBAL.userInfo.medals[i] ==
                      that.medalList[iindex].list[index].src
                    ) {
                      // that.GLOBAL.userInfo.medals.splice(i, 1);
                      Vue.delete(that.GLOBAL.userInfo.medals, i);
                      break;
                    }
                  }
                }
                localStorage.setItem(
                  "bc_userInfo",
                  JSON.stringify(that.GLOBAL.userInfo)
                );
                that.$nextTick(function() {
                  that.status = true;
                });
              } else {
                if (response.msg) {
                  that.$messagebox.alert(response.msg);
                } else {
                  that.$messagebox.alert("请检查网络是否正常！");
                }
              }
            },
            handType: "user"
          });
        }
      } else {
        this.$messagebox.alert("对不起，您操作的速度过快!");
      }
    },
    introduce(index1, index2) {
      this.$messagebox.alert(this.medalList[index1][index2].desc, "勋章描述");
    }
  }
};
</script>

<style scoped>
.pageContent {
  /* margin-top: 10.2vw; */
  /* padding-top: 8vw; */
  padding-bottom: 5vw;
  position: absolute;
  top: 8vw;
  left: 0vw;
}
.medalList {
  /* display: flex; */
  /* flex-wrap: nowrap; */
  /* margin: 15px 0; */
  margin-left: -3%;
}
.medal-box {
  display: flex;
  flex-wrap: wrap;
}
.medalBox {
  width: 20vw;
  margin-left: 3%;
  margin-top: 2vw;
  height: 30vw;
  overflow: hidden;
  position: relative;
}
.status {
  position: absolute;
  left: 60%;
  top: 0;
}
.status img {
  width: 25px;
  height: 25px;
}
.medalImg {
  display: flex;
  /* width: 20vw; */
  width: 100%;
  /* height: 20vw; */
  height: 78%;
  /* justify-content: center; */
  justify-items: center;
  align-items: center;
}
.medalImg img {
  max-width: 100%;
  max-height: 100%;
  display: block;
  margin: 0 auto;
}
.medalTitle {
  font-size: 4vw;
  border: 0.3vw solid #b48b45;
  border-radius: 3vw;
  height: 20%;
}
.medalTitle p {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
.medalBtn {
  color: #fff;
  background-color: #57d6dd;
  margin: 20px 0;
}
.medalType {
  padding: 0px;
  background: #e7e7e7;
  text-align: left;
  width: 100vw;
  height: 10vw;
  line-height: 10vw;
  box-sizing: border-box;
  padding-left: 2vw;
  font-size: 4.5vw;
  font-weight: bold;
  margin-left: -1.2%;
  margin-top: 2vw;
}
</style>