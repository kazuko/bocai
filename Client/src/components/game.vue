<script>
// 牌桌信息
const desk={
};
//房间信息
const room  = {
    "multiple":[
        '1', '1', '1', '1'
    ],
    "online_setting":[
        '', '', '', ''
    ],
    "gold_min":[
        '', '', '', ''
    ],
    "gold_max":[
        '', '', '', ''
    ]
};
const card = [
    {"banker0":""},
    {"banker1":""},
    {"banker2":""},
    {"player10":""},
    {"player11":""},
    {"player12":""},
    {"player20":""},
    {"player21":""},
    {"player22":""},
    {"player30":""},
    {"player31":""},
    {"player32":""},
];//没有玩家下注时的发牌
const bet = [];//下注信息
// const socketHand = "";
const setting = 10; //筹码|默认|设置
export default {
    desk,
    // socketHand,
    bet,//下注信息
    setting,
    room,
    card,
}
</script>
