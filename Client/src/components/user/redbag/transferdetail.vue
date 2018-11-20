<template>
  <div class="transfer-detail-box">
    <mt-header title="" fixed="true">
      <router-link to="/chat" slot="left">
        <mt-button icon="back" style="color:black;"></mt-button>
      </router-link>
    </mt-header>
    <div :class="status == '待收款' ? 'transfer-status-box-torecived':'transfer-status-box-recived'">{{status}}</div>
    <div class="transfer-jine-box">
      <span>￥</span>
      <span>{{gold}}</span>
    </div>
    <div class="transfer-tips-box">
      <p>{{tips}}</p>
      <p>转账时间： {{datetime}}</p>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      status: "待收款",
      gold: "0.00",
      tips: "24小时内未确认，将对还到我的账户上",
      datetime: "待定"
    };
  },
  mounted: function() {
    this.$parent.tabbarShow = false;
    let that = this;
    this.checkLogin(function() {
      that.getTransferInfo();
    });
    this.onmessage();
  },
  methods: {
    getTransferInfo() {
      let data = {
        type: "getTransferInfo",
        transfer_id: this.$route.query.transfer_id,
        con_id: this.GLOBAL.connectionId
        // send_id: this.GLOBAL.userInfo.id,
        // recive_id: this.GLOBAL.friendInfo.info.id,
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(respone) {
          // console.log(respone);
          that.datetime = respone.info.addtime;
          that.gold = respone.info.gold + "";
          if (that.gold.indexOf(".") == -1) {
            that.gold += ".00";
          } else {
            let a = that.gold.split(".");
            if (a[1].length < 2) {
              that.gold += "0";
            }
          }
          if (respone.info.status == 1) {
            that.status = "已收款";
            if (respone.info.send_id == that.GLOBAL.userInfo.id) {
              that.tips = "已存到对方的账户当中";
            } else {
              that.tips = "已存到我的账户当中";
            }
          } else if (respone.info.status == 2) {
            that.status = "已过期";
            if (respone.info.send_id == that.GLOBAL.userInfo.id) {
              that.tips = "金币已经退还至我的账户";
            } else {
              that.tips = "金币已退还到对方的账户";
            }
          } else {
            that.status = "待收款";
          }
        },
        handType: "user"
      });
    }
  }
};
</script>
<style>
.transfer-status-box-torecived {
  width: 35vw;
  height: 35vw;
  border-radius: 35vw;
  line-height: 35vw;
  text-align: center;
  color: #7dd1d5;
  margin: 20vw auto 0px;
  border: 1vw solid #56d5de;
  font-size: 6.5vw;
  /* font-weight: bold; */
}
.transfer-status-box-recived {
  width: 35vw;
  height: 35vw;
  border-radius: 35vw;
  line-height: 35vw;
  text-align: center;
  color: #eec03d;
  margin: 20vw auto 0px;
  border: 1vw solid #f7b724;
  font-size: 6.5vw;
  /* font-weight: bold; */
}
.transfer-jine-box {
  font-size: 10vw;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 25vw;
  font-weight: bold;
}
.transfer-jine-box span:nth-child(1) {
  font-size: 8vw;
}
.transfer-tips-box {
  font-size: 4.5vw;
  font-weight: bold;
  color: #585858;
}
.transfer-tips-box p {
  height: 10vw;
  line-height: 10vw;
}
.transfer-tips-box p:nth-child(2) {
  color: #909090;
  font-weight: 400;
}
</style>

