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
use data\service\CLogFileHandler as CLogFileHandler;
class Log
{
    private $handler = null;
    private $level = 15;
    
    private static $instance = null;
    
    private function __construct(){}

    private function __clone(){}
    
    public static function Init($handler = null,$level = 15)
    {
        if(!self::$instance instanceof self)
        {
            self::$instance = new self();
            self::$instance->__setHandle($handler);
            self::$instance->__setLevel($level);
        }
        return self::$instance;
    }
    
    
    private function __setHandle($handler){
        $this->handler = $handler;
    }
    
    private function __setLevel($level)
    {
        $this->level = $level;
    }
    
    public static function DEBUG($msg)
    {
        self::$instance->write(1, $msg);
    }
    
    public static function WARN($msg)
    {
        self::$instance->write(4, $msg);
    }
    
    public static function ERROR($msg)
    {
        $debugInfo = debug_backtrace();
        $stack = "[";
        foreach($debugInfo as $key => $val){
            if(array_key_exists("file", $val)){
                $stack .= ",file:" . $val["file"];
            }
            if(array_key_exists("line", $val)){
                $stack .= ",line:" . $val["line"];
            }
            if(array_key_exists("function", $val)){
                $stack .= ",function:" . $val["function"];
            }
        }
        $stack .= "]";
        self::$instance->write(8, $stack . $msg);
    }
    
    public static function INFO($msg)
    {
        self::$instance->write(2, $msg);
    }
    
    private function getLevelStr($level)
    {
        switch ($level)
        {
        case 1:
            return 'debug';
        break;
        case 2:
            return 'info';  
        break;
        case 4:
            return 'warn';
        break;
        case 8:
            return 'error';
        break;
        default:
                
        }
    }
    
    protected function write($level,$msg)
    {
        if(($level & $this->level) == $level )
        {
            $msg = '['.date('Y-m-d H:i:s').']['.$this->getLevelStr($level).'] '.$msg."\n";
            $this->handler->write($msg);
        }
    }
}