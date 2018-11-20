<template>
  <div>
    <div id="sangong">
      <div class="game-container">
        <div class="game-table">
          <div class="top">
            <div class="btn">
              <img src="./../../../assets/圆角矩形1拷贝@2x.png"
                   @click="goback">
              <img src="./../../../assets/-@2x.png">
              <img src="./../../../assets/矩形2拷贝@2x.png">
            </div>
            <div class="fapai"><img src="./../../../assets/发牌按钮@2x.png">
              <scan v-if="countDown>0">{{ countDown }}</scan>
              <scan v-else>开牌中</scan>
            </div>
          </div>
          <div class="banker">
            <div class="banker-card">
              <img v-for="(item,index) in cards.banker"
                   :key="index"
                   v-bind:src="'static/'+item"
                   alt="">
              <span v-show="cardValue.banker+''">{{cardValue.banker}}</span>
            </div>
            <div class="banker-info">
              <div class="banker-head">
                <img :src="host + banker.head">
              </div>
              <div class="banker-name">
                <p class="banker-nickname">{{banker.nickname}}</p>
                <p>金币：{{banker.gold}}</p>
              </div>
            </div>
          </div>
          <div class="player">
            <div class="player-box">
              <div class="player-card">
                <img v-for="(item,index) in cards.player1"
                     :key="index"
                     v-bind:src="'static/'+item"
                     alt="">
                <span v-show="cardValue.player1+''">{{cardValue.player1}}</span>
              </div>
              <div class="player-info">
                <div class="player-head">
                  <img src="./../../../assets/图层15@2x.png">
                </div>
                <div class="player-name">
                  <p class="player-nickname">nickname</p>
                  <p>gold 2000</p>
                </div>
              </div>
            </div>
            <div class="player-box">
              <div class="player-card">
                <img v-for="(item,index) in cards.player2"
                     :key="index"
                     v-bind:src="'static/'+item"
                     alt="">
                <span v-show="cardValue.player2+''">{{cardValue.player2}}</span>
              </div>
              <div class="player-info">
                <div class="player-head">
                  <img src="./../../../assets/图层15@2x.png">
                </div>
                <div class="player-name">
                  <p class="player-nickname">nickname</p>
                  <p>金币 1999</p>
                </div>
              </div>
            </div>
            <div class="player-box">
              <div class="player-card">
                <img v-for="(item,index) in cards.player3"
                     :key="index"
                     v-bind:src="'static/'+item"
                     alt="">
                <span v-show="cardValue.player3+''">{{cardValue.player3}}</span>
              </div>
              <div class="player-info">
                <div class="player-head">
                  <img src="./../../../assets/图层15@2x.png">
                </div>
                <div class="player-name">
                  <p class="player-nickname">nickname</p>
                  <p>金币 1998</p>
                </div>
              </div>
            </div>
          </div>
          <div class="bets">
            <div class="bets-title">庄对牌以上</div>
            <div class="row col-name">
              <div class="td">闲1</div>
              <div class="td">闲2</div>
              <div class="td">闲3</div>
            </div>
            <div class="row col-type">
              <div class="td"
                   value="player1"
                   :class="percent.toDeal.player1.active ? 'active':''"
                   @click="bet('toDeal', 'player1')">
                <p class="name"
                   value="toDeal">对牌以上</p>
              </div>
              <div class="td"
                   value="player2"
                   :class="percent.toDeal.player2.active ? 'active':''"
                   @click="bet('toDeal', 'player2')">
                <p class="name"
                   value="toDeal">对牌以上</p>
              </div>
              <div class="td"
                   value="player3"
                   :class="percent.toDeal.player3.active ? 'active':''"
                   @click="bet('toDeal', 'player3')">
                <p class="name"
                   value="toDeal">对牌以上</p>
              </div>
            </div>

            <div v-for="(item,index) in percent"
                 :key="index"
                 v-if="item.show"
                 class="row">
              <div class="td"
                   value="player1"
                   :class="item.player1.active ? 'active' : ''"
                   @click="bet(index, 'player1')">
                <p class="name"
                   value="sanGong">{{item.name}}</p>
                <p>1:{{item.player1.odds}}</p>
              </div>
              <div class="td"
                   value="player2"
                   :class="item.player2.active ? 'active' : ''"
                   @click="bet(index, 'player2')">
                <p class="name"
                   value="sanGong">{{item.name}}</p>
                <p>1:{{item.player2.odds}}</p>
              </div>
              <div class="td"
                   value="player3"
                   :class="item.player3.active ? 'active' : ''"
                   @click="bet(index, 'player3')">
                <p class="name"
                   value="sanGong">{{item.name}}</p>
                <p>1:{{item.player3.odds}}</p>
              </div>
            </div>

            <div class="row row-chip">
              <div class="td"
                   v-for="(value,index) in jetton"
                   :key="index"
                   :class="value.active?'active':''"
                   @click="choseGold(value.value, index)">{{value.value}}</div>
            </div>
            <div class="row user-chip">
              <div class="td">自定义下注:</div>
              <div class="td">
                <input v-model="betGold"
                       type="number"
                       class="betgold">
              </div>
              <div class="td user-td">剩余金币:</div>
              <div class="td user-td">{{this.GLOBAL.userInfo.gold}}</div>
            </div>
          </div>
          <mt-button class="bets-btn"
                     size="large"
                     @click="desicdeXiaZhe">下注</mt-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
console.log("GAME_SANGONG_SANGONG_VUE");

export default {
  data() {
    return {
      betGold: 0,
      palyer1ToDeal: false,
      palyer2ToDeal: false,
      palyer3ToDeal: false,
      cardCache: {},
      cardValue: {
        banker: "",
        player1: "",
        player2: "",
        player3: ""
      },

      // 庄家信息
      banker: {
        head: "",
        nickname: "",
        gold: ""
      },
      host: this.GLOBAL.Host,
      // 赔率
      percent: {
        toDeal: {
          player1: {
            active: false
          },
          player2: {
            active: false
          },
          player3: {
            active: false
          }
        }
      },
      // 下注金额
      jetton: {},
      // 最低下注
      limit: {},
      // 历史纪录
      history: {},
      // 开牌牌面信息
      openCards: {},
      // 显示牌面
      cards: {
        banker: {
          0: "反面.png",
          1: "反面.png",
          2: "反面.png"
        },
        player1: {
          0: "反面.png",
          1: "反面.png",
          2: "反面.png"
        },
        player2: {
          0: "反面.png",
          1: "反面.png",
          2: "反面.png"
        },
        player3: {
          0: "反面.png",
          1: "反面.png",
          2: "反面.png"
        }
      },
      // 开奖顺序列表
      nextOne: {
        0: "banker",
        1: "player1",
        2: "player2",
        3: "player3"
      },
      // 开奖循序
      nextIndex: 0,
      // 循环统计
      indexnum: 0,

      // 开牌倒计时句柄
      kaipaiHandle: "",
      // 发牌倒计时句柄
      fapaiHandle: "",
      // 倒计时句柄
      countDownHandle: "",
      // 倒计时时间
      countDown: 0,
      // 保存下注倒计时
      xiazhuDown: 0,
      // 开牌起始下标
      startIndex: 0,
      // 下注信息
      betInfo: {
        type: "play",
        player: "player",
        id: this.GLOBAL.userInfo.id,
        bet: [],
        gold: 0,
        allGold: 0,
        remaining: this.GLOBAL.userInfo.gold
      }
    };
  },
  watch: {
    betGold: function() {
      if (this.betGold) {
        this.betInfo.gold = this.betGold;
        for (let i = 0; i < Object.keys(this.jetton).length; i++) {
          this.jetton[i]["active"] = false;
        }
      } else {
      }
    }
  },
  methods: {
    /**
     * 下注按钮
     */
    desicdeXiaZhe() {
      if (Object.keys(this.betInfo.bet).length) {
        console.log("=============limit=============");
        console.log(
          "最低限制" + this.limit.gold + ":下注金币" + this.betInfo.gold
        );
        console.log("=============limit=============");
        if (this.betInfo.gold < this.limit.gold) {
          this.$messagebox.alert("最低下注金币不得低于" + this.limit.gold);
        } else {
          this.betInfo.allGold = this.betInfo.bet.length * this.betInfo.gold;
          if (this.betInfo.allGold > this.GLOBAL.userInfo.gold) {
            this.$messagebox.alert("您的金币已不足！");
          } else {
            this.GLOBAL.userInfo.gold -= this.betInfo.allGold;
            let that = this;
            this.senddata(this.betInfo, function(resopne) {
              console.log(resopne);
              that.clearStatu();
            });
            console.log(this.GLOBAL.userInfo.gold);
          }
        }
      } else {
        this.$messagebox.alert("您还没有下注！");
      }
    },
    /**
     * 清除选择状态
     */
    clearStatu() {
      // 清除下注项的选中状态
      for (var index in this.percent) {
        for (var jdex in this.percent[index]) {
          if (this.percent[index][jdex].active) {
            this.percent[index][jdex].active = false;
          }
        }
      }
      // 清除单注金额选择状态
      for (let i = 0; i < Object.keys(this.jetton).length; i++) {
        if (this.jetton[i].active) {
          this.jetton[i].active = false;
        }
      }
    },
    /**
     * 选择单注金额
     */
    choseGold(gold, index) {
      if (this.countDown) {
        this.betInfo.gold = gold;
        this.betGold = 0;
        console.log(Object.keys(this.jetton).length);
        for (let i = 0; i < Object.keys(this.jetton).length; i++) {
          if (i != index) {
            this.jetton[i]["active"] = false;
          } else {
            this.jetton[i]["active"] = true;
          }
        }
        console.log(gold + ":" + index);
        console.log(this.jetton);
        console.log(this.betInfo);
      } else {
        this.$messagebox.alert("现在是开牌时间！");
      }
    },
    /**
     * 选择下注项
     */
    bet: function(index1, index2) {
      console.log(index1 + ":" + index2);
      if (this.countDown) {
        // 下注时间
        // 修改状态
        this.percent[index1][index2]["active"] = !this.percent[index1][index2][
          "active"
        ];
        if (this.percent[index1][index2]["active"]) {
          // 压入选中列表
          let data = {
            player: index2,
            status: index1
          };
          this.betInfo.bet.push(data);
        } else {
          for (let i = 0; i < this.betInfo.bet.length; i++) {
            if (
              this.betInfo.bet[i].status == index1 &&
              this.betInfo.bet[i].player == index2
            ) {
              // 从选中列表中删除元素
              this.betInfo.bet.splice(i, 1);
              break;
            }
          }
        }
        console.log(this.betInfo.bet);
      } else {
        this.$messagebox.alert("现在是开牌时间！");
      }
    },
    /**
     * 后退
     */
    goback: function() {
      if (this.countDown <= 0 && Object.keys(this.betInfo.bet).length) {
        this.$messagebox
          .confirm("开牌中，您已经下注，贸然离开会造成损失，您确定离开吗？")
          .then(action => {
            let data = {
              type: "leaveDesk"
            };
            this.senddata(data);
            clearInterval(this.countDownHandle);
            if (this.fapaiHandle) {
              clearInterval(this.fapaiHandle);
            }
            if (this.kaipaiHandle) {
              clearInterval(this.kaipaiHandle);
            }
            this.$router.push("/sgHall");
          });
      } else {
        let data = {
          type: "leaveDesk"
        };
        this.senddata(data);
        clearInterval(this.countDownHandle);
        if (this.fapaiHandle) {
          clearInterval(this.fapaiHandle);
        }
        if (this.kaipaiHandle) {
          clearInterval(this.kaipaiHandle);
        }
        this.$router.push("/sgHall");
      }
    },
    /**
     * 倒计时
     */
    countDownf: function() {
      let that = this;
      this.countDownHandle = setInterval(function() {
        if (that.countDown > 0) {
          that.countDown--;
        } else {
          console.log("waiting....");
        }
      }, 1000);
    },
    /**
     * 清空牌桌
     */
    clearCards() {
      console.log("-----------------清牌--------------");
      for (var index in this.cards) {
        for (var i = 0; i < this.cards[index].length; i++) {
          this.cards[index][i] = "";
        }
      }
      for (var index in this.cardValue) {
        this.cardValue[index] = "";
      }
      this.$forceUpdate();
      this.fapai(this.startIndex);
    },
    /**
     * 发牌
     */
    fapai() {
      this.nextIndex = this.startIndex;
      console.log(
        "-----------------发牌（" + this.nextIndex + "）--------------"
      );
      let that = this;
      that.indexnum = 0;
      that.fapaiHandle = setInterval(function() {
        if (that.indexnum < 4) {
          // 发牌
          if (that.nextIndex > 3) {
            that.nextIndex = 0;
          }
          for (
            let i = 0;
            i < that.cards[that.nextOne[that.nextIndex]].length;
            i++
          ) {
            that.cards[that.nextOne[that.nextIndex]][i] = "反面.png";
          }
          that.$forceUpdate();
          that.nextIndex++;
          that.indexnum++;
        } else {
          // 进入开牌倒计时
          that.kaipai();
          // 关闭发牌倒计时
          clearInterval(that.fapaiHandle);
          that.fapaiHandle = null;
        }
      }, 1000);
    },
    kaipai() {
      this.nextIndex = this.startIndex;
      console.log(
        "-----------------开牌（" + this.nextIndex + "）--------------"
      );
      let that = this;
      that.indexnum = 0;
      this.kaipaiHandle = setInterval(function() {
        if (that.indexnum < 4) {
          // 开牌
          if (that.nextIndex > 3) {
            that.nextIndex = 0;
          }
          console.log("+++++++++++++++++++++++++++++++");
          console.log(
            that.nextOne[that.nextIndex] +
              ":" +
              that.openCards[that.nextOne[that.nextIndex]]
          );
          console.log("+++++++++++++++++++++++++++++++");
          console.log(that.nextOne[that.nextIndex] + ":" + that.cardCache);
          console.log("+++++++++++++++++++++++++++++++");
          that.cards[that.nextOne[that.nextIndex]] =
            that.openCards[that.nextOne[that.nextIndex]];
          that.cardValue[that.nextOne[that.nextIndex]] =
            that.cardCache[that.nextOne[that.nextIndex]];
          that.nextIndex++;
          that.indexnum++;
        } else {
          that.countDown = that.xiazhuDown;
          that.$forceUpdate();
          // 关闭开牌倒计时
          clearInterval(that.kaipaiHandle);
          that.fapaiHandle = null;
          // 开完牌之后清空下注内容
          that.betInfo.player = "player";
          that.betInfo.bet = [];
          that.betInfo.gold = 0;
          that.betInfo.allGold = 0;
        }
      }, 1000);
    },

    /**
     * onmessage的回掉函数，即接收数据处理数据的函数
     */
    reciveData: function(resopne) {
      if (resopne) {
        let data = JSON.parse(resopne);
        console.log("<respone>");
        console.log(data);
        console.log("</respone>");
        // 判断是否存在牌桌信息
        if (data.desk) {
          // 设置三公的牌桌信息
          this.setGlobalAttribute(this.GLOBAL.sanGongInfo, data);
          // 初始化庄家信息
          this.banker = data.desk.banker;
          // 判断玩家是否为庄家
          if (data.desk.banker.id == this.GLOBAL.userInfo.id) {
            this.betInfo.player = "banker";
          }
          // 初始化赔率信息
          this.percent = data.desk.percent;
          // 初始化下注信息
          this.jetton = data.desk.jetton;
          // 初始化最低限制
          this.limit = data.desk.limit;
          // 初始化历史信息
          this.history = data.desk.history;

          console.log("==============================");
          console.log(this.GLOBAL.userInfo);
          console.log("==============================");
          if (data.account) {
            // 存在开牌信息，表示现在是开牌状态
            // 初始化开牌信息
            // 展示牌面
            this.cards = data.account.deal.deal;
            this.cardValue = data.account.deal.cardType;
            let that = this;
            setTimeout(function() {
              // 初始化倒计时
              that.countDown = data.countDown;
              // 执行倒计时
            }, data.accountDown * 1000);
          } else {
            // 初始化倒计时
            this.countDown = data.countDown;
          }
        } else if (data.deal) {
          if (data.banker) {
            this.banker = data.banker;
          }
          if (data.remaining) {
            // 计算余额
            this.GLOBAL.userInfo.gold = data.remaining;
            this.betInfo.remaining = data.remaining;
            localStorage.setItem(
              "bc_userInfo",
              JSON.stringify(this.GLOBAL.userInfo)
            );
          }

          // 开牌数据
          this.openCards = data.deal;
          // 设置开牌起始位置
          this.startIndex = data.sub;
          this.xiazhuDown = data.countDown;
          this.cardCache = data.cardType;
          this.clearStatu();
          this.clearCards();
        }
      }
    }
  },
  /**
   *
   */
  mounted: function() {
    // 检测是否登陆
    if (!this.GLOBAL.userInfo.id) {
      this.$router.push("/login");
    } else {
      let that = this;
      // 判断是否创建socket
      if (!this.GLOBAL.gameSocketHand) {
        // 创建socket
        this.createSocket(this.GLOBAL.sanGongHost, function() {
          let data = {
            type: "enterDesk",
            desk: that.$route.query.desk,
            id: that.GLOBAL.userInfo.id
          };
          // 设置接收处理数据的回掉函数
          that.onmessage(that.reciveData);
          // 发送进入牌桌的请求
          that.senddata(data);
        });
      } else {
        // 设置接收数据和处理数据的回掉函数
        this.onmessage(that.reciveData);
        // 初始化数据
        console.log("==============================");
        console.log(this.GLOBAL.sanGongInfo);
        console.log(this.GLOBAL.userInfo.gold);
        console.log("==============================");
        this.$forceUpdate();
        // 庄家信息
        this.banker = this.GLOBAL.sanGongInfo.desk.banker;
        // 赔率信息
        this.percent = this.GLOBAL.sanGongInfo.desk.percent;
        // 下注信息
        this.jetton = this.GLOBAL.sanGongInfo.desk.jetton;
        // 最低限制
        this.limit = this.GLOBAL.sanGongInfo.desk.limit;
        // 历史信息
        this.history = this.GLOBAL.sanGongInfo.desk.history;
        this.betInfo.remaining = this.GLOBAL.userInfo.gold;

        if (this.GLOBAL.sanGongInfo.account) {
          // 存在开牌信息，表示现在是开牌状态
          // 初始化开牌信息
          this.cardValue = this.GLOBAL.sanGongInfo.account.deal.cardType;
          this.cards = this.GLOBAL.sanGongInfo.account.deal.deal;
          let that = this;
          // 设置this.GLOBAL.sanGongInfo.accountDown秒后开启进入下注时间，并开始倒计时
          setTimeout(function() {
            // 初始化倒计时
            that.countDown = that.GLOBAL.sanGongInfo.countDown;
          }, this.GLOBAL.sanGongInfo.accountDown * 1000);
        } else {
          // 初始化倒计时
          this.countDown = this.GLOBAL.sanGongInfo.countDown;
        }
      }
      this.countDownf();
      this.$parent.tabbarShow = false;
    }
  },
  beforeCreate: function() {
    // this.setGlobalAttribute(this.initCards, this.cards);
    // this.initCards = this.cards;
  },

  beforeDestroy: function() {
    document.getElementsByTagName("html")[0].removeAttribute("class", "mg0");
    document.body.removeAttribute("class", "mg0");
  }
};
</script>

<style scoped>
.betgold {
  background: transparent;
  width: 100%;
  height: 100%;
  border: none;
  outline: none;
  font-size: 5vw;
  color: yellow;
}
#sangong {
  background: url("./../../../assets/色彩平衡1@2x.png") no-repeat;
  background-size: 100% 100%;
  overflow: hidden;
  overflow-y: auto;
}
.game-container {
  padding: 10px;
}
.top {
  display: flex;
  justify-content: space-between;
}
.btn {
  display: flex;
  flex-wrap: nowrap;
}
.btn img {
  height: 35px;
  width: 35px;
  margin-left: 10px;
  margin-top: 2.5px;
}
.fapai img {
  height: 40px;
}
.fapai scan {
  color: white;
}
.banker {
  margin-top: 10px;
  padding: 0 30px;
  display: flex;
  flex-wrap: nowrap;
}
.banker-card {
  position: relative;
  height: 60px;
  width: 90px;
  padding-top: 10px;
}
.banker-card img {
  height: 60px;
  width: 40px;
  position: absolute;
}
.banker-card img:nth-child(2) {
  left: 20px;
}
.banker-card img:nth-child(3) {
  left: 40px;
}
.banker-info {
  margin-top: 2.5px;
  height: 60px;
  padding: 5px;
  border: 1px solid #aaa3e5;
  display: flex;
  flex-wrap: nowrap;
  border-radius: 5px;
}
.banker-head {
  height: 60px;
  width: 60px;
}
.banker-head img {
  height: 100%;
  width: 100%;
}
.banker-name {
  text-align: left;
  padding: 5px;
  font-size: 14px;
  color: #e7e3f6;
  line-height: 25px;
}
.banker-nickname {
  max-width: 70px;
  overflow: hidden;
}
.banker-name p:last-child {
  font-size: 16px;
}
.player {
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
}
.player-box {
  width: 30%;
  min-width: 90px;
  overflow: hidden;
}
.player-card {
  position: relative;
  height: 60px;
  width: 90px;
}
.player-card span,
.banker-card span {
  position: absolute;
  background: yellow;
  display: block;
  width: 22vw;
  height: 8vw;
  line-height: 8vw;
  left: 0px;
  top: 8.5vw;
  font-size: 7vw;
  opacity: 0.7;
  filter: opacity(7);
  color: green;
  border-radius: 1vw;
}
.banker-card span {
  top: 11.5vw;
}
.player-card img {
  height: 60px;
  width: 40px;
  position: absolute;
}
.player-card img:nth-child(2) {
  left: 20px;
}
.player-card img:nth-child(3) {
  left: 40px;
}
.player-info {
  height: 30px;
  padding: 5px 0;
  display: flex;
  justify-content: space-between;
}
.player-head {
  height: 30px;
  width: 30px;
}
.player-head img {
  height: 30px;
  width: 30px;
}
.player-name {
  text-align: left;
  font-size: 12px;
  color: #e7e3f6;
  line-height: 16px;
  margin-left: 3px;
}
.bets-title {
  color: #fff;
  font-size: 12px;
  padding: 5px;
  background-color: rgba(255, 255, 255, 0.1);
}
.row {
  display: flex;
  flex-wrap: nowrap;
}
.td {
  color: #ece4f1;
  border: 1px solid #7d4ed1;
  width: 33%;
  font-size: 12px;
  line-height: 20px;
}
.row .td:nth-child(2) {
  border-left: none;
  border-right: none;
}
.col-name .td {
  line-height: 26px;
  border: 1px solid #bacacd;
  background-color: #01c11c;
}
.col-type .td {
  line-height: 24px;
}
.row-chip .td {
  line-height: 30px;
}
.user-chip .td {
  line-height: 40px;
}
.user-td {
  color: #dcbd3a;
}
.bets-btn {
  font-size: 14px;
  color: #ece4f1;
  margin-top: 10px;
  background-color: #bb096e;
}
.active {
  background-color: #bb096e;
  border-color: #d3b7db;
}
</style>
