<?php

/*
* 帮助函数
*
* @copyright (c) 2012 Atom Projects More info http://Atom.com
* @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
* @author xuanyan <yxuan@myspace.cn>
*
*/


function stripslashes_recursive($array)
{
    $array = is_array($array) ? array_map(__FUNCTION__, $array) : stripslashes($array);

    return $array;
}

/**
 * 递归合并数组，并对没有下标的数组进行替换而不是相加操作（区别于array_merge_recursive）
 * @param array $a 原数组
 * @param array $b 追加，替换数组
 * @return array 合并后的数组
 * @author xuanyan
 */
function mergeRecursive($a, $b)
{
    foreach ($b as $key => $value) {
        // 没有key替换
        if (!isset($a[$key])) {
            $a[$key] = $value;
            continue;
        }
        // 原数组当前key不是数组，或者需替换数组当前值不是数组
        if (!is_array($value) || !is_array($a[$key])) {
            $a[$key] = $value;
            continue;
        }
        // 原数组和替换数组，当前都为数组，进行递归替换
        $a[$key] = mergeRecursive($a[$key], $value);
    }

    return $a;
}

function getClienip() {
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
         $onlineip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
         $onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
         $onlineip = $_SERVER['REMOTE_ADDR'];
    } else {
        return 'unknown';
    }

    return filter_var($onlineip, FILTER_VALIDATE_IP) !== false ? $onlineip : 'unknown';
}

function _GET($key = '', $default = '') {
    if (empty($key)) {
        return $_GET;
    }
    if (!isset($_GET[$key])) {
        return $default;
    }
    if (is_string($default)) {
        return trim($_GET[$key]);
    }
    if (is_int($default)) {
        return intval($_GET[$key]);
    }
    if (is_array($default)) {
        return (array)$_GET[$key];
    }
    return floatval($_GET[$key]);
}

function _POST($key = '', $default = '') {
    if (empty($key)) {
        return $_POST;
    }
    if (!isset($_POST[$key])) {
        return $default;
    }
    if (is_string($default)) {
        return trim($_POST[$key]);
    }
    if (is_int($default)) {
        return intval($_POST[$key]);
    }
    if (is_array($default)) {
        return (array)$_POST[$key];
    }
    return floatval($_POST[$key]);
}

function _ARGV($key = '', $default = '') {
    if (empty($GLOBALS['argv']) || !is_array($GLOBALS['argv'])) {
        $GLOBALS['argv'] = array();
    }

    $result = array();
    $last_arg = null;
    foreach ($GLOBALS['argv'] as $val) {
        $pre = substr($val, 0, 2);
        if ($pre == '--') {
            $parts = explode("=", substr($val, 2), 2);
            if (isset($parts[1])) {
                $result[$parts[0]] = $parts[1];
            } else {
                $result[$parts[0]] = true;
            }
        } elseif ($pre{0} == '-') {
            $string = substr($val, 1);
            $len = strlen($string);
            for ($i = 0; $i < $len; $i++) {
                $key = $string[$i];
                $result[$key] = true;
            }
            $last_arg = $key;
        } elseif ($last_arg !== null) {
            $result[$last_arg] = $val;
            $last_arg = null;
        }
    }

    if (empty($key)) {
        return $result;
    }
    if (!isset($result[$key])) {
        return $default;
    }
    if (is_string($default)) {
        return trim($result[$key]);
    }
    if (is_int($default)) {
        return intval($result[$key]);
    }

    return floatval($result[$key]);
}

function redirect($url = '') {
    $site = SITE_URL;
    if (!$url) {
        $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $site;
    }

    if (substr($url, 0, 4) != 'http') {
        if ($url{0} != '/') {
            $url = '/'.$url;
        }
        $url = $site.$url;
    }
    header('Location: ' . $url);
    exit;
}

function convertip($ip) {
    $dat_path = ROOT_PATH .'/qqwry.Dat';
    if(!$fd = @fopen($dat_path, 'rb')){
        return 'IP date file not exists or access denied';
    }
    $ip = explode('.', $ip);
    $ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];
    $DataBegin = fread($fd, 4);
    $DataEnd = fread($fd, 4);
    $ipbegin = implode('', unpack('L', $DataBegin));
    if($ipbegin < 0) $ipbegin += pow(2, 32);
    $ipend = implode('', unpack('L', $DataEnd));
    if($ipend < 0) $ipend += pow(2, 32);
    $ipAllNum = ($ipend - $ipbegin) / 7 + 1;
    $BeginNum = 0;
    $EndNum = $ipAllNum;
    $ip1num = $ip2num = $ipAddr2 = $ipAddr1 = null;
    while($ip1num>$ipNum || $ip2num<$ipNum) {
        $Middle= intval(($EndNum + $BeginNum) / 2);
        fseek($fd, $ipbegin + 7 * $Middle);
        $ipData1 = fread($fd, 4);
        if(strlen($ipData1) < 4) {
            fclose($fd);
            return 'System Error';
        }
        $ip1num = implode('', unpack('L', $ipData1));
        if($ip1num < 0) $ip1num += pow(2, 32);
        if($ip1num > $ipNum) {
            $EndNum = $Middle;
            continue;
        }
        $DataSeek = fread($fd, 3);
        if(strlen($DataSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
        fseek($fd, $DataSeek);
        $ipData2 = fread($fd, 4);
        if(strlen($ipData2) < 4) {
            fclose($fd);
            return 'System Error';
        }
        $ip2num = implode('', unpack('L', $ipData2));
        if($ip2num < 0) $ip2num += pow(2, 32);
        if($ip2num < $ipNum) {
            if($Middle == $BeginNum) {
                fclose($fd);
                return 'Unknown';
            }
            $BeginNum = $Middle;
        }
    }
    $ipFlag = fread($fd, 1);
    if($ipFlag == chr(1)) {
        $ipSeek = fread($fd, 3);
        if(strlen($ipSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
        fseek($fd, $ipSeek);
        $ipFlag = fread($fd, 1);
    }
    if($ipFlag == chr(2)) {
        $AddrSeek = fread($fd, 3);
        if(strlen($AddrSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $ipFlag = fread($fd, 1);
        if($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if(strlen($AddrSeek2) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }
        while(($char = fread($fd, 1)) != chr(0))
        $ipAddr2 .= $char;
        $AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
        fseek($fd, $AddrSeek);
        while(($char = fread($fd, 1)) != chr(0))
        $ipAddr1 .= $char;
    } else {
        fseek($fd, -1, SEEK_CUR);
        while(($char = fread($fd, 1)) != chr(0))
        $ipAddr1 .= $char;

        $ipFlag = fread($fd, 1);
        if($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if(strlen($AddrSeek2) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }
        while(($char = fread($fd, 1)) != chr(0)){
            $ipAddr2 .= $char;
        }
    }
    fclose($fd);
    if(preg_match('/http/i', $ipAddr2)) {
        $ipAddr2 = '';
    }
    $ipaddr = "$ipAddr1 $ipAddr2";
    $ipaddr = preg_replace('/CZ88.Net/is', '', $ipaddr);
    $ipaddr = preg_replace('/^s*/is', '', $ipaddr);
    $ipaddr = preg_replace('/s*$/is', '', $ipaddr);
    if(preg_match('/http/i', $ipaddr) || $ipaddr == '') {
        $ipaddr = 'Unknown';
    }
    $ipaddr = iconv('gbk', 'utf-8//IGNORE', $ipaddr);
    if( $ipaddr != '  ' )
        return $ipaddr;
    else
        $ipaddr = '地址未知！火星来客？';
        return $ipaddr;
}