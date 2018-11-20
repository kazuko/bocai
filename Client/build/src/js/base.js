exports.install = function(Vue, options) {
  console.log("---------------options---------------");
  console.log(options);
  console.log("---------------options---------------");
  // 发送数据的回掉函数
  var senddataCallback = function() {};
  // 判断是否存在发送数据的回掉函数
  var hascallback = false;
  Vue.prototype.checkLogin = function(callback) {
    //   判断是否登陆
    if (this.GLOBAL.userInfo.id) {
      // 判断是否创建socket
      if (!this.GLOBAL.socketHand) {
        console.log("I try to create a socket");
        this.createSocket(false, callback);
      } else {
        console.log("socket is listening!");
      }
    } else {
      this.$router.push("/login");
    }
  };
  Vue.prototype.isMobile = function() {
    // var sUserAgent = navigator.userAgent.toLowerCase();
    // var bIsIpad = sUserAgent.match(/ipad/i) == 'ipad';
    // var bIsIphone = sUserAgent.match(/iphone os/i) == 'iphone os';
    // var bIsMidp = sUserAgent.match(/midp/i) == 'midp';
    // var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == 'rv:1.2.3.4';
    // var bIsUc = sUserAgent.match(/ucweb/i) == 'web';
    // var bIsCE = sUserAgent.match(/windows ce/i) == 'windows ce';
    // var bIsWM = sUserAgent.match(/windows mobile/i) == 'windows mobile';
    // var bIsAndroid = sUserAgent.match(/android/i) == 'android';

    // if (bIsIpad || bIsIphone || bIsMidp || bIsUc7 || bIsUc || bIsCE || bIsWM || bIsAndroid) {
    return true;
    // } else {
    //     return false;
    // }
  };
  /**
   *
   * @param {*} time1 对比日期，此为必选参数
   * @param {*} time2 被对比日期，此为可选项，不填则表示是否显示当前日期
   */
  Vue.prototype.showDate = function(time1 = "", time2 = "", time3 = "") {
    if (time3) {
      // 返回时间戳time3对应的日期字符串
      return new Date(time3 * 1000).toLocaleString();
    }
    // 实例化当前的时间对象
    var nowObj = new Date();
    // 判断是否为两个时间的比较
    if (time1 && time2) {
      // 实例化上一条对比消息的时间对象
      var DateObj1 = new Date(Number(time1) * 1000);
      // 实例化需要显示的时间对象
      var DateObj2 = new Date(Number(time2) * 1000);
      // 判断是否需要显示时间（即与上一条消息的时间间隔是否大于30分钟）
      console.log("time2-time1:" + (DateObj2.getTime() - DateObj1.getTime()));
      if ((DateObj2.getTime() - DateObj1.getTime()) / 1000 >= 30 * 60) {
        // 判断时间戳之差是否小于24小时
        if ((nowObj.getTime() - DateObj2.getTime()) / 1000 <= 24 * 60 * 60) {
          // 判断日期是否相同（时间戳差值小于24小时，但是日期不同即为昨天，否则为当天)
          if (DateObj2.toLocaleDateString() != nowObj.toLocaleDateString()) {
            // 返回昨天拼接上时间
            return "昨天 " + DateObj2.toLocaleTimeString();
          } else {
            // 返回时间
            return DateObj2.toLocaleTimeString();
          }
        } else {
          // 返回完整的日期时间
          return DateObj2.toLocaleString();
        }
      } else {
        return false;
      }
    } else if (time1 && !time2) {
      // 是否显示当前的聊天消息时间
      // 实例化上一条对比消息的时间对象
      var DateObj1 = new Date(Number(time1) * 1000);
      return "今天 " + DateObj1.toLocaleTimeString();
    } else if (!time1 && time2) {
      // 历史聊天记录第一条时间
      // 实例化历史纪录第一条时间
      var DateObj2 = new Date(Number(time2) * 1000);
      var time = (Number(nowObj.getTime()) - Number(DateObj2.getTime())) / 1000;
      // 判断是否为今天或者昨天的消息
      if (time <= 24 * 60 * 60) {
        // 判断是否为今天的消息
        if (nowObj.toLocaleDateString() == DateObj2.toLocaleDateString()) {
          return "今天" + DateObj2.toLocaleTimeString();
        } else {
          return "昨天 " + DateObj2.toLocaleTimeString();
        }
      } else {
        // 返回日期时间
        return DateObj2.toLocaleString();
      }
    } else {
      return Math.ceil(Number(nowObj.getTime()) / 1000);
    }
  };
  /**
   *  动态添加属性
   * @param {*} obj 添加属性的对象
   * @param {*} data 数组或者对象
   */
  Vue.prototype.setGlobalAttribute = function(obj, data) {
    for (var index in data) {
      var type = Object.prototype.toString.call(data[index]);
      if (type == "[object Object]" || type == "[object Array]") {
        try {
          this.$set(obj, index, {});
        } catch (e) {
          this.$set(obj, index.toString(), {});
        }
        this.setGlobalAttribute(obj[index], data[index]);
      } else {
        try {
          this.$set(obj, index, data[index]);
        } catch (e) {
          this.$set(obj, index.toString(), data[index]);
        }
      }
    }
  };
  /**
   *  websocket 发送消息
   * @param {*} data 一维键值对数组
   */
  Vue.prototype.senddata = function(data, callback) {
    // console.log(this.GLOBAL.socketHand);
    console.log("sending Socket");
    let text = this.GLOBAL.ObjToString(data);
    console.log("sendText:" + text);
    console.log("gameOrNot:" + this.GLOBAL.gameOrNot);
    if (this.GLOBAL.gameOrNot) {
      if (
        this.GLOBAL.gameSocketHand &&
        this.GLOBAL.gameSocketHand.readyState == 1
      ) {
        this.GLOBAL.gameSocketHand.send(text);
      } else {
        if (callback) {
          callback(false);
        }
        return false;
      }
    } else {
      if (this.GLOBAL.socketHand && this.GLOBAL.socketHand.readyState == 1) {
        this.GLOBAL.socketHand.send(text);
      } else {
        if (callback) {
          callback(false);
        }
        return false;
      }
    }
    if (callback) {
      console.log("设置回掉函数");
      senddataCallback = callback;
      hascallback = true;
    }
  };
  /**
   * 关闭socket
   */
  Vue.prototype.closeSocket = function() {
    console.log("closing Socket");
    if (this.GLOBAL.socketHand) {
      var data = {
        send_id: this.GLOBAL.userInfo.id,
        nickname: "",
        get_id: "",
        fname: "",
        msg: "I am leaving",
        type: "close",
        rType: 1,
        fType: 0,
        con_id: this.GLOBAL.connectionId
      };
      this.senddata(data);
      this.GLOBAL.socketHand.close();
      this.GLOBAL.socketHand = "";
      this.GLOBAL.socketStatus = false;
    }
  };
  /**
   * 创建websocket
   */
  Vue.prototype.createSocket = function(websocketHost = "", opencallback = "") {
    // 创建websocket
    var that = this;
    if (websocketHost) {
      // 标志当前是否为游戏soket链接
      that.GLOBAL.gameOrNot = true;

      // websocketHost = this.GLOBAL.socketHost;
      this.GLOBAL.gameSocketHand = new WebSocket(websocketHost);
      this.GLOBAL.gameSocketHand.onopen = function(evt) {
        if (opencallback) {
          opencallback(evt);
        }
      };
    } else {
      this.GLOBAL.socketHand = new WebSocket(this.GLOBAL.socketHost);
      this.GLOBAL.socketHand.onopen = function(evt) {
        console.log("openning socket...");
        // 标志当前是否为游戏soket链接
        that.GLOBAL.gameOrNot = false;
        // 修改socket的链接状态
        that.GLOBAL.socketStatus = true;
        // 请求app信息
        let data = {
          type: "getAppInfo",
          send_id: "",
          con_id: that.GLOBAL.connectionId
        };
        that.senddata(data);
        if (that.GLOBAL.userInfo.id && !that.GLOBAL.loginStatus) {
          // 请求未读消息列表和好友列表
          let data = {
            con_id: that.GLOBAL.connectionId, //socket链接标识符
            send_id: that.GLOBAL.userInfo.id, //用户id
            nickname: that.GLOBAL.userInfo.nickname, //用户昵称
            type: "handle",
            rType: 1,
            fType: 0 // 标志是否为好友消息、陌生人消息，和user_message表的status对应
          };
          that.senddata(data);
        }
        if (opencallback) {
          opencallback(evt);
        }
      };
    }
    console.log("creating socket...");
  };
  /**
   * 监听消息
   * @param {*} onmessagecallback
   */
  Vue.prototype.onmessage = function(onmessagecallback) {
    console.log("onmessage....");
    let that = this;
    if (this.GLOBAL.gameOrNot) {
      // 游戏socket接收消息处理
      this.GLOBAL.gameSocketHand.onmessage = function(evt) {
        console.log("Recived gameMessage: " + evt.data);
        if (hascallback) {
          if (evt.data) {
            let data = JSON.parse(evt.data);
            senddataCallback(data);
          } else {
            senddataCallback(evt.data);
          }
          hascallback = false;
        }
        // 判断是否存在回掉函数
        if (onmessagecallback) {
          onmessagecallback(evt.data);
        }
      };
    } else {
      // 监听websockt，接收消息
      this.GLOBAL.socketHand.onmessage = function(evt) {
        console.log("Recived Message: " + evt.data);
        if (evt.data) {
          var data = JSON.parse(evt.data);
          switch (data.type) {
            case "ConnectionSuccess":
              that.GLOBAL.connectionId = data.connectionId;
              break;
            case "handle": //握手响应
              handle(data, that);
              break;
            case "heart": //心跳响应
              // 相应心跳信息
              var data = {
                con_id: that.GLOBAL.connectionId,
                msg: "I accept the heart",
                type: "heart",
                rType: 1,
                fType: 0 // 和user_message表的status对应，0表示没任何意义
              };
              that.senddata(data);
              break;
            case "searchFriendsResult": //查找好友
              if (hascallback) {
                // 调用发送数据的回掉函数
                senddataCallback(data.result);
                hascallback = false;
              } else {
                that.$messagebox.alert("没有回掉函数！");
              }
              break;
            case "ResoneAddFriendRequest": //响应添加好友事件
              if (hascallback) {
                // 调用发送数据的回掉函数
                senddataCallback(data);
                hascallback = false;
              } else {
                that.$messagebox.alert("没有回掉函数！");
              }
              break;
            case "addFriendRequest": //接收添加好友申请
              addFriendRequest(data, that);
              break;
            case "onlineNotice": //上线、下线提醒
              onlineNotice(data, that);
              break;
            case "deleteFriendNotice": //接收删除好友提醒
              deleteFriendNotice(data, that);
              break;
            case "PassFriendRequest": //接收好友申请通过提醒
              PassFriendRequest(data, that);
              break;
            case "RefuseFriendRequest": //接受拒绝好友申请的提醒
              RefuseFriendRequest(data, that);
              break;
            case "text":
              acceptMessage(data, that);
              break;
            case "appInfoAndSysNews":
              appInfoAndSysNews(data, that);
              break;
            default:
              // 判断是否存在发送数据后的回掉函数
              if (hascallback) {
                // 调用回掉函数
                senddataCallback(data, that);
                hascallback = false;
              } else {
                // 见接受到的数据保存到接受列表
                // that.GLOBAL.accept.push(data);
                // 未读消息条数+1
                // that.GLOBAL.msgLength++;
              }
          }
        } else {
          if (hascallback) {
            senddataCallback(evt.data, that);
            hascallback = false;
          }
        }
        // 判断是否存在回掉函数
        if (onmessagecallback) {
          onmessagecallback(evt.data);
        }
      };
    }
  };
  /**
   * 更新app信息和系统消息
   * @param {*} data
   * @param {*} that
   */
  function appInfoAndSysNews(data, that) {
    if (data.appInfo != that.GLOBAL.appInfo) {
      that.GLOBAL.appInfo = data.appInfo;
      localStorage.setItem(
        "bc_appInfo",
        that.GLOBAL.ObjToString(that.GLOBAL.appInfo)
      );
    }
    that.GLOBAL.sysNews = data.sysNews;
    that.$forceUpdate();
  }
  /**
   * 接收聊天消息
   * @param {*} data
   * @param {*} that
   */
  function acceptMessage(data, that) {
    let msg = {};
    if (data.data.msg) {
      msg = data.data.msg;
      if (data.data.userInfo) {
        // 存在用户信息，表示为陌生人，更新陌生人队列
        let user = data.data.userInfo;
        if (that.GLOBAL.friendLists.strange) {
          // 陌生人队列不为空
          if (!that.GLOBAL.friendLists.strange[user.id]) {
            // 不存在当前用户，则更行陌生人队列
            that.$set(that.GLOBAL.friendLists.strange, user.id, {});
            that.setGlobalAttribute(
              that.GLOBAL.friendLists.strange[user.id],
              user
            );
          }
        } else {
          // 陌生人队列为空，更新陌生人队列
          that.$set(that.GLOBAL.friendLists, "strange", {});
          that.$set(that.GLOBAL.friendLists.strange, user.id, {});
          that.setGlobalAttribute(
            that.GLOBAL.friendLists.strange[user.id],
            user
          );
        }
      }
    } else {
      msg = data.data;
    }
    // 更新消息队列
    let i = 0;
    let newList = {};
    if (that.GLOBAL.msgList[msg.send_id]) {
      // 存在当前用户的消息队列
      let msgList = that.GLOBAL.msgList[msg.send_id];
      // 初始化当前用户的消息队列
      that.GLOBAL.msgList[msg.send_id] = {};
      for (var index in msgList) {
        if (msgList[index]) {
          newList[i] = msgList[index];
          i++;
        }
      }
      newList[i] = msg;
    } else {
      // 不存在当前用户的消息队列
      that.$set(that.GLOBAL.msgList, msg.send_id, {});
      newList[i] = msg;
    }
    that.setGlobalAttribute(that.GLOBAL.msgList[msg.send_id], newList);
    // 未读消息数加1
    if (that.GLOBAL.msgLength) {
      that.GLOBAL.msgLength++;
    } else {
      that.$set(that.GLOBAL, "msgLength", 1);
    }
    // 好友未读消息数加1
    if (that.GLOBAL.friendMsgNum[msg.send_id]) {
      that.GLOBAL.friendMsgNum[msg.send_id]++;
    } else {
      that.$set(that.GLOBAL.friendMsgNum, msg.send_id, 1);
    }
    that.$forceUpdate();
  }
  /**
   * 好友申请被拒绝提醒
   * @param {*} data
   * @param {*} that
   */
  function RefuseFriendRequest(data, that) {
    // 更新系统消息列表
    let friendLists = that.GLOBAL.friendLists;
    let newList = {};
    let i = 1;
    that.GLOBAL.friendLists = {};
    newList[data.msg.id + "@" + 0] = data.msg;
    for (var index in friendLists.system) {
      if (friendLists.system[index]) {
        newList[friendLists.system[index].id + "@" + i] =
          friendLists.system[index];
        i++;
      }
    }
    friendLists.system = newList;
    that.setGlobalAttribute(that.GLOBAL.friendLists, friendLists);
    // 更新未读消息条数
    that.GLOBAL.msgLength++;
    if (that.GLOBAL.friendMsgNum.system) {
      that.GLOBAL.friendMsgNum.system++;
    } else {
      that.$set(that.GLOBAL.friendMsgNum, "system", 1);
    }
    that.$forceUpdate();
  }

  /**
   * 好友申请通过提醒
   * @param {*} data
   * @param {*} that
   */
  function PassFriendRequest(data, that) {
    data = data.data;
    console.log("<passRequest>");
    console.log(data);
    console.log("</passRequest>");
    // 更新好友列表
    that.$set(that.GLOBAL.friendLists.friends, data.fInfo.id, {});
    that.setGlobalAttribute(
      that.GLOBAL.friendLists.friends[data.fInfo.id],
      data.fInfo
    );
    // 更新陌生人列表
    if (that.GLOBAL.friendLists.strange[data.fInfo.id]) {
      that.GLOBAL.friendLists.strange[data.fInfo.id] = null;
    }
    // 更新好友消息列表
    let newList = {};
    if (that.GLOBAL.msgList[data.fInfo.id]) {
      let msgList = that.GLOBAL.msgList[data.fInfo.id];
      let i = 0;
      that.GLOBAL.msgList[data.fInfo.id] = {};
      for (var index in msgList) {
        if (msgList[index]) {
          newList[i] = msgList[index];
          i++;
        }
      }
      newList[i] = data.msg;
    } else {
      that.$set(that.GLOBAL.msgList, data.fInfo.id, {});
      newList[0] = data.msg;
    }
    that.setGlobalAttribute(that.GLOBAL.msgList[data.fInfo.id], newList);
    // 更新未读消息条数
    that.GLOBAL.msgLength++;
    // 更新好友未读消息条数
    if (that.GLOBAL.friendMsgNum[data.fInfo.id]) {
      that.GLOBAL.friendMsgNum[data.fInfo.id]++;
    } else {
      that.$set(that.GLOBAL.friendMsgNum, data.fInfo.id, 1);
    }
    that.$forceUpdate();
  }
  /**
   * 好友删除提示消息
   * @param {*} data
   * @param {*} that
   */
  function deleteFriendNotice(data, that) {
    // 系统消息信息
    let msg = {
      id: data.msg.send_id,
      head: that.GLOBAL.friendLists.friends[data.msg.send_id].head,
      on_status: that.GLOBAL.friendLists.friends[data.msg.send_id].on_status,
      nickname: that.GLOBAL.friendLists.friends[data.msg.send_id].nickname,
      content: data.msg.content,
      flag: data.msg.flag,
      status: data.msg.status,
      mid: data.msg.mid
    };
    // 将该用户从好友列表中除移
    that.GLOBAL.friendLists.friends[data.msg.send_id] = null;
    // 清空该用户的消息列表
    that.GLOBAL.msgList[data.msg.send_id] = null;
    // 未读消息总数 - 该用户未读消息条数
    that.GLOBAL.msgLength -= that.GLOBAL.friendMsgNum[data.msg.send_id];
    // 清除该用户未读消息条数
    that.GLOBAL.friendMsgNum[data.msg.send_id] = null;
    // 将删除提示信息压入系统消息队列
    let i = that.GLOBAL.friendLists.system.length;
    that.GLOBAL.friendLists.system[data.msg.send_id + "@" + i] = msg;
    // console.log(that.GLOBAL.friendLists.system);
    // 系统消息加1
    if (that.GLOBAL.friendMsgNum.system) {
      that.GLOBAL.friendMsgNum.system++;
    } else {
      that.GLOBAL.friendMsgNum.system = 1;
    }
    that.GLOBAL.msgLength++;
    that.$forceUpdate();
  }
  /**
   * 握手处理函数
   * @param {*} data
   * @param {*} that
   */
  function handle(data, that) {
    if (data.send_id == -1) {
      that.$messagebox.alert(data.msg);
      return;
    }
    console.log("+++++++++++++++++++++handling++++++++++++++++++++++++");
    console.log(data);
    console.log("+++++++++++++++++++++handling++++++++++++++++++++++++");
    // var sendList = that.GLOBAL.sendList;
    // // 查看是否存在待发送的数据
    // if (sendList.length > 0 && data.msg == 'ok') {
    //     for (var i = 0; i < sendList.length; i++) {
    //         that.senddata(sendList[i]);
    //     }
    //     // 发送完所有待发送的数据后，清空数据
    //     sendList = {};
    // } else if (data.msg != 'ok') {
    //     that.$messagebox.alert(data.msg);
    // }
    // 手动更新全局变量
    that.$forceUpdate();
    that.GLOBAL.connectionId = data.con_id;
    // 未读消息列表
    let msgList = data.userMsgs;
    // 未读消息中条数
    that.GLOBAL.msgLength = 0;
    // 保存未读消息条数
    for (var index in msgList) {
      if (index == "system") {
        // 系统消息
        // console.log("<systemlength>");
        // console.log(msgList[index]);
        // console.log("</systemlength>");
        // 设置系统消息的条数
        if (that.GLOBAL.friendMsgNum[index]) {
          that.GLOBAL.friendMsgNum[index] = msgList[index];
        } else {
          that.$set(that.GLOBAL.friendMsgNum, index, msgList[index]);
        }
        // 计算未读消息总条数
        that.GLOBAL.msgLength += msgList[index];
      } else {
        // 设置每个用户的未读消息条数； index = 用户id
        if (that.GLOBAL.friendMsgNum[index]) {
          that.GLOBAL.friendMsgNum[index] = msgList[index].length;
        } else {
          that.$set(that.GLOBAL.friendMsgNum, index, msgList[index].length);
        }
        // console.log("<" + index + "lenght>");
        // console.log(msgList[index]);
        // console.log("</" + index + "lenght>");
        // 计算未读消息总条数
        that.GLOBAL.msgLength += msgList[index].length;
        // 添加消息队列
        if (that.GLOBAL.msgList[index]) {
          that.GLOBAL.msgList[index] = msgList[index];
        } else {
          that.$set(that.GLOBAL.msgList, index, []);
          that.setGlobalAttribute(that.GLOBAL.msgList[index], msgList[index]);
        }
      }
    }
    // 添加朋友列表
    that.setGlobalAttribute(that.GLOBAL.friendLists, data.friendsList);
    that.GLOBAL.loginStatus = true;
    // 更新好友的链接标识符
    if (that.GLOBAL.friendInfo.info) {
      if (that.GLOBAL.friendInfo.status == 2) {
        that.GLOBAL.friendInfo.info.connectionID =
          that.GLOBAL.friendLists["strange"][
            that.GLOBAL.friendInfo.info.id
          ].connectionID;
      } else {
        that.GLOBAL.friendInfo.info.connectionID =
          that.GLOBAL.friendLists["friends"][
            that.GLOBAL.friendInfo.info.id
          ].connectionID;
      }
    }
  }

  /**
   * 好友上线通知处理
   * @param {*} data
   * @param {*} that
   */
  function onlineNotice(data, that) {
    if (that.GLOBAL.friendLists[data.fType][data.index]) {
      console.log("===============onlineNotice===============");
      console.log(data);
      console.log("===============onlineNotice===============");
      // 更新好友在线状态
      that.GLOBAL.friendLists[data.fType][data.index].on_status = data.status;
      // 更新好友链接标识符
      that.GLOBAL.friendLists[data.fType][data.index].connectionID =
        data.connectionID;
      if (that.GLOBAL.friendInfo.info.id == data.index) {
        that.GLOBAL.friendInfo.info.connectionID = data.connectionID;
      }
    }
  }
  /**
   * 接收到好友申请处理方法
   * @param {*} data
   * @param {*} that
   */
  function addFriendRequest(data, that) {
    if (data.fInfo) {
      console.log("add friends success notice!");
      // 更新好友列表
      that.$set(that.GLOBAL.friendLists.friends, data.fInfo.id, []);
      that.setGlobalAttribute(
        that.GLOBAL.friendLists.friends[data.fInfo.id],
        data.fInfo
      );
      // 更新消息列表
      that.$set(that.GLOBAL.msgList, data.fInfo.id, []);
      that.$set(that.GLOBAL.msgList[data.fInfo.id], 0, []);
      that.setGlobalAttribute(that.GLOBAL.msgList[data.fInfo.id][0], data.msg);
      // that.GLOBAL.msgList[data.fInfo.id].push(data.msg);
      // 好友未读消息初始化为1
      that.$set(that.GLOBAL.friendMsgNum, data.fInfo.id, 1);
    } else {
      console.log("add friends request notice!");
      let i = 1;
      let friendLists = that.GLOBAL.friendLists;
      that.GLOBAL.friendLists = {};
      let newSystem = {};
      newSystem[data.msg.id + "@" + 0] = data.msg;
      for (var index in friendLists.system) {
        if (friendLists.system[index]) {
          newSystem[friendLists.system[index].id + "@" + i] =
            friendLists.system[index];
          i++;
        }
      }
      friendLists.system = newSystem;
      console.log(friendLists);
      that.setGlobalAttribute(that.GLOBAL.friendLists, friendLists);
      console.log(that.GLOBAL.friendLists);
      // 未读系统消息加1
      if (that.GLOBAL.friendMsgNum.system) {
        that.GLOBAL.friendMsgNum.system++;
      } else {
        that.$set(that.GLOBAL.friendMsgNum, "system", 1);
      }
      console.log(that.GLOBAL.friendMsgNum.system);
    }
    // 未读消息总数加1
    if (that.GLOBAL.msgLength) {
      that.GLOBAL.msgLength += 1;
    } else {
      that.$set(that.GLOBAL, "msgLength", 1);
    }
    console.log(that.GLOBAL.msgLength);
    that.$forceUpdate();
  }

  /**
   * 发送消息时更新消息列表
   * @param {*} content
   */
  Vue.prototype.updateMsgList = function(content) {
    // 更新本地的消息队列
    let text = {
      id: "",
      send_id: this.GLOBAL.userInfo.id, // 消息发送方
      get_id: this.GLOBAL.friendInfo.info.id, // 消息接收方
      time: this.showDate(), // 消息发送时间
      status: this.GLOBAL.friendInfo.status, // 标志位：1为好友，2为陌生人
      content: content, // 聊天内容
      flag: 1, // 消息的阅读标志位
      chatType: true, // 标志为当前的聊天内容,
      send_status: true // 发送状态，发送成功与否
    };
    let newList = {};
    let i = 0;
    // 判断是否存在当前消息队列
    if (this.GLOBAL.msgList[text.get_id]) {
      let msgList = this.GLOBAL.msgList[text.get_id];
      // 初始化当前消息队列
      this.GLOBAL.msgList[text.get_id] = {};
      for (var index in msgList) {
        if (msgList[index]) {
          newList[i] = msgList[index];
          i++;
        }
      }
      newList[i] = text;
    } else {
      newList[i] = text;
      this.$set(this.GLOBAL.msgList, text.get_id, {});
    }
    console.log("----------------------------------------------");
    console.log(newList);
    console.log("----------------------------------------------");
    // 动态加载到消息队列
    this.setGlobalAttribute(this.GLOBAL.msgList[text.get_id], newList);

    return text.time;
  };
};
console.log("BASE_JS");
