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

    protected $_filter = array();

    /**
     * @param int $element_id
     */
    public function __construct(int $element_id) {

        $this->_filter = array(
            "IBLOCK_ID" => \Bitrix\Main\Config\Option::get("travelsoft.reviews", "REVIEWS_IBLOCK_ID"),
            "PROPERTY_LINK_ELEMENT_ID" => $element_id,
            "ACTIVE" => "Y"
        );
        
        $this->_total_cnt = \CIBlockElement::GetList(array(), $this->_filter, array(), false);
    }
    
    /**
     * @param int $stars
     * @return array
     */
    public function getForStars(int $stars) {

        $this->_filter["PROPERTY_RATING"] = $stars === 0 ? false : $stars;
        $cnt = \CIBlockElement::GetList(array(), $this->_filter, array(), false);
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
