<template>
    <div>
        <mt-header title="添加好友">
            <router-link to="/friend" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="pageContent">
            <div class="findBox">
                <div class="findId">
                    <mt-radio :options="['']" align="left"></mt-radio>
                    <mt-field label="用户ID查找：" placeholder="请输入用户ID" type="number" v-model="userId"></mt-field>
                </div>
                <div class="findName">
                    <mt-field label="用户昵称查找:" placeholder="请输入用户昵称" v-model="username"></mt-field>
                </div>
                <div class="btn">
                    <mt-button class="cancel">取消</mt-button>
                    <mt-button class="findbtn" @click="searchFriend">查找</mt-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
console.log("PAGES_PAGEFRIEND_ADDFRIEND_VUE");
    export default {
        data(){
            return{
                userId: '',
                username: '',

            }
        },
        methods: {
            searchFriend: function(){
                // 前端测试
                if(this.userId || this.username){
                    this.$router.push('/searchFriend')
                }
            }
        },
        beforeCreate: function() {
            document.getElementsByTagName("body")[0].className="bgc-fff";
        },
        beforeDestroy: function() {
            document.body.removeAttribute("class","bgc-fff");
        },
    }
</script>

<style scoped>
.findId{
    display: flex;
    flex-wrap: nowrap;
}
.findBox{
    font-size: 14px;
    padding: 10px;
    position: relative;
}

.findId,.findName{
    margin: 15px 0;
}
.btn{
    display: flex;
    justify-content: space-between;
}
.cancel{
    width: 45%;
    background-color: #fff;
    border: 1px solid #57D6DD;
    border-radius: 0;
    color: #57D6DD;
    font-size: 14px;
}
.findbtn{
    width: 45%;
    background-color: #57D6DD;
    border: 1px solid #57D6DD;
    color: #fff;
    border-radius: 0;
    font-size: 14px;
}

</style>