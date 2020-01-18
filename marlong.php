<?php
/**
	* Thanks To 	: Alif Dzikri & Will Pratama
	* Date Created 	: Sab, 18-Jan, 09:48:55
	* @category 	: Bot Marlboro
	* @link 		: https://xaynet.id/files/marlong-v9.zip
	* @author 		: Awaludin AR [https://facebook.com/awaludin.arr]
	* @copyright 	: 2020 Xaynet Project

**/
date_default_timezone_set("Asia/Jakarta");
class Marlong extends vendor{

	public $dir = "tempMarbor";
	public $lokTmp = "";

	public function __construct(){
		do{
			$namaFile = "tempMarbor-".$this->acak(5).".json";
			if(!is_dir($this->dir)){
				mkdir($this->dir);
			}

			if(@file_exists($this->dir."/".$namaFile)){
				$namaFile = "";
			}
		}while(empty($namaFile));
		$this->lokTmp = $this->dir."/".$namaFile;
	}

	private function login($token){
		$url = "https://www.marlboro.id/profile";
		print_r("[INFO]> Mencoba Login \n");
		$headers = array();
		$headers[] = "Cookie: deviceId=; _mm3rm4bre_=".$token."; decide_session=";

		$login = $this->curl($url, null, true, $headers);
		$cookie = $this->fetchCookies($login);
		if(isset($cookie['decide_session'], $cookie['deviceId'])){
			$username = $this->getStr($login, '<span class="name-user">Hi, ', '</span>', 1, 0);
			$decide_csrf = $this->getStr($login, 'name="decide_csrf" value="', '" />', 1, 0);
			print_r("[INFO]> Login Sukses [$username] \n");
			echo PHP_EOL;
			$cookie['decide_csrf'] = $decide_csrf;
			$cookie = json_encode($cookie);
			file_put_contents($this->lokTmp, $cookie);

			return true;
		}

		return false;

	}

	private function idVidio(){
		$data = json_decode(file_get_contents($this->lokTmp));

		$url = 'https://www.marlboro.id/article/explore?q=discovered';
		$headers = explode("\n", "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\nCookie: deviceId=".$data->deviceId."; decide_session=".$data->decide_session);
		$listIdVidio = $this->curl($url, null, true, $headers, 'GET');
		$cookie = $this->fetchCookies($listIdVidio);
		if(!isset($cookie['decide_session'])){
			return false;
		}

		$data = json_decode(file_get_contents($this->lokTmp));
		$data->decide_session = $cookie['decide_session'];
		file_put_contents($this->lokTmp, json_encode($data));
		return $listIdVidio;
	}

	private function getPage($id){
		$data = json_decode(file_get_contents($this->lokTmp));

		$url = "https://www.marlboro.id/discovered/article/$id";
		//$url = "https://www.marlboro.id/discovered/article/dedikasi-hidup-yang-berawal-dari-pengalaman";
		print_r("[INFO]> Mengambil Page: $url \n");
		$headers = explode("\n", "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\nCookie: deviceId=".$data->deviceId."; decide_session=".$data->decide_session);
		$get = $this->curl($url, null, true, $headers);
		$cookie = $this->fetchCookies($get);
		$decide_csrf = $this->getStr($get, 'name="decide_csrf" value="', '" />', 1, 0);
		if(isset($cookie['decide_session'])){
			$data->decide_session = $cookie['decide_session'];
			$data->decide_csrf = $decide_csrf;
			file_put_contents($this->lokTmp, json_encode($data));

			return true;
		}

		return false;
	}

	private function viewVideo($id){
		$this->getPage($id);
		$data = json_decode(file_get_contents($this->lokTmp));

		$url = "https://www.marlboro.id/article/video-play/$id";
		//$url = "https://www.marlboro.id/article/video-play/dedikasi-hidup-yang-berawal-dari-pengalaman";
		print_r("[INFO]> Menonton Page: $url \n");
		$headers = explode("\n", "Host: www.marlboro.id\nUpgrade-Insecure-Requests: 1\nSec-Fetch-Mode: navigate\nSec-Fetch-User: ?1\nSec-Fetch-Site: cross-site\nCookie: deviceId=".$data->deviceId."; decide_session=".$data->decide_session);
		$body = "decide_csrf=".$data->decide_csrf."&log_id=false&duration=0.012&total_duration=0&fetch=1&g-recaptcha-response=";
		//$view = $this->curl($url, $body, false, $headers, 'POST');
		$view = $this->request($url, $body, $headers, 'POST');
		$cookie = $this->fetchCookies($view[1]);
		if(isset($cookie['decide_session'])){
			$data->decide_session = $cookie['decide_session'];
			file_put_contents($this->lokTmp, json_encode($data));

			return json_decode($view[0]);
		}

		return false;

	}

	private function updateVideo($id, $log_id){
		$data = json_decode(file_get_contents($this->lokTmp));

		$url = "https://www.marlboro.id/article/video-play/$id";
		print_r("[INFO]> Mengupdate Page: $url \n");
		$headers = explode("\n", "Host: www.marlboro.id\nUpgrade-Insecure-Requests: 1\nSec-Fetch-Mode: navigate\nSec-Fetch-User: ?1\nSec-Fetch-Site: cross-site\nCookie: deviceId=".$data->deviceId."; decide_session=".$data->decide_session);
		$body = "decide_csrf=".$data->decide_csrf."&log_id=$log_id&duration=11.052&total_duration=5&fetch=2&g-recaptcha-response";
		$update = $this->request($url, $body, $headers);
		$cookie = $this->fetchCookies($update[1]);

		if(isset($cookie['decide_session'])){
			$data->decide_session = $cookie['decide_session'];
			file_put_contents($this->lokTmp, json_encode($data));

			return json_decode($update[0]);
		}

		return false;
	}

	public function sesi_login($token){
		if(!$this->login($token)){
			print_r("[INFO]> Login Failed");
			echo PHP_EOL;

			return false;
		}
		//print_r($this->getPoint());
		return true;
	}

	public function sesi_nonton(){
		for($i = 1; $i < 10; $i++){
			sleep(1);
			print_r("[INFO]> Point : ".$this->getPoint());
			echo PHP_EOL;
			$idVidio = $this->getStr($this->idVidio(), 'data-ref="https://www.marlboro.id/discovered/article/', '">', $i, 0);

			if(!empty($idVidio)){
				$video = $this->viewVideo($idVidio);
				//print_r($video);
				//echo PHP_EOL;
				if ($video === null && json_last_error() !== JSON_ERROR_NONE) {
					print_r("[INFO]> Pharse Failed [1] \n");
					echo PHP_EOL;
					continue;
				}

				if(isset($video->error->code)){
					print_r("[INFO]> ".$video->error->message);
					echo PHP_EOL.PHP_EOL;
					continue;
				}

				$log_id = $video->data->log_id;
				$update = $this->updateVideo($idVidio, $log_id);

				if ($update === null && json_last_error() !== JSON_ERROR_NONE) {
					print_r("[INFO]> Pharse Failed [2] \n");
					echo PHP_EOL;
					continue;
				}

				if(isset($update->error->code)){
					print_r("[INFO]> ".$video->error->message);
					echo PHP_EOL.PHP_EOL;
					continue;
				}
				sleep(1);
				if($update->data->finished){
					print_r("[INFO]> Sukses Menonton \n");
					echo PHP_EOL;
					continue;
				}

				print_r("[INFO]> Video Tidak Selesai Di Tonton. \n");
				echo PHP_EOL;

				continue;
			}
			print_r("[INFO]> ID Video Tidak di Temukan \n");
			echo PHP_EOL;
			break;
		}
		return false;
	}

	public function getPoint(){
		$data = json_decode(file_get_contents($this->lokTmp));

		$url = "https://www.marlboro.id/profile";
		$headers = explode("\n", "Host: www.marlboro.id\nUpgrade-Insecure-Requests: 1\nSec-Fetch-Mode: navigate\nSec-Fetch-User: ?1\nSec-Fetch-Site: cross-site\nCookie:deviceId=$data->deviceId; decide_session=$data->decide_session");
		$get = $this->curl($url, null, true, $headers, 'GET');
		$cookie = $this->fetchCookies($get);

		if(isset($cookie['decide_session'])){
			$data->decide_session = $cookie['decide_session'];
			file_put_contents($this->lokTmp, json_encode($data));
		}

		$points = $this->getStr($get, '/><div class="point">', '</div>', 1, 0);
		return trim($points);
	}

	public function updateProfile(){

		print_r("[INFO]> Point: ".$this->getPoint());
		echo PHP_EOL;
		$data = json_decode(file_get_contents($this->lokTmp));

		$url = "https://www.marlboro.id/auth/update-profile";
		$body = "decide_csrf=".$data->decide_csrf."&email=&password=&phone_number=0&city=598&address=Sudirman%0D%0ABlok+M%2C+KM+11%2C+Jl.+Pantura+Jagat+Surya&old_password_chg=&new_password_chg=&confirm_password_chg=&security_question=500001005&security_answer=Babi&fav_brand1=500025454&fav_brand2=500019272&interest_raw=Inspiration&interest_raw=Style&interest_raw=Music&interest_raw=Travel&province=4&postalcode=0&interest=Inspiration|Style|Music|Travel&stop_subscribe_email_promo=false";
		$headers = explode("\n", "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\nCookie: deviceId=".$data->deviceId."; decide_session=".$data->decide_session);
		$up = $this->curl($url, $body, false, $headers, 'POST');
		$cookie = $this->fetchCookies($up);
		if(isset($cookie['decide_session'])){
			$data->decide_session = $cookie['decide_session'];
			file_put_contents($this->lokTmp, json_encode($data));
		}

		$up = json_decode($up);
		if ($up === null && json_last_error() !== JSON_ERROR_NONE) {
			print_r("[INFO]> Pharse Failed [1] \n");
			echo PHP_EOL;
			return false;
		}

		if(isset($up->error->code)){
			print_r("[INFO]> ".$up->error->message);
			echo PHP_EOL;
			return false;
		}

		print_r("[INFO]> ".$up->data->message);
		echo PHP_EOL.PHP_EOL;
		return false;

	}
}

class vendor{

	public function curl($url, $params = null, $header = true, $httpheaders = null, $request = 'GET'){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
		curl_setopt($ch, CURLOPT_HEADER, $header);
		@curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheaders);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:52.0) Gecko/20100101 Firefox/52.0");
		$response = curl_exec($ch);
		return $response;
		curl_close($ch);
	}

	public function request($url, $param, $headers, $request = 'POST') 
	{
		$ch = curl_init();
		$data = array(
				CURLOPT_URL				=> $url,
				CURLOPT_POSTFIELDS		=> $param,
				CURLOPT_HTTPHEADER 		=> $headers,
				CURLOPT_CUSTOMREQUEST 	=> $request,
				CURLOPT_HEADER 			=> true,
				CURLOPT_RETURNTRANSFER	=> true,
				CURLOPT_FOLLOWLOCATION 	=> true,
				CURLOPT_SSL_VERIFYPEER	=> false
			);
		curl_setopt_array($ch, $data);
		$execute = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($execute, 0, $header_size);
		$body = substr($execute, $header_size);
		curl_close($ch);
		return [$body, $header];
	}

	public function getStr($page, $str1, $str2, $line_str2, $line)
	{
		$get = explode($str1, $page);
		$get2 = explode($str2, $get[$line_str2]);
		return $get2[$line];
	}

	function acak($q){
		$abc = "0123456789";
		$word = "";
		for ($i=0; $i < $q ; $i++) { 
			$word .=$abc{rand(0,strlen($abc)-1)};
		}
		return $word;
	}

	public function fetchCookies($source) 
	{
		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $source, $matches);
		$cookies = array();
		foreach($matches[1] as $item) 
		{
			parse_str($item, $cookie);
			$cookies = array_merge($cookies, $cookie);
		}

		return $cookies;
	}
}

$app = new marlong();

print "
==========================================
___  ___           _                   
|  \/  |          | |                  
| .  . | __ _ _ __| | ___  _ __   __ _ 
| |\/| |/ _` | '__| |/ _ \| '_ \ / _` |
| |  | | (_| | |  | | (_) | | | | (_| |
\_|  |_/\__,_|_|  |_|\___/|_| |_|\__, |
                                  __/ |
                                 |___/ 
===[ Coder : Awaludin AR ]=================".PHP_EOL.PHP_EOL;
$file = "";
do{
	print "[#]> Lokasi File [_mm3rm4bre_]: ";
	$file = trim(fgets(STDIN));
	if(!file_exists($file)) {
		$file = null;
		print "[INFO]> File Tidak di Temukan. \n";
	}elseif (!is_file($file)) {
		$file = null;
		print "[INFO]> $file Bukan Sebuah File \n";
	}elseif(!is_readable($file)) {
		$file = null;
		print "[INFO]> File tidak bisa di baca \n";
	}else{
		$file = $file;
	}
}while(empty($file));

$menu = 0;
print PHP_EOL.
"[+] Support Multi Terminal".PHP_EOL.
"[+] Rekomendasi IP Indoensia".PHP_EOL.PHP_EOL;
print "[PATH: $file] \n";
while(true){
	print "[1] Bot Nonton \n[2] Get Bonus [Akun Baru] \n[3] Cek Point \n[4] Stop";
	do{
		print "[#]> Menu: ";
		$menu = trim(fgets(STDIN));
	}while($menu < 1 || $menu > 4);

	$file = file($file);
	switch ($menu) {
		case 1:
			while(true){
				print "============================================================== \n";
				print "\t\t Bot Mulai : [".date("Y-m-d H:i:s")."]  \n";
				print "============================================================== \n";
				foreach ($file as $n => $val) {
					print "=============================[Akun ".($n+1)."]========================= \n";
					if(!$app->sesi_login(trim($val))){
						continue;
					}
					$app->sesi_nonton();
					print "============================================================== \n";
					
				}
				print "============================================================== \n";
				print "\t\t Mulai Ulang : [".date("Y-m-d H:i:s", time() + (60 * 60 * 24))."] \n";
				print "============================================================== \n";
				sleep(86400);
				echo PHP_EOL.PHP_EOL;
			}
			$menu = 0;
			break;
		case 2:
			foreach ($file as $n => $val) {
				print "=============================[Akun ".($n+1)."]========================= \n";
				if(!$app->sesi_login(trim($val))){
					continue;
				}
				$app->updateProfile();
				print "============================================================== \n";
			}
			$menu = 0;
			break;
		case 3:
			foreach ($file as $n => $val) {
				print "=============================[Akun ".($n+1)."]========================= \n";
				if(!$app->sesi_login(trim($val))){
					continue;
				}
				print "[INFO]> Point: ".$app->getPoint().PHP_EOL;
				print "============================================================== \n";
			}
			$menu = 0;
			break;
		case 4:
			print "[+]Ciprat Ya sob![+] \n";
			exit();
			break;
		
		default:
			print "[INFO]> Menu tidak ada!.";
			exit();
			break;
	}
}
