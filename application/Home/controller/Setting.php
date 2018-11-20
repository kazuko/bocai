<?php
namespace app\Home\controller;

use think\Controller;
use think\Db;
use think\facade\Session;
use think\facade\Request;

class Setting extends Controller
{
    /* setting展示用户的主界面
    ** userdata:用户资料
    ** chtrading:修改用户交易密码
    ** stmessage:陌生消息
    ** broadcast:广播消息
    ** frvalify:好友验证
    */
    //设置界面的展示
    public function index()
    {
        return $this->fetch();
    }

    //用户资料的展示与修改
    public function userdata()
    {

        // $request = Request::param();
        $request = Request::param();
        //修改用户头部
        if ($file = request()->file('file')) {
            self::changehead($file);
            return;
        }
        if ($request) {
            $userid=1789843;
            //查找用户的旧密码，用户密码输入错误不能修改密码，但是可以修改头像，昵称，跟个性标签（要进行敏感字符检测）
            $file = request()->file('image');
            // dump($file->getinfo());
            $name=$file->getinfo();
            $info = $file->move('./uploads', $name['name'], 'true');
            if ($info) {
                // 成功上传后 获取上传信息 并且存进数据库
                $user['head'] = "/bcweb/Uploads/".$info->getSaveName();
                $res = Db::name('user_user')->where(['id'=>$userid])->update($user);
                if ($res) {
                    echo '更新头像成功';
                }
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
            return;
        // $user = Db::name('user_user')->field('password')->where(['id'=>session::get('id')])->find();
            // $user = Db::name('user_user')->field('password')->where(['id'=>1789843])->find();
            //修改头像
        } else {
            $res = Db::table('bc_user_user')->field('username,signature,head')->where(['id'=>1789843])->find();
            // //冷畅json数据渲染
            // echo json_encode($res);

            //我的数组数据渲染
            if ($res) {
                $this->assign('user', $res);
            }
            return $this->fetch();
        }


        //修改密码 昵称 个性签名
        if ($request) {
            //开发时 使用 检测是否有id没有使用 jackie用户
            //把数据保存包临时json文件里
            $str =" ----------------------------userdata\r\n";
            foreach ($request as $key=>$vo) {
                $str .= '('.gettype($vo).')'.$key.":".$vo."\r\n";
            }
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bcweb/bank.json', $str, FILE_APPEND);
            $new['password'] = $request['password'];

            switch ($request['passwordType']) {
                //保存头部：功能移动到上方
                // case 1:
                // self::changehead($new);
                // break;

                //修改用户名
                case 2:
                self::changeusername($new);
                break;

                //修改密码
                case 3: //修改密码
                self::changepassword($new);
                break;

                //修改个性签名
                case 4:
                self::changesignature($new);
                break;
            }
        }
    }
    //修改头像
    public function changehead($file)
    {
        // 移动到框架应用根目录/uploads/ 目录下
        $savePath = config('template.tpl_replace_string.__PUBLIC__').DS.'heads';
        if (!is_dir($_SERVER['DOCUMENT_ROOT'].$savePath)) {
            mkdir($_SERVER['DOCUMENT_ROOT'].$savePath, 0777, true);
        }
        // echo $_SERVER['DOCUMENT_ROOT'].$savePath;die;
        $info = $file->move($_SERVER['DOCUMENT_ROOT'].$savePath);
        // dump($info);die;
        if ($info) {
            $userid = session::get('id')?session::get('id'):11;

            //提取旧的图片
            $old = Db::name('user_user')->field("head")->where(['id'=>$userid])->find();
            
            //把图片保存路径更新进数据库
            $head['head'] = $savePath.DS.$info->getSaveName();
            // $res = Db::name('user_user')->where(['id'=>$userid])->update($head);

            // $userhead = Db::name('user_user')->where(['id'=>$userid])->find();
            if (Db::name('user_user')->where(['id'=>$userid])->update($head)) {
                //新头像更新后删除服务器中旧的头像
                if (is_file($_SERVER['DOCUMENT_ROOT'].$old['head'])) {
                    unlink($_SERVER['DOCUMENT_ROOT'].$old['head']);
                }
                echo $head['head'];
            } else {
                //入库失败删除上传的图片
                // unlink($head['head']);
                if (is_file($_SERVER['DOCUMENT_ROOT'].$savePath)) {
                    unlink($_SERVER['DOCUMENT_ROOT'].$savePath);
                }
                echo '{"code":0}';
            }
        }
    }

    //修改用户密码
    public function changepassword($request)
    {
        if ($request) {
            $res = Db::name('user_user')->where(['id'=>session::get('id')])->update($request);
            if ($res) {
                echo '{"code":1}';
            } else {
                echo '{"code":0}';
            }
            return;
        }
    }

    //修改用户名
    public function changeusername($new)
    {
        // $a['english'] = mb_strlen("dddddddddd");
        // $a['chinese'] = mb_strlen("男男女女女女女女女女");
        if (!empty($new)) {
            //检测用户昵称是否合法
            if (self::checkNewName($new)) {
                return;
            }

            //用户昵称合乎规则存进数据库
            $userid = session::get('id')?session::get('id'):11;
            $res = Db::name('user_user')->where(['id'=>$userid])->update(['username'=>$new['password']]);
            $newname = Db::name('user_user')->field('username')->where(['id'=>$userid])->find();
            if ($res) {
                echo '{"code":0}';
            } else {
                echo '{"code":9999}';
            }
            return;
        } else {
            echo "数据为空";
            return;
        }
    }

    public function checkNewName($new)
    {
        /*检测用户昵称是否符合规则
        *@register_keyword 管理员设置昵称关键字
        *@register_max_length 用户注册限制长度
        */
        $rule = Db::name('system_info')->field('register_keyword,register_max_length')->find();

        preg_match_all('/'.$rule['register_keyword'].'/', $new['password'], $a);
        if (!empty($a[0])) {
            $data = [
                    'code' =>"1"
                ];
        }
        if (isset($data)) {
            echo json_encode($data);
            return 1;
        }
    }

    //修改昵称
    public function changesignature($new)
    {
        if (!empty($new)) {
            //用户个新签名合乎规则存进数据库
            $userid = session::get('id')?session::get('id'):11;
            $res = Db::name('user_user')->where(['id'=>$userid])->update(['username'=>$new['password']]);
            $new = Db::name('user_user')->field('signature')->where(['id'=>$userid])->find();
            if ($res) {
                echo '{"code":1}';
            } else {
                echo '{"code":0}';
            }
            return;
        } else {
            echo "数据为空";
            return;
        }
    }
    // //检测昵称是否合乎规则 **目前展示不需要**
    // 	public function checkNewSignature($new){
    // 		/*检测用户个新签名是否符合规则
    // 		*@register_keyword 用户注册关键字
    // 		*@register_max_length 用户注册限制长度
    // 		*/
    // 		$rule = Db::name('system_info')->field('register_keyword,register_max_length')->find();
    // 		if(mb_strlen($new['password'])>$rule['register_max_length']){
    // 			$data = ['message'=>"用户名过长"];
    // 		}
    // 		else{
    // 			preg_match_all($rule['register_keyword'], $new['password'],$a);
    // 			if(!empty($a[0])){
    // 				$data = [
    // 					'message' =>"昵称含有非法字眼"
    // 				];
    // 			}
    // 		}
    // 		if(isset($data)){
    // 			echo json_encode($data);
    // 			return 1;
    // 		}

    // 	}

    //修改交易密码
    public function trading()
    {
        $request = Request::param();
        if ($request) {
            if (session::has('id')) {
                $str = "-------------------TRADING\r\n";
                foreach ($request as $key=>$vo) {
                    $str .= $key.":".$vo."\r\n";
                }
                file_put_contents($_SERVER['DOCUMENT_ROOT']."/bcweb/bank.json", $str, FILE_APPEND);
                $userId = session::get('id')?session::get('id'):11;
                $user = Db::name('user_user')->field('trading')->where(['id'=>$userId])->find();
                $res = Db::name("user_user")->where(['id'=>$userId])->update($request);
                if ($res) {
                    echo '{"code":1}';
                } else {
                    echo '{"code":0}';
                }
            }
        }
    }

    //修改好友验证，陌生信息接受，广播信息接受
    public function oncommit()
    {
        $request = Request::param();
        $str =" ----------------------------\r\n";
        foreach ($request as $key=>$vo) {
            $str .= '('.gettype($vo).')'.$key.":".$vo."\r\n";
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bcweb/bank.text', $str, FILE_APPEND);
        if ($request) {
            $userid=session::get('id')?session::get('id'):11;
            $res = Db::name('user_user')->where(['id'=>session::get('id')])->update($request);
            if ($res) {
                echo '{"code":1}';
            } else {
                echo '{"code":0}';
            }
            return;
        }
    }
}
