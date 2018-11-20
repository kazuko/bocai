<?php
namespace app\Home\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\facade\Cache;
  // index()数据的展示；
class Test extends Controller
{
    public function index()
    {
      $a = Db::name("user_user")->where('id','12')->cache('abc',60)->find();
                $player['data'] = [
                '0'=>[
                    "player" =>'player',
                    "id" => 11, //用户的id
                    "bet"=>[     //用户的赌局
                       '0'=> ["player" =>"player2","status" =>"toDeal"],
                       '1'=> ["player" =>"player1","status" =>"win"]
                    ],
                    "gold" =>10,
                    "allGold"=>20,
                    "remaining"=>1000
                ],
                '1'=>[
                    "player" =>'player',
                    "id" => 12, //用户的id
                    "bet"=>[     //用户的赌局
                       '0'=> ["player" =>"player2","status" =>"toDeal"],
                       '1'=> ["player" =>"player1","status" =>"win"]
                    ],
                    "gold" =>10,
                    "allGold"=>20,
                    "remaining"=>1000
                ],
                '2'=>[
                    "player" =>'player',
                    "id" => 14, //用户的id
                    "bet"=>[     //用户的赌局
                       '0'=> ["player" =>"player2","status" =>"toDeal"],
                       '1'=> ["player" =>"player1","status" =>"win"]
                    ],
                    "gold" =>10,
                    "allGold"=>20,
                    "remaining"=>1000 
                ],
                '3'=>[
                    'player' =>'banker',
                    'id'=>13
                ]
            ];
            // echo json_encode($player['data']);
      return json_encode($player['data']);
      return $this->fetch();
    }
    public function get(){
    	$a = Cache::clear('abc');
    	dump($a);
    }
     public function get1(){
    	$a = Cache::get('abc');
    	dump($a);
    }

}