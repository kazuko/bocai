<template>
  <div>
    <mt-header title="发布帖子" style="background:#57D6DD;">
      <router-link :to="{path:'/theme', query:{zone_id: this.$route.query.zone_id}}" slot="left">
        <mt-button icon="back">返回</mt-button>
      </router-link>
      <mt-button class="publishBtn" @click="postTheme" slot="right">发布</mt-button>
    </mt-header>
    <div class="pageContent">
      <div class="post">
        <div class="title">
          <mt-field placeholder="请输入主题..." v-model="title"></mt-field>
          <!-- <div contenteditable="true" id="title-input-box" @input="title=$event.target.innerHTML" placeholder="请输入主题..."></div> -->
        </div>
        <div class="postContent">
          <div class="extend">
            <img @click="isFace = !isFace" src="./../../assets/表情@2x.png">
            <img src="static/截图.png">
            <input id="insert-img-input-box" ref="pathClear1" type="file" style="height:7vw;width:7vw;overflow:hidden;margin-left:-12vw;opacity:0;" @change="insertImg(false)">
          </div>
          <div v-if="isFace" class="faces">
            <img v-for="(img, index) in faceList" :key="index" :src="img" alt="" @click="addFace(img)">
          </div>
          <div class="text">
            <!-- <textarea id="postText" placeholder="请输入内容" rows="10" v-model="postText"></textarea> -->
            <div @input="afterContent=$event.target.innerHTML" contenteditable="true" id="postText" placeholder="请输入内容" rows="10" @keydown="beforeDel($event)" v-html="aftercontent"></div>
          </div>
          <div class="image">
            <div class="oneImgs" style="" v-for="(img, index) in imgs" :key="index">
              <img :src="srchost+img" alt="">
              <span @click="delImg(srchost+img, index)">x</span>
            </div>
            <div class="preview" style="position:relative;">
              <img src="./../../assets/添加相册@2x.png">
              <input type="file" id="add-img-input-box" ref="pathClear2" @change="insertImg(true)" style="position:absolute;top:0px;left:0px;width:28vw;height:28vw;border:0px solid red;opacity:0;">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="fatie-tips-box" v-if="rules.show">
      <h1>发帖小tips：</h1>
      <p>1、前{{rules.num}}帖可获得{{rules.gold}}{{this.GLOBAL.appInfo.coin1_name}}和{{rules.sliver}}{{this.GLOBAL.appInfo.coin2_name}}；</p>
      <p>2、标题字数不可超过{{rules.title_words}}个字；</p>
      <p>3、新用户在注册之后的{{rules.new_seconds}}秒内不可以发帖；</p>
      <p>4、发帖之后的{{rules.refender_seconds}}秒内禁止发帖，{{rules.refender_seconds}}秒之后才可以发帖；</p>
      <p>5、充值VIP后，在VIP期间可以忽略3、4两条规定。</p>
      <p v-if="rules.filter_mod">6、标题和内容不可出现如下关键字眼：{{rules.filter_words}}，若出现超过{{rules.filter_allow}}次则将面临被禁止发言或者禁止登录的风险。</p>
    </div>
  </div>
</template>

<script>
// console.log("FORUM_POST_VUE");
export default {
  data() {
    return {
      title: "", //标题
      srchost: this.GLOBAL.logoHost + "themeImgs/", //主题图片的保存目录
      imgs: [], //主题附属图片列表
      isFace: false, //是否插入表情
      // 表情列表
      faceList: this.GLOBAL.updateFile.faces,
      // 接受的图片格式
      imgType: "image/jpg, image/jpeg, image/png, image/gif",
      // 删除前的内容长度
      beforeLenght: 0,
      // 删除后的内容长度
      afterLength: 0,
      //保存删除前的内容字符串
      beforeContent: "",
      //保存删除后的内容字符串
      afterContent: "<p>史蒂芬孙方</p>",
      // 保存删除后的所有剩余图片
      afterImgs: [],
      // 发帖规则
      rules: [],
      // 字符串中出现的过滤关键词
      keywords: [],
      // range: null,
      tieNum: null
    };
  },
  mounted: function() {
    console.log(this.GLOBAL.userInfo);
    this.$parent.tabbarShow = false;
    var that = this;
    this.checkSocket(function() {
      that.getRules();
    });
    this.onmessage();
  },
  watch: {
    keywords: function() {
      if (this.rules.filter_mod == 3) {
        var obj = document.querySelector(".publishBtn");
        if (Object.keys(this.keywords).length) {
          obj.style.color = "#ccc";
        } else {
          obj.style.color = "white";
        }
      }
    },
    /**
     * 监测内容的变化
     */
    afterContent: function() {
      this.checkKeyWords(this.afterContent);
      this.afterDel();
    },
    title: function() {
      this.checkKeyWords(this.title);
    }
  },
  methods: {
    /**
     * 判断数组1和数组2是否完全在
     */
    arr1AllInArr2(arr1, arr2) {
      if (Object.keys(arr1).length == Object.keys(arr2).length) {
        var f1 = true;
        arr1.forEach((item, i) => {
          console.log("value=" + item + "; index=" + i);
          var f2 = true;
          arr2.forEach((element, j) => {
            if (item == element) {
              f2 = false;
              return false;
            }
          });
          if (f2) {
            f1 = false;
            return false;
          }
        });
        return f1;
      } else {
        return false;
      }
    },
    /**
     *检测字符串是否含有过滤关键词
     */
    checkKeyWords(content) {
      var regE = new RegExp(this.rules.filter_words, "gm");
      if (regE.test(content)) {
        var result = content.match(regE);
        console.log(result);
        console.log(this.keywords);
        if (!this.arr1AllInArr2(result, this.keywords)) {
          this.GLOBAL.userInfo.allow_times--;
          localStorage.setItem(
            "bc_userInfo",
            JSON.stringify(this.GLOBAL.userInfo)
          );
          this.senddata({
            data: {
              type: "allowTimesDec",
              uid: this.GLOBAL.userInfo.id,
              con_id: this.GLOBAL.connectionId
            },
            handType: "user"
          });

          if (this.checkUserLimit()) {
            return false;
          }
          this.keywords = result;
          var keys = "";
          result.forEach(element => {
            keys += element + "|";
          });
          keys = keys.substr(0, keys.length - 1);
          console.log("keys = " + keys);
          // if (this.rules.filter_mod == 3) {
          //   this.$messagebox.alert("");
          // } else {
          // 将会被替换为“"+this.rules.filter_replace+"”"
          this.$messagebox.alert(
            "包含敏感词“" +
              keys +
              "”，再出现" +
              this.GLOBAL.userInfo.allow_times +
              "次敏感词将会面临被禁止言论或者禁止登陆的风险！"
          );
          // }
          // if (Object.keys(this.keywords).length) {
          //   console.log(this.GLOBAL.userInfo.allow_times);
          //   this.$messagebox.alert("帖子标题或者内容存在非法关键词，禁止发布！");
          //   return false;
          // }
        } else {
          console.log("ssssssssssssssssssssssssssssssssssssssssssss");
        }
      } else {
        this.keywords = [];
      }
    },
    downLine: function() {
      let data = {
        con_id: this.GLOBAL.connectionId,
        send_id: this.GLOBAL.userInfo.id,
        type: "downLine"
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(respone) {
          if (respone.status) {
            that.GLOBAL.userInfo = {};
            that.GLOBAL.loginStatus = false;
            localStorage.removeItem("bc_userInfo");
            localStorage.removeItem("bc_friendInfo");
            that.$router.push("/");
          } else {
            that.$messagebox.alert("网络堵塞，请检查网络是否正常！");
          }
        },
        callbackFlag: "responeDownLine",
        handType: "user"
      });
    },
    /**
     * 获取发帖规则
     */
    getRules() {
      let data = {
        type: "getPostRules",
        con_id: this.GLOBAL.connectionId,
        uid: this.GLOBAL.userInfo.id,
        zone_id: this.$route.query.zone_id
      };
      let that = this;
      this.senddata({
        data: data,
        callback: function(response) {
          that.tieNum = response.tieNum;
          // console.log(response);
          var time = that.showDate();
          // console.log(time - response.lastTime);
          if (time < that.GLOBAL.userInfo.vip_time) {
            // 当前用户为vip用户
            that.rules = response.rules;
          } else {
            if (
              time - that.GLOBAL.userInfo.register_time <
              response.rules.new_seconds
            ) {
              // 新用户注册时间小于规定的发帖时间限制，不可发帖
              if (response.rules.recharge_flag) {
                that.$messagebox
                  .confirm(
                    "新用户" +
                      response.rules.new_seconds +
                      "秒之内不可以发帖,充值VIP即可发帖，立即充值？"
                  )
                  .then(action => {
                    that.$router.push("/recharge");
                  })
                  .catch(err => {
                    that.$router.push({
                      path: "/theme",
                      query: { zone_id: that.$route.query.zone_id }
                    });
                  });
              } else {
                that.$messagebox
                  .alert(
                    "新用户" + response.rules.new_seconds + "秒之内不可以发帖"
                  )
                  .then(action => {
                    that.$router.push({
                      path: "/theme",
                      query: { zone_id: that.$route.query.zone_id }
                    });
                  });
              }
            } else if (
              time - response.lastTime <
              response.rules.refender_seconds
            ) {
              // 用户发帖之间的时间间隔小于规定的时间间隔，不可发帖
              if (response.rules.recharge_flag) {
                that.$messagebox
                  .confirm(
                    response.rules.new_seconds -
                      (time - response.lastTime) +
                      "秒之后才可发帖,充值VIP可立即发帖，立即充值？"
                  )
                  .then(action => {
                    that.$router.push("/recharge");
                  })
                  .catch(err => {
                    console.log("sss");
                    that.$router.push({
                      path: "/theme",
                      query: { zone_id: that.$route.query.zone_id }
                    });
                  });
              } else {
                that.$messagebox
                  .alert(
                    response.rules.new_seconds -
                      (time - response.lastTime) +
                      "秒之后才可发帖！"
                  )
                  .then(action => {
                    that.$router.push({
                      path: "/theme",
                      query: { zone_id: that.$route.query.zone_id }
                    });
                  });
              }
            } else {
              // 满足条件，可发贴
              that.rules = response.rules;
            }
          }
          that.checkUserLimit();
        },
        callbackFlag: "responseGetPostRules",
        handType: "user"
      });
    },
    checkUserLimit() {
      console.log(this.GLOBAL.userInfo.allow_times);
      if (this.GLOBAL.userInfo.allow_times <= 0) {
        // 用户使用敏感词过多或者达到限定值
        console.log(this.rules.defriend_mod);
        if (parseInt(this.rules.defriend_mod) == 1) {
          // let that= this;
          // console.log("ddddkkkk");
          // 禁止发言
          this.$messagebox
            .alert("您因使用敏感词次数过多，已被禁止发言！")
            .then(() => {
              this.$router.push({
                path: "/theme",
                query: { zone_id: this.$route.query.zone_id }
              });
            });
        } else if (parseInt(this.rules.defriend_mod) == 2) {
          // 禁止登陆
          this.$messagebox
            .alert("您因使用敏感词次数过多，即将退出登录！")
            .then(() => {
              this.downLine();
            });
        }
        return true;
      } else {
        return false;
      }
    },
    /**
     * 执行删除操作前记录文本内容
     */
    beforeDel(textDom) {
      var e = event || window.event;
      var k = e.keyCode || e.which;
      // 获取删除前的内容
      var obj = document.querySelector("#postText");
      this.beforeContent = obj.innerHTML;
      this.beforeLenght = this.beforeContent.length;
      this.afterContent = this.beforeContent;
    },
    /**
     * 检测是否删除插入的图片
     */
    afterDel() {
      this.afterLength = this.afterContent.length;
      /**
       * 判断删除的是否为图片，相差大于3的话一定为图片
       */
      if (this.beforeLenght - this.afterLength >= 3) {
        let rege = /<img.*?src="(.*?)".*?>/gm;
        // 匹配删除前的内容的所有图片路径
        let beforeImgs = this.beforeContent.match(rege);
        // 匹配删除后的内容的所有图片路径
        this.afterImgs = this.afterContent.match(rege);
        if (beforeImgs) {
          // 匹配src路径
          let reg = new RegExp(/src="(.*?)"/);
          // 匹配表情
          let regEx = new RegExp(/src="static\/faces\/.*?"/);
          for (let i = 0; i < beforeImgs.length; i++) {
            // console.log(beforeImgs[i]);
            // 判断是否为表情包
            if (!regEx.test(beforeImgs[i])) {
              // 判断该图片是否被删除
              if (this.inArray(beforeImgs[i]) === -1) {
                let result = reg.exec(beforeImgs[i]);
                // console.log(result[1]);
                this.delImg(result[1]);
              }
            } else {
              // console.log("表情包");
            }
          }
        } else {
          // console.log("没有图片");
        }
      }
    },
    inArray(str) {
      if (this.afterImgs && this.afterImgs.length) {
        for (let i = 0; i < this.afterImgs.length; i++) {
          if (this.afterImgs[i] == str) {
            return i;
          }
        }
      }
      return -1;
    },
    /**
     * 删除图片
     */
    delImg(src, index) {
      src = src.replace(this.GLOBAL.Host, "");
      if (index >= 0) {
        this.imgs.splice(index, 1);
      }
      // 发送删除图片文件的请求
      let url = this.GLOBAL.Host + "/bcweb/index.php/Home/Uploads/unlinkFile";
      this.$axios
        .get(url, {
          params: {
            path: src
          }
        })
        .then(response => {
          // console.log(response.data);
        });
    },
    /**
     * 插入图片
     */
    insertImg(imgs = false) {
      if (!imgs) {
        document.querySelector("#postText").removeAttribute("contentEditable");
      }
      // console.log("imgs" + imgs);
      // 获取文件对象
      let file = event.target.files[0];
      // 获取对象的格式
      let type = file.type;
      // 获取对象的大小
      let size = file.size;
      // 检测对象的格式是否正确
      if (this.imgType.indexOf(type) == -1) {
        this.$messagebox.alert("请选择格式为：jpg|jpeg|png|gif的图片");
        return false;
      }
      // 检测对象的大小是否满足条件
      if (size > 3145728) {
        this.$messagebox.alert("照片不可以超过3M");
        return false;
      }
      let url =
        this.GLOBAL.Host + "/bcweb/index.php/Home/Uploads/uploadThemeImg";
      // 创建form表单对象
      let form = new FormData();
      // 向form表单对象添加文件对象
      form.append("file", file, file.name);
      let that = this;
      // 上传图片
      this.$axios
        .post(url, form, {
          // 设置上传文件所需的头信息
          headers: { "Content-Type": "multipart/form-data" }
        })
        .then(response => {
          // 判断是否为附属图片
          if (imgs) {
            // 附属图片
            // 判断是否上传成功
            if (response.status) {
              // 压入附属图片列表
              this.imgs.push(response.data.saveName);
              // document.getElementById("add-img-input-box").value = "";
            } else {
              that.$messagebox.alert("上传失败！");
            }
          } else {
            // 插入图片
            // 判断是否上传成功
            if (response.status) {
              // 向内容插入图片
              that.insertContent(
                '<img style="max-width:100%;display:inline-block;" src="' +
                  that.GLOBAL.Host +
                  response.data.src +
                  '" />'
              );
            } else {
              that.$messagebox.alert("上传失败！");
            }
          }
          this.$refs.pathClear1.value = "";
          this.$refs.pathClear2.value = "";
          // document.getElementById("add-img-input-box").val("");
        });
    },
    /**
     * 插入表情
     */
    addFace(img) {
      this.insertContent(
        '<img style="display:inline;width:6vw;height:6vw;margin:0px .5vw;" src="' +
          img +
          '" />'
      );
      this.isFace = !this.isFace;
    },
    //获取光标（插入）位置
    saveRange(flag = false) {
      /**
       * 1、Selection对象表示用户选择的文本范围或插入符号的当前位置。它代表页面中的文本选区，可能横跨多个元素。文本选区由用户拖拽鼠标经过文字而产生。
       * 要获取用于检查或修改的Selection对象，请调用 window.getSelection()。
       * 2、selection是对当前激活选中区（即高亮文本）进行操作。
       * 在非IE浏览器（Firefox、Safari、Chrome、Opera）下可以使用window.getSelection()获得selection对象，本文讲述的是标准的selection操作方法。文中绝大部分内容来自 https://developer.mozilla.org/en/DOM/Selection
       * 3、https://www.cnblogs.com/rainman/archive/2011/02/27/1966482.html
       */
      var selection = window.getSelection
        ? window.getSelection()
        : document.selection;
      // console.log(selection);
      // 判断插入位置是否为编辑区
      if (
        (!selection.rangeCount ||
          (selection.focusNode.id != "postText" &&
            selection.focusNode.parentNode.id != "postText" &&
            selection.focusNode.parentNode.parentNode.id != "postText")) &&
        !flag
      ) {
        this.$messagebox.alert("插入位置不在编辑区中！");
        return false;
      } else if (!selection.rangeCount && flag) {
        return false;
      }
      var range = selection.createRange
        ? selection.createRange()
        : selection.getRangeAt(0);
      return range;
    },
    // cursorToEnd(obj) {
    //   if (window.getSelection) {
    //     console.log("ggg");
    //     //ie11 10 9 ff safari
    //     obj.focus(); //解决ff不获取焦点无法定位问题
    //     var range = window.getSelection(); //创建range
    //     // range.selectAllChildren(obj); //range 选择obj下所有子内容
    //     // range.collapseToEnd(); //光标移至最后
    //   } else if (document.selection) {
    //     console.log("fff");
    //     //ie10 9 8 7 6 5
    //     var range = document.selection.createRange(); //创建选择对象
    //     //var range = document.body.createTextRange();
    //     // range.moveToElementText(obj); //range定位到obj
    //     // range.collapse(false); //光标移至最后
    //     // range.select();
    //   } else {
    //     console.log("不支持");
    //   }
    //   // console.log(range);
    //   // range.moveStart();
    //   // range.setStart(obj.lastChild,0);
    //   // this.range.selectNode(obj);
    //   // this.range.collapse(false);
    //   // obj.focus();
    //   // console.log("after->range => {");
    //   // console.log(this.range);
    //   // console.log(this.range.endContainer.childNodes.length);
    //   // console.log("}");
    //   // this.range.setStart(this.range.endContainer.childNodes[]);
    // },
    //向光标位置插入内容
    insertContent(str, flag = false) {
      var selection,
        range = this.saveRange(flag);
      if (!range) {
        return false;
      }
      if (!window.getSelection) {
        /**
         * pasteHTML()是一个方法，在指定的文字区域内替换该区域内的文本或者HTML，该方法必须应用于一个 createTextRange() 或者 document.selection.createRange() 创建的区域上
         */
        range.pasteHTML(str);
        range.collapse(false);
        range.select();
      } else {
        selection = window.getSelection
          ? window.getSelection()
          : document.selection;
        /**
         * range.collapse方法向边界点折叠该 Range,折叠后的 Range 为空，不包含节点内容。true 折叠到 Range 的 start 节点，false 折叠到 end 节点。如果省略，则默认为 false
         * range.createContextualFragment()该方法通过调用HTML片段解析算法或XML片段解析算法返回一个文档片段 DocumentFragment 。
         * hasR_lastChild.previousSibling获取当前节点的前一个节点
         */
        range.collapse(false);
        var hasR = range.createContextualFragment(str);

        var hasR_lastChild = hasR.lastChild;
        while (
          hasR_lastChild &&
          hasR_lastChild.nodeName.toLowerCase() == "br" &&
          hasR_lastChild.previousSibling &&
          hasR_lastChild.previousSibling.nodeName.toLowerCase() == "br"
        ) {
          var e = hasR_lastChild;
          hasR_lastChild = hasR_lastChild.previousSibling;
          hasR.removeChild(e);
        }
        /**
         * Range.insertNode() 是在Range的起始位置插入节点的方法。
         * 新节点是插入在 the Range起始位置。如果将新节点添加到一个文本 节点, 则该节点在插入点处被拆分，插入发生在两个文本节点之间
         * 如果新节点是一个文档片段，则插入文档片段的子节点。
         */
        range.insertNode(hasR);
        if (hasR_lastChild) {
          /**
           * 3.setStartBefore:表示用于将某个节点的起点位置设置为range对象的起点位置;
           * 4.setStartAfter:表示用于将某个节点的终点位置设置为range对象的起点位置;
           * 5.setEndBefore:表示用于将某个节点的起点位置设置为range对象的终点位置;
           * 6.setEndAfter:表示用于将某个节点的终点位置设置为range对象的终点位置;
           */
          range.setEndAfter(hasR_lastChild); //表示用于将某个节点的终点位置设置为range对象的终点位置;
          range.setStartAfter(hasR_lastChild); //表示用于将某个节点的终点位置设置为range对象的起点位置;
        }
        /**
         * Selection.removeAllRanges()方法会从当前selection对象中移除所有的range对象,取消所有的选择只 留下anchorNode 和focusNode属性并将其设置为null。
         */
        selection.removeAllRanges();
        selection.addRange(range);
      }
      this.afterContent = document.getElementById("postText").innerHTML;
      document
        .querySelector("#postText")
        .setAttribute("contentEditable", "true");
    },
    /**
     * 发帖子
     */
    postTheme: function() {
      //
      if (Object.keys(this.keywords).length) {
        if(this.rules.filter_mod == 3){
          this.$messagebox.alert("帖子包含有敏感词，禁止发布！");
          return false;
        }else{
          this.keywords.forEach((element, index)=>{
            this.title = this.title.replace(element, this.rules.filter_replace);
            this.afterContent = this.afterContent.replace(element, this.rules.filter_replace);
          })
        }
      }
      // 判断标题是否为空
      if (!this.title) {
        this.$messagebox.alert("请输入标题！");
        return false;
      }

      // 判断内容是否为空
      if (!this.afterContent) {
        this.$messagebox.alert("请输入帖子内容！");
        return false;
      }
      // 判断是否存在主题区id
      if (!this.$route.query.zone_id) {
        this.$messagebox.alert("数据丢失，请重新进入发帖页面！");
        return false;
      }

      let data = {
        type: "postTheme",
        zone_id: this.$route.query.zone_id, //分区id
        title: this.title, //标题
        content: this.afterContent.replace(/"/g, ""), //内容
        imgs: this.imgs, //附属图片列表
        user_id: this.GLOBAL.userInfo.id, //用户id
        user_name: this.GLOBAL.userInfo.nickname, // 用户昵称
        con_id: this.GLOBAL.connectionId, //链接资源标识符，
        tieNum: this.tieNum
      };
      // console.log(data);
      let that = this;
      // 发送请求
      this.senddata({
        data: data,
        callback: function(response) {
          // console.log(response);
          if (response.status) {
            if (response.gold || response.integral) {
              // 更新本地用户的金币和积分信息
              that.GLOBAL.userInfo.gold = response.gold;
              that.GLOBAL.userInfo.integral = response.sliver;
              localStorage.setItem(
                "bc_userInfo",
                JSON.stringify(that.GLOBAL.userInfo)
              );
              that.$messagebox
                .alert(
                  "您已获得" +
                    that.rules["gold"] +
                    "个金币和" +
                    that.rules["sliver"] +
                    "积分,当前金币为" +
                    response.gold +
                    "个，积分为" +
                    response.sliver +
                    "积分！"
                )
                .then(action => {
                  // 发表成功，跳转到主题列表页
                  that.$router.push({
                    path: "/theme",
                    query: {
                      themeInfo: that.$route.query.themeInfo,
                      zone_id: that.$route.query.zone_id
                    }
                  });
                });
            } else {
              // 发表成功，跳转到主题列表页
              that.$router.push({
                path: "/theme",
                query: {
                  themeInfo: that.$route.query.themeInfo,
                  zone_id: that.$route.query.zone_id
                }
              });
            }
          } else {
            that.$messagebox.alert(response.msg);
          }
        },
        callbackFlag: "responsePostTheme",
        handType: "user"
      });
    }
  }
};
</script>

<style scoped>
#title-input-box {
  height: 12vw;
  line-height: 12vw;
  font-size: 4vw;
  text-align: center;
  outline: none;
}
#title-input-box:empty::before {
  content: attr(placeholder);
  color: #ccc;
  font-size: 4vw;
  /* line-height: 35vh; */
}
.post {
  padding: 2vw;
}
.title {
  border: 0.3vw solid #d1d1d1;
  margin: 2vw 0;
}
.extend {
  padding: 2vw;
  display: flex;
  flex-wrap: nowrap;
  border: 0.3vw solid #d1d1d1;
}
.extend img {
  width: 7vw;
  height: 7vw;
  margin-right: 5vw;
}

.image {
  display: flex;
  /* flex-grow: 1; */
  /* flex-shrink: 1; */
  /* flex-basis: auto; */
  flex-wrap: wrap;
  margin-left: -0.5vw;
  /* margin-bottom: 15vw; */
}
.image .oneImgs {
  /* border: 1px solid red; */
  border: 0.3vw dashed #ccc;
  position: relative;
}
.image .preview,
.image .oneImgs {
  width: 27.7333vw;
  height: 27.7333vw;
  display: flex;
  justify-content: center;
  justify-items: center;
  align-content: center;
  align-items: center;
  flex-shrink: 0;
  margin-left: 1vw;
  margin-top: 1vw;
}
.image .preview img {
  width: 100%;
  height: 100%;
}
.image .oneImgs img {
  max-width: 100%;
  max-height: 100%;
}
.image .oneImgs span {
  position: absolute;
  top: 0px;
  left: 0px;
  width: 5vw;
  height: 5vw;
  border-radius: 5vw;
  background: red;
  box-shadow: 0vw 0vw 0.4vw 0.4vw yellow;
  color: #fff;
  font-size: 4vw;
  line-height: 5vw;
  text-align: center;
}
.faces {
  display: flex;
  flex-wrap: wrap;
  border: 0.3vw solid #d1d1d1;
  border-top: none;
  padding-bottom: 2vw;
}
.faces img {
  width: 6vw;
  height: 6vw;
  margin-left: 1vw;
  margin-top: 2vw;
}
.text {
  padding: 2vw;
  height: 35vh;
  border: 0.3vw solid #d1d1d1;
  border-top: none;
  background-color: white;
}
#postText:empty::before {
  content: attr(placeholder);
  color: #ccc;
  font-size: 4vw;
  /* line-height: 35vh; */
}
#postText {
  width: 100%;
  height: 100%;
  border: none;
  text-align: left;
  overflow: auto;
}
#postText:focus {
  border: none;
  outline: none;
}
#postText:-moz-placeholder {
  text-align: center;
}
#postText::-webkit-input-placeholder {
  text-align: center;
}
.fatie-tips-box {
  text-align: left;
  box-sizing: border-box;
  padding: 0px 4vw 2vw 4vw;
  color: red;
  font-size: 4vw;
  border: 1px solid pink;
  margin-bottom: 3vw;
}
.fatie-tips-box h1 {
  font-size: 4vw;
  font-weight: bold;
  color: #666;
}
</style>