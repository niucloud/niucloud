<?php

namespace util\file;

class Write
{
	/**
	 * 文件指针
	 * @var resource
	 */
	private $fp;
	
	/**
	 * 备份文件信息
	 * @var array
	 */
	private $path;
	
	/**
	 * 当前需要写入文件的数据
	 * @var integer
	 */
	private $data = 0;
	
	/**
	 * 写入文件构造方法
	 * @param string $path
	 * @param array $data
	 */
	public function __construct($path, $data)
	{
		$this->path = $path;
		$this->data = $data;
		$this->fp = @fopen($path, 'w');
	}
	
	/**
	 * 写入初始数据
	 * @return boolean true - 写入成功，false - 写入失败
	 */
	public function create()
	{
		$code = "<?php\n";
		$code .= "// +---------------------------------------------------------------------+\n";
		$code .= "// | NiuCloud   | [ WE CAN DO IT JUST NiuCloud ]                         |\n";
		$code .= "// +---------------------------------------------------------------------+\n";
		$code .= "// | Copy  right   2019-2029   www.niucloud.com                          |\n";
		$code .= "// +---------------------------------------------------------------------+\n";
		$code .= "// | Author     | NiuCloud <niucloud@outlook.com>                        |\n";
		$code .= "// +---------------------------------------------------------------------+\n";
		$code .= "// | Repository | https://github.com/niucloud/framework.git              |\n";
		$code .= "// +---------------------------------------------------------------------+\n";
		$code .= "\nreturn [";
		$code .= $this->parseArray($this->data);
		$code .= "\n];";
		return $this->write($code);
	}
	
	/**
	 * 写入SQL语句
	 * @param  string $sql 要写入的SQL语句
	 * @return boolean     true - 写入成功，false - 写入失败！
	 */
	private function write($code)
	{
		return @fwrite($this->fp, $code);
	}
	
	/**
	 * 解析数据
	 * @param data $array
	 * @param number $level
	 * @return string
	 */
	private function parseArray($array, $level = 1)
	{
		$code = '';
		if (is_array($array)) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					if (isset($v['action'])) {
						if ($v['action'] == 'ADD' || $v['action'] == 'EDIT') {
							unset($v['action']);
						} elseif ($v['action'] == 'DELETE') {
							continue;
						}
					}
					if (is_numeric($k)) {
						$code .= "\n" . $this->getTab($level) . "[";
					} else {
						$code .= "\n" . $this->getTab($level) . "'$k' => [";
					}
					$code .= $this->parseArray($v, $level + 1);
					$code .= "\n" . $this->getTab($level) . "],";
				} else {
					$code .= "\n" . $this->getTab($level) . "'$k' => '$v',";
				}
			}
		}
		return $code;
	}
	
	/**
	 * 获取tab
	 * @param int $num
	 */
	private function getTab($num)
	{
		$t = "";
		for ($i = 0; $i < $num; $i++) {
			$t .= "\t";
		}
		return $t;
	}
	
	/**
	 * 析构方法，用于关闭文件资源
	 */
	public function __destruct()
	{
		@fclose($this->fp);
	}
}