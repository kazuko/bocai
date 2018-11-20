<template>
  <div class="odds-main">
    <!-- 玩法提示 -->
    <div @click="$emit('ruleModalShow')"
         class="odds-right-tips">玩法提示<span class="iconfont icon-htbarrowright02"></span></div>

    <div v-if="betsOdds"
         class="odds-left-tips">赔率: <span class="font-theme">{{betsOdds|filter_toFixed(3)}}</span></div>

    <!-- 广东11选5 -->
    <template v-if="pk10">
      <div v-for="(pk) in pkShouldRender"
           class="odds odds-pk bgc-fff">
        <!-- 玩法的名称 -->
        <div v-if="pkShouldRender.length > 1"
             class="pk-tips fz-12">{{pkName(pk)}}</div>
        <!-- 点击头部 -->
        <div @click="autoSelect($event,pk)"
             class="odds-pk-header flex justify-sb">
          <span v-for='(item,index) in ["全","大","小","单","双","清"]'
                :data-key="item"
                dentHoverclass="hoverclass">{{item}}</span>
        </div>
        <!-- 号码 -->
        <div class="flex pk-item-wrap">
          <label v-for="n in 11"
                 class="showAllNum-item pk-item">
            <input type="checkbox"
                   :value="n"
                   v-model="pkModel[pk]"
                   class="dent-bg-input hidden">
            <div class="bg flex flow-col justify-ct odds-bets">{{n | filter_toTwo}}</div>
          </label>
        </div>
      </div>
    </template>

    <!-- 手动选号 -->
    <div v-if="handy"
         class="odds bgc-fff">
      <textarea v-model="textareaValue"
                @focus="textareaFocus=true"
                @blur="textareaFocus=false"
                :class="textareaFocus?'odds-textarea-focus':''"
                class="odds-textarea"
                placeholder="请手动输入号码">
      </textarea>
      <p class="textarea-tips fontc-9">每一注号码之间请用逗号[,]、空格[ ]、或者 换行 隔开。</p>
    </div>

  </div>
</template>

<script>
export default {
  props: {
    betsObj: [Array,Object],  //需要渲染的对象或者数组
    currentOdds: String, //当前的子玩法 用来切换不同的赔率展示方式
    value: Array, //value 用来接受v-model传进来的值
  },
  data() {
    return{
      /* 数据 */
      textareaValue:'', //手动选号绑定的model
      //v-model
      pkModel: {0:[], 1:[], 2:[], 3:[], 4:[]},
      textareaLength: 0,

      /* 状态 */
      pk10:false, //显示
      handy:false,  //手动选号
      textareaFocus:false,
    }
  },
  computed:{

    //pk10显示
    pkShouldRender(){
      let temp = {
        "sanma-qiansanzhixuan-zx_fushi": [0,1,2], //三码 前三直选 复式
        "erma-qianerzhixuan-zx_fushi":[0,1],  //二码 前二直选 复式
        "dingweidan-dingweidan-dingweidan":[0,1,2,3,4],  //定位胆
      }

      return temp[this.currentOdds] || [0];
    },

    //betsOdds 当前玩法的赔率
    betsOdds(){
      return typeof(this.betsObj) === "object" ? false : this.betsObj;
    }

  },
  methods:{

    //pk10的玩法名称
    pkName(pkindex){
      let pkName = {0:"一",1:"二",2:"三",3:"四",4:"五"};

      return`第${pkName[pkindex]}名`;
    },

    autoSelect(event,pkModelIndex){
      let temp = {
        "全":[1,2,3,4,5,6,7,8,9,10,11],
        "大":[7,8,9,10,11],
        "小":[1,2,3,4,5,6],
        "单":[1,3,5,7,9,11],
        "双":[2,4,6,8,10],
        "清":[]
      }

      let tempKey = event.target.dataset.key;
      let newArray = temp[tempKey];
      this.$set( this.pkModel, pkModelIndex, newArray )
    }
  },
  watch: {
    //value，向外部派发下注的信息
    value(value) {
      this.$emit('input',value);
    },

    //pkModel 定位胆向外部派发信息
    pkModel:{
      handler(pkModel){
        this.value = pkModel;
      },
      deep:true
    },

    //把用户输入的字符 截取成后台需要的格式
    textareaValue(val){
      if(val === ''){
        this.value = [];
        return;
      };
      //  /([0-9]+)(?=\s|,|\n|$)/g 正则匹配 除了0之外 并且以 空格 逗号 换行符 分割的数字
      let tempArray = val.match(/([0-9]+)(?=\s|,|，|\n|$)/g).filter( num => {
        let numLength = 0 < num && num < 12;  //1-10
        return numLength && num;
      });

      let array = Array.from(new Set(tempArray)); //去重

      this.value = array.splice( 0, this.textareaLength );
    },

    //currentOdds 根据传递进来的玩法 切换不同的显示结构
    currentOdds:{
      handler(currentOdds) {
        //手动选号
        let handy = new RegExp(/danshi/);

        this.handy = handy.test(currentOdds);
        this.pk10 = !this.handy;

        if( this.handy ) {
          let temp = [
            [/dx_one$/, 1],  //任选 1中1
            [/sanma|dx_three$/, 3], //三码-单式 任选 3中3
            [/erma|dx_two$/, 2],  //二码-单式  任选 2中2
            [/dx_four$/, 4],  //任选 4中4
            [/dx_five$/, 5],  //任选 5中5
            [/dx_fiveInSix$/, 6],  //任选 6中5
            [/dx_fiveInSeven$/, 7],  //任选 7中5
            [/dx_fiveInEight$/, 8],  //任选 8中5
          ]

          temp.every( arrayLike => {
            if( new RegExp( arrayLike[0] ).test( currentOdds ) ){
              this.textareaLength = arrayLike[1];
              return false
            }else {
              return true
            }
          })
        }

        // 清空注单信息
        if( this.pk10 ){
          this.pkModel = {0:[], 1:[], 2:[], 3:[], 4:[]};
        }else {
          this.value = [];
        }
      },
      immediate: true
    }
  }
}
</script>

<style scoped>
.hoverclass {
  background: rgba(230, 70, 0, 0.1);
}
/* 下注信息展示区 */
.odds-main {
  flex: 1;
  position: relative;
  padding-top: 12vw;
  width: 100%;
  overflow: auto;
}
.odds-right-tips,
.odds-left-tips {
  position: absolute;
  z-index: 1;
  top: 3vw;
  padding: 0 2vw;
  background: #f4f6f9;
  color: #7f8baa;
  line-height: 6vw;
}
.odds-right-tips {
  right: 0;
  border-top-left-radius: 3vw;
  border-bottom-left-radius: 3vw;
}
.odds-right-tips .iconfont {
  font-size: 3.46vw;
}
.odds-left-tips {
  left: 0;
  border-top-right-radius: 3vw;
  border-bottom-right-radius: 3vw;
}
.odds {
  flex: 1;
  flex-wrap: wrap;
  line-height: 1.6;
}
.odds-normal {
  padding-right: 6vw;
}
.odds-item-wrap {
  margin-bottom: 5vw;
}
/*手动选号 */
.odds-textarea {
  position: relative;
  box-sizing: border-box;
  margin: 8px;
  padding: 8px;
  width: calc(100% - 16px);
  height: 57.534vw;
  background: #fdfdfd;
  border: 8px solid #f6f9fc;
  outline: 1px solid #dadee4;
  outline-offset: -8px;
  box-shadow: 0 0 0 1px #dadee4;
  caret-color: #e64600;
  resize: none;
}
.odds-textarea-focus {
  height: auto;
}
.textarea-tips {
  text-align: left;
  text-indent: 8px;
}
.odds-item {
  margin-left: 6vw;
  width: calc(100% / 3 - 6vw);
  height: 14vw;
  box-sizing: border-box;
}
.odds-normal .odds-item {
  margin-bottom: 6vw;
}
.showAllNum-item {
  margin-left: 30px;
  width: 28px;
  height: 28px;
  background: #f7f9fa;
  border-radius: 50%;
  overflow: hidden;
}
.showNumAndOdds-item {
  margin-left: 6vw;
  margin-bottom: 8vw;
  width: 10vw;
  height: 10vw;
}
.showNumAndOdds-item .bg,
.showAllNum-item .bg {
  border-radius: 50%;
}
.showAllNumAndNum {
  line-height: 2;
}
.odds-bets {
  font-size: 14px;
}
.odds-num {
  font-size: 3.74vw;
}
/* 北京pk10 */
.odds-pk {
  position: relative;
  margin: 0 8px 24px 24px;
}
.odds-pk-header {
  padding: 0 8px;
  background: #f6f9fc;
  border: 1px solid #dadee4;
  font-size: 12px;
  line-height: 20px;
  border-radius: 10px;
  overflow: hidden;
}
.odds-pk-header span {
  flex: 1;
}
.odds-pk-header span:first-child {
  position: relative;
  margin-left: -8%;
}
.odds-pk-header span:last-child {
  position: relative;
  margin-right: -8%;
}
.pk-item-wrap {
  flex-wrap: wrap;
}
.pk-item {
  margin-top: 12px;
  margin-left: 34px;
}
.pk-item:nth-child(6n + 1) {
  margin-left: 0;
}
.pk-tips {
  position: absolute;
  left: 0;
  top: 0;
  color: #9aa4bc;
  writing-mode: vertical-rl;
  transform: translate(-1.8em, -5%);
}
.odds-item-title {
  margin-left: 1.5em;
  writing-mode: vertical-rl;
}
/* 背景色 */
.bg {
  width: 100%;
  height: 100%;
  border: 1px solid #c9c9c9;
  border-radius: 4px;
  transition: 0.5s;
}
.bg span {
  transition: 0.5s;
}
.dent-bg-input:checked + .bg {
  background: #e64600;
  border-color: #e64600;
  color: #fff;
}
.dent-bg-input:checked + .bg span {
  color: #fff;
}
</style>
