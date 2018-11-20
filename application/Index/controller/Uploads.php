<?php
namespace app\Index\controller;

/**
 * 
 */
class Uploads
{
	private $save_path;
	private $size;
	private $type;
	private $new_w;
	private $new_h;
	function __construct($save_path=''){
		defined("DS") or define("DS", DIRECTORY_SEPARATOR);
		$this->save_path = '';
		$this->size = 0;
		$this->type = ['jpg','jpeg','png','gif'];
		$this->new_w = 400;
		$this->new_h = 400;
	}
	public function setSavePath($path){
		$this->save_path = $path;
	}
	public function setSize($size){
		$this->size = $size;
	}
	public function setType($type){
		if(is_array($type)){
			$this->type = $type;
		}else if(strpos($type, ',')!==false){
			$this->type = explode(',', $type);
		}else{
			$this->type = [$type];
		}
	}
	public function setNewWH($width,$height){
		$this->new_w = $width;
		$this->new_h = $height;
	}
	public function uploadImg($thum=0){
		if(!empty($_FILES)){
			$sourcePath = [];
			$thumPath = [];
			foreach ($_FILES as $key => $file) {
				if($file&&$file['error']!=4){
					$type = substr($file['type'], (strpos($file['type'], '/')+1));
					if(($this->size && $file['size']>$this->size) || !in_array($type, $this->type) || $file['error']){
						foreach ($sourcePath as $path) {
							if(is_array($path)){
								foreach ($path as $value) {
									if(is_file($this->save_path.DS.$value)){
										unlink($this->save_path.DS.$value);
									}
								}
							}else{
								if(is_file($this->save_path.DS.$path)){
									unlink($this->save_path.DS.$path);
								}
							}
						}
						foreach ($thumPath as $path) {
							if(is_array($path)){
								foreach ($path as $value) {
									if(is_file($this->save_path.DS.$value)){
										unlink($this->save_path.DS.$value);
									}
								}
							}else{
								if(is_file($this->save_path.DS.$path)){
									unlink($this->save_path.DS.$path);
								}
							}
						}
						if($file['error']){
							$result = [
								'code'=>555,
								'msg'=>'图片上传失败。'
							];
						}else{
							$result = [
								'code'=>444,
								'msg'=>'图片不符合要求。'
							];
						}
						return $result;
					}
					switch ($thum) {
						case 0:
						$result = $this->sourceImg($file,$sourcePath,$key);
						if(!$result['code']){
							$sourcePath = $result['savePath'];
						}else{
							return $result;
						}
						break;
						case 1:
						$result = $this->thumImg($file,$thumPath,$key);
						if(!$result['code']){
							$thumPath = $result['savePath'];
						}else{
							return $result;
						}
						break;
						case 2:
						$result = $this->thumImg($file,$thumPath,$key,true);
						if(!$result['code']){
							$thumPath = $result['savePath'];
						}else{
							foreach ($sourcePath as $path) {
								if(is_file($this->save_path.DS.$path)){
									unlink($this->save_path.DS.$path);
								}
							}
							return $result;
						}
						break;

						default:
						return [
							'code'=>303,
							'msg'=>'参数不正确！'
						];
						break;
					}
				}
			}
			if($sourcePath||$thumPath){
				return [
					'code' => 0,
					'source' => $sourcePath,
					'thum' => $thumPath
				];
			}else{
				return [
					'code'=>404,
					'msg'=>'没找到file文件！',
					'source'=>[],
					'thum'=>[]
				];
			}
		}else{
			return [
				'code'=>404,
				'msg'=>'没找到file文件！',
				'source'=>[],
				'thum'=>[]
			];
		}
	}

	public function sourceImg($file,$savePath,$key=''){
		if(!$this->save_path){
			$this->save_path = $_SERVER['DOCUMENT_ROOT'].DS.'uploads';
		}
		$save_dir = date('Ymd');
		$save_name = date('His').'-'.base64_encode(substr($file['name'],0,strrpos($file['name'], '.'))).substr($file['name'], strrpos($file['name'], '.'));
		$this->save_path .= DS.$save_dir;
		if(!is_dir($this->save_path)){
			mkdir($this->save_path,0777,true);
		}
		$this->save_path .= DS.$save_name;
		if(move_uploaded_file($file['tmp_name'],$this->save_path)){
			$savePath[$key] = $save_dir.'/'.$save_name;
			$result = [
				'code'=>0,
				'savePath'=>$savePath
			];
		}else{
			foreach ($savePath as $path) {
				if(is_file($this->save_path.DS.$path)){
					unlink($this->save_path.DS.$path);
				}
			}
			$result = [
				'code'=>222,
				'msg'=>'图片上传失败'
			];
		}
		return $result;
	}
	public function thumImg($file,$savePath,$key='',$copy=false){
		$result = $this->my_thum($file,$copy);
		if($result['code']>0){
			if(isset($savePath)){
				foreach ($savePath as $path) {
					if(is_array($path)){
						foreach ($path as $key => $value) {
							if(is_file($this->save_path.DS.$value)){
								unlink($this->save_path.DS.$value);
							}	
						}
					}else{
						if(is_file($this->save_path.DS.$path)){
							unlink($this->save_path.DS.$path);
						}
					}
				}
			}
		}else{
			$savePath[$key] = $result['save_path'];
			$result = [
				'code'=>0,
				'savePath'=>$savePath
			];
		}
		return $result;
	}
// 生成缩略图
	public function my_thum($file,$copy=false){
		$tmp_name = $file['tmp_name'];
		$name = substr($file['name'], 0, strrpos($file['name'], '.'));
		$suffix = substr($file['name'], strrpos($file['name'], '.'));
		$type = $file['type'];
		if($file['error']){
			return [
				'code'=>505,
				'msg'=>'上传失败'
			];
		}
		// 获取原图片的宽高
		list($src_w,$src_h) = getimagesize($tmp_name);
		// 原图片资源创建方法
		$sourceFun = str_replace('/', 'createfrom', $type);
		// 缩略图展示方法
		$outFun = str_replace('/', NULL, $type);
		// 创建原图片资源
		$source_img = $sourceFun($tmp_name);
		// 求指定宽高和图片原宽高比值
		$rate_w = $this->new_w/$src_w;
		$rate_h = $this->new_h/$src_h;
		// 取最小值
		$rate = $rate_w < $rate_h ? $rate_w : $rate_h;
		// 求出缩略图宽高
		$this->new_w = $src_w * $rate;
		$this->new_h = $src_h * $rate;
		// 判断
		if(function_exists("imagecopyresampled")){
			$new_img = imagecreatetruecolor($this->new_w, $this->new_h);
			imagecopyresampled($new_img, $source_img, 0, 0, 0, 0, $this->new_w, $this->new_h, $src_w, $src_h);
			if($copy){
				$copy_img = imagecreatetruecolor($src_w,$src_h);
				imagecopy($copy_img, $source_img, 0, 0, 0, 0, $src_w, $src_h);
			}
		}else{
			$new_img = imagecreate($this->new_w, $this->new_h);
			imagecopyresized($new_img, $source_img, 0, 0, 0, 0, $this->new_w, $this->new_h, $src_w, $src_h);
			if($copy){
				$copy_img = imagecreate($src_w, $src_h);
				imagecopy($copy_img, $source_img, 0, 0, 0, 0, $src_w, $src_h);
			}
		}
		if($this->save_path){
		// 拼接保存路径
			$file_dir = date('Ymd');
			$this->save_path .= DS.$file_dir;
			if(!is_dir($this->save_path)){
				mkdir($this->save_path,0777,true);
			}
			$file_name = date('His').'-thum-'.base64_encode($name).$suffix;
			$save_path = $this->save_path.DS.$file_name;
		// 保存缩略图
			if($outFun($new_img,$save_path)){
				$return_value = [
					'code'=>0,
					'save_path'=>$file_dir.'/'.$file_name
				];
				if($copy){
					$file_name_c = date("His").'-'.base64_encode($name).$suffix;
					$save_path = $this->save_path.DS.$file_name_c;
					if($outFun($copy_img,$save_path)){
						$return_value =[
							'code' => 0,
							'save_path' => [
								'thum' => $file_dir.'/'.$file_name_c,
								'src' => $file_dir.'/'.$file_name
							]
						];
					}else{
						if(is_file($this->save_path.DS.$file_name)){
							unlink($this->save_path.DS.$file_name);
						}
						$return_value = [
							'code'=>666,
							'msg'=>'原图保存失败。'
						];
					}
					imagedestroy($copy_img);
				}
			}else{
				$return_value = [
					'code'=>333,
					'msg'=>'缩略图生成失败。'
				];
			}
		}else{
			Header("Content-type: image/png");
			$outFun($new_img);
			$return_value = [
				'code'=>-1,
				'save_path'=>''
			];
		}

		imagedestroy($new_img);
		imagedestroy($source_img);
		return $return_value;
	}
}

?>