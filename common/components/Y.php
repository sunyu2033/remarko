<?php
namespace common\components;
/**
 * Created by PhpStorm.
 * User: Samsun
 * Date: 2017/5/16
 * Time: 22:09
 */

use Yii;

class Y extends  \yii\base\Component{

    /**
     * SQL查询
     * @param   [string]    $sql    [sql]
     * @return  [object]    $arr    [discription]
     */
    public static function M($sql) {
        return Yii::$app->db->createCommand($sql);
    }

    /**
     * SQL查询
     * @param   [string]    $sql    [sql]
     * @return  [object]    $arr    [discription]
     */
    public static function I() {
        return Yii::$app->db->getLastInsertID();
    }

    /**
     * 获取params
     */
    public static function P() {
        return Yii::$app->params;
    }

    /**
     * 获取db
     */
    public static function DB() {
        return Yii::$app->db;
    }

    /**
     * 获取request
     */
    public static function R() {
        return Yii::$app->request;
    }

    /**
     * session操作
     */
    public static function S() {
        return Yii::$app->session;
    }

    /**
     * response
     */
    public function Response() {
        return Yii::$app->getResponse();
    }

    /**
     * 获取用户实例
     */
    public static function U() {
        return Yii::$app->user;
    }

    /**
     * 获取用户customer_id
     */
    public static function uid(){
        return Y::U()->id;
    }

    /**
     * 生成密码
     */
    public static function makePassword($password=''){
        $params = Y::P();
        $prefix = $params['sy_prefix'];
        return md5($prefix.$password);
    }

    /**
     * 获取配置值
     * @param string $name
     *        'path.to:name' 如果需要获取特定值，path.to后面加":名称"来获取
     * @param mixed $default
     *
     * 与Y::P()的功能类似
     */
    public static function getConfig($name, $default = null) {
        static $_result = array ();

        if (! $name || empty ( $name )) {
            return $default;
        }

        // 如果要获取某个特定配置项的值
        if (strpos ( $name, ':' ) > 0) {
            $names = explode ( ':', $name );
            $file = $names [0];
            $configName = $names [1];
        } else {
            $file = $name;
            $configName = '';
        }

        $file = str_replace ( '.', '/', $file );

        $file = Yii::$app->getBasePath () . '/config/' . $file . '.php';

        if (! isset ( $_result [$name] )) {
            $_result [$name] = require $file;
        }

        $config = $_result [$name];

        if (! empty ( $configName ) && isset ( $config [$configName] )) {
            return $config [$configName];
        }

        return $config;
    }

    /**
     * 判断当前服务器系统
     * @return string
     */
    public static function getOS(){
        if(PATH_SEPARATOR == ':') {
            return 'Linux';
        }else{
            return 'Windows';
        }
    }

    //获取用户头像
    public static function getImage(){
        $image= Y::D()->createCommand("SELECT image FROM ".CustomerAccount::model()->tableName()." WHERE id=".Y::U()->getId())->queryScalar();
        if($image){
            $image=Y::R()->hostInfo.'/'.$image;
        }else{
            $image=Yii::$app->theme->baseUrl.'/static/image/avatar.jpg';
        }
        return $image;
    }
}