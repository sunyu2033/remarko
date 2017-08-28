<?php
namespace common\components;

/**
 * @name api地址集合器
 * @version 2015/12/08
 */
class ApiCollector extends \yii\base\Component {

    public static function apiUrl($name='') {
        $url = array(
            // ++++++++++++++++++++++++ 本站接口中心  +++++++++++++++++++++++++
            'Iapi' 				=> 'http://zz.253.com/api/index',
        );

        return $name ? $url[$name] : 'unknown uri';
    }

}