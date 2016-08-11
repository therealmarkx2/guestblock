<?php
/**
* @author Matthew Hadley
* @package guestblock
* @license http://www.gnu.org/licenses/gpl.txt
* @desc contains code for cookie class
*/
/**
* @author Matthew Hadley
* @package guestblock
* @subpackage functional
* @desc cookie class handles cookie creation and usage
*/
class Cookie
{
	/** @var string $md5String string for md5 seed */
	var $md5String='encryptTheData';
	
	/**
	* constructor function
	* @param string $md5 md5 seed string to use
	*/
	function cookie($md5=NULL)
	{
		if($mdf!=NULL)$md5String=$md5;	
	}
	
	/**
	* create a cookie
	* @param string $name name for cookie
	* @param string $data data to put into cookie
	* @param string $expire when cookie expires
	* @param string $path path for cookie
	* @param string $domain domain for cookie
	* @param string $encrypt whether to encrypt data with md5, ON by default
	*/
	function setCookie($name, $data, $expire, $path=NULL, $domain=NULL, $encrypt=TRUE)
	{
		/* note that $data can be an array */
		$data = serialize($data);
		if($encrypt==TRUE)
		{
			$chksum = md5($data . md5($this->md5String));
			$var = serialize(array($data,$chksum));
		}
		setcookie($name, $var, $expire, $path, $domain);
	}
	
	/**
	* get cookie contents
	* @param string $data name of cookie to retrieve
	* @param string $dencrypt whether to decrypt data with md5, ON by default
	* @return mixed cookie data
	*/
	function getCookie($name, $decrypt=TRUE)
	{
		$var = unserialize($_COOKIE[$name]);
		if($decrypt==TRUE)
		{
			$chksum = md5($name . md5($this->md5String));
			list($name,$chksum) = $var;
			if (md5($name . md5($this->md5String)) == $chksum)
			{
			    $name = unserialize($name);
			}
		}
		else
		{
			$name = unserialize($name);
		}
		return $name;
	}
	
	/**
	* get cookie contents
	* @param string $name name of cookie to delete
	*/
	function deleteCookie($name)
	{
		setcookie ($name, '', time() - 3600);	
	}
}
?>