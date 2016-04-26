<!doctype html>  
<html>  
<head>  


<meta name="viewport" content="width=device-width,initial-scale=1" charset="utf8">  
<meta http-equiv="content-type" content="text/html;charset=utf8">
<title>MOSIGE</title>  
<link rel="stylesheet" href="/tryclass/themes/mo5.min.css" />
<link rel="stylesheet" href="/tryclass/themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile.structure-1.4.5.min.css" />

<script src="/tryclass/js/mobile/jquery-2.1.4.min.js"></script>
<script src="/tryclass/js/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
<script src="/tryclass/js/jquery.validate.min.js"></script>


<script type="text/javascript" src="lib/jquery.raty.min.js"></script>
<script type="text/javascript">
<? 


date_default_timezone_set('PRC'); 
 
if($_SESSION["end_date"]!=NULL  AND ($_SESSION["end_date"]!='0000-00-00 00:00:00') AND strtotime(date('Y-m-d H:i:s'))>strtotime($_SESSION["end_date"]))
	{$_SESSION["overend"] =1;}
	else{$_SESSION["overend"] =0;}
	
 $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
if(strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false ){
//echo " 非微信浏览器禁止访问www.buyerinfo.biz";
//header("location:welcometoMOSIGE.html");
}else{
//echo "微信浏览器允许访问www.buyerinfo.biz";
} 	

/**
  * wechat php test
  */

//define your token
define("TOKEN", "lovemosige");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = "Welcome to wechat world!";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

	
	
?> 
function checkavailable(x,y)
{
	

    if (x<=0) {		
        //text = "Input not valid"+x;
		document.getElementById(y).disabled = true;
    } else {
         
    }
    
 
  
}
function checkdate(overend,count_overmax,isleave,checkbox)
{
	

    if (overend==1||count_overmax==1||isleave==1) {		
        //text = "Input not valid"+x;
		document.getElementById(checkbox).disabled= true;
    }  
    
 
  
}
function checkdisable(dis,checkbox)
{
	

    if (dis==1) {		
        //text = "Input not valid"+x;
		document.getElementById(checkbox).disabled= true;
    }  
    
 
  
}
//$(".btn1").click(function(){
// $("p").hide();
//});
</script>
</head> 