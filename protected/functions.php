<?php
	/**
	 * 打印函数
	 */
	function P($array){
		echo "<pre>";
			print_r($array);
		echo "</pre>";
	}
	
	function current_time(){
		$time=date("Y-m-d H:i:s",time());
		return $time;
	}
	
	/**
	 * 自定义curl函数
	 *@param String $url 请求的url,需要通过get传递的参数，直接在url后面加参数,如http://localhost/curl.php?name=test
	 *@param Array $post  需要通过post传递的参数,如array('name'=>'jack')
	 *@param Boolean $print  调试使用，默认false,设置为true时返回的结果将打印的浏览器上
	 */
	function xcurl($url,$post=array(),$print=false,$ref=null,$ua="Mozilla/5.0 (X11; Linux x86_64; rv:2.2a1pre) Gecko/20110324 Firefox/4.2a1pre") {
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		
		if(!empty($ref)) {
			curl_setopt($ch, CURLOPT_REFERER, $ref);
		}
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		if(!empty($ua)) {
			curl_setopt($ch, CURLOPT_USERAGENT, $ua);
		}
		
		if(count($post) > 0){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);	
		}
		
		$output = curl_exec($ch);
		curl_close($ch);
		
		if($print) {
			var_dump($output);
		} else {
			return $output;
		}
	
	}


	/**
	 * 异步请求 fsockopen
	 *@param  String $url  要发送的url
	 *@param  Array  $post_data 使用POST方式发送url所要传递的参数,此参数为空,则使用GET方式发送	
	 */
	function triggerRequest($url, $post_data = array(), $cookie = array()){
	        //设置默认访问方式
	        $method = "GET";  

	        /**
	         * 分解url,如$url="http://www.baidu.com:80/test/index.php?name=yangyang&age=24"  
	         * $url_array['scheme']传输协议,如$url_array['scheme']="http"
	         * $url_array['host'] 访问网址(ip),如$url_array['host']="www.baidu.com"
	         * $url_array['port']端口号,如$url_array['host']=80
	         * $url_array['path']路径,如$url_array['path']="/test/index.php"
	         * $url_array['query']发送的url中的参数,如$url_array['query']="name=yangyang&age=24";
	         */	 
	        $url_array = parse_url($url);
	        $port = isset($url_array['port'])? $url_array['port'] : 80; 

	        //使用fsockopen发送请求,$errno错误号,$errstr错误信息,30访问限制的最大时间
	        $fp = fsockopen($url_array['host'], $port, $errno, $errstr, 30); 
	        if (!$fp) {
	                return FALSE;
	        }
	        
	        //将$url_array['path'] 与url_array['query']拼接
	        $getPath = $url_array['path'];
	        if(isset($url_array['query'])){
	            $getPath .= '?'.$url_array['query'];
	        }
	        
	        //根据$post_data是否为空,修改发送方式
	        if(!empty($post_data)){
	                $method = "POST";
	        }

	        //拼接header头信息
	        $header = $method . " " . $getPath;
	        $header .= " HTTP/1.1\r\n";
	        $header .= "Host: ". $url_array['host'] . "\r\n "; //HTTP 1.1 Host域不能省略
	        /*以下头信息域可以省略
	        $header .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13 \r\n";
	        $header .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,q=0.5 \r\n";
	        $header .= "Accept-Language: en-us,en;q=0.5 ";
	        $header .= "Accept-Encoding: gzip,deflate\r\n";
	         */
	        $header .= "Connection:Close\r\n\r\n";
	        if(!empty($cookie)){
	                $_cookie = strval(NULL);
	                foreach($cookie as $k => $v){
	                        $_cookie .= $k."=".$v."; ";
	                }
	                $cookie_str =  "Cookie: " . base64_encode($_cookie) ." \r\n";//传递Cookie
	                $header .= $cookie_str;
	        }
	        if(!empty($post_data)){
	                $_post = strval(NULL);
	                foreach($post_data as $k => $v){
	                        $_post .= $k."=".$v."&";
	                }
	                $post_str  = "Content-Type: application/x-www-form-urlencoded\r\n";//POST数据
	                $post_str .= "Content-Length: ". strlen($_post) ." \r\n";//POST数据的长度
	                $post_str .= $_post."\r\n\r\n "; //传递POST数据
	                $header .= $post_str;
	        }

	        fwrite($fp, $header);
	        //echo fread($fp,1024); //我们不关心服务器返回
	        fclose($fp);
	        return true;
	    }


	/**
	 * 自定义错误日志
	 */
	function myErrorLog($str){
		$str = trim($str);
		$time = time();
		$date = date('Y-m-d H:i:s',$time);
		$today = date('Ymd',$time);
		error_log("{$date} {$str}\t\n",3,"error_logs/error_{$today}.log");
	}
	
	/**
	 * 接口调用日志
	 */
	function myAccessLog($str){
		$str = trim($str);
		$time = time();
		$date = date('Y-m-d H:i:s',$time);
		$today = date('Ymd',$time);
		error_log("{$date} {$str}\t\n",3,"access_logs/access_{$today}.log");
	}
	
	/**
	 * 路由器调用接口log
	 */
	function RouterAccessLog($str){
		$str=trim($str);
		$time=time();
		$date=date("Y-m-d H:i:s",$time);
		$today=date("Ymd");
		error_log("{$date} {$str}\t\n",3,"router_logs/access_{$today}.log");
	}

	/**
	 * 生成手机验证码随机数
	 *@param int $num 验证码个数
	 *@param int type 验证码类型 1数字 2数字和字母
	 */
	function verificationCode($num=4,$type=1){
		if(intval($type)===1){
			$code=array(0,1,2,3,4,5,6,7,8,9);
		}
		if(intval($type)===2){
			$code=array(0,1,2,3,4,5,6,7,8,9,a,b,c,d,e,f,g,h,i,j,k,L,m,n,O,p,q,r,s,t,u,v,w,x,y,z);
		}
		$code_data=array_rand($code,$num);
		$code_result=implode("",$code_data);
		return  $code_result;
		
	}

	/**
	 * 上传图片函数
	 * @param String $key 图片接收关键词
	 * @param Int $out_width 上传后图片的宽度,如果为0则不做任何裁剪
	 * @param Int $out_height 上传后图片的高度
	 * @param String $prefix 上传后图片的扩展名
	 * @param String $file_path 图片保存目录
	 * @param Int $size 上传图片的最大值
	 * @param Array $ext 允许上传的扩展名
	 * @return Array status:成功或者失败标识码 message:失败信息或成功文件名
	 */
	function uploadFile($key='file',$out_width=150,$out_height=0,$prefix='',$file_path='uploads/',$size=5000000,$ext=array("jpg","gif","png","jpeg","wav","mp3","m4a","amr","caf")){
		
		$data = array();
		$data['status'] = '0';
    	
		$up = new CUploadedFile($key,$file_path);
		$param['size'] = $size;
		$param['ext'] = $ext;
		$up->setParam($param);
		$file = $up->upload();
    	
		if(!$file){
			$data['message'] = $up->errinfo;
			return $data;
		}
		
		$type = trim(strrchr($file,'.'),'.');
	       	 $data['type'] = 1;
	       	 if(in_array($type,array('wav','mp3','m4a','amr','caf'))){
	               	 $data['type'] = 2;
	       	 }

		if($out_width !== 0 && $data['type'] === 1){
			$s_width = $out_width;	
			$s_height = $out_height;
			if($out_height === 0){
				$pic_info = getimagesize($file_path.$file);
				$width = $pic_info[0];
				$height = $pic_info[1];
				$s_height = ($s_width/$width)*$height;
			}

			$cut = new CImage();
			$new_file = $cut->thumb($file_path.$file,$s_width,$s_height,$prefix,$file_path.'thumb/');
			if(is_file($file_path.'thumb/'.$new_file) && is_file($file_path.$file)){
				//unlink($file_path.$file);
			}
			$file = $new_file;
		}
		
		$data['status'] = '1';
		$data['message'] = $file;
		
		return $data;
	}

	/**
	 * 自定义格式化时间
	 * @param Int $time 时间戳
	 * @return String
	 */
	function dateformat($time){
		$time = intval($time);
		//before yesterday
		if($time < strtotime('yesterday')){
			return date('m-d',$time);
		}
		
		//yesterday
		if($time >= strtotime('yesterday') and $time < strtotime('today')){
			return '昨天';
		}
		
		//hours
		if($time >= strtotime('today') and $time <= strtotime('-1 hours')){
			return ceil((time() - $time)/3600).'小时前';
		}
		
		//minutes
		return ceil((time() - $time)/60).'分钟前';
	}

	/**
	 * 生成订单号
	 * @return String
	 */
	function autocode(){
		$time = time();
		$prefix = rand(100000,999999);
		$date = date('YmdHis',$time);
		return $prefix.$date;
	}


	/**
	 * 获取中文字符首字母
	 *
	 * 使用方法:
	 * $getZhWords = new getZhWords("爱美丽");
	 * echo $getZhWords;
	 *
	 */
	class UWords{	
		function __construct($str=null){
			$this->str	= $str;
			$result	= $this->getWords($this->str);
			return strtoupper($result);
		}

		function getLimit(){
			$limit = array( //gb2312 拼音排序
				array(45217,45252), //A
				array(45253,45760), //B
				array(45761,46317), //C
				array(46318,46825), //D
				array(46826,47009), //E
				array(47010,47296), //F
				array(47297,47613), //G
				array(47614,48118), //H
				array(0,0),         //I
				array(48119,49061), //J
				array(49062,49323), //K
				array(49324,49895), //L
				array(49896,50370), //M
				array(50371,50613), //N
				array(50614,50621), //O
				array(50622,50905), //P
				array(50906,51386), //Q
				array(51387,51445), //R
				array(51446,52217), //S
				array(52218,52697), //T
				array(0,0),         //U
				array(0,0),         //V
				array(52698,52979), //W
				array(52980,53688), //X
				array(53689,54480), //Y
				array(54481,55289), //Z
			);
			return $limit;
		}

		function getWords($str){
			$str= iconv("UTF-8","gb2312", $str);
			$i=0;
			while($i<strlen($str)){
				$tmp=bin2hex(substr($str,$i,1));
				if($tmp>='B0'){ //汉字的开始
					$t=$this->getLetter(hexdec(bin2hex(substr($str,$i,2))));
					$value[] = sprintf("%c",$t==-1 ? '*' : $t );
					$i+=2;
				}
				else{
					$value[] = sprintf("%s",substr($str,$i,1));
					$i++;
				}
			}
			$result = implode('',$value); ;
			return $result;
		}

		function getLetter($num){
			$limit	= $this->getLimit();
			$char_index=65;
			foreach($limit as $k=>$v){
				if($num>=$v[0] && $num<=$v[1]){
					$char_index+=$k;
					return $char_index;
				}
			}
			return -1;
		}
		
		function ufirst(){
			$result	= $this->getWords($this->str);
			return strtoupper($result);
		}

	}

	/**
	 * 提取中文字符串首字母大写或英文大写
	 * @param String $str 要提取的中英文字符串
	 * @return String
	 */
	function my_ucwords($str){
		$uwords = new UWords($str);
		return $uwords->ufirst();
	}

	/**
	 * 获取某个表的最大的id值
	 * @param String $table 表名称（不写前缀）
	 * @param String $condition 条件（暂时只接收array('field'=>'value'),字段field的值为value）
	 * @param String $tablePrefix 表前缀
	 * @return Int 最大的id
	 */
	function maxid($table,$condition=array(),$tablePrefix='axs_'){
		
		$table = $tablePrefix.$table;
		$sql = "SELECT id FROM {$table}";
		if(!empty($condition)){
			$where = ' WHERE';
			foreach ($condition as $field => $value) {
				$where .= " {$field} = '{$value}' AND";
			}
			$where = rtrim($where,' AND');
			$sql .= $where;
		}
		$sql .= " ORDER BY id DESC LIMIT 1";
		$result = Rain::app()->db->scalar($sql);

		$id = intval($result);
		return $id;
		
	}

	/**
	 * 截取指定个数的字符串
	 * @param String $str 要截取的字符串
	 * @param String $len 要截取的长度
	 * @return String 截取后的字符串
	 */

	function cut_str($str,$len,$append='...'){
		$real_len = mb_strlen($str,'UTF-8');
		if($real_len <= $len){
			return $str;
		}
		$str = mb_substr($str,0,$len,'UTF-8').$append;
		return $str;
	}

	//设置聊天界面时间
	function setListTime($list){
		$time_init = 0;
		$new_list = array();

		foreach ($list as $key => $value) {
			$item = $value;
			if($time_init === 0 || intval($item['send_time']) - $time_init > 60){
				$item['send_time'] = date('Y-m-d H:i:s',$item['send_time']);
				$new_list[] = $item;
			}else{
				$item['send_time'] = '';
				$new_list[] = $item;
			}
			$time_init = intval($value['send_time']);
		}

		return $new_list;
	}

	//获取地址坐标
	function getLocation($address){

		$result = array();
		$result['status'] = '300';

		if(empty($address)){
			$result['message'] = '地址不能为空';
			return $result;
		}
		$address = urlencode($address);
		$url = "http://api.map.baidu.com/geocoder/v2/?address={$address}&output=json&ak=XvvyPTFZsmfftQnF34vG4H1b";

		$res = xcurl($url);
		$res = json_decode($res);

		if($res->status !== 0){
			$result['message'] = $res->msg;
			return $result;
		}

		$location = array();
		$location['lon'] = $res->result->location->lng;
		$location['lat'] = $res->result->location->lat;
		
		$result['status'] = '200';
		$result['message'] = $location;
		return $result;
	}






	













