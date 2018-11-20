<template>
  <div>
    <section class="top-box">
      <mt-header v-bind:title="title" style="background:#B34B39 !important;">
        <router-link to="/user" slot="left">
          <mt-button icon="back">返回</mt-button>
        </router-link>
        <div class="ellipsis-box" slot="right" @click="changeRedStatus">...</div>
      </mt-header>
      <div class="user-information-box">
        <span class="year-box" @click="choseYear">{{year}}
          <mt-button icon="back" :class="(deg ? 'upbtn':'downbtn')"></mt-button>
        </span>
        <div>
          <div class="user-head-box"><img :src="this.GLOBAL.Host + this.GLOBAL.userInfo.head" alt=""></div>
        </div>
        <p class="user-tips-box">{{title1}}</p>
      </div>
      <div v-if="flag" class="red-total-money">
        <span>{{totalSendSum}}</span>
        <span class="yung-box">元</span>
      </div>
      <div v-else class="red-total-money">
        <span>{{totalReciveSum}}</span>
        <span class="yung-box">元</span>
      </div>
      <div v-if="flag" class="red-total-number">发出红包
        <span class="total-number">{{totalSendNum}}</span>个
      </div>
      <div v-else class="red-total-number">收到红包
        <span class="total-number">{{totalReciveNum}}</span>个
      </div>
    </section>
    <section class="red-list-box">
      <ul v-if="list && Object.keys(list).length">
        <li class="one-red-info-box" v-for="(item,index) in list" v-bind:key="index" @click="gotoRedDetail(item.id)">
          <!-- 发出的红包列表 -->
          <div class="red-detail-up">
            <span class="red-detail-child red-type">{{item.type}}</span>
            <span class="red-detail-child red-money">
              <span>{{item.money}}</span>
              <img src="static/64-金币.png" alt="">
            </span>
          </div>
          <div class="red-detail-down">
            <span class="red-detail-child red-date">{{item.send_time}}</span>
            <span class="red-detail-child red-status">已领取{{item.recived_num}}/{{item.recive_num}}个</span>
          </div>
        </li>

        <li v-if="(flag && redSendList.length < totalSendNum) || (!flag && redReciveList.length < totalReciveNum)">
          <button style="border:1px solid #999;color:#999;" @click="moreList">查看更多</button>
        </li>
      </ul>
      <ul v-else>
        <li v-if="flag" style="width:90vw;margin:auto;height:20vw;line-height:20vw;">哎呀，今年忘发红包了！</li>
        <li v-else style="width:90vw;margin:auto;height:20vw;line-height:20vw;">真遗憾，今年一个红包都没抢到！</li>
      </ul>
    </section>
    <section>
      <div v-show="choseYearStatus || redStatus" class="bgbox" @click="cancel"></div>
      <div v-show="choseYearStatus" class="show-year-box">
        <ul>
          <li><span>年份</span></li>
          <li class="year-options" v-for="(item, index) in yearList" :key="index">
            <span>{{item}}年</span>
            <input v-if="item == year" type="radio" v-model="year" :value="item" checked>
            <input v-else type="radio" v-model="year" :value="item">
          </li>
        </ul>
      </div>
      <ul v-show="redStatus" class="red-status-box">
        <li @click="changeToRecivedRed">收到的红包</li>
        <li @click="changeToSendRed">发出的红包</li>
      </ul>
    </section>
  </div>
</template>
<script>
console.log("USER_REDBAG_REDBAG_VUE");
export default {
  data() {
    return {
      title: "发出的红包",
      title1: this.GLOBAL.userInfo.nickname + "发出的红包",
      deg: true, //年份收起或者展开
      year: "", //展示的年份
      yearList: [], //年份列表
      list: "", //展示的红包列表

      // 发出的红包信息
      totalSendSum: 0,
      totalSendNum: 0,
      redSendYear: "",
      redSendPage: 0,
      redSendList: "",

      // 收到的红包信息
      totalReciveSum: 0, //收到的红包总金额
      totalReciveNum: 0, //收到的红包总数量
      redReciveYear: "", //当前展示的红包获取年份
      redRecivePage: 0, //页数
      redReciveList: "", //收到的红包列表

      choseYearStatus: false, //展示或者隐藏年份列表
      flag: true, //标志发红包列表，false标志收到的红包列表
      redStatus: false, //显示红包选项，点击右上角的三点触发,
      watchStatus: false
    };
  },
  created: function() {
    let dateobj = new Date();
    this.year = dateobj.getFullYear();
    this.redSendYear = this.year;
    this.redReciveYear = this.year;
    for (let i = 0; i < 5; i++) {
      this.yearList[i] = this.year - i;
    }
    this.$parent.tabbarShow = false;
    this.GLOBAL.friendInfo = {};
  },
  mounted: function() {
    // document.getElementsByTagName("body")[0].className = "bgc-fff";
    let that = this;
    this.checkLogin(function() {
      console.log("hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh");
      if (that.$route.query.flag) {
        that.flag = false;
        that.title = "收到的红包";
        that.title1 = that.GLOBAL.userInfo.nickname + "收到的红包";
        // 获取接收的红包列表
        console.log("::::::::::::::::::::::::::::::::::::::::::::::::::::::");
        that.getReciveRedList();
      } else {
        console.log("''''''''''''''''''''''''''''''''''''''''''''''''''''''");
        // 获取发送的红包列表
        that.getSendRedList();
      }
      that.watchStatus = true;
    });
    this.onmessage();
  },
  watch: {
    // 检测年份是否发生变化
    year: function() {
      if (this.flag) {
        this.deg = true; //收起年份标志
        this.choseYearStatus = false; //隐藏年份列表
        // 初始化数据
        this.redSendYear = this.year;
        this.redSendList = "";
        this.redSendPage = 0;
        this.totalSendSum = 0;
        this.totalSendNum = 0;
        if (this.watchStatus) {
          // 获取发送当前年份发送的红包列表
          this.getSendRedList();
        }
      } else {
        this.deg = true;
        this.choseYearStatus = false;
        // 初始化数据
        this.redReciveYear = this.year;
        this.redReciveList = "";
        this.redRecivePage = 0;
        this.totalReciveSum = 0;
        this.totalReciveNum = 0;
        if (this.watchStatus) {
          // 获取当前年份收到的红包列表
          this.getReciveRedList();
        }
      }
    }
  },
  methods: {
    gotoRedDetail(red_id) {
      this.$router.push({
        path: "/redbagdetail",
        query: { red_id: red_id, flag: this.flag }
      });
    },
    // 取消
    cancel() {
      // 隐藏透明背景
      this.choseYearStatus = false;
      // 隐藏红包选项
      this.redStatus = false;
      // 收起年份列表
      this.deg = true;
    },
    /**
     * 选择展示发送的红包信息
     */
    changeToSendRed() {
      this.redStatus = false;
      this.flag = true;
      this.title = "发出的红包";
      this.title1 = this.GLOBAL.userInfo.nickname + "发出的红包";
      this.year = this.redSendYear;
      if (!this.redSendList) {
        this.getSendRedList();
      } else {
        this.list = this.redSendList;
      }
    },
    /**
     * 选择展示收到的红包信息
     */
    changeToRecivedRed() {
      this.redStatus = false;
      this.flag = false;
      this.title = "收到的红包";
      this.title1 = this.GLOBAL.userInfo.nickname + "收到的红包";
      this.year = this.redReciveYear;
      if (!this.redReciveList) {
        this.getReciveRedList();
      } else {
        this.list = this.redReciveList;
      }
    },
    /**
     * 显示红包选项
     */
    changeRedStatus() {
      this.redStatus = true;
    },
    /**
     * 选择年份
     */
    choseYear() {
      this.choseYearStatus = true;
      this.deg = false;
    },
    /**
     *获取发送的红包列表
     */
    getSendRedList() {
      let data = {
        type: "getSendRedList",
        year: this.year,
        page: this.redSendPage,
        send_id: this.GLOBAL.userInfo.id,
        con_id: this.GLOBAL.connectionId
      };
      let that = this;
      this.senddata({
        data: data,
        callback: Response => {
          // console.log("<<<<<<<<<<<redlist>>>>>>>>>>>>>");
          // console.log(Response);
          // console.log("<<<<<<<<<<<redlist>>>>>>>>>>>>>");
          if (that.redSendList) {
            // 向列表追加数据
            that.redSendList = that.redSendList.concat(Response.list);
          } else {
            that.redSendList = Response.list;
          }
          that.list = that.redSendList;
          if (Response.totalNum) {
            that.totalSendNum = Response.totalNum;
          }
          if (Response.totalSum) {
            that.totalSendSum = Response.totalSum;
          }
        },
        callbackFlag: "responeGetSendRedList",
        handType: "user"
      });
    },
    /**
     * 获取更多，相当于翻页
     */
    moreList() {
      // console.log(this.flag);
      if (this.flag) {
        this.redSendPage++;
        this.getSendRedList();
      } else {
        this.redRecivePage++;
        this.getReciveRedList();
      }
    },
    /**
     * 获取接收到的红包列表
     */
    getReciveRedList() {
      // this.$messagebox.alert("查询收到的红包裂帛");
      // return false;
      let data = {
        type: "getReciveRedList",
        year: this.year,
        page: this.redRecivePage,
        send_id: this.GLOBAL.userInfo.id,
        con_id: this.GLOBAL.connectionId
      };
      let that = this;
      this.senddata({
        data: data,
        callback: Response => {
          // console.log("<<<<<<<<<<<redlist>>>>>>>>>>>>>");
          // console.log(Response);
          // console.log("<<<<<<<<<<<redlist>>>>>>>>>>>>>");
          if (that.redReciveList) {
            // 向收到的红包列表追加信息
            that.redReciveList = that.redReciveList.concat(Response.list);
          } else {
            that.redReciveList = Response.list;
          }
          that.list = that.redReciveList;
          if (Response.totalNum) {
            that.totalReciveNum = Response.totalNum;
          }
          if (Response.totalSum) {
            that.totalReciveSum = Response.totalSum;
          }
        },
        callbackFlag: "responeGetReciveRedList",
        handType: "user"
      });
    }
  }
};
</script>

<style scoped>
.year-box {
  color: #f0c69e;
  font-size: 5vw;
  position: absolute;
  top: -2vw;
  right: 0px;
}
.upbtn {
  -ms-transform: rotate(90deg);
  -webkit-transform: rotate(90deg);
  -moz-transform: rotate(90deg);
  -o-transform: rotate(90deg);
  transform: rotate(90deg);
  background: #b34b39;
  border: 0px;
  box-shadow: 0px 0px 0px 0px;
  color: #f0c69e;
  font-size: 7vw;
}
.downbtn {
  -ms-transform: rotate(-90deg);
  -webkit-transform: rotate(-90deg);
  -moz-transform: rotate(-90deg);
  -o-transform: rotate(-90deg);
  transform: rotate(-90deg);
  background: #b34b39;
  border: 0px;
  box-shadow: 0px 0px 0px 0px;
  color: #f0c69e;
  font-size: 7vw;
}
.top-box {
  height: 70vw;
  background: #b34b39;
}
.ellipsis-box {
  width: 1vw;
  text-align: center;
  height: 55vw;
  font-size: 7vw;
  margin-top: -4vw;
  transform: rotate(90deg);
  -ms-transform: rotate(90deg);
  -moz-transform: rotate(90deg);
  -webkit-transform: rotate(90deg);
  -o-transform: rotate(90deg);
}
.user-information-box {
  position: relative;
  /* padding-top: 6vw; */
}
.user-head-box {
  width: 20vw;
  height: 20vw;
  border-radius: 20vw;
  overflow: hidden;
  border: 1px solid white;
  margin: 5vw auto 0px;
  justify-content: center;
  justify-items: center;
  align-content: center;
  align-items: center;
  display: flex;
}
.user-head-box img {
  max-width: 100%;
  max-height: 100%;
}
.user-tips-box {
  color: #ffffff;
  font-size: 4.5vw;
  height: 10vw;
  line-height: 9vw;
  /* text-shadow: 0px 0px 1px white; */
  /* font-weight:  */
  letter-spacing: 1px;
}
.red-total-money {
  font-size: 8vw;
  color: #fed291;
}
.red-total-money .yung-box {
  font-size: 5vw;
}
.red-total-number {
  font-size: 4.5vw;
  color: #ffffff;
}
.red-total-number .total-number {
  color: #fed291;
}
.one-red-info-box {
  height: 18vw;
  background: white;
  box-sizing: border-box;
  padding: 0px 4vw;
  margin-top: 0.5vw;
  border-bottom: 0.5vw solid #e7e7e7;
}
.red-detail-up,
.red-detail-down {
  display: flex;
  height: 9vw;
}
.red-detail-up {
  font-size: 4vw;
  line-height: 13vw;
}
.red-detail-down {
  font-size: 4vw;
  color: #999;
  line-height: 8vw;
}
.red-detail-up .red-detail-child,
.red-detail-down .red-detail-child {
  display: flex;
  width: 50vw;
  /* align-items: center; */
  /* align-content: center; */
}
.red-money,
.red-status {
  align-content: center;
  justify-content: flex-end;
}
.red-money img {
  width: 6vw;
  height: 6vw;
  margin-top: 3vw;
}
.bgbox {
  width: 100vw;
  height: 100vh;
  position: fixed;
  top: 0px;
  left: 0px;
  z-index: 1001;
  background: black;
  opacity: 0.3;
}
.show-year-box {
  width: 80vw;
  height: 76vw;
  background: white;
  position: fixed;
  top: 32vw;
  left: 10vw;
  z-index: 1002;
  overflow: auto;
  border-radius: 1vw;
}
.show-year-box li {
  height: 12vw;
  margin-top: 0px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0px 8vw;
  box-sizing: border-box;
  border-bottom: 0.5vw solid #f1f1f1;
}
.to-send-btn {
  color: red;
  text-decoration: underline;
}
.red-status-box {
  position: fixed;
  bottom: 0px;
  left: 0px;
  z-index: 1003;
  height: 30vw;
  width: 100vw;
  background: #ffffff;
  box-shadow: 1vw 1vw 1vw 1vw #000000;
  text-align: left;
}
.red-status-box li {
  height: 12vw;
  line-height: 12vw;
  box-sizing: border-box;
  padding: 0px 2vw;
  font-size: 4.5vw;
  font-weight: 600;
}
.red-status-box li:nth-child(1) {
  border-bottom: 0.5vw solid #efefef;
}
</style>