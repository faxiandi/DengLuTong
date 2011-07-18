<?php
namespace DengLuTong;
use DengLuTong\lib\OAuth\TaoBao\TaoBaoOauth;
class Vendor_TaoBao extends lib\Vendor
{
  static $site='TaoBao';
  static $name='淘宝';
  static $logo='';
  //static $theme='';
  protected $oauth,$client;
  function __construct()
  {
    parent::__construct(self::$site);
    $this->oauth=new TaoBaoOauth($this->appid, $this->secid);
    $this->callback=urlencode($this->callback);
  }
  

  
  /**
   * 获取登录条
   * @param string $url	链接地址
   */
//  static function getBar($theme='',$url='',$vendor='',$name='')
//  {
//    return parent::getBar($url,self::$site,self::$name,$theme);
//  }
  
  function gotoLoginPage()
  {
    $url = "https://oauth.taobao.com/authorize?response_type=code&client_id=".$this->appid."&redirect_uri=".$this->callback."&state=1";
    header('Location: '.$url);
    die;
  }
  
  function getRequestToken()
  {
    
  }
  
  function logout()
  {
    $url='https://oauth.taobao.com/logoff?client_id='.$this->appid.'&redirect_uri='.str_replace('callback','logout',$this->callback);    
    header('Location: '.$url);
    die;
  }

  
  function getAccessToken($args=array())
  {
    if(!$args['code'])return FALSE;
    $params=array(
      'client_id'=>$this->appid,
      'client_secret'=>$this->secid,
      'redirect_uri'=>urldecode($this->callback),
      'code'=>$args['code'],
      'grant_type'=>'authorization_code',
    );
  	$url = "https://oauth.taobao.com/token";
		$file = $this->oauth->curl( $url ,$params);
		$keys = json_decode ( $file ,true);
		if($keys)
		{
  			DengLuTong::setKeys($keys);	
  			return $keys;

		}else{
			return FALSE;
		}
  }


  function showUser($keys=array())
  {
    if(!empty($keys['access_token']))
    {
      $request['action']='taobao.user.get';
      $request['paras']['fields']='avatar,email,user_id,uid,nick,sex,buyer_credit,seller_credit,location,created,last_visit,birthday,type,auto_repost,status,alipay_bind,promoted_type,alipay_account,alipay_no';
      $tb_user=$this->oauth->execute($request,$keys['access_token']);
      if($tb_user && isset($tb_user['user_get_response']['user']['user_id']))
      {
        $_user=$tb_user['user_get_response']['user'];
        $user=array(
          'id'=>$_user['user_id'],
          'name'=>$_user['nick'],
          'screen_name'=>$_user['nick'],
          'desc'=>'',
          'url'=>'',
          'img'=>$_user['avatar'],
          'gender'=>isset($_user['sex'])?$_user['sex']:'',
          'email'=>$_user['email'],
          'location'=>$_user['location']['state'].$_user['location']['city'],
        );
        return $user;
      }
    }
    return FALSE;
    
  }

}
