<template>
    <div style="background:#E7E7E7;border:0px solid red;height:100vh;">
        <mt-header title="提现" style="background:#57D6DD;">
            <router-link to="/user" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="pageContent">
            <div class="attention">
                * 提现请绑定以下提现方式
            </div>
            <mt-cell title="银行卡提现" is-link class="takeway bank-card" :to="bankCard">
                <span style="color: #E94E2F; font-size: 12px" v-show="!isBankCard">未绑定</span>
                <img slot="icon" src="./../../../assets/bankcard.png" width="24" height="24">
            </mt-cell>
            <mt-cell title="支付宝提现" is-link class="takeway alipay" :to="Alipay">
                <span style="color: #E94E2F; font-size: 12px" v-show="!isAlipay">未绑定</span>
                <img slot="icon" src="./../../../assets/iconalipay.png" width="24" height="24">
            </mt-cell>
            <mt-cell title="微信提现" is-link class="takeway wechat" :to="Wechat">
                <span style="color: #E94E2F; font-size: 12px" v-show="!isWechat">未绑定</span>
                <img slot="icon" src="./../../../assets/wechat.png" width="24" height="24">
            </mt-cell>
            <div class="contactManager">
                如需修改绑定资料，请联系管理员
            </div>
        </div>
    </div>
</template>

<script>
console.log("USER_CASH_PUBFORWARD_VUE");
    export default {
        data(){
            return{
                isBankCard: true,
                isAlipay:false,
                isWechat: false,
                bankCard: '',
                Alipay: '',
                Wechat: ''
            }
        },
        methods: {

        },
        created: function(){
            this.$parent.tabbarShow = false;
            if(this.isBankCard){
                    this.bankCard = '/bankCard'
                }else{
                    this.bankCard = '/bindBankCard'
            }
            if(this.isAlipay){
                    this.Alipay = '/alipay'
                }else{
                    this.Alipay = '/bindAlipay'
            }
            if(this.isWechat){
                    this.Wechat = '/wechat'
                }else{
                    this.Wechat = '/bindWechat'
            }
        }
    }
</script>

<style scoped>
.pageContent img{
    display: inline-block;
    margin-right: 10px;
}
.attention{
    font-size: 14px;
    color: #a6a6a6;
    text-align: left;
    line-height: 40px;
    height: 40px;
    text-indent: 1em;
}
.takeway{
    text-align: left;
}
.alipay{
    margin-top: 10px;
}
.wechat{
    margin-top: 1px;
}
.contactManager{
    color: #a6a6a6;
    font-size: 14px;
    height: 60px;
    line-height: 60px;
}
</style>