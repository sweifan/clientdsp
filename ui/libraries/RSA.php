<?php
class RSA {

    const RSA_PUB = '
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAMIOcqEZ7SnLN52pfZbjyHTKccy+UQ54
diV7FgNYrnVZPGw/4coII4HqOEgcbQGfPkQMS0DVF+3Rk2AHQ5DJ8csCAwEAAQ==
-----END PUBLIC KEY-----
';
    const RSA_PRI = '
-----BEGIN PRIVATE KEY-----
MIIBVQIBADANBgkqhkiG9w0BAQEFAASCAT8wggE7AgEAAkEAwg5yoRntKcs3nal9
luPIdMpxzL5RDnh2JXsWA1iudVk8bD/hyggjgeo4SBxtAZ8+RAxLQNUX7dGTYAdD
kMnxywIDAQABAkB2cZ0TqaBxDwFuMOJf874JUvtsrYkJ3Qq3y83e0wUAyBmlNiJc
0YGFiGXw8NV6EKIbqTxGTkFsJF1prmXSyT9JAiEA+1YNzv8Py2Hvcnoq137c6MwO
G7YfsoRQGHGtUjasIhcCIQDFqErlDA6rzXXnDk1Kgv7F2Rhx1ZVUClkRbEnh4ebC
bQIhAKUQOhdk+dmHoztasoI+hhS51tYqQRz7uqKjHcItt9TXAiBlfw6+YRujgwS5
GjH8Qhn7lIgl5CwjFJE6DiY+NJcfXQIhAJqQb9yvNGxZiDssdj7Bo2qmlsV68avL
ETcBh2rcO2Y+
-----END PRIVATE KEY-----
';

    public function __construct() {
        //$this->generateRsaKey();
    }

    public function rsaEncrypt($str) {
        $encrypted = '';
        $public_key = openssl_pkey_get_public(self::RSA_PUB);
        openssl_public_encrypt($str, $encrypted, $public_key);
        return base64_encode($encrypted);
    }

    public function rsaDecrypt($str) {
        $decrypted = "";
        $private_key = openssl_pkey_get_private(self::RSA_PRI);
        openssl_private_decrypt(base64_decode($str), $decrypted, $private_key);
        return $decrypted;
    }

	private function generateRsaKey() {
		//创建公钥和私钥
		$res=openssl_pkey_new(array('private_key_bits' => 512)); #此处512必须不能包含引号。

		//提取私钥
		openssl_pkey_export($res, $private_key);

		//生成公钥
		$public_key=openssl_pkey_get_details($res);
		/*Array
		(
			[bits] => 512
			[key] =>
			[rsa] =>
			[type] => 0
		)*/
		$public_key=$public_key["key"];
        echo $public_key . '</br>' . $private_key;exit;
		return [$public_key, $private_key];
    }

}
