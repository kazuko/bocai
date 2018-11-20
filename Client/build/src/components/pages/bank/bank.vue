<template>
    <div>
        <mt-header title="社区银行">
            <router-link to="/" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="pageContent bank">
            <div class="mt10 bankMoney">
                已存金币： {{bankMoney}}
            </div>
            <div class="mt10 option">
                <mt-field label="当前金币" type="number" v-model="money" autocomplete="off"></mt-field>
            </div>
            <div class="mt10 option">
                <mt-field type="number" placeholder="请输入金额" v-model="storage" autocomplete="off" :disableClear="true">
                    <mt-button @click="operationStorage">存金币</mt-button>
                </mt-field>
                <mt-field type="number" placeholder="请输入金额" v-model="takeout" autocomplete="off" :disableClear="true">
                    <mt-button @click="operationTakeout">取金币</mt-button>
                </mt-field>
            </div>
        </div>
    </div>
</template>

<script>
console.log("PAGES_BANK_BANK_VUE");
    export default {
        data(){
            return{
                money: this.$store.state.songInfo.user.gold,
                bankMoney: this.$store.state.songInfo.user.bank,
                storage: '',
                takeout: '',
                true: true,
                transPsd: '456',
            }
        },
        methods:{
            operationStorage: function(){
                if(parseInt(this.storage)<=parseInt(this.money)&&parseInt(this.storage)>0){
                    this.bankMoney = parseInt(this.storage) + parseInt(this.bankMoney);
                    this.money = parseInt(this.money) - parseInt(this.storage);
                    this.$axios.post('api/bocai/index.php/Home/bank/index.html',{
                        operationBankType: 1,
                        newProperty: this.money,
                        newBank: this.bankMoney,
                        storage: this.storage
                    }).then(response => {
                        console.log(response)
                        if(response.data.code == 1){
                            this.$store.state.songInfo.user.gold = this.money;
                            this.$store.state.songInfo.user.bank = this.bankMoney;
                            this.$messagebox.alert("存钱成功!","提示").then(action=>{
                                this.storage = '';
                            });
                        }
                        // this.$store.state.songInfo.user.gold = 
                    })
                }else if(parseInt(this.storage)>parseInt(this.money)){
                    this.$messagebox.alert("当前金币不足，存钱失败","提示").then(action=>{
                        this.storage = '';
                    });
                }else{
                    this.$messagebox.alert("请输入正确的储存金额").then(action=>{
                        this.storage = '';
                    });
                }
            },
            operationTakeout: function(){
                if(parseInt(this.takeout)>parseInt(this.bankMoney)){
                    this.$messagebox.alert("银行剩余金币不足，取钱失败！","提示").then(action => {
                        this.takeout = ''
                    })
                }else if(parseInt(this.takeout)<=parseInt(this.bankMoney)&&parseInt(this.takeout)>0){
                    this.$messagebox.prompt('请输入交易密码','',{inputType: 'password',}).then(({ value,action }) => {
                        if(parseInt(value) == parseInt(this.transPsd)){
                            this.bankMoney = parseInt(this.bankMoney) - parseInt(this.takeout);
                            this.money = parseInt(this.money) + parseInt(this.takeout);
                            this.$axios.post('api/bocai/index.php/Home/bank/index.html',{
                                operationBankType: 0,
                                newProperty: this.money,
                                newBank: this.bankMoney,
                                takeout: this.takeout
                            }).then(response => {
                                console.log(response);
                                if(response.data.code == 1){
                                    this.$store.state.songInfo.user.gold = this.money;
                                    this.$store.state.songInfo.user.bank = this.bankMoney;
                                    this.$messagebox.alert("取钱成功","提示").then(action => {
                                        this.takeout = '';
                                    });
                                }
                            })
                        }else{
                            this.$messagebox.alert("交易密码输入错误","提示")
                        }
                    })
                }else{
                    this.$messagebox.alert("银行剩余金币不足，取钱失败！","提示").then(action => {
                        this.takeout = ''
                    })
                }

                
            }
        }
    }
</script>

<style scoped>
.bankMoney{
    padding: 0 10px;
    color: #ffba00;
    text-align: left;
    font-weight: 600;
    font-family: "黑体";
    font-size: 15px;
}
.option button{
    background-color: #57d6dd;
    color: #fff;
    font-size: 14px;
}
</style>