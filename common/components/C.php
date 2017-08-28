<?php
namespace common\components;
/**
 * Created by PhpStorm.
 * User: Samsun
 * Date: 2017/5/16
 * Time: 22:09
 */

use \common\components\ApiCollector;

class C {

    /**
     * 转换数组,将指定value转换成key值
     * @param   [array]     $temp   [原始数据]
     * @param   [string]    $key    [指定键名]
     * @return  [array]     $arr    [新数据]
     */
    public static function keyArrayByPrimary($temp, $key) {
        $arr = array();
        foreach($temp as $val) {
            $arr[$val[$key]] = $val;
        }
        return $arr;
    }

    /**
     * 转换对象成为id=>name的数组
     */
    public static function objToArray($objs,$id,$name) {
        $arrs=array();
        foreach($objs as $val){
            $arrs[$val->{$id}]=$val->{$name};
        }
        return $arrs;
    }

    /**
     * 转换数组为可识别类型
     */
    public static function arrayToNormal($arr) {
        if(!is_array($arr)){
            return false;
        }
        $str='';
        foreach($arr as $key=>$val){
            if(is_numeric($key)){
                $str.="{$val},";
            }else{
                $str.="{$key}={$val},";
            }
        }
        $str=substr($str,0,-1);
        return $str;
    }

    /**
     * base64URL加密
     */
    public static function base64Encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * base64URL解密
     */
    public static function base64Decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * log日志
     */
    public static function log($msg){
        Yii::log(urldecode(json_encode($msg)),'info');
    }


    /**
     * utf-8和gb2312自动转化
     */
    public static function safeEncoding($string, $outEncoding = 'UTF-8') {
        $encoding = "UTF-8";
        for($i = 0; $i < strlen ( $string ); $i ++) {
            if (ord ( $string {$i} ) < 128)
                continue;

            if ((ord ( $string {$i} ) & 224) == 224) {
                // 第一个字节判断通过
                $char = $string {++ $i};
                if ((ord ( $char ) & 128) == 128) {
                    // 第二个字节判断通过
                    $char = $string {++ $i};
                    if ((ord ( $char ) & 128) == 128) {
                        $encoding = "UTF-8";
                        break;
                    }
                }
            }
            if ((ord ( $string {$i} ) & 192) == 192) {
                // 第一个字节判断通过
                $char = $string {++ $i};
                if ((ord ( $char ) & 128) == 128) {
                    // 第二个字节判断通过
                    $encoding = "GB2312";
                    break;
                }
            }
        }

        if (strtoupper ( $encoding ) == strtoupper ( $outEncoding ))
            return $string;
        else
            return @iconv ( $encoding, $outEncoding, $string );
    }

    /**
     * 身份证验证
     */
    protected static function checkIdCard($idcard='') {
        if(empty($idcard)) return false;
        $City = array(11=>"北京",12=>"天津",13=>"河北",14=>"山西",15=>"内蒙古",21=>"辽宁",22=>"吉林",23=>"黑龙江",31=>"上海",32=>"江苏",33=>"浙江",34=>"安徽",35=>"福建",36=>"江西",37=>"山东",41=>"河南",42=>"湖北",43=>"湖南",44=>"广东",45=>"广西",46=>"海南",50=>"重庆",51=>"四川",52=>"贵州",53=>"云南",54=>"西藏",61=>"陕西",62=>"甘肃",63=>"青海",64=>"宁夏",65=>"新疆",71=>"台湾",81=>"香港",82=>"澳门",91=>"国外");
        $iSum = 0;
        $idCardLength = strlen($idcard);
        //长度验证
        if(!preg_match('/^\d{17}(\d|x)$/i',$idcard) and !preg_match('/^\d{15}$/i',$idcard)) {
            return false;
        }
        //地区验证
        if(!array_key_exists(intval(substr($idcard,0,2)),$City)) {
            return false;
        }
        // 15位身份证验证生日，转换为18位
        if ($idCardLength == 15) {
            $sBirthday = '19'.substr($idcard,6,2).'-'.substr($idcard,8,2).'-'.substr($idcard,10,2);
            $d = new DateTime($sBirthday);
            $dd = $d->format('Y-m-d');
            if($sBirthday != $dd) {
                return false;
            }
            $idcard = substr($idcard,0,6)."19".substr($idcard,6,9);//15to18
            $Bit18 = $this->getVerifyBit($idcard);//算出第18位校验码
            $idcard = $idcard.$Bit18;
        }
        // 判断是否大于2078年，小于1900年
        $year = substr($idcard,6,4);
        if ($year<1900 || $year>2078 ) {
            return false;
        }

        //18位身份证处理
        $sBirthday = substr($idcard,6,4).'-'.substr($idcard,10,2).'-'.substr($idcard,12,2);
        $d = new DateTime($sBirthday);
        $dd = $d->format('Y-m-d');
        if($sBirthday != $dd) {
            return false;
        }
        //身份证编码规范验证
        $idcard_base = substr($idcard,0,17);
        if(strtoupper(substr($idcard,17,1)) != $this->getVerifyBit($idcard_base)) {
            return false;
        }
        return $idcard;
    }

    // 计算身份证校验码，根据国家标准GB 11643-1999
    private function getVerifyBit($idcard_base) {
        if(strlen($idcard_base) != 17) {
            return false;
        }
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }

    /**
     * 获取用户IP
     */
    public static function getIps() {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $IP = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $IP = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $IP = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $IP = $_SERVER['REMOTE_ADDR'];
        }
        return $IP ? $IP : "unknow";
    }

    /**
     * 手机号加*
     */
    public static function hidetel($phone='') {
        $IsWhat = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i',$phone); //固定电话
        if($IsWhat == 1){
            return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i','$1****$2',$phone);
        }else{
            return  preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2',$phone);
        }
    }

    /**
     * 用curl发表POST请求
     * @param string $url
     * @param string $params
     */
    public static function curlPost( $url, $params = array(), $catagoray = 'tornado api', $isUrl = false){
        $url = $isUrl ? ApiCollector::apiUrl($url) : $url;
        $ch=curl_init ();
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($params));
        curl_setopt($ch, CURLOPT_TIMEOUT,3);
        $result=curl_exec($ch);
        Yii::log('请求地址：' . $url, 'info', $catagoray);
        Yii::log('请求参数：' . json_encode($params), 'info', $catagoray);
        Yii::log('返回结果：' . json_encode($result), 'info', $catagoray);
        curl_close($ch);
        return $result;
    }

    /**
     * 用curl发表GET请求
     * @param string $url
     * @param string $params
     */
    public static function curlGet( $url, $catagoray = 'tornado api', $query = '', $isUrl = false){
        $url = $isUrl ? ApiCollector::apiUrl($url) : $url;
        if (!empty($query)) {
            $query = "?" . http_build_query($query);
        }
        $ch=curl_init ();
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_URL,$url.$query);
        $result=curl_exec($ch);
        Yii::log('请求地址：' . $url, 'info', $catagoray);
        curl_close($ch);
        return $result;
    }


    /**
     * 格式化数据的树
     * @param array $items
     * @param intger $id
     * @param intger $pid
     * @param string $child
     */
    public static function genTree9($items, $id='id', $pid='parent_id', $child='children') {
        $tree = array();
        foreach ($items as $item) {
            if (isset($items[$item[$pid]])) {
                $items[$item[$pid]][$child][] = &$items[$item[$id]];
            } else {
                $tree[] = &$items[$item[$id]];
            }
        }
        return $tree;
    }


    /**
     * 计算UTF8的字符串长度
     * @param unknown $str
     * @param string $encoding
     * @return number
     */
    public static function utf8Strlen($str) {
        $i = 0;
        $count = 0;
        $len = strlen ($str);
        while ($i < $len) {
            $chr = ord ($str[$i]);
            $count++;
            $i++;
            if($i >= $len) break;
            if($chr & 0x80) {
                $chr <<= 1;
                while ($chr & 0x80) {
                    $i++;
                    $chr <<= 1;
                }
            }
        }
        return $count;
    }

    public static function validIp($ip) {
        return preg_match('/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/',$ip);
    }

    public static function strToHex($string) {
        $hex = "";
        for($i=0;$i<strlen($string);$i++) {
            $hex.=dechex(ord($string[$i]));
        }
        return $hex;
    }

    /**
     * 获取数组中某个值
     * @param array $data
     * @param string $field
     * @return string
     */
    public static function getArrValue( $data = array(), $field = '', $default = null ){
        if( empty($data) || empty($field) ){
            return $default;
        }
        if( !isset($data[$field]) ){
            return $default;
        }
        return $data[$field];
    }

    /**
     * 解析数据库的字段的值
     * @param array $row
     */
    public static function compose_field_value( $row ) {
        if ( empty($row) ) return ;
        $res = "(";
        foreach ($row as $field) {
            $res .= "'". ( $field ). "',";
        }
        $res = substr($res, 0, -1). ")";

        return $res;
    }

    /**
     *根据值获取对应时间戳
     **/
    public static function getTime($tag=''){
        $s_start=0;
        $s_end=strtotime("now")+1;
        switch($tag){
            case 1:
                $s_start=strtotime(date('Y').'-'.date('m').'-'.'01');
                break;
            case 2:
                $s_start=strtotime(date('Y-m-d'));
                break;
            case 3:
                $s_start=strtotime(date('Y-m-d',strtotime('-1 day')));
                $s_end=strtotime(date('Y-m-d',strtotime('-1 day')).' 23:59:59');
                break;
            case 4:
                $s_start=strtotime(date('Y-m-d',strtotime('-6 day')));
                break;
            case 5:
                $s_start=strtotime(date('Y-m-d',strtotime('-29 day')));;
                break;
            default:
                break;

        }
        return array('s_start'=>$s_start,
            's_end'=>$s_end
        );
    }

    //数组重组
    public static function newArr($obj,$key1,$key2){
        $newArr=array();
        if(is_array($obj)){
            foreach($obj as $key=>$val){
                $newArr[$val[$key1]]=$val[$key2];
            }
        }elseif(is_object($obj)){
            foreach($obj as $key=>$val){
                $newArr[$val->{$key1}]=$val->{$key2};
            }
        }else{
            return false;
        }
        return $newArr;
    }


    /**
     * ajax返回值
     *
     * status==success，操作成功
     * status==error，操作失败
     * status==url，跳转，跳转到msg
     */
    public static function ajaxRtn($status, $msg = '', $url = '', $isReturn=false) {
        $ret = json_encode ( array (
            'status' => $status,
            'msg' => $msg,
            'url' => $url,
        ) );
        if (!$isReturn) {
            echo $ret;
        } else {
            return $ret;
        }

        exit ();
    }
}