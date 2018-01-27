<?php
namespace travelsoft\reviews;
/**
 * Адаптер для механизма кеширования
 *
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class Cache {
    /**
     * @var \Bitrix\Main\Data\Cache
     */
    protected $_cache = null;
    /**
     * @var string
     */
    protected $_id = null;
    /**
     * @var string
     */
    protected $_dir = null;
    /**
     * @var int
     */
    protected $_time = null;
    /**
     * @param string  $id
     * @param string  $dir
     * @param int $time в секундах
     */
    public function __construct(string $id, string $dir = "/travelsoft/reviews", int $time = 3600) {
        $this->_cache = \Bitrix\Main\Data\Cache::createInstance();
        $this->_id = $id;
        $this->_dir = $dir;
        $this->_time = $time;
    }
    /**
     * Получение из кеша
     * @return array
     */
    public function get () {
        $result = array();
        if ($this->_cache->initCache($this->_time, $this->_id, $this->_dir)) {
            $result = $this->_cache->getVars();
        }
        return $result;
    }
    /**
     * Кеширование информации
     * @param callable $callback
     * @return array
     */
    public function caching (callable $callback) {
        $result = array();
        if ($this->_cache->startDataCache()) {
            $result = (array)$callback();
            if (is_array($result) && !empty($result)) {
                $this->_cache->endDataCache($result);
            } else {
                $this->_cache->abortDataCache();
            }
        }
        return $result;
    }
    
    /**
     * @return string
     */
    public function getCacheDir() {
        return $this->_dir;
    }
}