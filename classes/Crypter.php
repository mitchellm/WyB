<?
/**
 * Originally used for the session encryption so it could be decrypted and read, but has since been removed. keeping the class incase.
 * 
 * @author Mitchell Murphy
 * @author Unknown
 * @package What's Your Beef
 * @version 1.0.9
 */

interface ICrypter{
	public function __construct($Key, $Algo = MCRYPT_BLOWFISH);
	public function Encrypt($data);
	public function Decrypt($data);
}

class Crypter implements ICrypter{
	private $Key;
	private $Algo;
	private static $instance;

	public function __construct($Key, $Algo = MCRYPT_BLOWFISH){
		$this->Key = substr($Key, 0, mcrypt_get_key_size($Algo, MCRYPT_MODE_ECB));
		$this->Algo = $Algo;
	}

	public static function getInstance($key, $algo = MCRYPT_BLOWFISH) {
		if(!self::$instance) 
			self::$instance = new Crypter($key, $algo);

		return self::$instance;
	}

	public function Encrypt($data){
		if(!$data){
			return false;
		}
		
		//Optional Part, only necessary if you use other encryption mode than ECB
		$iv_size = mcrypt_get_iv_size($this->Algo, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		
		$crypt = mcrypt_encrypt($this->Algo, $this->Key, $data, MCRYPT_MODE_ECB, $iv);
		return trim(base64_encode($crypt));
	}
	
	public function Decrypt($data){
		if(!$data){
			return false;
		}
		
		$crypt = base64_decode($data);
		
		//Optional Part, only necessary if you use other encryption mode than ECB
		$iv_size = mcrypt_get_iv_size($this->Algo, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		
		$decrypt = mcrypt_decrypt($this->Algo, $this->Key, $crypt, MCRYPT_MODE_ECB, $iv);
		return trim($decrypt);
	
	}
}

?>