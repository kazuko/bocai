<?php
namespace app\forum\model;
use think\Model;
/**
* 
*/
class Forum_zone extends Model
{
	// protected $table = "bc_Forum_zone";
	//查询list
	function find() {
		return  $this->query('SELECT t.id,
       t.name,
       t.title,
       t.door,
       t.img,
       t.gold,
       t.pride_percent,
       t.limit_tie,
        (SELECT    
       id
      FROM bc_forum_post tf
         WHERE tf.zone_id = t.id
          ORDER BY `addtime` DESC LIMIT 1)  post_id,
        (SELECT    
       user_id
      FROM bc_forum_post tf
         WHERE tf.zone_id = t.id
          ORDER BY `addtime` DESC LIMIT 1)  user_id,
				(SELECT    
       user_name
      FROM bc_forum_post tf
         WHERE tf.zone_id = t.id
          ORDER BY `addtime` DESC LIMIT 1)  user_name,
       (SELECT    
       title
      FROM bc_forum_post tf
         WHERE tf.zone_id = t.id
          ORDER BY `addtime` DESC LIMIT 1)  post_title,
          (SELECT    
       ADDTIME
      FROM bc_forum_post tf
         WHERE tf.zone_id = t.id
          ORDER BY `addtime` DESC LIMIT 1)  ADDTIME,
           (SELECT    
       COUNT(1)
      FROM bc_forum_post tf
         WHERE tf.zone_id = t.id
          ORDER BY `addtime` DESC)  COUNT
  FROM bc_forum_zone t ');
	}
}