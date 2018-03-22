<?php
namespace framework;
class Verify
{
    //验证码的个数
    protected $number;
    //验证码的类型
    protected $codeType;
    //验证码的宽
    protected $width;
    //验证码的高
    protected $height;
    //图片的类型
    protected $imageType;
    //验证码
    protected $code;
    //图片资源
    protected $imageSrc;

    //初始化成员属性

     public function __construct($number = 4,$codeType = 2,$width = 100,$height = 30,$imageType = 'png')
     {
        $this->number     = $number;
        $this->codeType   = $codeType;
        $this->width      = $width;
        $this->height     = $height;
        $this->imageType  = $imageType;

        //调用生成验证码的函数
        $this->code = $this->getCode();


     }
     public function __get($name)
     {
       if($name == 'code')
       {
        return $this->code;
       }
       return false;
     }
     protected function getCode()
     {
        switch ($this->codeType)
        {
            //纯数字
            case 0:
            $code = $this->getNumberCode();
           // var_dump($code);
            break;
            //字符串
            case 1:
            $code = $this->getCharCode();
            //混合
            break;
            case 2:
            $code = $this->getMaxCode();
            break;
            default:
            exit('不支持的类型');

        }
        return $code;
     }
     protected function getNumberCode()
     {
        $str = join('',range(0,9));

        return substr(str_shuffle($str),0,$this->number);
     }
     protected function getCharCode()
     {
        $str = join('',range('a','z'));

        $str = substr(str_shuffle($str),0,$this->number);

        return $str;
     }
     protected function getMaxCode()
     {
        $str = '';
        for($i = 0;$i<$this->number;$i++)
        {
            $arr = mt_rand(0,2);
            switch($arr)
            {
                case 0:
                $str .= chr(mt_rand(48,57));
                break;
                case 1:
                $str .= chr(mt_rand(65,90));
                break;
                case 2:
                $str .= chr(mt_rand(97,122));
                break;
            }
        }
        return $str;
     }
     //读取验证码
     public function outImage()
     {
        //准备画布
        $this->imageSrc = $this->createImage();

        //准备颜色
        $this->fillBackground();
        //写验证码
        $this->drawCode();
        //花干扰元素
        $this->playDistrub();
        //输出到浏览器
        $this->putImage();

     }
     protected function createImage()
     {
        return imagecreatetruecolor($this->width, $this->height);
     }
     protected function fillBackground()
    {
        imagefill($this->imageSrc,0, 0, $this->lightColor());
    }
    protected function lightColor()
    {
        return imagecolorallocate($this->imageSrc, mt_rand(130,255), mt_rand(130,255), mt_rand(130,255));
    }
    protected function darkColor()
    {
        return imagecolorallocate($this->imageSrc, mt_rand(0,120), mt_rand(0,120), mt_rand(0,120));
    }
    protected function drawCode()
    {
        for($i = 0;$i < $this->number;$i++)
        {
            $yzm = $this->code[$i];
            $width = ceil($this->width/$this->number);
            $x = mt_rand($i * $width + 10,($i +1) * $width -10 );
            $y = mt_rand(0, $this->height - 15);
            imagechar($this->imageSrc , 12, $x, $y, $yzm, $this->darkColor());

        }
    }
    protected function playDistrub()
    {
        //干扰点
       for ($i=0; $i < $this->width * $this->height / 10; $i++)
        {
            $x = mt_rand(0, $this->width);
            $y = mt_rand(0, $this->height);
            imagesetpixel($this->imageSrc, $x, $y, $this->darkColor());
        }
        //干扰线
        for ($i=0;$i<$this->number;$i++)
         {
        imagearc($this->imageSrc , mt_rand(10 , $this->width) , mt_rand(10 , $this->height) , mt_rand(10 , $this->width) , mt_rand(10 , $this->height) , mt_rand(0 , 20) , mt_rand(0 , 270) , $this->darkColor());
         }
    }
    protected function putImage()
    {
        header('Content-Type:image/'.$this->imageType);
        //输出
        $func = 'image'.$this->imageType;
        if(function_exists($func))
       {
           $func($this->imageSrc);
        }else
        {
           exit('不支持的图片格式');
        }
    }
}
 // $code = new Code();
 // $code->outImage();


