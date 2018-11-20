<?php
namespace app\Home\controller;

use think\Controller;
use think\Request;
class Ajaxfileupload extends Controller
{

    public function ajaxfileupload(){
        if(isset($_FILES['file'])){
            if ($_FILES["file"]["error"] > 0)
            {
                echo "错误：" . $_FILES["file"]["error"] . "<br>";
            }
            else
            {
                echo "上传文件名: " . $_FILES["file"]["name"] . "<br>";
                echo "文件类型: " . $_FILES["file"]["type"] . "<br>";
                echo "文件大小: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
                echo "文件临时存储的位置: " . $_FILES["file"]["tmp_name"];
            }
        }
        else{
            return $this->fetch();
        }
    }

}
        

