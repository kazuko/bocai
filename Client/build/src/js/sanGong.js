exports.install = function (Vue, options) {
    // 用户进入牌桌
    Vue.prototype.SanGongEnter = function ( desk ){
       
    }

    //没有玩家下注发牌渲染事件
    Vue.prototype.SanGongDeal = function( deal ){
            for(var index in deal['card']){
            var tmp=deal.player+index;
            console.log("--------new card---------");
            console.log(deal);
            console.log("--------new card end---------");
            // console.log(deal[index][card]);
            // console.log(that.card[tmp]);
            this.SANGONG.card[tmp]=deal['card'][index];
            this[tmp]=deal['card'][index];
            }
    }

    //玩家有押注时的牌组结果渲染
    Vue.prototype.SanGongBet = function( deal ){
        for(var index in deal['card']){
            var tmp=deal.player+index;
            console.log("--------new card---------");
            console.log(deal);
            console.log("--------new card end---------");
            // console.log(deal[index][card]);
            // console.log(that.card[tmp]);
            this.SANGONG.card[tmp]=deal['card'][index];
            this[tmp]=deal['card'][index];
            }
    }

    //玩家有结果时的金币渲染
    Vue.prototype.SanGongGold = function( data ){
        this.remaining = data.result;
        this.GLOBAL.userInfo.gold= data.result; 
    }
    //点击下注增加事件
    Vue.prototype.addJson = function( clickPlayer,clickType ){
        var bet = '{"player":"'+clickPlayer+'",'
                    +'"status":"'+clickType+'"}';
        this.GLOBAL.userInfo.gold = this.GLOBAL.userInfo.gold-this.SANGONG.setting;//减去下注的金额（用户金额存在GLOBAL里面）
        if(this.GLOBAL.userInfo.gold<0){
            console.log("-----remaining is not enough-----");
        }
        else{
            this.SANGONG.bet.push(bet);
            console.log(this.SANGONG.bet);
            return this.GLOBAL.userInfo.gold;
        }
    }
    //点击下注删除事件
    Vue.prototype.delJson = function( clickPlayer,clickType ){
        var bet = '{"player":"'+clickPlayer+'",'
                    +'"status":"'+clickType+'"}';
        var index = this.SANGONG.bet.indexOf(bet);
        this.GLOBAL.userInfo.gold = this.GLOBAL.userInfo.gold+this.SANGONG.setting;//加上下注的金额（用户金额存在GLOBAL里面）
        this.SANGONG.bet.splice(index,1);
        console.log(this.SANGONG.bet);
        return this.GLOBAL.userInfo.gold;
    }
    //下注按钮触发事件
    Vue.prototype.betting = function(){
        //赌注信息格式 
        var deck = '{"type":"play","player":"player","id": 11,"bet":[{"player": "player2","status": "toDeal"},{"player": "player1","status": "win"}],"gold": 10,"allGold": 20,"remaining": 1000}';
        let desk = {
            
        }
        var allGold = this.SANGONG.bet.length*this.SANGONG.setting;
        var info='{"type":"play","player":"player","id":'+this.GLOBAL.userInfo.id+',"gold":'+this.SANGONG.setting+',"bet":['+this.SANGONG.bet+'],"allGold":'+allGold+',"remaining":'+this.GLOBAL.userInfo.gold+'}';
        return info;
    }
}