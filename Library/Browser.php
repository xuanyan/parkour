<?php

/*
* 浏览器识别类
*
* @copyright (c) 2012 Atom Projects More info http://Atom.com
* @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
* @author hanlei <lhan@myspace.cn>
*
*/

class Browser
{
    public $_userAgent;
    public $_platform;
    public $_browser;
    public $_version;

	/**
	 * detectBrowser function
	 *
	 * @return void
	 */
    public function detectBrowser()
    {
        if (preg_match('/linux/i', $this->_userAgent)) {
            $this->_platform = 'Linux';
        } elseif (preg_match('/ipad/i', $this->_userAgent)) {
            $this->_platform = 'iPad';
        } elseif (preg_match('/iphone/i', $this->_userAgent)) {
            $this->_platform = 'iPhone';
        } elseif (preg_match('/macintosh|mac os x/i', $this->_userAgent)) {
            $this->_platform = 'Mac';
        } elseif (preg_match('/android/i', $this->_userAgent)) {
            $this->_platform = 'Android';
        } elseif (preg_match('/windows|win32/i', $this->_userAgent)) {
            $this->_platform = 'Windows';
        } else {
            $this->_platform = 'Unknow';
        }

        if(preg_match('/MSIE/i',$this->_userAgent) && !preg_match('/Opera/i',$this->_userAgent)) {
            $this->_browser = 'MSIE';
        } elseif (preg_match('/Firefox/i',$this->_userAgent)) {
            $this->_browser = 'Firefox';
        } elseif (preg_match('/Chrome/i',$this->_userAgent)) {
            $this->_browser = 'Chrome';
        } elseif (preg_match('/Safari/i',$this->_userAgent)) {
            $this->_browser = 'Safari';
        } elseif (preg_match('/Opera/i',$this->_userAgent)) {
            $this->_browser = 'Opera';
        } elseif (preg_match('/Netscape/i',$this->_userAgent)) {
            $this->_browser = 'Netscape';
        } else {
            $this->_browser = 'Unknow';
        }

        if (preg_match("#(Version)[/]?([0-9.]*)#", $this->_userAgent, $match)){
            $this->_version = $match[2];
        } elseif (preg_match("#($this->_browser)[/ ]?([0-9.]*)#", $this->_userAgent, $match)) {
            $this->_version = $match[2];
        } else{
            $this->_version = 'Unknow';
        }
    }//end getBrowser

	/**
	 * getBrowser function
	 *
	 * @param string $$userAgent 
	 * @return array('platform'=>string,'browser'=>string,'version'=>string)
	 */
    public function getBrowser($userAgent)
    {
        $this->_userAgent = $userAgent;
        $this->detectBrowser();
        
        return array('platform'=>$this->_platform,'browser'=>$this->_browser,'version'=>$this->_version);
    }
}
