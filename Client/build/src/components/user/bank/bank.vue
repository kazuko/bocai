<template>
    <div>
        <mt-header title="社区银行" fixed='true' style="height:10vw;font-size:4vw;">
            <router-link to="/user" slot="left">
                <mt-button style="height:10vw;" icon="back" class="BACKBTN">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="pageContent bank">
            <div class="mt10 bankMoney">
                已存金币： {{this.GLOBAL.userInfo.bank}}
            </div>
            <div class="mt10 option">
                <!-- <mt-field label="当前金币" type="number" v-model="this.GLOBAL.userInfo.gold" autocomplete="off"></mt-field> -->
                <div class="nowgold">
                    <span>当前金币</span>
                    <span style="margin-left:15vw;">{{this.GLOBAL.userInfo.gold}}</span>
                </div>
            </div>
            <div class="mt10 option bankbox">
                <mt-field type="number" placeholder="请输入金额" v-model="storage_gold" autocomplete="off" :disableClear="true">
                    <mt-button @click="operationStorage">存金币</mt-button>
                </mt-field>
                <mt-field type="number" placeholder="请输入金额" v-model="get_gold" autocomplete="off" :disableClear="true">
                    <mt-button @click="operationTakeout">取金币</mt-button>
                </mt-field>
            </div>
        </div>
    </div>
</template>

<script>
console.log("USER_BANK_BANK_VUE");
export default {
  data() {
    return {
      true: true,
      storage_gold: "",
      get_gold: ""
    };
  },
  methods: {
    operationStorage: function() {
      if(this.storage_gold){
          if(this.storage_gold>this.GLOBAL.userInfo.gold){
             this.$messagebox.alert("存放金额超出当前金额！"); 
          }else{

          }
      }else{
          this.$messagebox.alert("请输入存放金额！");
      }
    },
    operationTakeout: function() {
      // if(parseInt(this.takeout)>parseInt(this.bankMoney)){
      //     this.$messagebox.alert("银行剩余金币不足，取钱失败！","提示").then(action => {
      //         this.takeout = ''
      //     })
      // }else if(parseInt(this.takeout)<=parseInt(this.bankMoney)&&parseInt(this.takeout)>0){
      //     this.$messagebox.prompt('请输入交易密码','',{inputType: 'password',}).then(({ value,action }) => {
      //         if(parseInt(value) == parseInt(this.transPsd)){
      //             this.bankMoney = parseInt(this.bankMoney) - parseInt(this.takeout);
      //             this.money = parseInt(this.money) + parseInt(this.takeout);
      //             this.$axios.post('api/bocai/index.php/Home/bank/index.html',{
      //                 operationBankType: 0,
      //                 newProperty: this.money,
      //                 newBank: this.bankMoney,
      //                 takeout: this.takeout
      //             }).then(response => {
      //                 console.log(response);
      //                 if(response.data.code == 1){
      //                     this.$store.state.songInfo.user.gold = this.money;
      //                     this.$store.state.songInfo.user.bank = this.bankMoney;
      //                     this.$messagebox.alert("取钱成功","提示").then(action => {
      //                         this.takeout = '';
      //                     });
      //                 }
      //             })
      //         }else{
      //             this.$messagebox.alert("交易密码输入错误","提示")
      //         }
      //     })
      // }else{
      //     this.$messagebox.alert("银行剩余金币不足，取钱失败！","提示").then(action => {
      //         this.takeout = ''
      //     })
      // }
    }
  },
  beforeCreate: function() {},
  created: function() {},
  beforeMount: function() {},
  mounted: function() {
    this.checkLogin();
    this.onmessage();
    console.log(this.GLOBAL.userInfo);
    this.$parent.tabbarShow = true;
    this.$parent.tabbarWhic = false;
  },
  beforeUpdate: function() {},
  updated: function() {
    this.$parent.tabbarShow = true;
    this.$parent.tabbarWhic = false;
  },
  beforeDestroy: function() {},
  destroyed: function() {}
};
</script>

<style scoped>
.bankMoney {
  padding: 0 10px;
  color: #ffba00;
  text-align: left;
  font-weight: 600;
  font-family: "黑体";
  font-size: 4vw;
  height: 10vw;
  line-height: 10vw;
}
.option button {
  background-color: #57d6dd;
  color: #fff;
  font-size: 14px;
}
.nowgold{
    background: white;
    text-align: left;
    height: 10vw;
    line-height: 10vw;
    box-sizing: border-box;
    padding: 0px 2vw;
    font-size: 3.5vw;
}
.bank{
    margin-top: 10vw;
}
.bankbox .mint-button{
    font-size: 3vw;
    height: 6vw;
    line-height: 6vw;
}
.bankbox .mint-cell-value{
    height: 10vw;
    font-size: 3.5vw;
}
.mint-button-icon .mintui,.mintui-back{
    font-size: 4vw !important;
}
</style>