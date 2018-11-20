<?php
namespace app\index\controller;

use app\index\controller;
use think\Db;
use think\facade\Session;
use think\facade\Config;

class Rank extends Common
{
    // 显示勋章列表
    public function index()
    {
        // 获取勋章列表
        $list = DB::name('system_ranks')->order('flag asc,min asc')->select();
        // 勋章分类
        $list1 = $list2 = $list3 = [];
        foreach ($list as $key => $value) {
            if ($value['flag'] == 0) {
                $list1[] = $value;
            } elseif ($value['flag'] == 1) {
                $list2[] = $value;
            } else {
                $list3[] = $value;
            }
        }
        $this->assign([
            'title'=>'等级规划',
            'back_url'=>url('Index/Index/index'),
            'list1'=>$list1,
            'list2'=>$list2,
            'list3'=>$list3,
        ]);
        return $this->fetch();
    }
    // 保存勋章信息
    public function save()
    {
        // 保存勋章图标缩略图
        $file = $this->saveImg();
        if ($file['code']) {
            echo json_encode($file);
            return;
        }
        $param = $this->request->param();
        if (isset($file['srcs']['src'])) {
            $param['src'] = $file['srcs']['src'];
        }
        // 判断是否为更新操作
        if ($param['id']) {
            // 获取原来的勋章图标路径
            $src = DB::name('system_ranks')->where('id', $param['id'])->value('src');
            // 执行更新操作
            if (DB::name('system_ranks')->update($param)) {
                $result = [
                    'code'=>0,
                    'msg'=>'保存成功！',
                    'id'=>''
                ];
                // 更新成功，删除原来的勋章图标
                if (is_file($_SERVER['DOCUMENT_ROOT'].$src)) {
                    unlink($_SERVER['DOCUMENT_ROOT'].$src);
                }
            } else {
                $result = [
                    'code'=>-2,
                    'msg'=>'保存失败！',
                    'id'=>''
                ];
                // 更新失败，删除刚上传的图标
                if (isset($param['src']) && is_file($_SERVER['DOCUMENT_ROOT'].$param['src'])) {
                    unlink($_SERVER['DOCUMENT_ROOT'].$param['src']);
                }
            }
        } else {
            // 执行插入操作
            if ($id = DB::name('system_ranks')->insertGetId($param)) {
                // 保存成功，返回插入的id
                $result = [
                    'code'=>0,
                    'msg'=>'保存成功！',
                    'id'=>$id
                ];
            } else {
                $result = [
                    'code'=>-3,
                    'msg'=>'保存失败！',
                    'id'=>''
                ];
                // 保存失败，删除刚上传的勋章图标
                if (isset($param['src']) && is_file($_SERVER['DOCUMENT_ROOT'].$param['src'])) {
                    unlink($_SERVER['DOCUMENT_ROOT'].$param['src']);
                }
            }
        }
        echo json_encode($result);
    }
    // 保存勋章图标
    public function saveImg()
    {
        $files = $this->request->file();
        // 用户存放上传图标列表
        $stemp = [];
        // 判断是否上传图标
        if (!empty($files)) {
            // 根目录
            $rootPath = $_SERVER['DOCUMENT_ROOT'];
            // 图标路径
            $savePath = Config::get('template.tpl_replace_string.__PUBLIC__').DS.'uploads'.DS.date('Ymd');
            foreach ($files as $key => $file) {
                if (!is_dir($rootPath.$savePath)) {
                    mkdir($rootPath.$savePath, 0777, true);
                }
                // 实例化图片上传处理器
                $image = \think\Image::open($file);
                // 产生一个随机数，减少文件重名的几率
                $n = mt_rand();
                $savePath .= DS.md5('xunzhang').time().$n.'.'.$image->type();
                // 保持图片缩略图
                $image->thumb(100, 100)->save($rootPath.$savePath);
                // 判断是否保存成功
                if (!is_file($rootPath.$savePath)) {
                    // 保存失败，删除刚上传的所有图片
                    foreach ($stemp as $k => $v) {
                        if (is_file($rootPath.$v)) {
                            unlink($rootPath.$v);
                        }
                    }
                    return ['code'=>-1,'msg'=>'下标为'.$key.'的图片保存失败'];
                } else {
                    $stemp[$key] = $savePath;
                }
            }
        }
        return ['code'=>0,'srcs'=>$stemp];
    }

    public function delete()
    {
        // 开启事务
        DB::startTrans();
        try {
            // 获取要删除的勋章id
            $id = $this->request->param('id');
            // 获取勋章保存的路径
            $src = DB::name('system_ranks')->where('id', $id)->value('src');
            // 删除勋章
            DB::name('system_ranks')->where('id', $id)->delete();
            // 删除用户勋章列表中该勋章信息
            DB::name('user_medal')->where('medal_id',$id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            echo json_encode([
                'code'=>-1,
                'msg'=>'删除失败！'
            ]);
            return false;
        }
        if (is_file($_SERVER['DOCUMENT_ROOT'].$src)) {
            unlink($_SERVER['DOCUMENT_ROOT'].$src);
        }
        echo json_encode([
            'code'=>0,
            'msg'=>'删除成功！'
        ]);
    }
}
