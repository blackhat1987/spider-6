<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<?php
set_time_limit(0);
error_reporting(0);
require('helper/phpquery/phpQuery/phpQuery.php');
$username = 'PartyJat';
getFollowers($username);

//获取用户个人信息
function getUserInfo($username){
	$url = 'http://my.csdn.net/'.$username;
	$flag = existNickname($username);
	if ($flag === false) {
		phpQuery::newDocumentFile($url);  
		$nickname = pq('.person-nick-name')->find('span')->text(); 
		$info = pq('.person-detail')->text(); 
		$info = trim($info);
		$info_arr = @explode('|', substr($info, 0, -1));
		$infoStr = $username.'--'.$nickname;
		foreach ($info_arr as $key => $value) {
			# code...
			$infoStr .= '--'.trim($value);
		}
		$infoStr .= PHP_EOL;

		file_put_contents('csdnInfo.txt', $infoStr,FILE_APPEND);
		
	}
}

//获取用户
function getFollowers($username){
	$followers_url = 'http://my.csdn.net/'.$username;//个人信息页
	phpQuery::newDocumentFile($followers_url);
	$peopleUrlArr = pq('.focus')->find('.header a');

	//先把本页面已显示用户信息抓取到
	foreach ($peopleUrlArr as $key => $value) {
		$nickname =  pq($value)->attr('href');
		$flag = existNickname($nickname);

		if (!$flag) {
			$nickArrValid[] = $nickname;//不存在的用户数组 
			getUserInfo($nickname);
		}
		
	}

	if(!empty($nickArrValid)){

		//然后再抓取页面已显示用户
		foreach ($nickArrValid as $key => $value) {
			getFollowers($value);
		}
	}
	

}

//获取关注了
function getFollowees($followeesUrl){
	phpQuery::newDocumentFile('http://www.jokeji.cn/jokehtml/bxnn/201605112306007.htm');
	$jokes = pq('.menu')->find('li');
	foreach ($jokes as $key => $value) {
		echo pq($value)->html();exit;
	}
	
	
}

//判断是否存在该昵称
function existNickname($nickname){
	//文件不存在 或为空则视为无重复
	if (!file_exists('csdnInfo.txt')) {
		return false;
	}

	$info_str = file_get_contents('csdnInfo.txt');

	if($info_str == ''){
		return false;
	}
	return strpos($info_str,$nickname);
}

/**
 * 通过用户名抓取个人中心页面并存储
 * 
 * @param $username str :用户名 flag
 * @return boolean   :成功与否标志
 */
function spiderUser($username)
{
 
  $url_info = 'http://my.csdn.net/sunboy_2050'; //此处cui-xiao-zhuai代表用户ID,可以直接看url获取本人id
  $ch = curl_init($url_info); //初始化会话
  curl_setopt($ch, CURLOPT_HEADER, 0);
  //curl_setopt($ch, CURLOPT_COOKIE, $cookies2); //设置请求COOKIE
  curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  $result = curl_exec($ch);
 
   file_put_contents('2.txt',$result);
   return true;
 }
