<?php
 /*
 * i18n 多语言类
 *
 * @copyright (c) 2012 Atom Projects More info http://Atom.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author quqiang <qqiang@myspace.cn>
 *
 */

/* demo
	Language::setLocale('zh-cn');
	echo Language::getText('hello');
	echo Language::genData('cn-us');
*/

class Language
{
	private static $data = NULL;
	public static $locale = 'zh-cn';
	public static $path = 'locale';

	/**
	 * 设置配置参数
	 *
	 * @param array $parms 
	 */
	public static function setLocale($locale)
	{
		if (self::$data !== NULL && self::$locale == $locale) {
			return '';
		}
        // @xuanyan 记录上次加载路径,如果一样,就不要加载
		self::$path = ROOT_PATH.'/Locale';
		$langFile = self::$path . '/' . $locale . '.php';
        
		if (file_exists($langFile)) {
			self::$locale = $locale;
		} else {
			$langFile = self::$path . '/' . self::$locale . '.php';
		}
        $t = include_once $langFile;
        if (is_array($t)) {
            self::$data = $t;
        }
	}
	
	/**
	 * 获取多语言的信息
	 *
	 * @param string $message 
	 * @return string
	 */
	public static function getText($message)
	{
		if (!$message = trim($message)) return $message;
		if (!isset(self::$data['data'][$message])) return $message;
		$args = func_get_args();
		if (count($args) < 2) {
			return self::$data['data'][$message];
		}
		array_shift($args);
		if (substr_count(self::$data['data'][$message], '%s') != count($args)) {
			return $message;
		}
		$parm = array();
		$parm[] = self::$data['data'][$message];
		foreach ($args as $value) {
			$parm[] = $value;
		}
		return call_user_func_array("sprintf", $parm);
	}

	/**
	 * 格式化时间
	 * @param string $time 时间戳
	 * @param string $type 需要格式化的类型
	 * @return string
	 */
	public static function dateFormat($time, $type)
	{
		$formatStr =  self::$data['dateFormats'][$type];
		return date($formatStr, $time);
	}
	
	/**
	 * 根据某语言作为模板生成其他语言
	 *
	 * @param string $genLang 要生成的语言
	 * @param string $defaultLang 使用某语言做为模板
	 */
	public static function genData($genLang)
	{
		$genLang = (array) $genLang;
		foreach ($genLang as $value) {
			if (file_exists(self::$path . '/' . $value . '.php') && is_array($tempData = include_once(self::$path . '/' . $value . '.php'))) {
				$data = self::arrayMerge($tempData);
			} else {
				$data = self::$data;
			}
			$data = '<?php' . "\n\n\n\nreturn " . var_export($data, true) . ';';
			file_put_contents(self::$path . '/' . $value . '.php', $data);
		}
	}
	
	/**
	 * 合并数组
	 *
	 * @param string $data 
	 * @param string $default 
	 * @param string $return 
	 * @return array
	 * @author quqiang
	 */
	public static function arrayMerge($data, $default = array(), $return = array())
	{
		$default = empty($default) ? self::$data : $default;
		foreach ($default as $key => $value) {
			if (!isset($data[$key])) {
				$return[$key] = $value;				
				continue;
			}
			if (isset($data[$key]) && !is_array($data[$key])) {
				$return[$key] = $data[$key];
				continue;
			}
			if (is_array($data[$key])) {
				$return[$key] = self::arrayMerge($data[$key], $default[$key]);
			}
		}
		return $return;
	}
	
	
	/**
	 * 切换语言需要清理数据
	 *
	 * @return array
	 * @author quqiang
	 */
	public function cleanData()
	{
		self::$data = array();
	}

}
