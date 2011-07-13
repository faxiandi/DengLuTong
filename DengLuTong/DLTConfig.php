<?php
use DengLuTong\lib\Config;
use DengLuTong\Vendor_Msn;
use DengLuTong\Vendor_Sohu;
use DengLuTong\Vendor_TaoBao;
use DengLuTong\Vendor_KaiXin;
use DengLuTong\Vendor_RenRen;
use DengLuTong\Vendor_NetEase;
use DengLuTong\Vendor_Gmail;
use DengLuTong\Vendor_DouBan;
use DengLuTong\Vendor_QQ;
use DengLuTong\Vendor_Sina;
use DengLuTong\Vendor_TianYa;

$config=Config::getInstance();
$config->DB=array(
  'type'=>'Mysql',
  'dbhost'=>'localhost',
  'dbname'=>'test',
  'dbuser'=>'root',
  'dbpass'=>'root',
  'tablename'=>'denglutong_user',
  'charset'=>'utf8',
);



$config->KEYID = array ( 
  
    Vendor_Sina::$site => '' ,
    Vendor_QQ::$site => '' ,
    Vendor_DouBan::$site=>'',
    Vendor_NetEase::$site => '' ,
    Vendor_RenRen::$site => '' ,
    Vendor_KaiXin::$site => '' ,
    Vendor_TaoBao::$site => '' ,
    Vendor_Sohu::$site=>'',
    Vendor_Msn::$site=>'',
    Vendor_TianYa::$site=>'',
    Vendor_Gmail::$site=>TRUE,      //OPENID类网站为TRUE
);
$config->SECID = array ( 
  
    Vendor_Sina::$site => '' ,
    Vendor_QQ::$site => '' ,
    Vendor_DouBan::$site=>'',
    Vendor_NetEase::$site => '' ,
    Vendor_RenRen::$site => '' ,
    Vendor_KaiXin::$site => '' ,
    Vendor_TaoBao::$site => '' ,
    Vendor_Sohu::$site=>'',
    Vendor_Msn::$site=>'',
    Vendor_TianYa::$site=>'',
    Vendor_Gmail::$site=>TRUE,      //OPENID类网站为TRUE
    
);
$config->LOGO = array ( 
  
    Vendor_Sina::$site => 'http://open.sinaimg.cn/wikipic/button/24.png' ,
    Vendor_QQ::$site => 'http://qzonestyle.gtimg.cn/qzone/vas/opensns/res/img/Connect_logo_3.png' ,
    Vendor_DouBan::$site=>'http://img3.douban.com/pics/doubanicon-24-full.png',
    Vendor_Gmail::$site=>'DengLuTong/tpl/logo_gmail_small.gif',
    Vendor_NetEase::$site=>'http://img1.cache.netease.com/cnews/img/wblogostandard/logo3.png',
    Vendor_RenRen::$site=>'http://wiki.dev.renren.com/mediawiki/images/2/2b/%E8%93%9D%E8%89%B2_112X23.png',
    Vendor_KaiXin::$site=>'http://img1.kaixin001.com.cn/i3/platform/login_1.png',
    Vendor_TaoBao::$site=>'http://img01.taobaocdn.com/tps/i1/T1T2RZXf8nXXXXXXXX-140-35.png',
    Vendor_Sohu::$site=>'http://s1.cr.itc.cn/img/i2/t/130.png',
    Vendor_Msn::$site=>'http://col.stb.s-msn.com/i/B7/EB75D45B8948F72EE451223E95A96.gif',
    Vendor_TianYa::$site=>'http://open.tianya.cn/static/wiki/tylogin16.png',
    
);

//客户端文件（如：DLTClient.php）位置，如http://localhost/DLTClient.php
$baseurl='http://'.$_SERVER['SERVER_NAME'].str_replace(basename($_SERVER['PHP_SELF']),'DLTClient.php',$_SERVER['PHP_SELF']);
$config->BaseUrl = $baseurl;
$config->LoginUrl=$baseurl.'?dltact=login&vendor=';                //登录条链接地址
$config->CallBack = $baseurl.'?dltact=callback&vendor=';         //回调地址
$config->SaveUrl = $baseurl.'?dltact=save';                                    //保存用户绑定信息页面地址
$config->Local='Local';                  //默认本地用户处理类
$config->themefile='showBars';  //showMiniBars  样式文件
$config->theme='theme';            //minitheme         样式
