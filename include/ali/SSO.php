<?php
/**
 * take from opensearch
 */
class MAli_SSO
{
    const VERSION      = "0.3.0";

    const HOST         = "https://login.alibaba-inc.com";
    const TEST_HOST    = "https://login-test.alibaba-inc.com";

    const INFO_PATH    = "/rpc/sso/communicate.json";
    const LOGIN_PATH   = "/ssoLogin.htm";
    const LOGOUT_PATH  = "/sso_logout.htm";
    const STARTUP_PATH = "/updateAppVersion.do";

    const APP_NAME = 'etao-team-share';

    protected $appName;
    protected $bucHost;

    public function __construct()
    {
        $this->appName = self::APP_NAME;
        $this->bucHost = self::HOST;
    }

    public function startUp()
    {
        $startupParams = array(
            "APP_NAME" => $this->appName,
            "CLIENT_VERSION" => self::VERSION,
        );
        return self::curlFetch($this->bucHost . self::STARTUP_PATH ,  $startupParams, "post");
    }

    public function login($backUrl, $contextPath="/")
    {
        $loginParams = array(
            "APP_NAME"     =>  $this->appName,
            "BACK_URL"     =>  $backUrl,
            "CONTEXT_PATH" =>  $contextPath,
        );

        $redirectUrl = $this->bucHost . self::LOGIN_PATH . "?" . http_build_query($loginParams);
        header("Location: " . $redirectUrl);
        exit;
    }

    public function getUserInfo($token)
    {
        $communicateParams = array(
            'SSO_TOKEN' =>  $token,
            'RETURN_USER' => 'true',
        );

        $json_str = self::curlFetch($this->bucHost . self::INFO_PATH, $communicateParams, "post");

        $json = array();
        if(!empty($json_str)){
            $json = json_decode($json_str, true);
            if(!empty($json["content"])) {
                $json["content"] = json_decode($json["content"], true);
            }
        }

        return $json;
    }

    public function logout($backUrl, $contextPath)
    {
        $loginParams =  array(
            "APP_NAME"     =>  $this->appName,
            "BACK_URL"     =>  $backUrl,
            "CONTEXT_PATH" =>  $contextPath,
        );

        $redirectUrl = $this->bucHost . self::LOGOUT_PATH. "?" . http_build_query($loginParams);
        header("Location: " . $redirectUrl);
        exit;
    }

    public function getLogoutUrl()
    {
        return $this->bucSsoProtocol . self::LOGOUT_PATH;
    }

    public static function curlFetch($url, $params = array(), $method="get")
    {
        $ch = curl_init();
        if(strtolower($method) == "post")
        {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        else
        {
            curl_setopt($ch, CURLOPT_URL, $url . "?". http_build_query($params));
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
