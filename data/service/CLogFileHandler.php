<?php
/**
 * BaseService.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 
 * @version : v1.0.0.0
 */
namespace data\service;
use data\api\ILogHandler as ILogHandler;

class CLogFileHandler implements ILogHandler
{
    private $handle = null;
    
    public function __construct($file = '')
    {
        $this->handle = fopen($file,'a');
    }
    
    public function write($msg)
    {
        fwrite($this->handle, $msg, 4096);
    }
    
    public function __destruct()
    {
        fclose($this->handle);
    }
}