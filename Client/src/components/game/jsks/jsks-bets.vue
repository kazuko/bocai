<template>
  <div class="odds-main">
    <!-- 玩法提示 -->
    <div @click="$emit('ruleModalShow')"
         class="odds-right-tips">玩法提示<span class="iconfont icon-htbarrowright02"></span></div>

    <div class="odds-left-tips">赔率: <span class="font-theme">6.300</span></div>

    <!-- 1-6数字展示 -->
    <div v-if="showNum || showSecondNum"
         class="odds bgc-fff">

      <div class="odds-item-wrap flex justify-sa">
        <span v-if="currentOdds==='twoDiffrent-twoDiffrent-dantuo'"
              class="odds-item-title fontc-6 fz-12">胆码</span>
        <span v-if="currentOdds==='twoSame-twoSameSg-commom'"
              class="odds-item-title fontc-6 fz-12">二同号</span>
        <span v-if="['twoDiffrent-twoDiffrent-commom','threeDiffrent-threeDiffrent-commom'].includes(currentOdds)"
              class="odds-item-title fontc-6 fz-12">号码</span>
        <label v-for="n in showNumShouldRender"
               class="odds-item showAllNum-item">
          <!-- 当只有1行 1-6 数字的时候，直接把选中的号码绑定到value就行，
           showSecondNum 当有 2行 1-6 数字的时候，需要另外处理-->
          <input v-if="showSecondNum"
                 type="radio"
                 :value="n"
                 v-model="showSecondNumFirstValue"
                 class="dent-bg-input hidden">
          <input v-else
                 type="checkbox"
                 :value="n"
                 v-model="value"
                 class="dent-bg-input hidden">
          <div class="bg flex flow-col justify-ct">
            <span class="odds-bets">{{n}}</span>
          </div>
        </label>
      </div>

      <div v-if="showSecondNum"
           class="odds-item-wrap flex justify-sa">
        <span v-if="currentOdds==='twoSame-twoSameSg-commom'"
              class="odds-item-title fontc-6 fz-12">不同号</span>
        <span v-if="currentOdds==='twoDiffrent-twoDiffrent-dantuo'"
              class="odds-item-title fontc-6 fz-12">拖码</span>
        <label v-for="n in 6"
               class="odds-item showAllNum-item">
          <input type="checkbox"
                 :value="n"
                 v-model="showSecondNumSecondValue"
                 class="dent-bg-input hidden">
          <div class="bg flex flow-col justify-ct">
            <span class="odds-bets">{{n}}</span>
          </div>
        </label>
      </div>

    </div>
    <!-- 手动选号 -->
    <div v-if="handy"
         class="odds bgc-fff">
      <textarea v-model="textareaValue"
                class="odds-textarea"
                placeholder="请手动输入号码">
      </textarea>
      <p class="textarea-tips fontc-9">每一注号码之间请用逗号[,]、空格[ ]、或者 换行 隔开。</p>
    </div>
    <!-- 显示 块 号码和赔率 -->
    <div v-if="normal"
         class="odds odds-normal flex bgc-fff">

      <label v-for="(objValue,objKey) in normalShouldRender"
             class="odds-item">
        <!-- 三同号通选 三连号通选 需要全选和反选 -->
        <input v-if="['threeConAll-threeConAllCommom-threeConAllCommom','threeSame-threeSameDb-threeSameDb'].includes(currentOdds)"
               type="checkbox"
               v-model="toggleCheckAll"
               class="dent-bg-input hidden">
        <!-- 三同号-三同号单选  和 二同号-二同号复选 展示的是objKey值 和 绑定到value的值是objValue -->
        <input v-else-if="['threeSame-threeSameSg-threeSameSg','twoSame-twoSameDb-twoSameDb'].includes(currentOdds)"
               type="checkbox"
               :value="objValue"
               v-model="value"
               class="dent-bg-input hidden">
        <!-- 正常情况下 展示的值和绑定到value的是同一个值-->
        <input v-else
               type="checkbox"
               :value="objKey"
               v-model="value"
               class="dent-bg-input hidden">
        <div class="bg flex flow-col justify-ct">
          <span class="odds-bets">{{objKey | keyToCharacter}}</span>
          <span v-if="normalShowOdds"
                class="odds-num font-theme">赔率:{{objValue}}</span>
        </div>
      </label>

    </div>
    <!-- 显示 球 号码和赔率 -->
    <div v-if="showNumAndOdds"
         class="odds flex bgc-fff">

      <label v-for="(objvalue,objkey,objindex) in betsObj"
             class="odds-item showNumAndOdds-item">
        <input type="checkbox"
               :value="objkey"
               v-model="value"
               class="dent-bg-input hidden">
        <div class="bg flex flow-col justify-ct">
          <span class="odds-bets">{{objkey | keyToCharacter}}</span>
        </div>
        <span class="font-theme showAllNumAndNum">{{Number(objvalue).toFixed(2)}}</span>
      </label>

    </div>

  </div>
</template>

<script>
import { jsks as  keyToCharacter } from '@/js/keyToCharacter';

export default {
  props: {
    betsObj: [Array,Object],  //需要渲染的对象或者数组
    currentOdds: String, //当前的子玩法 用来切换不同的赔率展示方式
    activeTab: [String,Number], //6合彩页面 激活tab，配合 type 切换不同的赔率展示方式
    value: Array, //value 用来接受v-model传进来的值
  },
  filters: {
    // 键值转成中文
    keyToCharacter: function(value) {
      let character = '';
      if(value.match(/_/)) {
        value = value.split('_')[1];
        return value;
      }
      for( let key in keyToCharacter){
        if( key === value ) {
          character = keyToCharacter[key]
          break;
        }
      }

      if(!character)character = value;
      return character;
    }
  },
  data() {
    return{
      normal:false, //显示号码和赔率
      handy:false,  //手动选号
      showNum:false,  //显示 1-6 号码
      showSecondNum:false,  //显示二行 1-6 号码
      showNumAndOdds:false, //显示 3-18 号码和赔率
      textareaValue:'', //手动选号绑定的model
      showSecondNumFirstValue:0,  //显示二行 1-6 号码,第一行1-6绑定的v-model值
      showSecondNumSecondValue:[],  //显示二行 1-6 号码，第二行1-6绑定的v-model值
    }
  },
  computed:{
    //normal 应该渲染的数组或对象
    normalShouldRender (){
      let normalShouldRender;
      let {currentOdds,betsObj} = this;
      //二同号-复选
      if (currentOdds === "twoSame-twoSameDb-twoSameDb") normalShouldRender = {'11*':1,'22*':2,'33*':3,'44*':4,'55*':5,'66*':6};
      //三同号-单选
      if (currentOdds === "threeSame-threeSameSg-threeSameSg") normalShouldRender = {'111':1,'222':2,'333':3,'444':4,'555':5,'666':6};
      //三同号-通选
      if (currentOdds === "threeSame-threeSameDb-threeSameDb") normalShouldRender = {111:'111',222:'222',333:'333',444:'444',555:'555',666:'666'};
      //三连号-三连号通选
      if (currentOdds === "threeConAll-threeConAllCommom-threeConAllCommom") normalShouldRender = {123:'123',234:'234',345:'345',456:'456'};
      //如果外面有传递进来的对象，渲染外面的
      return betsObj || normalShouldRender;
    },
    //showNum 应该渲染的数组
    showNumShouldRender (){
      let showNumShouldRender;
      let {currentOdds} = this;
      if(currentOdds === "twoSame-twoSameSg-commom") showNumShouldRender = ['11','22','33','44','55','66'];
      //正常渲染1-6
      return showNumShouldRender || 6;
    },
    //normal 要不要显示赔率
    normalShowOdds(){
      return !["twoSame-twoSameDb-twoSameDb","threeSame-threeSameDb-threeSameDb",
      "threeSame-threeSameSg-threeSameSg","threeConAll-threeConAllCommom-threeConAllCommom"].includes(this.currentOdds);
    },
    toggleCheckAll:{
      //checkbox 没有绑定value时，值是 true 或 false
      get(){
        let checkLength = this.value instanceof Array ? this.value.length : 0;
        let totalLength = this.normalShouldRender ? Object.keys(this.normalShouldRender).length : null;
        return totalLength === checkLength;
      },
      //根据 true 或 false 决定全选还是清空数组
      set(boolean){
        this.value=[];
        if(boolean){
          let normalShouldRender = Object.keys(this.normalShouldRender);
          normalShouldRender.forEach(num => {
            this.value.push(num);
          });
        }
      }
    }
  },
  methods:{
    //twoDiffrent-dantuo 二不同号-胆拖选号 构造成后台需要的数据格式
    twoDiffrentdantuo(){
      if( !this.showSecondNum ) return;
      let {showSecondNumFirstValue,showSecondNumSecondValue} = this;
      let temp =  { first: showSecondNumFirstValue, second: showSecondNumSecondValue };
      this.$emit('input',temp);
    }
  },
  watch: {
    //value，向外部派发下注的信息
    value(value) {
      this.$emit('input',value);
    },
    //把用户输入的字符 截取成后台需要的格式
    textareaValue(val){
      if(val === ''){
        this.value = [];
        return;
      };
      //  /([1-6]+)(?=\s|,|\n|$)/g 正则匹配 除了0之外 并且以 空格 逗号 换行符 分割的数字
      let tempArray = val.match(/([1-6]+)(?=\s|,|，|\n|$)/g).filter( num => {
        let numLength = false;
        let numUnipe = false;
        //twoDiffrent-handy 二不同号 过滤规则
        if( this.currentOdds === "twoSame-twoSameSg-handy" ) {
          // 2位数，并且 个位和十位不等 才匹配
          numLength = 11 < num && num < 66;
          numUnipe = num % 11 !== 0;
        }
        //twoSame-handy 二同号 过滤规则
        if( this.currentOdds === "twoSame-twoSameSg-handy" ) {
          //3位数 并且 其中2个号码相同，第三个不同
          numLength = 111 < num && num < 666;
          if(numLength){
            let numStringArray = num.toString().split('');
            //数组去重之后 如果长度为2，即原数组有2个相同
            let newArry = Array.from(new Set(numStringArray));
            numUnipe = newArry.length === 2;
          }
        }
        //threeDiffrent-handy 三不同号 过滤规则
        if( this.currentOdds === "threeDiffrent-threeDiffrent-handy" ){
          //3位数 并且 三个号码不相同
          numLength = 122 < num && num < 655;
          if(numLength){
            let numStringArray = num.toString().split('');
            let repeat = numStringArray[0] === numStringArray[1] || numStringArray[0] === numStringArray[2];
            numUnipe = !repeat;
          }
        }

        return numLength && numUnipe && num;
      });

      let array = Array.from(new Set(tempArray)); //去重
      this.value = array;
    },
    /**
     * 拖码 和 胆码 不能一样
     * 显示 2行 1-6 号码时，2行绑定的值 不能有一样的
     */
    showSecondNumFirstValue(val){
      //二同号11 和 二不同号1 的匹配规则一样,所以当传入11时 改成1
      if( this.currentOdds === "twoSame-twoSameSg-commom" ) val /= 11;
      let valIndex = this.showSecondNumSecondValue.indexOf(val);
      if( valIndex !== -1) this.showSecondNumSecondValue.splice(valIndex,1);
      this.twoDiffrentdantuo();
    },
    showSecondNumSecondValue(val){
      let newVal = val[val.length-1];
      //二同号11 和 二不同号1 的匹配规则一样,所以当传入1时 改成11
      if( this.currentOdds === "twoSame-twoSameSg-commom" ) newVal *= 11;
      if( newVal == this.showSecondNumFirstValue) this.showSecondNumFirstValue = null;
      this.twoDiffrentdantuo();
    },
    //currentOdds 根据传递进来的玩法 切换不同的显示结构
    currentOdds:{
      handler(currentOdds) {
        //显示块 号码
        let normal = ["twoSame-twoSameDb-twoSameDb","threeSame-threeSameSg-threeSameSg","threeSame-threeSameDb-threeSameDb","threeConAll-threeConAllCommom-threeConAllCommom",
            "sum-sum-size"];
        //手动选号
        let handy =  new RegExp(/handy/);
        //显示 1-6号码
        let showNum = ["threeDiffrent-threeDiffrent-commom","twoDiffrent-twoDiffrent-commom"];
        let showSecondNum = ["twoDiffrent-twoDiffrent-dantuo","twoSame-twoSameSg-commom"];
        //显示块 号码和赔率
        let showNumAndOdds = ["sum-sum-sum"];

        this.normal = normal.includes(currentOdds);
        this.handy = handy.test(currentOdds);
        this.showNum = showNum.includes(currentOdds);
        this.showSecondNum = showSecondNum.includes(currentOdds);
        this.showNumAndOdds = showNumAndOdds.includes(currentOdds);

        //切换玩法时 清空下注信息
        if( this.showSecondNum ) {
          this.showSecondNumFirstValue = 0;
          this.showSecondNumSecondValue = [];
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
.hidden {
  display: none;
}
/* 下注信息展示区 */
.odds-main {
  flex: 1;
  position: relative;
  padding-top: 12vw;
  width: 100%;
}
.odds-main-ovfa {
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
.odds-item-title {
  writing-mode: vertical-rl;
}
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
.textarea-tips {
  text-align: left;
  text-indent: 8px;
}
.odds-same {
  padding-right: 6vw;
}
.odds-same > label {
  margin-bottom: 6vw;
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
  margin-left: 0;
  width: 28px;
  height: 28px;
  background: #f7f9fa;
  border-radius: 50%;
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
  font-size: 4.26vw;
}
.odds-num {
  font-size: 3.74vw;
}
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
}
.dent-bg-input:checked + .bg span {
  color: #fff;
}
</style>
