//请求和处理数据
const gameFunc = {
  data() {
    return {
      nextIssue: 0, //下期开奖期号
      recentInfo: [], //近期开奖信息
      haveGLOBALOrderList: false //是否未支付的订单
    };
  },
  methods: {
    /**
     *getOddsData 请求赔率数据
     * @param {key} 游戏的关键字 例如 江苏快三 jsks
     * @param {断网了} 设为true 方便本地测试
     */
    mixins_getOddsData({ gameKey, 断网了 }) {
      ["odds", "haoma", "shengxiao"].forEach(subkey => {
        if (this[subkey] !== undefined) {
          let haveCache = this.$getLocalCache(gameKey + "-" + subkey);
          if (haveCache !== undefined && haveCache !== false) {
            this[subkey] = haveCache;
          }
        }
      });
      if (断网了) {
        return;
      }

      let version = this.$getLocalCache(gameKey + "-version");
      let odds = this.$getLocalCache(gameKey + "-odds");
      if (version === false || odds === false) version = -1;
      let sendDataToServer = () => {
        this.senddata({ data: { type: "deskInfo", game: gameKey, version } });
      };

      //发起请求
      this.checkGameSocket(() => {
        this.dealOnMessage(); //监听返回的信息
        sendDataToServer();
      });
    },
    //处理返回的数据
    dealOnMessage() {
      let gameKey = this.gameKey;
      this.onmessage(res => {
        res = JSON.parse(res);
        //最新期号
        if (res.issue) {
          this.nextIssue = res.issue;
          this.GLOBAL[gameKey].nextIssue = res.issue;
        }
        //保持心跳
        if (res.code === "pong") {
          this.senddata({ data: { type: "ping" } });
          return;
        }
        //处理赔率信息
        if (res.deskInfo) deskInfoFunc.call(this);
        //处理 近期开奖信息
        if (res.recentInfo) recentInfoFunc.call(this);

        //countdown 启动倒计时
        if (res.countdown && res.interval) {
          this.GLOBAL[gameKey].countdown = res.countdown.shijiancuo * 1000;
          this.GLOBAL[gameKey].interval = res.interval;
          this.mixins_countTime(
            res.countdown.shijiancuo * 1000,
            res.interval,
            "timeToOpen"
          );
        }

        function deskInfoFunc() {
          //对比 version 版本号
          let serverVersion = res.deskInfo.version;
          let localVersion = this.$getLocalCache(gameKey + "-version");
          localVersion = localVersion === false ? -1 : localVersion;

          if (serverVersion !== localVersion) {
            ["odds", "haoma", "shengxiao"].forEach(subkey => {
              if (
                this[subkey] !== undefined &&
                res.deskInfo[subkey] !== undefined
              ) {
                this[subkey] = res.deskInfo[subkey];
                this.$setLocalCache(
                  gameKey + "-" + subkey,
                  res.deskInfo[subkey]
                );
              }
            });
            this.$setLocalCache(gameKey + "-rules", res.deskInfo["rules"]);
            this.$setLocalCache(gameKey + "-version", res.deskInfo["version"]);
          } else {
            ["odds", "haoma", "shengxiao"].forEach(subkey => {
              if (this[subkey] !== undefined) {
                let haveCache = this.$getLocalCache(gameKey + "-" + subkey);
                if (haveCache !== undefined && haveCache !== false) {
                  this[subkey] = haveCache;
                }
              }
            });
            console.log(
              gameKey + " %c赔率没有更改，从localStorage读取",
              "color: red;"
            );
          }
        }

        function recentInfoFunc() {
          console.log("%c 更新开奖信息", "color: red;");
          this.recentInfo = res.recentInfo;
        }
      });
    },

    //切换不同的玩法
    mixins_onSwitch(val) {
      this.currentOdds = val;
    },

    //右上角常用操作弹窗
    mixins_onClickOpr(index) {
      let { gameKey, gameName } = this;
      switch (index) {
        case 0:
          this.$router.push(`/${gameKey}/${gameKey}-charts`);
          break;
        case 1:
          this.$router.push({
            name: "dentGameOpenLately",
            query: { gameKey, gameName }
          });
          break;
        case 2:
          this.$router.push({
            name: "dentGameOrder",
            query: { gameKey }
          });
          break;
      }
    },

    //订单生产页
    mixins_goTOPay(blooean = true) {
      this.$router.push({
        name: "dentGameWaiForPay",
        query: { getOrder: blooean, gameKey: this.gameKey }
      });
    }
  },
  mounted() {
    this.$parent.tabbarShow = false;
  },
  activated() {
    this.$parent.tabbarShow = false;
    this.nextIssue = this.GLOBAL[this.gameKey].nextIssue;
    this.haveGLOBALOrderList = Object.keys(
      this.GLOBAL[this.gameKey].orderList
    ).length;
  }
};

//倒计时
const countDownFunc = {
  data() {
    return {
      timer: {}
    };
  },
  methods: {
    // endTimeStamp时间戳 interval每次开奖间隔
    mixins_countTime(endTimeStamp, interval, dataStr) {
      console.log(
        "开启倒计时" + new Date(endTimeStamp).Format("yyyy-MM-dd hh:mm:ss")
      );
      let setTimer = () => {
        let leftTime = endTimeStamp - new Date().getTime(); //时间差
        // 定义变量 d,h,m,s保存倒计时的时间
        let h, m, s;
        function toFix(str) {
          return str < 10 ? "0" + str : str;
        }
        if (leftTime >= 0) {
          h = toFix(Math.floor((leftTime / 1000 / 60 / 60) % 24));
          m = toFix(Math.floor((leftTime / 1000 / 60) % 60));
          s = toFix(Math.floor((leftTime / 1000) % 60));
          this.$set(this.timer, dataStr, `${h}:${m}:${s}`);
        } else {
          clearInterval(this.timer[dataStr + "Interval"]);
          //开始下一期的倒计时
          this.nextIssue += 1;
          this.GLOBAL[this.gameKey].nextIssue = this.nextIssue + 1;
          this.GLOBAL[this.gameKey].countdown = endTimeStamp + interval * 1000;
          this.mixins_countTime.call(
            this,
            endTimeStamp + interval * 1000,
            interval,
            dataStr
          );
        }
      };
      setTimer();
      clearInterval(this.timer[dataStr + "Interval"]);
      this.timer[dataStr + "Interval"] = setInterval(setTimer, 1000);
    }
  },
  destroyed() {
    let timer = this.timer;
    let reg = new RegExp(/Interval/);
    for (let key in timer) {
      if (reg.test(key)) {
        clearInterval(this.timer[key]);
      }
    }
  }
};

//缓存 每个游戏主界面
const toggleShouldKeepAlive = {
  //离开页面之前
  beforeRouteLeave(to, from, next) {
    let temp = this.$parent.shouldKeepAlive;
    let name = from.name;
    //离开游戏主界面时，判断是进游戏子界面，还是退出到大厅
    if (to.meta.index < from.meta.index) {
      //退出到大厅 ，把组件名字 从缓存数组中 删除，以注销组件
      temp.splice(temp.indexOf(from.name), 1);
    } else {
      //进游戏子界面 ，把组件名字 添加到 缓存数组中，以缓存组件
      if (temp.indexOf(name) === -1) {
        this.$parent.shouldKeepAlive.push(name);
      }
    }

    next();
  },
  //游戏页面即组件注销之后，还原缓存数组的数据。
  destroyed() {
    this.$parent.shouldKeepAlive = [...this.GLOBAL.shouldKeepAlive];
  }
};

//将键值翻译成成为中文
import * as keyJSON from "@/js/keyToCharacter";
const filterMethods = {
  methods: {
    mixin_KeyToCharacter(value, gamekey) {
      if (value === "zhm1-6") return "正码1-6"; //这个是最先做，后续代码影响到了 改起来不容易
      let matchKey = gamekey || this.gameKey;
      if (matchKey === undefined) return value;
      //数字不用匹配直接返回
      if (!isNaN(value)) {
        return value;
      }

      let reg = /_|-/;
      let neeDsplit = typeof value === "string" && value.match(reg);

      if (neeDsplit) {
        let array = value.split(neeDsplit);
        let objkey = array[array.length - 1];
        return isNaN(objkey) ? keyJSON[matchKey][objkey] : objkey;
      } else {
        return keyJSON[matchKey][value] || value;
      }
    }
  }
};

export { gameFunc, countDownFunc, toggleShouldKeepAlive, filterMethods };
