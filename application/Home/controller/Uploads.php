<?php
namespace app\Home\controller;

use think\Controller;
use think\Db;

class Uploads extends Controller
{
    /**
      * 修改头像
      */
    public function uploadHead()
    {
        header("Access-Control-Allow-Headers: Content-Type,XFILENAME,XFILECATEGORY,XFILESIZE");
        header('Access-Control-Allow-Origin:*');
        $uid = $this->request->param('uid');
        $file = $this->request->file('file');
        if ($file) {
            // 移动到框架应用根目录/uploads/ 目录下
            $savePath = config('template.tpl_replace_string.__PUBLIC__').DS.'heads';
            if (!is_dir($_SERVER['DOCUMENT_ROOT'].$savePath)) {
                mkdir($_SERVER['DOCUMENT_ROOT'].$savePath, 0777, true);
            }
            $info = $file->move($_SERVER['DOCUMENT_ROOT'].$savePath);
            if ($info) {
                //提取旧的图片
                $oldhead = Db::name('user_user')->where(['id'=>$uid])->value('head');
            
                //把图片保存路径更新进数据库
                $head['head'] = $savePath.DS.$info->getSaveName();
                if (Db::name('user_user')->where(['id'=>$uid])->update($head)) {
                    //新头像更新后删除服务器中旧的头像
                    $defaultHead = config('template.tpl_replace_string.__PUBLIC__').DS.'Home'.DS.'static'.DS.'images'.DS.'head'.DS.'defaultHead.png';
                    if ($oldhead != $defaultHead && is_file($_SERVER['DOCUMENT_ROOT'].$oldhead)) {
                        unlink($_SERVER['DOCUMENT_ROOT'].$oldhead);
                    }
                    echo  json_encode([
                        'status'=>true,
                        'head'=>$head['head']
                    ]);
                } else {
                    //入库失败删除上传的图片
                    if (is_file($info->getPathName())) {
                        unlink($info->getPathName());
                    }
                    echo json_encode([
                        'status'=>false,
                        'msg'=>'头像上传失败！'
                    ]);
                }
            }else{
                echo json_encode([
                    'status'=>false,
                    'msg'=>'头像上传失败！'
                ]);
            }
        } else {
            echo json_encode([
                'status'=>false,
                'msg'=>'null'
            ]);
        }
    }

    /**
     * 论坛评论—上传图片
     */
    public function uploadCommentImg()
    {
        header("Access-Control-Allow-Headers: Content-Type,XFILENAME,XFILECATEGORY,XFILESIZE");
        header('Access-Control-Allow-Origin:*');
        $file = $this->request->file('file');
        if ($file) {
            $savePath = config('template.tpl_replace_string.__PUBLIC__').DS.'commentImgs';
            if (!is_dir($_SERVER['DOCUMENT_ROOT'].$savePath)) {
                mkdir($_SERVER['DOCUMENT_ROOT'].$savePath, 0777, true);
            }
            $info = $file->move($_SERVER['DOCUMENT_ROOT'].$savePath);
            if ($info) {
               echo json_encode([
                   'status'=>true,
                   'src'=>$info->getSaveName()
               ]);
            }else{
                echo json_encode([
                    'status'=>false,
                    'msg'=>'图片上传失败！'
                ]);
            }
        } else {
            echo json_encode([
                'status'=>false,
                'msg'=>'null'
            ]);
        }
    }

    /**
     * 上传帖子图片
     */
    public function uploadThemeImg()
    {
        header("Access-Control-Allow-Headers: Content-Type,XFILENAME,XFILECATEGORY,XFILESIZE");
        header('Access-Control-Allow-Origin:*');
        $file = $this->request->file('file');
        if ($file) {
            $savePath = config('template.tpl_replace_string.__PUBLIC__').DS.'themeImgs';
            if (!is_dir($_SERVER['DOCUMENT_ROOT'].$savePath)) {
                mkdir($_SERVER['DOCUMENT_ROOT'].$savePath, 0777, true);
            }
            $info = $file->move($_SERVER['DOCUMENT_ROOT'].$savePath);
            if ($info) {
               echo json_encode([
                   'status'=>true,
                   'src'=>$savePath.DS.$info->getSaveName(),
                   'saveName'=>$info->getSaveName()
               ]);
            }else{
                echo json_encode([
                    'status'=>false,
                    'msg'=>'图片上传失败！'
                ]);
            }
        } else {
            echo json_encode([
                'status'=>false,
                'msg'=>'null'
            ]);
        }
    }
    /**
     * 删除图片
     * param path 图片相对于网站根目录的路径
     */
    public function unlinkFile($path){
        header("Access-Control-Allow-Headers: Content-Type,XFILENAME,XFILECATEGORY,XFILESIZE");
        header('Access-Control-Allow-Origin:*');
        if(is_file($_SERVER['DOCUMENT_ROOT'].$path)){
            if(unlink($_SERVER['DOCUMENT_ROOT'].$path)){
                echo json_encode([
                    'status'=>true,
                ]);
            }else{
                echo json_encode([
                    'status'=>false,
                    'msg'=>'删除失败'
                ]);
            }
        }else{
            echo json_encode([
                'status'=>false,
                'msg'=>'该文件不存在'
            ]);
        }
    }
}

