<template>
  <div class="odds-main">
    <!-- 玩法提示 -->
    <div @click="$emit('oddsTips')"
         class="odds-tips">玩法提示<span class="iconfont icon-htbarrowright02"></span></div>

    <!-- 不同的赔率展示 -->
    <div v-if="normal"
         class="odds flex bgc-fff">

      <label v-for="(objValue,objKey) in betsObj"
             class="odds-item">
        <input type="checkbox"
               :value="objKey"
               v-model="value"
               class="dent-bg-input hidden">
        <div class="bg flex flow-col justify-ct">
          <span v-if="getColor"
                :class="getColorFunc(objKey)"
                class="odds-bets">{{objKey | keyToCharacter}}</span>
          <span v-else
                class="odds-bets">{{objKey | keyToCharacter}}</span>
          <span class="odds-num font-theme">赔率:{{objValue}}</span>
        </div>
      </label>

    </div>

    <div v-if="needToShowNum"
         class="odds flex bgc-fff">

      <label v-for="(objValue,key) in betsObj"
             class="odds-item wx-odds-item ">
        <input type="checkbox"
               :value="key"
               v-model="value"
               class="dent-bg-input hidden">
        <div class="bg flex flow-col justify-ct">
          <span class="odds-bets">{{key | keyToCharacter}}</span>
          <span class="odds-num font-theme">赔率:{{objValue}}</span>
          <div v-if="type === 'qmwx'"
               class="wx-num flex">
            <span v-for="n in wx[key]">{{ n.toString().length === 1 ? '0'+n : n }}</span>
          </div>
          <div v-else-if="type === 'shx' || type === 'lqlw' || currentSubOdds === 'hq'"
               class="wx-num flex">
            <span v-for="n in animals[key]">{{ n.toString().length === 1 ? '0'+n : n }}</span>
          </div>
        </div>
      </label>

    </div>

    <div v-if="showAllNum"
         class="odds flex bgc-fff">

      <label v-for="n in 49"
             class="odds-item showAllNum-item">
        <input type="checkbox"
               :value="n"
               v-model="value"
               class="dent-bg-input hidden">
        <div class="bg flex flow-col justify-ct">
          <span class="odds-bets">{{n}}</span>
        </div>
      </label>

    </div>

    <div v-if="showAllNumAndNum"
         class="odds flex bgc-fff">

      <label v-for="(objvalue,objkey,objindex) in betsObj"
             class="odds-item showAllNum-item">
        <input type="checkbox"
               :value="objkey"
               v-model="value"
               class="dent-bg-input hidden">
        <div class="bg flex flow-col justify-ct">
          <span class="odds-bets">{{objindex.toString().length === 1 ? '0'+ objindex : objindex}}</span>
        </div>
        <span class="font-theme showAllNumAndNum">{{objvalue}}</span>
      </label>

    </div>

    <!-- 正码过关 特殊处理 -->
    <template v-if="currentSubOdds == 'zhmgg'">
      <div v-for="(objValue,objKey,objindex) in betsObj"
           class="odds odds-zhmgg flex bgc-fff">

        <div class="zhmgg">正码{{objindex | numToCharacter}}</div>

        <label v-for="(subValue,subKey) in objValue"
               class="odds-item">
          <input type="checkbox"
                 :value="subKey"
                 v-model="value"
                 class="dent-bg-input hidden">
          <div class="bg flex flow-col justify-ct">
            <span class="odds-bets">{{subKey | keyToCharacter}}</span>
            <span class="odds-num font-theme">赔率:{{subValue}}</span>
          </div>
        </label>

      </div>
    </template>
  </div>
</template>

<script>
import { keyToCharacter } from '@/js/6hecai-odds';
import { wx, animals } from "@/js/6hecai-odds";

/**
 * 10-11 宁柏龙
 * 组件用到了外部的 type 和 activeTab属性，耦合度非常高，没办法复用
 * 这个组件的目的是，把复杂的逻辑和结构封装起来，尽量保持6合彩页面的整洁
 */

export default {
  props: {
    betsObj: [Array,Object],  //需要渲染的对象或者数组
    type: String, //组件的 type 用来切换不同的赔率展示方式
    currentSubOdds: String, //当前的子玩法 用来切换不同的赔率展示方式
    activeTab: [String,Number], //6合彩页面 激活tab，配合 type 切换不同的赔率展示方式
    value: Array, //value 用来接受v-model传进来的值

  },
  filters: {
    // 键值转成中文
    keyToCharacter: function (value) {
      let character = '';
      for( let key in keyToCharacter){
        if( key === value ) {
          character = keyToCharacter[key]
          break;
        }
      }
      if(!character)character = value;

      return character;
    },
    // 数字转成中文
    numToCharacter: function (index) {
      let arr =['一','二','三','四','五','六']

      return arr[index];
    }
  },
  data() {
    return{
      wx,
      animals,
      normal:false,
      needToShowNum:false,
      showAllNum:false,
      showAllNumAndNum:false,
      getColor:false,
      hqBetsObj:{}
    }
  },
  computed:{
  },
  methods:{
    // 根据色波的key值的后缀 ,返回不同的颜色类名 , 比如 ssbhb_h 也就是_h返回font_red
    getColorFunc(objKey) {
      const color = {
        h:'font_red',
        l:'font_blue',
        lv:'font_green'
      }
      let key = objKey.split('_')[1];
      return color[key];
    }
  },
  watch: {
    // watch value，向外部派发下注的信息
    value(value) {
      this.$emit('input',value);
    },
    currentSubOdds:{
      handler(currentSubOdds) {
        //显示 名字 赔率
        let normal = ['lm','qm','twsh','zhtwsh','bb','bbb','qsb','ssb','zq','2lw','3lw','4lw','5lw',
          'zh1','zh2','zh3','zh4','zh5','zh6'];
        //显示 名字 赔率 号码
        let needToShowNum =  ['wx','tq','yq','zhq','2lq','3lq','4lq','5lq','hq'];
        //显示 1-49
        let showAllNum = ['zxbzh10bzh','zxbzh11bzh','zxbzh12bzh','zxbzh5bzh','zxbzh6bzh','zxbzh7bzh',
          'zxbzh8bzh','zxbzh9bzh','lmaerqzh','lmaerzht','lmasiqzh','lmasqzh','lmaszher','lmatch',
          'zhy5zh1','zhy6zh1','zhy7zh1','zhy8zh1','zhy9zh1','zhy10zh1'];
        //显示 1-49 赔率
        let showAllNumAndNum = ['zhert','zhlt','zhsit','zhst','zhwt','zhyt','zhm','tm'];

        this.normal = normal.includes(currentSubOdds);
        this.needToShowNum = needToShowNum.includes(currentSubOdds);
        this.showAllNum = showAllNum.includes(currentSubOdds);
        this.showAllNumAndNum = showAllNumAndNum.includes(currentSubOdds);


        //合肖特殊处理
        // if(currentSubOdds === 'hq'){
        //   //拷贝一份待用
        //   let temp = JSON.parse( JSON.stringify( this.betsObj ) );
        //   // this.hqBetsObj = JSON.parse( JSON.stringify( temp ) );
        //   // delete temp['fenlei'];  //删除不需要渲染出来的
        //   // delete temp['fensang']; //删除不需要渲染出来的
        //   this.betsObj = temp.show;
        // }

        //色波 红 绿 蓝 渲染不同的颜色
        if( ['bb','bbb','qsb','ssb'].includes(currentSubOdds) ){
          this.getColor = true;
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
.font_red {
  color: #cc0000;
}
.font_blue {
  color: #0e86e3;
}
.font_green {
  color: #38be4f;
}
/* 下注信息展示区 */
.odds-main {
  flex: 1;
  position: relative;
  overflow: auto;
}
.odds-tips,
.zhmgg {
  position: absolute;
  z-index: 1;
  top: 2vw;
  padding: 0 2vw;
  background: #f4f6f9;
  color: #7f8baa;
  line-height: 6vw;
}
.odds-tips {
  right: 0;
  border-top-left-radius: 3vw;
  border-bottom-left-radius: 3vw;
}
.odds-tips .iconfont {
  font-size: 13px;
}
.odds-zhmgg {
  position: relative;
}
.zhmgg {
  left: 0;
  border-top-right-radius: 3vw;
  border-bottom-right-radius: 3vw;
}
.odds {
  flex: 1;
  flex-wrap: wrap;
  padding-top: 4vw;
  padding-right: 6vw;
  padding-bottom: 6vw;
  line-height: 1.6;
}
.odds-item {
  margin-left: 6vw;
  margin-top: 6vw;
  width: calc(100% / 3 - 6vw);
  height: 14vw;
  border: 1px solid #c9c9c9;
  border-radius: 4px;
  box-sizing: border-box;
}
.showAllNum-item {
  margin-top: 8.3vw;
  margin-left: 8.3vw;
  width: 10vw;
  height: 10vw;
  background: #f7f9fa;
  border-radius: 50%;
}
.showAllNum-item .bg {
  border-radius: 50%;
}
.showAllNumAndNum {
  line-height: 2;
}
.odds-bets {
  font-size: 16px;
}
.odds-num {
  font-size: 14px;
}
.bg {
  width: 100%;
  height: 100%;
  transition: 0.5s;
}
.bg span {
  transition: 0.5s;
}
.dent-bg-input:checked + .bg {
  background: #e64600;
}
.dent-bg-input:checked + .bg span {
  color: #fff;
}
/* 5行 */
.wx-odds-item {
  height: auto;
}
.wx-num {
  flex-wrap: wrap;
  margin: 0 0 2vw 2vw;
  font-size: 12px;
}
.wx-num span {
  margin-right: 0.5em;
}
</style>
