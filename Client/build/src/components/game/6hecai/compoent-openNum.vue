<template>
  <div class="balls flex">
    <div class="balls-main flex">
      <div v-for="n in newArr"
           class="flex flow-col">
        <span :class="getColor(n)"
              class="balls-item">{{n.toString().length === 1?'0'+n:n}}</span>
        <span v-if="showAnimals"
              class="balls-text"> {{n | NumToAnimals | keyToCharacter}} </span>
      </div>
    </div>
    <span class="fontc-9 balls-link">+</span>
    <div class="balls-spec flex">
      <div class="flex flow-col">
        <span :class="getColor(newArrShift)"
              class="balls-spec-item">{{newArrShift.toString().length === 1?'0'+newArrShift:newArrShift}}</span>
        <span v-if="showAnimals"
              class="balls-text">{{newArrShift | NumToAnimals | keyToCharacter}}</span>
      </div>
    </div>
  </div>
</template>

<script>
/**
 * 10-12 宁柏龙
 * 根据传递进来的数组 生成开奖号码的色波和生肖
 */
import { animals, color, keyToCharacter } from "@/js/6hecai-odds";

export default {
  props: {
    arr: Array,
    showAnimals: Boolean,
  },
  filters: {
    // 键值转成中文
    NumToAnimals: function (num) {
      return Object.keys(animals).filter(v => animals[v].includes(num))[0];
    },
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
  },
  computed:{
    newArr() {
      let newArr = [...this.arr];
      newArr.pop();
      return newArr;
    },
    newArrShift() {
      let newArr = [...this.arr];
      return newArr.pop();
    }
  },
  methods:{
    getColor(n){
      return Object.keys(color).filter(v => color[v].includes(n));
    }
  }
}
</script>

<style scoped>
.balls-item,
.balls-spec-item {
  margin: 0 0.5vw;
  width: 6vw;
  height: 6vw;
  background: #e64600;
  font-size: 12px;
  line-height: 6vw;
  text-align: center;
  color: #fff;
  border-radius: 50%;
}
.balls-spec-item {
  background: #008000;
}
.balls-text {
  color: #535353;
}
.balls-link {
  position: relative;
  top: -0.5em;
  margin: 1vw;
}
.red {
  background: #cc0000;
}
.blue {
  background: #0e86e3;
}
.green {
  background: #38be4f;
}
</style>

