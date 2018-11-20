<template>
    <div>
        <mt-header title="添加好友">
            <router-link to="/addFriend" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="pageContent">
            <div class="searchBox" v-show="searchType">
                <div class="title">搜索结果</div>
                <!-- <div class="list" v-for="(item,index) in this.$store.state.addFriendResult" :key="index">
                    <div class="head">
                        <img :src="'http://192.168.0.133'+item.head">
                    </div>
                    <div class="info">
                        <div class="info-top">
                            <div class="nickname">{{item.nickname}}</div>
                            <div class="id">ID:{{item.id}}</div>
                        </div>
                        <div class="medal" v-for="(medal,mIndex) in item.medal" :key="mIndex">
                            <img :src="'http://192.168.0.133'+medal">
                        </div>
                    </div>
                    <mt-button class="btn" @click="sendAdd(item,index)">添加好友</mt-button>
                </div> -->
            </div>
            <div class="searchBox" v-show="!searchType">
                <div class="title">搜索结果</div>
                <div class="list">
                    <div class="head">
                        <!-- <img :src="'http://192.168.0.133'+this.$store.state.addFriendResult.head"> -->
                    </div>
                    <div class="info">
                        <div class="info-top">
                            <!-- <div class="nickname">{{this.$store.state.addFriendResult.nickname}}</div> -->
                            <!-- <div class="id">ID:{{this.$store.state.addFriendResult.id}}</div> -->
                        </div>
                        <div class="medal">
                            <!-- <div  v-for="(medal,mIndex) in this.$store.state.addFriendResult.medal" :key="mIndex"> -->
                                <!-- <img :src="'http://192.168.0.133'+medal"> -->
                            <!-- </div> -->
                        </div>
                    </div>
                    <mt-button class="btn" @click="sendAdd1">添加好友</mt-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
console.log("USER_PAGEFRIEND_SEARCHFRIEND_VUE");
import root from '@/config/root.js'
    export default {
        data(){
            return{
                searchType: false,
            }
        },
        created: function(){
            // if(this.$store.state.addFriendResult.length){
            //     this.searchType = true
            // }else{
            //     this.searchType = false
            // }
        },
        methods: {
            sendAdd: function(item,index){
                this.$axios.post(root.sendAdd,{
                    id: item.id
                }).then(response => {
                    // console.log(response)
                    // if(item.id == this.$store.state.songInfo.user.id){
                    //     this.$messagebox.alert('不能添加自己为好友！')
                    // }
                    if(response.data.code==2){
                        this.$messagebox.alert('已经是好友了！')
                    }
                    if(response.data.code==0){
                        this.$messagebox.alert('发送添加好友请求失败！')
                    }
                    if(response.data.code==1){
                        this.$messagebox.alert('发送添加好友请求成功！')
                    }
                    if(response.data.code==3){
                        this.$messagebox.alert('请不要多次点击添加！')
                    }
                    if(response.data.code==4){
                        this.$messagebox.alert('添加好友成功！')
                        // this.$store.state.songInfo.user.friends = response.data.friends;
                    }
                })
            },
            sendAdd1: function(){
                this.$axios.post(root.sendAdd,{
                    // id: this.$store.state.addFriendResult.id
                }).then(response => {
                    console.log(response)
                    // if(this.$store.state.addFriendResult.id == this.$store.state.songInfo.user.id){
                    //     this.$messagebox.alert('不能添加自己为好友！')
                    // }
                    if(response.data.code==2){
                        this.$messagebox.alert('已经是好友了！')
                    }
                    if(response.data.code==0){
                        this.$messagebox.alert('发送添加好友请求失败！')
                    }
                    if(response.data.code==1){
                        this.$messagebox.alert('发送添加好友请求成功！')
                    }
                    if(response.data.code==3){
                        this.$messagebox.alert('请不要多次点击添加！')
                    }
                    if(response.data.code==4){
                        this.$messagebox.alert('添加好友成功！')
                        // this.$store.state.songInfo.user.friends = response.data.friends;
                    }
                })
            }
        },
        // beforeCreate: function() {
        //     document.getElementsByTagName("body")[0].className="bgc-fff";
        // },
        // beforeDestroy: function() {
        //     document.body.removeAttribute("class","bgc-fff");
        // },
    }
</script>

<style scoped>
.title{
    text-align: left;
    font-size: 14px;
    line-height: 40px;
}
.list{
    display: flex;
    /* flex-wrap: nowrap; */
    justify-content: space-between;
    height: 50px;
    line-height: 50px;
    padding: 10px 0;
    border-top: 1px solid #e7e7e7;
}
.head{
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
}
.head img{
    width: 100%;
    height: 100%;
}
.info{
    width: 55%;
}
.info-top{
    width: 100%;
    display: flex;
    flex-wrap: nowrap;
    line-height: 26px;
}
.nickname{
    margin-left: 10px;
}
.id{
    font-size: 12px;
    color: #999999;
    margin-left: 10px;
}
.medal{
    display: flex;
    flex-wrap: nowrap;
}
.medal img{
    width: 14px;
    height: 20px;
    margin-left: 10px;
}
.btn{
    font-size: 12px;
    background-color: #57D6DD;
    color: #fff;
    height: 30px;
    margin-top: 10px;
    border-radius: 0;
}
</style>