<template>
    <div>
        <mt-header title="我的好友">
            <router-link to="/" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
            <router-link to="/addFriend" slot="right">
                <mt-button><i class="iconfont icon-tianjiahaoyou" style="font-size:20px"></i></mt-button>
            </router-link>
        </mt-header>
        <div class="module">
            <div class="f-title">全部好友 (100/100) <i class="iconfont" :class="friendList?iconDown:iconUp" @click="friendSlide"></i></div>
            <div  v-for="(value,key,index) in friendInfo" :key="index" v-show="friendShow">
                <div class="f-list">
                    <div class="f-info">
                        <div class="f-pic">
                            <img :src="value.fPic">
                            <mt-badge type="error" size="small" class="badge friend-badge">{{value.fMsg}}</mt-badge>
                        </div>
                        <div class="f-main">
                            <div class="f-username">{{value.fUsername}} <span v-show="value.fStatus">[在线]</span></div>
                            <div class="f-medal">
                                <img src="./../../../assets/medal1.png">
                                <img src="./../../../assets/medal1.png">
                                <img src="./../../../assets/medal1.png">
                                <img src="./../../../assets/medal1.png">
                                <img src="./../../../assets/medal1.png">
                            </div>
                        </div>
                    </div>
                    <div class="f-btn">
                        <button class="del">删除好友</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="module">
            <div class="f-title">陌生人 (100/100) <i class="iconfont" :class="strangerList?iconDown:iconUp" @click="strangerSlide"></i></div>
            <div  v-for="(value,key,index) in strangerInfo" :key="index" v-show="strangerShow">
                <div class="f-list">
                    <div class="f-info">
                        <div class="f-pic">
                            <img :src="value.fPic">
                            <mt-badge type="error" size="small" class="badge friend-badge">{{value.fMsg}}</mt-badge>
                        </div>
                        <div class="f-main">
                            <div class="f-username">{{value.fUsername}} <span v-show="value.fStatus">[在线]</span></div>
                            <div class="f-medal" v-show="!value.fType">
                                <img src="./../../../assets/medal1.png">
                                <img src="./../../../assets/medal1.png">
                                <img src="./../../../assets/medal1.png">
                                <img src="./../../../assets/medal1.png">
                                <img src="./../../../assets/medal1.png">
                            </div>
                            <div class="f-medal" v-show="value.fType">
                                请求添加您为好友！
                            </div>
                        </div>
                    </div>
                    <div class="f-btn" v-show="!value.fType">
                        <button class="del">删除好友</button>
                    </div>
                    <div class="f-btn" v-show="value.fType">
                        <button class="accept">接受</button>
                        <button class="del">拒绝</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="module">
            <div class="f-title">系统消息 <i class="iconfont" :class="systemList?iconDown:iconUp" @click="systemSlide"></i></div>
            <div  v-for="(value,key,index) in strangerInfo" :key="index" v-show="systemShow">
                <div class="f-list">
                    <div class="f-info">
                        <div class="f-pic">
                            <img :src="value.fPic">
                            <mt-badge type="error" size="small" class="badge friend-badge">{{value.fMsg}}</mt-badge>
                        </div>
                        <div class="f-main">
                            <div class="f-username">{{value.fUsername}} <span v-show="value.fStatus">[在线]</span></div>
                            <div class="f-medal" v-show="!value.fType">
                                <img src="./../../../assets/medal1.png">
                                <img src="./../../../assets/medal1.png">
                                <img src="./../../../assets/medal1.png">
                                <img src="./../../../assets/medal1.png">
                                <img src="./../../../assets/medal1.png">
                            </div>
                            <div class="f-medal" v-show="value.fType">
                                请求添加您为好友！
                            </div>
                        </div>
                    </div>
                    <div class="f-btn" v-show="!value.fType">
                        <button class="del">删除好友</button>
                    </div>
                    <div class="f-btn" v-show="value.fType">
                        <button class="accept">接受</button>
                        <button class="del">拒绝</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
console.log("PAGES_PAGEFRIEND_FRIEND_VUE");
import pic1 from './../../../assets/pic1.png'
    export default {
        
        data(){
            return{
                friendList: true,
                strangerList: true,
                systemList: true,
                iconDown: 'icon-jiantouxia',
                iconUp: 'icon-jiantoushang',
                friendInfo: {
                    friend1: {
                        fType: 0,
                        fPic: pic1,
                        fUsername: 'abc',
                        fMsg: '5',
                        fStatus: true,
                        fmedal: 3
                    },
                    friend2: {
                        fType: 0,
                        fPic: pic1,
                        fUsername: 'abcsadsad',
                        fMsg: '3',
                        fStatus: false,
                        fmedal: 4
                    }
                },
                strangerInfo:{
                    stranger1: {
                        fType: 0,
                        fPic: pic1,
                        fUsername: 'abc',
                        fMsg: '5',
                        fStatus: true,
                        fmedal: 3
                    },
                    stranger2: {
                        fType: 1,
                        fPic: pic1,
                        fUsername: 'abc',
                        fMsg: '5',
                        fStatus: true,
                        fmedal: 3
                    },
                    stranger3: {
                        fType: 1,
                        fPic: pic1,
                        fUsername: 'abc',
                        fMsg: '5',
                        fStatus: true,
                        fmedal: 3
                    },
                },
                friendShow: true,
                strangerShow: true,
                systemShow: true,
            
            }
        },
        methods: {
            friendSlide:function(){
                this.friendShow = !this.friendShow;
                this.friendList = !this.friendList;
            },
            strangerSlide:function(){
                this.strangerShow = !this.strangerShow;
                this.strangerList = !this.strangerList;
            },
            systemSlide:function(){
                this.systemShow = !this.systemShow;
                this.systemList = !this.systemList;
            },
        }
    }
</script>

<style scoped>
.module{
    padding: 0 10px;
    margin: 10px 0;
    background-color: #fff;
}
.f-title{
    text-align: left;
    height: 40px;
    line-height: 40px;
    font-size: 14px;
}
.f-title .iconfont{
    font-size: 14px;
    color: #999;
}
.f-list{
    border-top: 1px solid #e7e7e7;
    height: 45px;
    line-height: 45px;
    padding: 10px 0;
    display: flex;
    justify-content: space-between;
}
.friend-badge{
    display: block;
    width: 16px;
    height: 16px;
    font-size: .75rem;
    padding: 1px 1px;
    line-height: normal;
    position: absolute;
    left: 30px;
    top: -5px;
}
.f-pic{
    width: 45px;
    height: 45px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 10px;
    
}
.f-pic img{
    width: 100%;
    height: 100%;
}
.f-info{
    width: 60%;
    display: flex;
    flex-wrap: nowrap;
    position: relative;
}
.f-medal{
    display: flex;
    flex-wrap: nowrap;
    font-size: 12px;
    color: #999;
    line-height: 20px;
}
.f-medal img{
    width: 20px;
    height: 20px;
}
.f-username{
    height: 25px;
    line-height: 25px;
    text-align: left;
}
.f-username span{
    font-size: 12px;
    color: #64D7E3;
}
.f-btn{
    line-height: 45px;
    height: 45px;
    width: 40%;
    text-align: end;
}
.del{
    background-color: #e7e7e7;
    color: #999;
    font-size: 12px;
    border: none;
    height: 30px;
    line-height: 30px; 
    padding: 0 15px;
}
.accept{
    background-color: #64D7E3;
    color: #fff;
    font-size: 12px;
    border: none;
    height: 30px;
    line-height: 30px; 
    padding: 0 15px;
}
</style>