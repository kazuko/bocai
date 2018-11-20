exports.install = function (Vue, options) {
    console.log('---------------options---------------');
    console.log(options);
    console.log('---------------options---------------');
    var handlecallback = null;
    // 发送数据的回掉函数
    var senddataCallback = function () { };
    // 判断是否存在发送数据的回掉函数
    var hascallback = false;
    // 发送数据回调相应标志
    var senddataFlag = '';
    // 发送检测次数，超过5次则提示错误，不再尝试连接
    var senddataTimes = 0;
    // 系统消息回掉（hall）
    var sysNewsCallback = null;
    /**
     * 登陆检测
     */
    Vue.prototype.checkLogin = function (callback) {
        //   判断是否登陆
        if (this.GLOBAL.userInfo.id) {
            // 判断是否创建socket
            if (this.GLOBAL.gameOrNot) {
                this.checkGameSocket(callback);
            } else {
                this.checkSocket(callback);
            }
        } else {
            this.$router.push("/login");
        }
    }
    /**
     * 检测游戏socket是否正常
    //  */
    Vue.prototype.checkGameSocket = function (callback) {
        console.log('checking game socket...');
        this.createSocket(true, callback);
    }
    /**
     * 检测socket是否正常
     */
    Vue.prototype.checkSocket = function (callback1, callback2 = null) {
        console.log("checking user socket...");
        if (callback2) {
            sysNewsCallback = callback2;
        }
        this.createSocket(false, callback1);
    }
    /**
     * 判断是否为手机打开
     */
    Vue.prototype.isMobile = function () {
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
    }
    /**
     * 
     * @param {*} time1 对比日期，此为必选参数
     * @param {*} time2 被对比日期，此为可选项，不填则表示是否显示当前日期
     */
    Vue.prototype.showDate = function (time1 = '', time2 = '', time3 = '') {
        if (time3) {
            console.log(time3);
            // 返回时间戳time3对应的日期字符串
            return (new Date(time3 * 1000)).toLocaleString();
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
            // console.log("time2-time1:" + (DateObj2.getTime() - DateObj1.getTime()));
            if ((DateObj2.getTime() - DateObj1.getTime()) / 1000 >= 30 * 60) {
                // 判断时间戳之差是否小于24小时
                if ((nowObj.getTime() - DateObj2.getTime()) / 1000 <= 24 * 60 * 60) {
                    // 判断日期是否相同（时间戳差值小于24小时，但是日期不同即为昨天，否则为当天)
                    if (DateObj2.toLocaleDateString() != nowObj.toLocaleDateString()) {
                        // 返回昨天拼接上时间
                        return '昨天 ' + DateObj2.toLocaleTimeString();
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
            return "今天 " + DateObj1.toLocaleTimeString()
        } else if (!time1 && time2) {
            // 历史聊天记录第一条时间
            // 实例化历史纪录第一条时间
            var DateObj2 = new Date(Number(time2) * 1000);
            var time = (Number(nowObj.getTime()) - Number(DateObj2.getTime())) / 1000;
            // 判断是否为今天或者昨天的消息
            if (time <= 24 * 60 * 60) {
                // 判断是否为今天的消息
                if (nowObj.toLocaleDateString() == DateObj2.toLocaleDateString()) {
                    return '今天' + DateObj2.toLocaleTimeString();
                } else {
                    return '昨天 ' + DateObj2.toLocaleTimeString();
                }
            } else {
                // 返回日期时间
                return DateObj2.toLocaleString();
            }
        } else {
            return Math.ceil(Number(nowObj.getTime()) / 1000);
        }
    }
    /**
     * 获取用户好友消息列表信息
     */
    Vue.prototype.getUserInfo = function (callback) {
        // 判断用户是否登陆并且登陆状态为false
        if (this.GLOBAL.userInfo.id && !this.GLOBAL.loginStatus) {
            if (callback) {
                handlecallback = callback;
            }
            // 请求未读消息列表和好友列表
            let data = {
                con_id: this.GLOBAL.connectionId,
                send_id: this.GLOBAL.userInfo.id,
                nickname: this.GLOBAL.userInfo.nickname,
                broadcast: this.GLOBAL.userInfo.broadcast,//0:不接收广播消息；1：接收广播消息
                stmessage: this.GLOBAL.userInfo.stmessage,//0：不接收陌生人信息；1：接受陌生人信息
                type: "handle",
                rType: 1,
                fType: 0 // 标志是否为好友消息、陌生人消息，和user_message表的status对应
            };
            this.senddata({ data: data, handType: 'user' });
        } else {
            if (callback) {
                callback();
            }
        }
    }
    /**
     *  动态添加属性
     * @param {*} obj 添加属性的对象 
     * @param {*} data 数组或者对象
     */
    Vue.prototype.setGlobalAttribute = function (obj, data) {
        for (var index in data) {
            var type = Object.prototype.toString.call(data[index]);
            if (type == '[object Object]' || type == '[object Array]') {
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
    }
    /**
     * @param.data  发送的数据集（Object）
     * @param.callback  接收到数据时的回掉函数
     * @param.callbackFlag  标志位，若存在，则在接收到response时，当response.type == @param.callbackFlag时执行回调函数
     * @param.handType  标识符，默认值:'game',表示发送的是游戏数据；@param.handType='user'表示发送的非游戏数据
     */
    Vue.prototype.senddata = function ({
        data = {},
        callback = null,
        callbackFlag = null,
        handType = 'game'
    }) {
        let text = this.GLOBAL.ObjToString(data);
        let that = this;
        console.log("sendText(" + handType + "):" + text);
        // console.log("gameOrNot:" + this.GLOBAL.gameOrNot);
        // 判断是否为游戏socket
        var hand = null;
        if (handType == 'game') {
            hand = this.GLOBAL.gameSocketHand;
        } else {
            hand = this.GLOBAL.socketHand;
        }

        // 判断游戏socket是否正常
        if (hand && hand.readyState == 1) {
            if (callback) {
                // 存在回掉函数，设置回掉函数
                console.log('（game）设置发送数据回掉函数');
                senddataCallback = callback;
                hascallback = true;
                if (callbackFlag) {
                    senddataFlag = callbackFlag;
                }
            }
            // 发送游戏数据
            hand.send(text);
            senddataTimes = 0;
        } else {
            if (senddataTimes < 5) {
                senddataTimes++;
                if (handType == 'game') {
                    this.checkGameSocket(function () {
                        that.senddata({ data: data, callback: callback, callbackFlag: callbackFlag, handType: handType });
                    });
                } else {
                    this.checkSocket(function () {
                        that.senddata({ data: data, callback: callback, callbackFlag: callbackFlag, handType: handType });
                    });
                }
            } else {
                // senddataTimes = 0;
                // 游戏socket不正常，输出提示
                this.$messagebox.alert("网络堵塞！");
                // 调用回掉函数，并且传参false
                // if (callback) {
                //     callback(false);
                // }
            }
        }
    }
    /**
     * 关闭socket
     */
    Vue.prototype.closeSocket = function () {
        console.log("closing Socket");
        if (this.GLOBAL.socketHand) {
            var data = {
                send_id: this.GLOBAL.userInfo.id,
                msg: 'I am leaving',
                type: 'close',
                con_id: this.GLOBAL.connectionId,
            };
            this.senddata({ data: data, handType: 'user' });
            this.GLOBAL.socketHand.close();
            this.GLOBAL.socketHand = null;
            // this.GLOBAL.socketStatus = false;
        }
        if (this.GLOBAL.gameSocketHand) {
            this.GLOBAL.gameSocketHand.close();
            this.GLOBAL.gameSocketHand = null;
        }
    }
    /**
     * 创建websocket
     */
    Vue.prototype.createSocket = function (gameSocket = true, opencallback = null) {
        // 创建websocket
        var that = this;
        if (!this.GLOBAL.socketHand || this.GLOBAL.socketHand.readyState == 3) {
            console.log("creating user socket...");
            // 标志当前是否为游戏soket链接
            this.GLOBAL.socketHand = new WebSocket(this.GLOBAL.socketHost);
            this.GLOBAL.socketHand.onopen = function (evt) {
                // 请求app信息
                let data = {
                    type: "getAppInfo",
                    send_id: "",
                    con_id: that.GLOBAL.connectionId
                };
                that.senddata({ data: data, handType: 'user' });
                that.getUserInfo(gameSocket ? null : opencallback);
            }
        } else {
            console.log('user socket listening...');
            that.getUserInfo(gameSocket ? null : opencallback);
            if (sysNewsCallback) {
                sysNewsCallback();
                sysNewsCallback = null;
            }
        }
        if (gameSocket) {
            // 标志当前是否为游戏soket链接
            this.GLOBAL.gameOrNot = true;
            if (!this.GLOBAL.gameSocketHand || this.GLOBAL.gameSocketHand.readyState == 3) {
                console.log("creating game socket....");
                this.GLOBAL.gameSocketHand = new WebSocket(this.GLOBAL.gameSocketHost);
                this.GLOBAL.gameSocketHand.onopen = function (evt) {
                    // 链接成功时执行的回掉函数
                    if (opencallback) {
                        opencallback();
                    }
                }
            } else {
                console.log('game socket listening...');
                if (opencallback) {
                    opencallback();
                }
            }
        }
    }

    // }
    /**
     * 监听消息
     * @param {*} onmessagecallback 
     */
    Vue.prototype.onmessage = function (onmessagecallback) {
        let that = this;
        // 监听用户websockt，接收消息
        this.GLOBAL.socketHand.onmessage = function (evt) {
            console.log("user socket onmessage is opening...");
            dealWithData(evt, that.GLOBAL.gameOrNot ? null : onmessagecallback, that);
        }
        if (this.GLOBAL.gameOrNot) {
            // 游戏socket接收消息处理
            this.GLOBAL.gameSocketHand.onmessage = function (evt) {
                console.log('game socket onmessage is opening...');
                if (onmessagecallback) {
                    onmessagecallback(evt.data);
                }
            }
        }

    }

    /**
     * 处理接收到的消息
     * @param {*} evt 
     * @param {*} callback 
     */
    function dealWithData(evt, callback, that) {
        var data = '';
        if (evt.data) {
            // 解析json字符串
            data = JSON.parse(evt.data);
            console.log("reciveData => {");
            // console.log(evt.data);
            console.log(data);
            console.log("}");
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
                        msg: 'I accept the heart',
                        type: 'heart',
                        rType: 1,
                        fType: 0 // 和user_message表的status对应，0表示没任何意义
                    }
                    that.senddata({ data: data, handType: 'user' });
                    break;
                // case "searchFriendsResult"://查找好友
                //     break;
                // case "ResoneAddFriendRequest"://响应添加好友事件
                //     break;
                case "addFriendRequest"://接收添加好友申请
                    addFriendRequest(data, that);
                    break;
                case "onlineNotice": //上线、下线提醒
                    onlineNotice(data, that);
                    break;
                case "deleteFriendNotice": //接收删除好友提醒
                    deleteFriendNotice(data, that);
                    break;
                case "PassFriendRequest"://接收好友申请通过提醒
                    PassFriendRequest(data, that);
                    break;
                case "RefuseFriendRequest"://接受拒绝好友申请的提醒
                    RefuseFriendRequest(data, that);
                    break;
                case "text":
                    acceptMessage(data, that);
                    break;
                case "appInfoAndSysNews":
                    appInfoAndSysNews(data, that);
                    break;
                case "redbagRecivedNotice":
                    redbagRecivedNotice(data, that);
                    break;
                case "recivedTransferNotice":
                    recivedTransferNotice(data, that);
                    break;
                case "radioText":
                    that.radioText(data);
                    break;
                case "reciveStangerMessageChange":
                    reciveStangerMessageChange(data, that);
                    break;
                case "giveBackRedbag":
                    giveBackRedbag(data, that);
                    break;
                case "changeRedbagStatus":
                    changeRedbagStatus(data, that);
                    break;
                case "ChatRoomMsg":
                    ChatRoomMsg(data, that);
                    break;
                case "giveBackTransferNotice":
                    giveBackTransferNotice(data, that);
                    break;
                case "updateUserGold":
                    updateUserGold(data, that);
                    break;
                default:
            }

        }
        // 判断是否存在接收数据的回掉函数
        if (callback) {
            callback(evt.data);
        }
        let senddataParam = data ? data : false;
        if (hascallback && (!senddataFlag || (senddataFlag && senddataParam && senddataParam['type'] == senddataFlag))) {
            // 调用发送数据的回掉函数
            senddataCallback(senddataParam, that);
            hascallback = false;
            senddataFlag = '';
        }
    }
    /**
     * 更新用户金币信息
     */
    function updateUserGold(data, that) {
        that.$forceUpdate();
        if (data.gold >= 0) {
            that.GLOBAL.userInfo.gold = data.gold;
            localStorage.setItem('bc_userInfo', that.GLOBAL.ObjToString(that.GLOBAL.userInfo));
        }
        if (that.GLOBAL.msgList.systemNotice) {
            that.$set(that.GLOBAL.msgList.systemNotice, that.GLOBAL.getObjLenght(that.GLOBAL.msgList.systemNotice), data.noticeMsg);
        } else {
            that.$set(that.GLOBAL.msgList, 'systemNotice', {});
            that.$set(that.GLOBAL.msgList.systemNotice, 0, data.noticeMsg);
        }
        that.GLOBAL.msgLength++;
    }
    /**
     * 转账过期退还通知
     */
    function giveBackTransferNotice(data, that) {
        that.$forceUpdate();
        if (data.gold) {
            that.GLOBAL.userInfo.gold = data.gold;
            localStorage.setItem('bc_userInfo', that.GLOBAL.ObjToString(that.GLOBAL.userInfo));
        }
        if(data.systemNotice){
            // data.systemNotice = JSON.parse(data.systemNotice);
            if(that.GLOBAL.msgList.systemNotice){
                that.$set(that.GLOBAL.msgList.systemNotice, that.GLOBAL.getObjLenght(that.GLOBAL.msgList.systemNotice), data.systemNotice);
            }else{
                that.$set(that.GLOBAL.msgList, 'systemNotice', {});
                that.$set(that.GLOBAL.msgList.systemNotice, 0, data.systemNotice);
            }
            that.GLOBAL.msgLength++;
        }
        if (data.fid) {
            if (that.GLOBAL.msgList[data.fid]) {
                that.$forceUpdate();
                var reg = '^(false:|true:|)\\(' + data.tid + '\\).*';
                var regE = new RegExp(reg);
                console.log(regE);
                for (var index in that.GLOBAL.msgList[data.fid]) {
                    console.log(that.GLOBAL.msgList[data.fid][index].content)
                    if (regE.test(that.GLOBAL.msgList[data.fid][index].content)) {
                        var result = regE.exec(that.GLOBAL.msgList[data.fid][index].content);
                        console.log("result => {");
                        console.log(result);
                        console.log("}");
                        if (!result[1]) {
                            that.GLOBAL.msgList[data.fid][index].content = 'true:' + that.GLOBAL.msgList[data.fid][index].content;
                        }
                        break;
                    } else {

                    }
                }
            }
        }
    }

    /**
     * 群聊消息
     * @param {*} data 
     * @param {*} that 
     */
    function ChatRoomMsg(data, that) {
        // let len = Object.keys(that.GLOBAL.chatRoomMsgList).length;
        var len = that.GLOBAL.getObjLenght(that.GLOBAL.chatRoomMsgList);
        that.$set(that.GLOBAL.chatRoomMsgList, len, {});
        that.setGlobalAttribute(
            that.GLOBAL.chatRoomMsgList[len],
            data.msg
        );
        that.$forceUpdate();
    }

    /**
     * 接收修改红包消息状态的消息
     * @param {*} data 
     * @param {*} that 
     */
    function changeRedbagStatus(data, that) {
        if (data.code == 1) {
            // 个人红包消息
            var reg = '^(false:|true:|)\\(' + data.red_id + '\\).*';
            console.log(reg);
            var regE = new RegExp(reg);
            for (var index in that.GLOBAL.msgList[data.send_id]) {
                if (regE.test(that.GLOBAL.msgList[data.send_id][index].content)) {
                    var result = regE.exec(that.GLOBAL.msgList[data.send_id][index].content);
                    if (!result[1]) {
                        that.GLOBAL.msgList[data.send_id][index].content = 'true:' + that.GLOBAL.msgList[data.send_id][index].content;
                    }
                    break;
                }
            }
        } else {
            // 群发红包消息
            var reg = '^(false:|true:|)\\[.*\\]\\(' + data.red_id + '\\).*';
            var regE = new RegExp(reg);
            for (var index in that.GLOBAL.chatRoomMsgList) {
                console.log(that.GLOBAL.chatRoomMsgList[index].content);
                if (regE.test(that.GLOBAL.chatRoomMsgList[index].content)) {
                    var result = regE.exec(that.GLOBAL.chatRoomMsgList[index].content);
                    if (!result[1]) {
                        that.GLOBAL.chatRoomMsgList[index].content = 'true:' + that.GLOBAL.chatRoomMsgList[index].content;
                    }
                    break;
                }
            }
        }
        that.$forceUpdate();
    }

    /**
     * 接受红包退还消息
     * @param {*} data 
     * @param {*} that 
     */
    function giveBackRedbag(data, that) {
        // 修改用户金币信息
        that.GLOBAL.userInfo.gold = data.gold;
        localStorage.setItem('bc_userInfo', that.GLOBAL.ObjToString(that.GLOBAL.userInfo));
        // 修改红包消息的状态
        if (data.fid) {
            // 个人红包消息
            var reg = '^(false:|true:|)\\(' + data.red_id + '\\).*';
            var regE = new RegExp(reg);
            for (var index in that.GLOBAL.msgList[data.fid]) {
                if (regE.test(that.GLOBAL.msgList[data.fid][index].content)) {
                    var result = regE.exec(that.GLOBAL.msgList[data.fid][index].content);
                    if (!result[1]) {
                        that.GLOBAL.msgList[data.fid][index].content = 'true:' + that.GLOBAL.msgList[data.fid][index].content;
                    }
                    break;
                }
            }
        } else {
            // 群发红包消息
            var reg = '^(false:|true:|)\\[.*\\]|\\(' + data.red_id + '\\).*';
            var regE = new RegExp(reg);
            for (var index in that.GLOBAL.chatRoomMsgList) {
                if (regE.test(that.GLOBAL.chatRoomMsgList[index].content)) {
                    var result = regE.exec(that.GLOBAL.chatRoomMsgList[index].content);
                    if (!result[1]) {
                        that.GLOBAL.chatRoomMsgList[index].content = 'true:' + that.GLOBAL.chatRoomMsgList[index].content;
                    }
                    break;
                }
            }
        }
        that.$forceUpdate();
    }
    /**
     * 陌生人队列中的好友修改是否接受陌生人信息配置是发送过的配置状态
     * @param {*} data 
     * @param {*} that 
     */
    function reciveStangerMessageChange(data, that) {
        if (that.GLOBAL.friendLists.strange[data.stranger_id]) {
            that.$set(that.GLOBAL.friendLists.strange[data.stranger_id], 'stmessage', data.stmessage);
        }
    }
    /**
     * 接受喇叭广告信息
     * @param {*} data 
     * @param {*} that 
     */
    Vue.prototype.radioText = function (data) {
        let radioText = new Object();
        this.$set(radioText, 'text', data.content);
        this.$set(radioText, 'send_user', data.send_user);
        this.$set(radioText, 'send_id', data.send_id);
        this.GLOBAL.radioTextList.push(radioText);
        this.$forceUpdate();
    }
    /**
     * 收取转账提醒
     * @param {*} data 
     * @param {*} that 
     */
    function recivedTransferNotice(data, that) {
        let list = that.GLOBAL.msgList[data.friend_id];
        let replace_str = '(@transfer#' + data.transfer_id + '-' + data.transfer_gold + '#transfer@)'
        for (var index in list) {
            if (list[index].content.indexOf(replace_str) != -1) {
                that.GLOBAL.msgList[data.friend_id][index].content = 'false:' + list[index].content;
                that.$forceUpdate();
                console.log("changeTransferStatus => {");
                console.log("index:" + index);
                console.log("content:" + that.GLOBAL.msgList[data.friend_id][index].content);
                console.log("}")
                break;
            } else {
                console.log("origilTransferStatus => {");
                console.log("index:" + index);
                console.log("content:" + list[index].content);
                console.log("}")
            }
        }
    }
    /**
     * 收取个人红包提醒
     * @param {*} data 
     * @param {*} that 
     */
    function redbagRecivedNotice(data, that) {
        console.log("redbagSrc_before => {");
        console.log('red_id:' + data.red_id);
        console.log(that.GLOBAL.redbagsrc);
        console.log("}");
        let rege = '(@redbag' + data.red_id + 'redbag@)';
        for (var index in that.GLOBAL.msgList[data.friend_id]) {
            if (that.GLOBAL.msgList[data.friend_id][index].content.indexOf(rege) != -1) {
                that.GLOBAL.msgList[data.friend_id][index].content = 'false:' + that.GLOBAL.msgList[data.friend_id][index].content;
                console.log('content => {');
                console.log(that.GLOBAL.msgList[data.friend_id][index]);
                console.log('}');
                that.$forceUpdate();
                break;
            }
        }
        console.log("redbagSrc_after => {");
        console.log('red_id:' + data.red_id);
        console.log(that.GLOBAL.redbagsrc);
        console.log("}");
    }
    /**
     * 更新app信息和系统消息
     * @param {*} data 
     * @param {*} that 
     */
    function appInfoAndSysNews(data, that) {
        if (data.appInfo != that.GLOBAL.appInfo) {
            that.GLOBAL.appInfo = data.appInfo;
            localStorage.setItem("bc_appInfo", that.GLOBAL.ObjToString(that.GLOBAL.appInfo));
        }
        that.GLOBAL.sysNews = data.sysNews;
        that.$forceUpdate();
        if (sysNewsCallback) {
            sysNewsCallback();
            sysNewsCallback = null;
        }
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
                        that.setGlobalAttribute(that.GLOBAL.friendLists.strange[user.id], user);
                    }
                } else {
                    // 陌生人队列为空，更新陌生人队列
                    that.$set(that.GLOBAL.friendLists, 'strange', {});
                    that.$set(that.GLOBAL.friendLists.strange, user.id, {});
                    that.setGlobalAttribute(that.GLOBAL.friendLists.strange[user.id], user);
                }
            }
        } else {
            msg = data.data;
        }

        // 未读消息数加1
        // if (that.GLOBAL.msgLength) {
        that.GLOBAL.msgLength++;
        console.log('msgLenght=' + that.GLOBAL.msgLength);
        // } else {
        // that.$set(that.GLOBAL, 'msgLength', 1);
        // that.GLOBAL.msgLength = 1;
        // }
        // 好友未读消息数加1
        if (that.GLOBAL.friendMsgNum[msg.send_id]) {
            that.GLOBAL.friendMsgNum[msg.send_id]++;
        } else {
            that.$set(that.GLOBAL.friendMsgNum, msg.send_id, 1);
        }
        // 更新消息队列
        let i = 0;
        let newList = {};
        console.log(that.GLOBAL.msgList[msg.send_id]);
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
            // let newMsg = [msg];
            // that.GLOBAL.msgList[msg.send_id].concat(newMsg);
        } else {
            // 不存在当前用户的消息队列
            that.$set(that.GLOBAL.msgList, msg.send_id, {});
            newList[i] = msg;
            // that.setGlobalAttribute(that.GLOBAL.msgList[msg.send_id], newList);
        }
        that.setGlobalAttribute(that.GLOBAL.msgList[msg.send_id], newList);

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
                newList[friendLists.system[index].id + "@" + i] = friendLists.system[index];
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
        that.setGlobalAttribute(that.GLOBAL.friendLists.friends[data.fInfo.id], data.fInfo);
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
        }
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
        // if (data.gold >= 0) {
        that.GLOBAL.userInfo.gold = data.gold;
        localStorage.setItem('bc_userInfo', that.GLOBAL.ObjToString(that.GLOBAL.userInfo));
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
            if (index == 'system') {
                // 系统消息
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
            console.log(that.GLOBAL.friendInfo);
            if (that.GLOBAL.friendInfo.status == 2) {
                that.GLOBAL.friendInfo.info.connectionID = that.GLOBAL.friendLists['strange'][that.GLOBAL.friendInfo.info.id].connectionID;
            } else {
                that.GLOBAL.friendInfo.info.connectionID = that.GLOBAL.friendLists['friends'][that.GLOBAL.friendInfo.info.id].connectionID;
            }
        }

        if (handlecallback) {
            console.log("ffffffffffffffffffffffffffffffffffffffffffffffffff");
            handlecallback();
            handlecallback = null;
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
            that.GLOBAL.friendLists[data.fType][data.index].connectionID = data.connectionID;
            if (that.GLOBAL.friendInfo.info && that.GLOBAL.friendInfo.info.id == data.index) {
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
            console.log('add friends success notice!');
            // 更新好友列表
            that.$set(that.GLOBAL.friendLists.friends, data.fInfo.id, []);
            that.setGlobalAttribute(that.GLOBAL.friendLists.friends[data.fInfo.id], data.fInfo);
            // 更新消息列表
            that.$set(that.GLOBAL.msgList, data.fInfo.id, []);
            that.$set(that.GLOBAL.msgList[data.fInfo.id], 0, []);
            that.setGlobalAttribute(that.GLOBAL.msgList[data.fInfo.id][0], data.msg);
            // that.GLOBAL.msgList[data.fInfo.id].push(data.msg);
            // 好友未读消息初始化为1
            that.$set(that.GLOBAL.friendMsgNum, data.fInfo.id, 1);
        } else {
            console.log('add friends request notice!');
            let i = 1;
            let friendLists = that.GLOBAL.friendLists;
            that.GLOBAL.friendLists = {};
            let newSystem = {};
            newSystem[data.msg.id + "@" + 0] = data.msg;
            for (var index in friendLists.system) {
                if (friendLists.system[index]) {
                    newSystem[friendLists.system[index].id + "@" + i] = friendLists.system[index];
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
                that.$set(that.GLOBAL.friendMsgNum, 'system', 1);
            }
            console.log(that.GLOBAL.friendMsgNum.system);
        }
        // 未读消息总数加1
        if (that.GLOBAL.msgLength) {
            that.GLOBAL.msgLength += 1;
        } else {
            that.$set(that.GLOBAL, 'msgLength', 1);
        }
        console.log(that.GLOBAL.msgLength);
        that.$forceUpdate();
    }

    /**
     * 发送消息时更新消息列表
     * @param {*} content 
     */
    Vue.prototype.updateMsgList = function (content) {
        this.$forceUpdate();
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
        if (!this.GLOBAL.msgList[text.get_id]) {
            this.$set(this.GLOBAL.msgList, text.get_id, {});
        }
        var len = this.GLOBAL.getObjLenght(this.GLOBAL.msgList[text.get_id]);
        this.$set(this.GLOBAL.msgList[text.get_id], len, text);
        console.log(this.GLOBAL.msgList[text.get_id]);

        // console.log(this.GLOBAL.use);
        // let newList = {};
        // let i = 0;
        // // 判断是否存在当前消息队列
        // if (this.GLOBAL.msgList[text.get_id]) {
        //     let msgList = this.GLOBAL.msgList[text.get_id];
        //     // 初始化当前消息队列
        //     this.GLOBAL.msgList[text.get_id] = {};
        //     for (var index in msgList) {
        //         if (msgList[index]) {
        //             newList[i] = msgList[index];
        //             i++;
        //         }
        //     }
        //     newList[i] = text;
        // } else {
        //     newList[i] = text;
        //     this.$set(this.GLOBAL.msgList, text.get_id, {});
        // }
        // console.log('----------------------------------------------');
        // console.log(newList);
        // console.log('----------------------------------------------');
        // // 动态加载到消息队列
        // this.setGlobalAttribute(this.GLOBAL.msgList[text.get_id], newList);

        return text.time;
    }

    /**
     * 匀速滑动
     */
    Vue.prototype.mytransition = function ({
        elementId = null, //滚动元素的id
        transW = "linear", //滚动的方式，默认匀速
        speed = 100, // 控制滚动的速度
        delayT = 500, //开始滚动的时间，单位ms
        beforeCallback = null, // 滚动前的回调函数
        middleCallback = null, // 滚动元素最有一个字或字符(滚动元素的结束标签)出现后的回调函数
        afterCallback = null, // 滚动元素被隐藏后的回调函数
        beforeClass = null, // 滚动前的自定义的类名，不填写则默认类名为：elementId+‘-before’
        beginClass = null // 滚动开始的自定义类名，不填则默认类名为：elementID+‘-begin’
    }) {
        this.$nextTick(() => {
            // 获取滚动元素对象
            var obj = document.querySelector("#" + elementId);
            var objWidth = obj.offsetWidth;
            var parentObjWidth = obj.parentNode.offsetWidth;
            var middleFlag = true;
            var afterFlag = true;
            if (middleCallback) {
                // 添加滚动完成时的监听事件
                obj.addEventListener("transitionend", function () {
                    if (middleFlag) {
                        // 执行回调
                        middleCallback();
                        middleFlag = false;
                    }
                    // 设置继续往下滚动
                    obj.style.transform = "translate(-100%, 0)";
                    // 设置继续往下滚动的时间
                    var t = parentObjWidth / speed;
                    console.log('mt=' + t + "; len=" + parentObjWidth);
                    obj.style.transition = "all " + t + "s " + transW;
                    // console.log(obj.eventList);
                    // console.log(this);
                    obj.removeEventListener('transitionend', this);
                    setTimeout(() => {
                        obj.addEventListener("transitionend", function () {
                            // 去除添加的类名
                            if (beginClass) {
                                obj.classList.remove(beginClass);
                            } else {
                                obj.classList.remove(elementId + "-begin");
                            }
                            // 清除滑动样式
                            if (middleCallback) {
                                obj.style.transition = null;
                                obj.style.transform = null;
                            }
                            // 执行滚动结束回调
                            if (afterCallback && afterFlag) {
                                afterCallback();
                                afterFlag = false;
                            }
                            // obj.removeEventListener('transitionend', this);
                        });
                    }, delayT);
                });
            } else if (afterCallback) {
                // 添加滚动完成时的监听事件
                obj.addEventListener("transitionend", function () {
                    // 去除添加的类名
                    if (beginClass) {
                        obj.classList.remove(beginClass);
                    } else {
                        obj.classList.remove(elementId + "-begin");
                    }
                    // 清除滑动样式
                    if (middleCallback) {
                        obj.style.transition = null;
                        obj.style.transform = null;
                    }
                    // 执行滚动结束回调
                    if (afterCallback && afterFlag) {
                        afterCallback();
                        afterFlag = false;
                    }
                    // obj.removeEventListener('transitionend', this);
                });
            }
            // 添加滚动前的类
            if (beforeClass) {
                obj.classList.add(beforeClass);
            } else {
                obj.classList.add(elementId + "-before");
            }
            // 定时开启滚动
            setTimeout(() => {
                if (beforeCallback) {
                    beforeCallback();
                }
                // 滚动
                if (beginClass) {
                    obj.classList.add(beginClass);
                } else {
                    obj.classList.add(elementId + "-begin");
                }
                if (beforeClass) {
                    obj.classList.remove(beforeClass);
                } else {
                    obj.classList.remove(elementId + "-before");
                }

                if (middleCallback) {
                    var toleft = parentObjWidth - objWidth;
                    var t = objWidth / speed;
                    console.log('t=' + t + "; len=" + Math.abs(objWidth));
                    obj.style.transition = "all " + t + "s " + transW;
                    obj.style.transform = "translate(" + toleft + "px,0)";
                } else {
                    var t = objWidth / speed;
                    obj.style.transition = "all " + t + "s " + transW;
                }
            }, delayT);
        });
    }

    /**
     * 判断某个值是否在数组中
     * @param value 需要判断的值
     * @param arr 需要判断的数组
     */
    Vue.prototype.inArray = function (value, arr) {
        var result = false;
        arr.forEach(element => {
            if (value == element) {
                result = true;
                return false;
            }
        });
        return result;
    }

    /**
     * 上拉或下拉刷新
     * @param elementId 元素id
     * @param callback 上拉或下拉到底后触发的方法
     * @param type up：表示上拉刷新，down：表示下拉刷新，默认为up
     */
    Vue.prototype.refresh = function ({
        elementId = null,
        callback = null,
        type = null
    }) {
        if (!elementId) {
            throw new Error("refresh->param.elementId is null");
        }
        var oldScrollTop = 0;
        var endY = 0;
        var startY = 0;
        var elementObj = document.querySelector("#" + elementId);
        elementObj.addEventListener("touchstart", function (e) {
            var even = e || window.event;
            startY = even.targetTouches[0].pageY;
            oldScrollTop = elementObj.scrollHeight;
        });
        elementObj.addEventListener("touchmove", function (e) {
            var even = e || window.event;
            endY = even.targetTouches[0].pageY;
        });
        elementObj.addEventListener("touchend", function (e) {
            if (type == 'up') {
                if (endY - startY > 0 && elementObj.scrollTop == 0) {
                    if (callback) {
                        callback(elementObj, startY, endY);
                    }
                    elementObj.scrollTop = elementObj.scrollHeight - oldScrollTop;
                }
            }
            else if (type == 'down') {
                // console.log('ssdown');
                // console.log('endY-startY=' + (endY - startY));
                // console.log('scrollTop = ' + elementObj.scrollTop);
                // console.log("scrollHeight = " + elementObj.scrollHeight);
                // console.log('clientHeight = ' + elementObj.clientHeight);
                if (endY - startY < 0 && (elementObj.scrollTop + elementObj.clientHeight) >= elementObj.scrollHeight) {
                    if (callback) {
                        // console.log('callback');
                        callback(elementObj, startY, endY);
                    } else {
                        // console.log('noncallback...');
                    }
                    elementObj.scrollTop = oldScrollTop;
                } else {
                    // console.log('over....');
                }
            }
            else {
                if (callback) {
                    callback(elementObj, startY, endY);
                }
            }
        });

    }

    /**
     * 滚动事件
     * @param elementId 滚动元素id
     * @param scrollingCallback 滚动过程中的id
     * @param scrollEndCallback 滚动结束的回掉
     * @param type 决定什么时候执行scrollEndCallback,默认为null，top：表示滚动到顶部的时候执行scrollEndCallback，bottom：表示滚动到底部执行scrollEndCallback
     */
    Vue.prototype.scroll = function ({
        elementId = null,
        scrollingCallback = null,
        scrollEndCallback = null,
        type = null
    }) {
        var elementObj = document.querySelector('#' + elementId);
        var lastTop = 0;
        var handler = null;
        elementObj.onscroll = function (e) {
            // 判断是否存在滚动过程中的回调函数
            if (scrollingCallback) {
                scrollingCallback();
            }
            if (handler == null) {
                handler = setInterval(function () {
                    if (lastTop == elementObj.scrollTop) {
                        clearInterval(handler);
                        handler = null;
                        if (type == 'top') {
                            // 滚动到顶部执行回掉
                            if (elementObj.scrollTop == 0) {
                                if (scrollEndCallback) {
                                    scrollEndCallback();
                                }
                            }
                        }
                        else if (type == 'bottom') {
                            // 滚动到底部执行回掉
                            if (elementObj.scrollTop + elementObj.clientHeight >= elementObj.scrollHeight) {
                                if (scrollEndCallback) {
                                    scrollEndCallback();
                                }
                            }
                        }
                        else {
                            // 滚动结束执行回掉
                            if (scrollEndCallback) {
                                scrollEndCallback();
                            }
                        }
                    } else {
                        lastTop = elementObj.scrollTop;
                    }
                }, 100);
            }
        };
    }

    /**
     * 检测表情包（或者更多的更新内容）是否更新
     */
    Vue.prototype.checkUpdate = function () {
        var url = this.GLOBAL.Host + '/bcweb/lib/controller/checkUpdate.php';
        var that = this;
        var updatefile = localStorage.getItem('bc_update');
        this.$axios.post(url).then(function (Response) {
            var data = Response.data;
            if (data && that.GLOBAL.ObjToString(data) != updatefile) {
                console.log(data);
                localStorage.setItem('bc_update', that.GLOBAL.ObjToString(data));
                that.GLOBAL.updateFile = data;
            } else {
                console.log("sssffff");
                if (updatefile) {
                    that.GLOBAL.updateFile = JSON.parse(updatefile);
                } else {
                    console.log('voer.....');
                }
            }
        }).catch(function(err) {
            console.log(err);
            if(updateFile){
                console.log('errr.....');
                that.GLOBAL.updateFile = JSON.parse(updatefile);
            }
        });
    }
}
console.log("BASE_JS");
