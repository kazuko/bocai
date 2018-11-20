<template>
  <div class="contanier">
    <!-- 导航栏 -->
    <mt-header title="档期注单"
               fixed="true">
      <router-link to="/11xuan5"
                   slot="left">
        <mt-button icon="back"></mt-button>
      </router-link>
    </mt-header>

    <div class="bets-counts">
      <p class="fl">注单数：15</p>
      <p class="fl bets-gold">下注金额：<span class="font-theme">15260</span></p>
    </div>

    <table class="dent-table">
      <COLGROUP>
        <col width="16.5%">
        <col width="16.5%">
        <col width="16.5%">
        <col width="16.5%">
        <col width="16.5%">
        <col width="16.5%">
      </COLGROUP>
      <thead>
        <tr>
          <th>注单号</th>
          <th>下注时间</th>
          <th>类别</th>
          <th>赔率</th>
          <th>金额</th>
          <th>可赢金额</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="font-theme">20180907</td>
          <td>18/09/07 18:30</td>
          <td>正码特</td>
          <td class="font-theme">3.15</td>
          <td>1000</td>
          <td class="font-theme">+12022</td>
        </tr>
      </tbody>
    </table>

  </div>
</template>

<script>
export default {
data(){
    return{

    }
  },
  methods:{

  },
  mounted (){
    this.$parent.tabbarShow = false;
  },
}
</script>

<style scoped>
.contanier {
  padding: 10vw 10px 0;
  background: #fff;
  color: #333;
  font-size: 13px;
  font-family: pingfang sc regular;
}
.bets-counts {
  line-height: 44px;
  overflow: hidden;
}
.bets-gold {
  margin-left: 10vw;
}

.dent-table {
  width: 100%;
  border-spacing: 0;
  border-collapse: collapse;
  font-size: 12px;
}
.dent-table th,
.dent-table td {
  border: 1px solid #ddd;
  font-weight: normal;
}
.dent-table th {
  line-height: 28px;
}
.dent-table td {
  height: 44px;
}
</style>
