<template>
    <div>
        <mt-header title="个人资料">
            <router-link to="/mySystem" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="pageContent personal">
            <div class="purikura">
                <div class="purimg">
                    <li v-for="(items,index) in purimg" :key='index'>
                        <img id="purpic" :src="items">
                    </li>
                </div>
                <div class="uppurBtn">
                    <input id="uppic" type="file" @change="previewPic" style="opacity: 0; position: absolute; top: 0; left: 0; width: 100%; height: 100%">
                    <i class="iconfont icon-weibiaoti1"></i>
                </div>
            </div>
            <div class="userId">
                <!-- <mt-field label="用户ID：" disabled v-model="this.$store.state.songInfo.user.id" :disableClear="disableClear"></mt-field> -->
            </div>
            <div class="userName">
                <mt-field label="昵称：" v-model="username">
                    <i class="iconfont icon-xiugaiziliao" @click="changeUsername"></i>
                </mt-field>
            </div>
            <div class="perSign">
                <mt-field label="个性签名：" placeholder="写点什么吧..." type="textarea" rows="3" v-model="perSign">
                    <i class="iconfont icon-xiugaiziliao" @click="changeSign"></i>
                </mt-field>
            </div>
        </div>
        <div class="interval"></div>
        <div class="pageContent">
            <div class="password">
                <div class="psdTitle">登录密码修改</div>
                <div class="original-psd">
                    <mt-field type="password" placeholder="请输入原始密码" v-model="originalPsd" autocomplete="off"></mt-field>
                </div>
                <div class="psdError" :class="{ psdErrorActive:oriActive }">*密码不正确</div>
                <div class="new-psd1">
                    <mt-field type="password" placeholder="请输入新密码" v-model="newPsd1"></mt-field>
                </div>
                <div class="psdError"></div>
                <div class="new-psd2">
                    <mt-field type="password" placeholder="请确认新密码" v-model="newPsd2"></mt-field>
                </div>
                <div class="psdError" :class=" { psdErrorActive:newActive } ">*新密码不一致请再次输入</div>
                <mt-button size="large" class="psdBtn" @click="psdVerify">确定修改</mt-button>
            </div>

        </div>
    </div>
</template>

<script>
console.log("USER_MYSYSTEM_PERSONAL_VUE");
import pic1 from './../../../assets/pic1.png'
import root from '@/config/root.js'
    export default {
        data(){
            return{
                disableClear: false,
                // username: this.$store.state.songInfo.user.nickname,
                // perSign: this.$store.state.songInfo.user.signature,
                originalPsd: '',
                newPsd1: '',
                newPsd2: '',
                // originalPsdGet: this.$store.state.songInfo.user.password,
                oriActive: false,
                newActive: false,
                // purimg: [ this.pic2 = 'http://192.168.0.133'  + this.$store.state.songInfo.user.head],
                imgData: {
                    accept: 'image/gif, image/jpeg, image/jpg, image/png'
                }
            }
        },
        watch: {
           originalPsd: function(){
            //    console.log(this.originalPsd);
           }
        },
        methods: {
            psdVerify: function () {
                // if(this.originalPsd == this.originalPsdGet && this.newPsd1 == this.newPsd2){
                //     console.log(this.newPsd1)
                //     this.$axios.post(root.personal,{
                //         passwordType: 4,
                //         password: this.newPsd1
                //     }).then(response => {
                //         console.log(response)
                //     })
                //     this.$messagebox.alert("修改密码成功","提示").then(action=> {
                //         this.originalPsd = '';
                //         this.newPsd1 = '';
                //         this.newPsd2 = '';
                //     });
                //     this.oriActive = false;
                //     this.newActive = false;
                // }else{
                //     this.$messagebox.alert("修改密码失败，请重新输入！","错误");
                //     if(parseInt(this.originalPsd) != parseInt(this.originalPsdGet) && this.newPsd1 != this.newPsd2){
                //         this.oriActive = true;
                //         this.newActive = true;
                //     }else if(parseInt(this.originalPsd) != parseInt(this.originalPsdGet)){
                //         this.oriActive = true;
                //         this.newActive = false;
                //     }else{
                //         this.newActive = true;
                //         this.oriActive = false;
                //     }
                // }
            },
            //上传图片
            previewPic: function(event){
                let render = new FileReader();
                let img1 = event.target.files[0];
                let type = img1.type; //文件的类型，判断是否是图片  
                let size = img1.size; //文件的大小，判断图片的大小 
                if(this.imgData.accept.indexOf(type) == -1) {
                    this.$messagebox.alert('请选择我们支持的图片格式');
                    return false
                }
                if(size>3145728) {
                    this.$messagebox.alert('请选择3M以内的图片');
                    return false
                }
                var url = '';
                let form = new FormData();
                form.append('file',img1,img1.name);
                this.$axios.post(root.personal,form,{  
                headers:{'Content-Type':'multipart/form-data'}  
                }).then(response => {
                    console.log(response);
                    url = 'http://192.168.0.133' + response.data;
                    console.log(url)
                    // this.purimg.splice(0, purimg.length);
                    // console.log(purimg.push(url))
                    render.readAsDataURL(img1);
                    var that = this;
                    render.onload = function(){
                        that.purimg.splice(0, 1,url);
                    }
                })


                // render.onload = function(){
                //     this.pic1 = render.result;
                // }
            },
            //修改昵称
            changeUsername: function(){
                // if(this.username == this.$store.state.songInfo.user.username || this.username == ''){
                //     return
                // }
                // else{
                //     this.$axios.post(root.personal,{
                //         passwordType: 2,
                //         password: this.username
                //     }).then(response => {
                //         if(response.data.code){
                //             this.$messagebox.alert('昵称不能含有非法字符，请重新修改！')
                //             this.username = this.$store.state.songInfo.user.username
                //         }else{
                //             this.$messagebox.alert('昵称修改成功')
                //             this.$store.state.songInfo.user.username = this.username;
                //         }
                //     })
                // }
            },
            //修改个性签名
            changeSign: function(){
                // if(this.perSign == this.$store.state.songInfo.user.signature){
                //     return
                // }
                // else{
                //     this.$axios.post(root.personal,{
                //         passwordType: 4,
                //         password: this.perSign
                //     }).then(response => {
                //         if(response.data.code){
                //             this.$messagebox.alert('个性签名修改成功！')
                //             this.$store.state.songInfo.user.signature = this.perSign;
                //         }else{
                //             this.$messagebox.alert('个性签名修改失败，不能含有非法字符');
                //             this.perSign = this.$store.state.songInfo.user.signature
                //         }
                //     })
                // }
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
.purikura{
    position: relative;
    
}
.purimg{
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    margin: 20px auto;
    border: 1px solid #e7e7e7;
}
.purimg img{
    width: 100%;
    height: 100%;
}
.original-psd,.new-psd1,.new-psd2{
    position: relative;
}
.uppurBtn{
    background-color: #fff;
    width: 30px;
    height: 30px;
    line-height: 30px;
    border-radius: 50%;
    overflow: hidden;
    position: absolute;
    top: 55px;
    left: 55%;
    border: 1px solid #e7e7e7;
}
.uppurBtn .iconfont{
    color: #57d6dd;
    font-size: 20px;
}
.userName .iconfont,.perSign .iconfont{
    color: #a3a3a3;
    font-size: 20px;
}
.psdTitle{
    text-align: left ;
    padding: 0 10px;
    font-size: 14px;
    line-height: 40px;
}
.userName,.perSign{
    border-top: 1px solid #e7e7e7;
}
.psdBtn{
    background-color: #57d6dd;
    color: #fff;
    font-size: 14px;
    margin-top: 15px;
}
.psdError{
    height: 16px;
    color: #E84120;
    font-size: 12px;
    text-align: left;
    padding: 0 20px;
    opacity: 0;
}
.psdErrorActive{
    opacity: 1;
}
</style>