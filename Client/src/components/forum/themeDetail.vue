<template>
  <div>
    <mt-header title="帖子详情" style="border-bottom:.5vw solid #F2F2F2;background:#57D6DD;height: 10vw;" fixed>
      <router-link :to="this.$route.query.mytheme? {path:'/mytheme'}:{path:'/theme', query:{zone_id: this.$route.query.zone_id}}" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
    </mt-header>
    <section class="theme-info-box">
      <div class="user-info">
        <div class="user-head">
          <img :src="host + themeDetail.head" alt="">
        </div>
        <div class="user-name-box">
          <div class="user-name">{{themeDetail.user_name}}</div>
          <div class="theme-time">{{showTime(themeDetail.addtime)}}</div>
        </div>
        <div class="zone-img">
          <img :src="host + themeInfo.img" alt="">
        </div>
      </div>
      <div class="theme-info">
        <div class="theme-title">{{themeDetail.title}}</div>
        <div class="theme-content" v-html="themeDetail.content"></div>
        <div class="theme-imgs" v-show="themeDetail.imgs">
          <img v-for="(item, index) in themeDetail.imgs" :key="index" :src="imgHost + item" alt="">
        </div>
      </div>
    </section>
    <div class="box-border"></div>
    <section class="comment-reply-box">
      <h3>查看全部评论<img src="static/右箭头.png" alt="" /></h3>
      <ul id="comment-parent-box">
        <li class="one-comment-box" v-for="(item,index) in commentList" :key="index">
          <div class="commentor-info">
            <div style="display:flex;align-items:center;">
              <div class="commentor-head">
                <img :src="host + item.head" alt="">
              </div>
              <div class="commentor-name-box">
                <div class="commentor-name">{{item.user_name}}</div>
                <div class="comment-time">{{item.addtime}}</div>
              </div>
            </div>
            <div class="comment-zan">
              <img :src="checkZan(item, index)" alt="点赞" @click="zanComment(item,index)">
              <span>{{item.zan}}</span>
            </div>
          </div>
          <div>
            <div class="comment-content"><span v-html="item.content" style="max-width:90%;"></span> <span class="reply-btn" @click="reply(item.user_name, item.user_id, index)">回复</span></div>
            <div v-if="item.imgs" class="comment-imgs">
              <img v-for="(img, i) in item.imgs" :key='i' :src="srchost+img" alt="">
            </div>
            <div v-if="Object.keys(item.replys).length" class="comment-replys">
              <div v-for="(one, i) in item.replys" :key="i" class="one-comment-reply">
                <span><span class="reply-object" @click="reply(one.user_name, one.user_id, index)">“{{one.user_name}}”</span>回复<span class="reply-object" @click="reply(one.reply_user, one.reply_id, index)">“{{one.reply_user}}”</span>：</span>
                <span v-html="one.content"></span>
              </div>
            </div>
          </div>
        </li>
      </ul>
      <div class="talk-about-your-advice-box">
        <div v-if="Object.keys(imgsrcs).length" class="upload-img">
          <div style="position:relative;" v-for="(img, index) in imgsrcs" :key="index">
            <span @click="cancleImg(img, index)">x</span>
            <img :src="srchost+img" alt="">
          </div>
        </div>
        <div class="talk-about-your-advice">
          <div class="talk-about-content">
            <div class="send-content-box">
              <div class="input-box" @input="commentContent=$event.target.innerHTML" contenteditable="true" :placeholder="tips" type="text" id="comment-content"></div>
            </div>
            <button @click="sendComment">发送</button>
          </div>
          <div class="talk-other-box" v-show="!isFace">
            <img @click="isFace=true" src="./../../assets/表情@2x.png" alt="">
            <img v-if="reply_index<0" @click="addImg=true" src="static/截图.png" alt="">
            <input v-if="reply_index<0" @change="addImgToComment" type="file" id="addImg">
            <button v-if="reply_index>=0" @click="cancleReply" style="" class="cancel-reply-btn">取消</button>
          </div>
        </div>
        <transition class="faces-box" name="facesBao">
          <ul v-show="isFace" class="faces-box">
            <div class="hide-face-btn-box">
              <span class="hide-face-btn" @click="isFace=false">x</span>
            </div>
            <li v-for="(item, index) in faces" :key="index">
              <img :src="item" alt="" @click="choseFace(item)">
            </li>
          </ul>
        </transition>

      </div>
    </section>
  </div>
</template>
<script>
export default {
  data() {
    return {
      tips: "谈谈您的看法",
      themeDetail: {},
      themeInfo: {},
      host: this.GLOBAL.Host,
      imgHost: this.GLOBAL.logoHost + "themeImgs/",
      commentList: {},
      page: 0,
      order: "desc",
      // content: "",
      // commentZanList: null,
      faces: this.GLOBAL.updateFile.faces,
      isFace: false,
      addImg: false,
      imgType: "image/gif, image/jpeg, image/jpg, image/png",
      srchost: this.GLOBAL.logoHost + "commentImgs/",
      imgsrcs: [],
      reply_index: -1, //小于0表示评论帖子，大于0表示回复某个人
      reply_user: null,
      reply_user_id: null,
      commentContent: null,
      rules: [],
      lastTime: null,
      totalNum: null,
    };
  },
  mounted: function() {
    this.$parent.tabbarShow = false;
    if (localStorage.getItem("bc_comment_zans")) {
      this.commentZanList = localStorage.getItem("bc_comment_zans");
    }
    let that = this;
    this.checkSocket(function() {
      that.getComment();
    });
    this.onmessage();
  },
  watch: {
    commentContent: function() {
      console.log(this.commentContent);
      if(this.rules.filter_mod){
        this.checkKeyWord();
      }
    }
  },
  methods: {
    checkKeyWord(){
      var reg = new RegExp(this.rules.filter_words, "gm");
    },
    /**
     * 取消回复
     */
    cancleReply() {
      this.reply_index = -1;
      this.tips = "谈谈您的看法";
      this.reply_user = "";
    },
    /**
     * 进入回复模式
     */
    reply(user_nickname, user_id, index) {
      if (user_id == this.GLOBAL.userInfo.id) {
        this.$messagebox.alert("不允许自己回复自己！");
        return false;
      }

      this.tips = "回复@" + user_nickname;
      this.reply_index = index;
      this.reply_user = user_nickname;
      this.reply_user_id = user_id;
      document.getElementById("comment-content").focus();
    },
    /**
     * 删除图片
     */
    cancleImg(img, index) {
      this.imgsrcs.splice(index, 1);
      var url = this.GLOBAL.Host + "/bcweb/index.php/Home/Uploads/unlinkFile";
      let that = this;
      this.$axios
        .get(url, {
          params: {
            path: "/bcweb/public/commentImgs/" + img
          }
        })
        .then(response => {
          console.log("++++++++++++++++respone++++++++++++++");
          console.log(response.data);
          console.log("++++++++++++++respone++++++++++++++++");
        });
    },
    /**
     * 上传图片
     */
    addImgToComment() {
      let file = event.target.files[0];
      let type = file.type;
      let size = file.size;
      if (this.imgType.indexOf(type) == -1) {
        this.$messagebox.alert("请选择gif|jpeg|jpg|png格式的图片！");
        return false;
      }
      if (size > 3145728) {
        this.$messagebox.alert("请选择3M以内的图片！");
        return false;
      }
      var url =
        this.GLOBAL.Host + "/bcweb/index.php/Home/Uploads/uploadCommentImg";
      let form = new FormData();
      form.append("file", file, file.name);
      // form.append("uid", this.GLOBAL.userInfo.id);
      let that = this;
      this.$axios
        .post(url, form, {
          headers: { "Content-Type": "multipart/form-data" }
        })
        .then(response => {
          console.log("++++++++++++++++respone++++++++++++++");
          console.log(response.data);
          console.log("++++++++++++++respone++++++++++++++++");
          console.log(response);
          console.log("+++++++++++++++respone+++++++++++++++");
          if (response.status) {
            that.imgsrcs.push(response.data.src);
            document.getElementById("addImg").value = "";
          } else {
            that.$messagebox.alert("上传失败！");
          }
        });
      console.log();
    },
    // commentContent(){
    // },
    /**
     * 插入表情
     */
    choseFace(item) {
      let obj = document.getElementById("comment-content");
      obj.innerHTML +=
        '<img style="width:5vw;height:5vw;display:inline;" src="' +
        item +
        '" />';
      this.isFace = false;
      this.$nextTick(function() {
        console.log(obj);
        console.log(window.getSelection);
        console.log(document.getSelection);
        if (window.getSelection) {
          obj.focus(); //解决ff不获取焦点无法定位问题
          let range = window.getSelection(); //创建range
          range.selectAllChildren(obj); //range 选择obj下所有子内容
          range.collapseToEnd(); //光标移至最后
        } else if (document.selection) {
          let range = document.selection.createRange(); //创建选择对象
          range.moveToElementText(obj); //range定位到obj
          range.collapse(false); //光标移至最后
          range.select();
        } else {
          this.$messagebox.alert("当前设备不支持光标重定位，请手动点击");
        }
        console.log(obj.innerText.length);
        if (obj.lastChild) {
          obj.lastChild.scrollIntoView();
        }
      });
    },
    /**
     * 点赞
     */
    zanComment(item, index) {
      if (item.zan_status) {
        this.$messagebox.alert("您已经点过赞了！");
        return false;
      }
      let data = {
        type: "zanComment",
        comment_id: item.id,
        con_id: this.GLOBAL.connectionId,
        send_id: this.GLOBAL.userInfo.id
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(respone) {
          // console.log("responeZanComment");
          // console.log(respone);
          // console.log("responeZanComment");
          if (respone.status) {
            item.zan++;
            item.zan_status = respone.zan_status;
          } else {
            if (respone.zan_status) {
              item.zan_status = respone.zan_status;
            }
            that.$messagebox.alert(respone.msg);
          }
        },
        handType: "user"
      });
    },
    /**
     * 检测是否已经点过赞
     */
    checkZan(item, index) {
      // console.log("+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+");
      // console.log(this.commentZanList);
      // console.log(item);
      // console.log("+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+");
      if (item.zan_status) {
        // console.log('zan');
        return "static/点赞.png";
      } else {
        // console.log('buzan');
        return "static/赞.png";
      }
    },
    /**
     * 发表看法
     */
    sendComment() {
      // 获取评论内容对象
      let obj = document.getElementById("comment-content");
      if (!obj.innerHTML) {
        this.$messagebox.alert("请输入你的评论！");
        return false;
      }
      console.log(obj.innerHTML);
      if (this.reply_index < 0) {
        // 发送回帖
        let data = {
          type: "themeComment",
          post_id: this.themeDetail.id,
          content: obj.innerHTML.replace(/"/g, ""), //去除评论内容的双引号
          send_id: this.GLOBAL.userInfo.id,
          nickname: this.GLOBAL.userInfo.nickname,
          con_id: this.GLOBAL.connectionId,
          imgs: this.imgsrcs
        };
        let that = this;
        this.senddata({
          data: data,
          callback: function(respone) {
            if (respone.status) {
              let comment = [
                {
                  id: respone.id,
                  user_id: data.send_id,
                  user_name: data.nickname,
                  post_id: data.post_id,
                  content: data.content,
                  addtime: respone.addtime,
                  replys: [],
                  head: that.GLOBAL.userInfo.head,
                  zan: 0,
                  imgs: data.imgs
                }
              ];
              that.imgsrcs = [];
              if (Object.keys(that.commentList).length) {
                that.commentList = comment.concat(that.commentList);
              } else {
                that.commentList = comment;
              }
              // that.content = "";
              obj.innerHTML = "";
              console.log(that.commentList);
              that.$nextTick(function() {
                document
                  .getElementById("comment-parent-box")
                  .firstChild.scrollIntoView();
              });
            } else {
              that.$messagebox.alert(respone.msg);
            }
          },
          handType: "user"
        });
      } else {
        // 发表回复
        let data = {
          user_id: this.GLOBAL.userInfo.id,
          user_name: this.GLOBAL.userInfo.nickname,
          comment_id: this.commentList[this.reply_index].id,
          content: obj.innerHTML.replace(/"/g, ""), //去除评论内容的双引号
          reply_user: this.reply_user,
          reply_id: this.reply_user_id,
          type: "ReplyComment",
          con_id: this.GLOBAL.connectionId
        };
        let that = this;
        this.senddata({
          data: data,
          callback: function(respone) {
            console.log(respone);
            if (respone.status) {
              let reply = {
                id: respone.id,
                user_id: data.user_id,
                user_name: data.user_name,
                comment_id: data.comment_id,
                content: data.content,
                reply_user: data.reply_user,
                addtime: respone.addtime,
                reply_id: data.reply_id
              };
              that.commentList[that.reply_index].replys.push(reply);
            } else {
              if (respone.msg) {
                that.$messagebox.alert(respone.msg);
              } else {
                that.$messagebox.alert("请检查网络是否正常！");
              }
            }
            that.reply_index = -1;
            that.tips = "谈谈您的看法";
            that.reply_user = "";
            obj.innerHTML = "";
          },
          callbackFlag: "responseReplyComment",
          handType: "user"
        });
      }
    },
    /**
     * 显示时间
     */
    showTime(time) {
      return this.showDate(false, time);
    },
    /**
     * 获取评论
     */
    getComment() {
      let data = {
        type: "getComment",
        post_id: this.$route.query.post_id,
        zone_id: this.$route.query.zone_id,
        page: this.page,
        order: this.order,
        // gold: this.themeInfo.gold,
        send_id: this.GLOBAL.userInfo.id,
        con_id: this.GLOBAL.connectionId
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(respone) {
          if (respone.status) {
            that.$forceUpdate();

            that.commentList = respone.commentList;
            if (!that.page) {
              // if (respone.zoneInfo) {
                that.themeInfo = respone.zoneInfo;
              // }
              // if (respone.postInfo) {
                that.themeDetail = respone.postInfo;
              // }
              // if (respone.rules) {
                that.rules = respone.rules;
              // }
              that.lastTime = respone.lastTime;
              that.totalNum = respone.totalNum;
              if (respone.gold >= 0 && respone.gold != null) {
                that.GLOBAL.userInfo.gold = respone.gold;
                localStorage.setItem(
                  "bc_userInfo",
                  JSON.stringify(that.GLOBAL.userInfo)
                );
              }
              if (respone.noticeMsg) {
                if (that.GLOBAL.msgList.systemNotice) {
                  that.$set(
                    that.GLOBAL.msgList.systemNotice,
                    Object.keys(that.GLOBAL.msgList.systemNotice).length,
                    respone.noticeMsg
                  );
                } else {
                  that.$set(that.GLOBAL.msgList, "systemNotice", {});
                  that.$set(
                    that.GLOBAL.msgList.systemNotice,
                    0,
                    respone.noticeMsg
                  );
                }
                console.log(that.GLOBAL.msgList.systemNotice);
                that.GLOBAL.msgLength++;
              }
              that.page++;
            }
          } else {
            that.$messagebox.alert(respone.msg).then(action => {
              that.$router.push({
                path: "/theme",
                query: {
                  zone_id: that.$route.query.zone_id
                }
              });
            });
          }
        },
        callbackFlag: "resopneGetComment",
        handType: "user"
      });
    }
  },
};
</script>
<style>
#addImg {
  overflow: hidden;
  opacity: 0;
  margin-left: -6vw;
  width: 6vw;
  height: 6vw;
  border-radius: 6vw;
}
.one-comment-box {
  padding: 3vw 0px;
  border-bottom: 0.4vw solid #e9e9e9;
}
.box-border {
  height: 3vw;
  background: #e7e7e7;
  width: 100vw;
}
.theme-info-box {
  /* margin-top: 10vw; */
  width: 95vw;
  margin: 10vw auto 0px;
}
.user-info {
  display: flex;
  justify-content: flex-start;
  justify-items: flex-start;
  align-items: center;
  align-items: center;
}
.user-info .user-head {
  width: 12vw;
  height: 12vw;
  border-radius: 12vw;
  /* border: 1px solid red; */
  background: #afaffb;
  display: flex;
  justify-content: center;
  justify-items: center;
  align-content: center;
  align-items: center;
  overflow: hidden;
}
.user-info .user-head img {
  max-width: 100%;
  max-height: 100%;
}
.user-info .user-name-box {
  text-align: left;
  margin-left: 1vw;
}
.user-name-box .user-name {
  font-size: 4vw;
}
.user-name-box .theme-time {
  font-size: 3vw;
}
.user-info .zone-img {
  width: 7vw;
  height: 7vw;
  display: flex;
  justify-content: center;
  justify-items: center;
  align-content: center;
  align-items: center;
  /* border: 1px solid red; */
  margin-left: 1vw;
}
.user-info .zone-img img {
  max-width: 100%;
  max-height: 100%;
}
.theme-info {
  margin-bottom: 2vw;
}
.theme-title {
  font-size: 4.5vw;
  text-align: left;
  font-weight: 600;
  line-height: 10vw;
}
.theme-content {
  font-size: 4vw;
  text-align: left;
  line-height: 6vw;
}
.theme-imgs {
  padding: 1vw 0;
  display: flex;
  flex-wrap: wrap;
  margin-left: -3.33%;
  /* justify-content: stretch; */
  /* justify-content: start; */
}
.theme-imgs img {
  display: block;
  width: 30%;
  max-height: 30vw;
  margin-top: 1vw;
  margin-left: 3.33%;
}
.comment-reply-box {
  width: 95vw;
  margin: 0px auto 20vw;
}
.comment-reply-box h3 {
  font-size: 4vw;
  font-weight: 600;
  text-align: left;
  height: 10vw;
  line-height: 10vw;
  /* background: red; */
  display: flex;
  align-content: center;
  align-items: center;
}
.comment-reply-box h3 img {
  width: 5vw;
  height: 5vw;
  transform: rotate(90deg);
  -moz-transform: rotate(90deg);
  -ms-transform: rotate(90deg);
  -o-transform: rotate(90deg);
  -webkit-transform: rotate(90deg);
}
.commentor-info {
  display: flex;
  justify-content: space-between;
}
.commentor-info .commentor-head {
  display: flex;
  justify-content: center;
  justify-items: center;
  align-content: center;
  align-items: center;
  width: 12vw;
  height: 12vw;
  border-radius: 12vw;
  background: #afaffb;
  overflow: hidden;
}
.commentor-head img {
  max-width: 100%;
  max-height: 100%;
}
.commentor-name-box {
  margin-left: 1vw;
  font-size: 4vw;
}
.commentor-name-box .commentor-name {
  text-align: left;
}
.commentor-name-box .comment-time {
  text-align: left;
  font-size: 3vw;
  color: #999;
}
.comment-zan {
  display: flex;
  justify-content: center;
  justify-items: center;
  align-content: center;
  align-items: center;
}
.comment-zan img {
  width: 6vw;
  height: 6vw;
}
.comment-zan span {
  font-size: 4vw;
}
.comment-content {
  margin-top: 1.5vw;
  text-align: left;
  display: flex;
  justify-content: space-between;
  font-size: 4vw;
}
.comment-content .reply-btn {
  font-size: 3.5vw;
  color: #999;
}
.comment-replys {
  margin-top: 1.5vw;
  width: 100%;
  background: #e7e7e7;
  box-sizing: border-box;
  padding: 2vw;
  text-align: left;
  font-size: 4vw;
}
/* .comment-replys .one-comment-reply {
} */
.reply-object {
  color: #16b1ba;
}
.talk-about-your-advice-box {
  position: fixed;
  /* top: 100vh; */
  bottom: 0px;
}
.talk-about-your-advice {
  /* position: absolute;
  top: -20vw;
  height: 20vw; */
  background: white;
  width: 100vw;
  border-top: 0.4vw solid #e9e9e9;
}
.talk-about-content,
.talk-other-box {
  height: 10vw;
  /* background: yellow; */
  display: flex;
  justify-content: flex-start;
  align-items: center;
  border-bottom: 0.4vw solid #e9e9e9;
}
.talk-about-content .send-content-box {
  width: 80%;
}
.send-content-box .input-box {
  width: 93%;
  height: 10vw;
  line-height: 10vw;
  border: 0px;
  text-align: left;
  padding: 0px 2vw;
  font-size: 4vw;
  outline: none;
  display: flex;
  justify-content: flex-start;
  justify-items: flex-start;
  align-content: center;
  align-items: center;
  overflow-x: auto;
  /* line-height: 5vw; */
  white-space: nowrap;
}
.talk-about-content button {
  width: 13%;
  height: 7vw;
  border: 0px;
  background: white;
  border-left: 0.5vw solid #bbbbbb;
  color: #666;
  font-weight: 600;
  font-size: 4vw;
  outline: none;
}

.talk-other-box img {
  width: 6vw;
  margin-left: 4vw;
}
.faces-box {
  border-top: 0.5vw solid #e7e7e7;
  width: 100vw;
  padding: 1.5vw;
  background: white;
  position: relative;
  /* position: absolute; */
  bottom: 0px;
  left: -2.5vw;
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
  justify-items: auto;
}
.faces-box li {
  width: 7vw;
  height: 7vw;
  margin-left: 1.5vw;
  margin-top: 1.5vw;
  display: flex;
  justify-content: center;
  justify-items: center;
  align-items: center;
  align-content: center;
}
.faces-box li img {
  max-width: 100%;
  max-height: 100%;
}
.facesBao-enter-active {
  bottom: 0px;
}
.facesBao-leave-active {
  bottom: 0px;
}
.facesBao-enter,
.facesBao-leave-to {
  top: 0px;
}
.comment-imgs {
  padding: 1vw 0px;
  display: flex;
  flex-wrap: wrap;
  margin-left: -3.33%;
}
.comment-imgs img {
  display: block;
  max-width: 30%;
  max-height: 30vw;
  margin-top: 1vw;
  margin-left: 3.33%;
}
.upload-img {
  display: flex;
  /* position: fixed; */
  flex-wrap: wrap;
  /* bottom: 20vw; */
  /* left: 0px; */
  background: #eeeeb3;
  width: 100%;
  box-sizing: border-box;
  padding: 2vw;
}
.upload-img img {
  max-width: 20vw;
  max-height: 20vw;
}
.upload-img span {
  position: absolute;
  left: 0px;
  top: 0px;
  display: inline-block;
  width: 5vw;
  height: 5vw;
  background: red;
  font-size: 5vw;
  line-height: 4.5vw;
  text-align: cne;
  text-align: center;
  border-radius: 5vw;
  color: white;
}
#comment-content:empty:before {
  content: attr(placeholder);
  color: #999;
}
.cancel-reply-btn {
  border: 0.5vw solid #ccc;
  font-size: 4vw;
  margin-left: 2vw;
  color: #666;
}
.hide-face-btn-box {
  display: block;
  width: 100%;
  text-align: right;
}
.hide-face-btn {
  display: inline-block;
  width: 4vw;
  height: 4vw;
  background: red;
  border-radius: 4vw;
  font-size: 4vw;
  text-align: center;
  line-height: 4vw;
  color: white;
  margin-right: 4vw;
}
</style>


