<?php

namespace travelsoft\reviews;

/**
 * Статистика по отзывам
 *
 * @author dimabresky
 * @copyright (c) 2018, travelsoft  
 */
class Statistics {

    /**
     * @var int 
     */
    protected $_total_cnt = 0;
    
    protected $_reviews = null;

    /**
     * @param int $element_id
     */
    public function __construct(int $element_id) {

        $this->_reviews = new Reviews;
        
        $this->_total_cnt = $this->_reviews->getCount();
    }
    
    /**
     * @param int $stars
     * @return array
     */
    public function getForStars(int $stars) {

        $cnt = $this->_reviews->stars($stars)->getCount();
        return array(
            "stars" => $stars,
            "cnt" => $cnt,
            "percent" => (float)round((100 * $cnt / $this->_total_cnt), 1)
        );
    }

    /**
     * @return array
     */
    public function get() {
        
        if (!$this->_total_cnt) {
            return array(
                "stars" => array(),
                "total_count" => 0,
                "middle" => 0
            );
        }
        
        $statistics = array();
        for ($stars = \Bitrix\Main\Config\Option::get("travelsoft.reviews", "MAX_RATING_VALUE"); $stars >=1; $stars--) {
            
            $stat = $this->getForStars($stars);
            $statistics["stars"][] = $stat;

            $total_stars += $stat["cnt"] * $stars;
        }

        $statistics["total_count"] = $this->_total_cnt;
        $statistics["middle"] = round($total_stars / $this->_total_cnt, 1);

        return $statistics;
    }

}
