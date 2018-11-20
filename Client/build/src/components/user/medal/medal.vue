<template>
    <div>
        <mt-header title="我的设置">
            <router-link to="/user" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="pageContent">
            <div class="medalList">
                <!-- <div v-for="(item,index) in this.$store.state.medal.medal" :key="index">
                    <div class="medalBox">
                        <div class="status">
                            <img src="./../../../assets/未获得@2x.png" v-show="!item.status">
                            <img src="./../../../assets/已获得@2x.png" v-show="item.status&&!item.wear">
                            <img src="./../../../assets/矩形752拷贝@2x.png" v-show="item.wear">
                        </div>
                        <div class="medalImg" @click="changeStatus($event,index)">
                            <img :src="'http://192.168.0.133'+item.src">
                        </div>
                        <div class="medalTitle" @click="introduce($event,index)">
                            <p>
                                {{item.name}}
                            </p>
                        </div>
                    </div>
                </div> -->
            </div>
            <div class="clearfix"></div>
            <mt-button class="medalBtn" size="large" @click="submit">提交</mt-button>
        </div>
    </div>
</template>

<script>
console.log("USER_MEDAL_MEDAL_VUE");
import root from '@/config/root.js'
    export default {
        data(){
            return{
                medalStatus: true,
                medalCount: 0
            }
        },
        created: function(){
            this.$axios.post(root.medal).then(response => {
                // console.log(response)
                // this.$store.commit("getMedal", response.data);
                // this.$store.state.medal.medal.forEach(element => {
                //     if(element.wear){
                //         this.medalCount++
                //     }
                // });
            })
        },
        beforeCreate: function() {
            document.getElementsByTagName("body")[0].className="bgc-fff";
            
        },
        beforeDestroy: function() {
            document.body.removeAttribute("class","bgc-fff");
        },
        methods: {
            changeStatus: function($event,index){
                // if(this.$store.state.medal.medal[index].status){
                //     if(this.$store.state.medal.medal[index].wear){
                //         this.$store.state.medal.medal[index].wear = 0;
                //         this.medalCount --;
                //     }else{
                //         if(this.medalCount>4){
                //             this.$messagebox.alert('最多只能佩戴5枚勋章');
                //         }else{
                //             this.$store.state.medal.medal[index].wear = 1;
                //             this.medalCount ++;
                //         }
                //     }
                // }
                // else{
                //     this.$messagebox.alert('您还未获得该勋章，戳勋章名字查看获取途径！')
                // }
            },
            introduce($event,index){
                // this.$messagebox.alert(this.$store.state.medal.medal[index].rule,this.$store.state.medal.medal[index].desc)
            },
            submit: function(){
                // let arr = [];
                // this.$store.state.medal.medal.forEach(element => {
                //     if(element.wear){
                //         arr.push(element.id)
                //     }
                // })
                // this.$axios.post(root.medal,arr).then(response => {
                //     console.log(response)
                //     if(response.data){
                //         this.$messagebox.alert('修改勋章信息成功！')
                //     }
                // })
            }
        },
        watch: {
            // medalCount: function(){
            //     if(this.medalCount)
            // }
        }
    }
</script>

<style scoped>
.medalList{
    /* display: flex; */
    /* flex-wrap: nowrap; */
    margin: 15px 0;
    margin-left: -3%;
}
.medalBox{
    width: 22%;
    margin-left: 3%;
    margin-top: 10px;
    float: left;
    height: 105px;
    overflow: hidden;
    position: relative;
}
.status{
    position: absolute;
    left: 60%;
    top: 0;
}
.status img{
    width: 25px;
    height: 25px;
}
.medalImg img{
    width: 60%;
    height: 50px;
    display: block;
    margin: 0 auto;
}
.medalTitle{
    margin-top: 5px;
    padding: 5px;
    font-size: 12px;
    border: 1px solid #b48b45;
    border-radius: 15px;
}
.medalTitle p{
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.medalBtn{
    color: #fff;
    background-color: #57d6dd;
    margin: 20px 0;
}
</style>