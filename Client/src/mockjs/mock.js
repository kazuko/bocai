console.log("MOCKJS_MOCK.JS");
const Mock = require('mockjs')
const Random = Mock.Random;

//模拟用户
//login
Mock.mock('/mock/login',{

})

// const type = ['普通区', 'VIP区', '高手区']
//贴吧数据mock
Mock.mock('/mock/forum',function(){
    let forum = [];
    for (let index = 0; index < Random.natural(3,10); index++) {
        let newObj = {
            icon: Random.dataImage('100x100', '分区图片'),
            title: Random.title(2,5),
            count: Random.natural(1000,10000000),
            type: Random.pick(['普通区', 'VIP区', '高手区']),
            news: Random.sentence(6,20),
            newsTime: Random.natural(1,60) 
        }
        forum.push(newObj);
    }
    return {
        forum: forum
    }
})