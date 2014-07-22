<?php

namespace jin\external\jasig\cas;
use jin\JinCore;
use jin\lang\StringTools;
use \phpCAS as phpCAS;

class CasSSO{
    private static $host;
    private static $port;
    private static $context;
    private static $ssl;
    private static $debugging;
    private static $serviceId;
    
    public static function configure($host, $port, $serviceId, $context = 'cas', $ssl = true, $debugging = false){
	self::$host = $host;
	self::$port = $port;
	self::$context = $context;
	self::$ssl = $ssl;
	self::$debugging = $debugging;
	self::$serviceId = $serviceId;
	
	$casPath = JinCore::getRoot().JinCore::getRelativeExtLibs().'cas/';
	require_once  $casPath.'CAS.php';
	
	if(self::$debugging){
	    phpCAS::setDebug();
	}
	
	$serviceId = StringTools::urlEncode(self::$serviceId);
	phpCAS::client(CAS_VERSION_2_0, self::$host, self::$port, self::$context);
	
	if(!self::$ssl){
	    phpCAS::setNoCasServerValidation();
	}
	
	phpCAS::setServerLoginURL(self::getBaseUrl().'login?service='.$serviceId);
	phpCAS::setServerServiceValidateURL(self::getBaseUrl().'serviceValidate');
	phpCAS::setServerProxyValidateURL(self::getBaseUrl().'proxyValidate');
	phpCas::setServerLogoutURL(self::getBaseUrl().'logout?destination='.$serviceId);
    }
    
    
    public static function login(){
	phpCAS::forceAuthentication();
    }
    
    public static function logout(){
	phpCAS::logout();
    }
    
    public static function isLogin(){
	return phpCAS::checkAuthentication();
    }
    
    public static function autoLogin(){
	if(!self::isLogin()){
	    self::login();
	}
    }
    
    public static function getUser(){
	return phpCAS::getUser();
    }
    
    public static function getCasVersion(){
	return phpCAS::getVersion();
    }
    
    private static function getBaseUrl(){
	$baseUrl = 'https://';
	if(!self::$ssl){
	    $baseUrl = 'http://';
	}
	$baseUrl .= self::$host.':'.self::$port.'/'.self::$context.'/';
	return $baseUrl;
    }
    
    
}
