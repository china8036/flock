<?php

/**
 * Copyright 2017 QQES, Inc.
 * 
 */

namespace Qqes\Flock;

class Flock {

    /**
     * @const FILE_EXT lock file ext
     */
    const FILE_EXT = '.lock';

    /**
     *
     * @var string the path of lock file put
     */
    protected $path;

    /**
     *
     * @var array name map resource file array 
     */
    private $_fp = [];

    public function __construct($path) {
        if (!is_dir($path)) {
            throw new Exception('path not found', Exception::PATH_NOT_FOUND);
        }
        $this->path = $path;
        register_shutdown_function($this, 'shutDown');
    }

    /**
     * 锁定一个文件
     * @param string $name 锁定的名字
     * @return boolean
     */
    public function lock($name) {
        $encodeName = $this->encodeName($name);
        $this->_fp[$encodeName] = fopen($this->path . DIRECTORY_SEPARATOR . $encodeName . self::FILE_EXT, 'w+');
        if (flock($this->_fp[$encodeName], LOCK_EX)) {
            return true;
        }
        $this->closeFile($encodeName);
        return false;
    }

    /**
     * 释放锁定
     * @param string $name
     */
    public function unLock($name) {
        $encodeName = $this->encodeName($name);
        flock($this->_fp[$encodeName], LOCK_UN);
        $this->closeFile($encodeName); //锁定失败释放资源
    }

    /**
     * 根据名称获取文件
     * @param string $encodeName
     * @return string
     */
    protected function getFileByEncodeName($encodeName) {
        return $this->path . DIRECTORY_SEPARATOR . $encodeName . self::FILE_EXT;
    }

    /**
     * 关闭文件 包裹删除文件 等
     * @param type $encodeName
     */
    protected function closeFile($encodeName) {
        fclose($this->_fp[$encodeName]); //释放资源
        @unlink($this->getFileByEncodeName($encodeName));//删除文件
        unset($this->_fp[$encodeName]);//清除键值
    }

    /**
     * 注册进程完毕清理方法
     */
    protected function shutDown() {
        if (empty($this->_fp)) {//为空的话 跳过处理步骤
            return;
        }
        $fpEncodeName = array_keys($this->_fp);
        foreach ($fpEncodeName as $encodeName) {//清除遗留
            $this->closeFile($encodeName);
        }
    }

    /**
     * 编码name
     * @param string $name
     * @return string
     */
    protected function encodeName($name) {
        return md5($name);
    }

}
