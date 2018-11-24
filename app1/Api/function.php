<?php

/**
 * 实现将数组按照值分组重组
 * @param  [array] $array 要重组的数组
 * @param  [key]  分组值的键名
 * @return [array]        新的数组
 */
function array_val_chunk($array, $group)
{
    $result = array();
    foreach ($array as $key => $value) {
        $result[$value[$group]][] = $value;
    }
    $ret = array();
    //这里把简直转成了数字的，方便同意处理
    foreach ($result as $key => $value) {
        array_push($ret, $value);
    }
    return $ret;
}

/**
 * 随机数 nonce_str 生成函数
 * @long 随机数长度
 */
function create_nonce_str($long = 20)
{
    $str   = 'abcdefghijklmnopqrstuvwxyzABCDEFGHHIJKLMNOPQRSTUVWXYZ0123456789';
    $len   = strlen($str) - 1;
    $nonce = '';
    for ($i = 0; $i < $long; $i++) {
        $nonce .= $str[mt_rand(0, $len)];
    }
    return $nonce;
}

/**
 * 获取用户openid
 * @param  [string] $code      wx.login获取的code
 * @param  [string] $appid     小程序appid
 * @param  [string] $appsecret 小程序appsecret
 * @return [object]            微信反回的openid等信息的组合对象
 */
function get_openid($code, $appid, $appsecret)
{
    $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $appsecret . '&js_code=' . $code . '&grant_type=authorization_code';
    $obj = json_decode(file_get_contents($url));
    if (isset($obj->errcode)) {
        return false;
    }

    return $obj->openid;
}

/**
 * [getJsApiParameters description]
 * @param  [array] $UnifiedOrderResult
 * @return json
 */
function getJsApiParameters($UnifiedOrderResult)
{
    //判断是否统一下单返回了prepay_id
    if (!array_key_exists("appid", $UnifiedOrderResult)
        || !array_key_exists("prepay_id", $UnifiedOrderResult)
        || $UnifiedOrderResult['prepay_id'] == "") {
        throw new WxPayException("参数错误");
    }
    $jsapi = new WxPayJsApiPay();
    $jsapi->SetAppid($UnifiedOrderResult["appid"]);
    $timeStamp = time();
    $jsapi->SetTimeStamp("$timeStamp");
    $jsapi->SetNonceStr(WxPayApi::getNonceStr());
    $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
    $jsapi->SetSignType("MD5");
    $jsapi->SetPaySign($jsapi->MakeSign());
    //$parameters = json_encode($jsapi->GetValues());
    $parameters = $jsapi->GetValues();
    return $parameters;
}
/**
 * [getAccessToken description]
 * @param  [type] $appid     [description]
 * @param  [type] $appsecret [description]
 * @param  [type] $mysqli    [description]
 * @return [type]            [description]
 * @param  [int] $type 1:小程序用户版  2：小程序商户版   3：公众号版本
 */
function getAccessToken($appid, $appsecret, $mysqli, $type = 1)
{
    $sql    = "select*from access_token where id =" . $type;
    $result = $mysqli->query($sql);
    if ($result === false) {
        $data = json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret), true);
        $time = time() + 7000;
        $sql2 = "insert into access_token(access_token,time) values('" . $data['access_token'] . "', {$time})";
    } else {
        $row  = $result->fetch_assoc();
        $time = time() + 7000;
        if (empty($row)) {
            $data = json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret), true);
            $sql2 = "insert into access_token(access_token,time) values('" . $data['access_token'] . "',{$time})";
        } else {
            if ($row['time'] > time()) {
                $data = $row;
            } else {
                $data = json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret), true);
                $sql2 = "update access_token set access_token='" . $data['access_token'] . "',time={$time} where id=" . $type;
            }
        }
    }
    if (isset($sql2) && !empty($sql2)) {
        $result2 = $mysqli->query($sql2);
    }

    return $data['access_token'];
}

/**
 * [https_curl_json] 发送curl请求
 * @param  [type] $url  [description]
 * @param  [type] $data [description]
 * @param  [type] $type [description]
 * @return [type]       [description]
 */
function https_curl_json($url, $data, $type)
{
    if ($type == 'json') {
//json $_POST=json_decode(file_get_contents('php://input'), TRUE);
        $headers = array("Content-type: application/json;charset=UTF-8", "Accept: application/json", "Cache-Control: no-cache", "Pragma: no-cache");
        $data    = json_encode($data);
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_TIMEOUT, 100);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $output = curl_exec($curl);
    if (curl_errno($curl)) {
        echo 'Errno' . curl_error($curl); //捕抓异常
    }
    curl_close($curl);
    return $output;
}

/**
 * 创建发送模板数据
 * @param  [type] $tempId [description]
 * @param  [type] $preId  [description]
 * @param  [type] $openId [description]
 * @param  [type] $page   [description]
 * @return [type]         [description]
 */
function createTemplateData($tempId, $preId, $openId, $page, $lastNumber, $pro_name, $num, $address, $fee)
{
    $postData = array();
    $values   = [
        "keyword1" => [
            "value" => $lastNumber,
            "color" => "#ed5c38",
        ],
        "keyword2" => [
            "value" => $pro_name,
            "color" => "#4a4a4a",
        ],
        "keyword3" => [
            "value" => date('Y-m-d H:i:s'),
            "color" => "#4a4a4a",
        ],
        "keyword4" => [
            "value" => $fee,
            "color" => "#4a4a4a",
        ],
        "keyword5" => [
            "value" => $address,
            "color" => "#4a4a4a",
        ],
        "keyword6" => [
            "value" => $num . '个',
            "color" => "#4a4a4a",
        ],
        "keyword7" => [
            "value" => '您购买的冰淇淋已经下单成功，请您于今天（' . date('Y-m-d') . '）商户下班时间前取货',
            "color" => "#4a4a4a",
        ],
    ];
    $postData['touser']           = $openId;
    $postData['template_id']      = $tempId;
    $postData['page']             = $page;
    $postData['form_id']          = $preId;
    $postData['data']             = $values;
    $postData['emphasis_keyword'] = "keyword1.DATA";
    return $postData;
}
/**
 * 二维数组排序
 * @param  [type] &$array [description]
 * @param  [type] $keyid  [description]
 * @param  string $order  [description]
 * @param  string $type   [description]
 * @return [type]         [description]
 */
function sortArray(&$array, $keyid, $order = 'asc', $type = 'number')
{
    if (is_array($array)) {
        foreach ($array as $val) {
            $order_arr[] = $val[$keyid];
        }
        $order = ($order == 'asc') ? SORT_ASC : SORT_DESC;
        $type  = ($type == 'number') ? SORT_NUMERIC : SORT_STRING;
        array_multisort($order_arr, $order, $type, $array);
    }
}

/**
 * 计算当前位置正方形的四个点
 * @param  [type]  $longitude [description]
 * @param  [type]  $latitude  [description]
 * @param  integer $distance  方圆千米范围
 * @param  integer $radius    [description]
 * @return [type]             [description]
 */
function getSquarePoint($lng, $lat, $distance = 10, $radius = 6371)
{
    $dlng = 2 * asin(sin($distance / (2 * $radius)) / cos(deg2rad($lat)));
    $dlng = rad2deg($dlng);

    $dlat = $distance / $radius;
    $dlat = rad2deg($dlat);

    $squarePoint = array(
        'left-top'     => array(
            'lat' => $lat + $dlat,
            'lng' => $lng - $dlng,
        ),
        'right-top'    => array(
            'lat' => $lat + $dlat,
            'lng' => $lng + $dlng,
        ),
        'left-bottom'  => array(
            'lat' => $lat - $dlat,
            'lng' => $lng - $dlng,
        ),
        'right-bottom' => array(
            'lat' => $lat - $dlat,
            'lng' => $lng + $dlng,
        ),
    );
    return $squarePoint;
}

/**
 * 计算两点地理坐标之间的距离
 * @param Decimal $longitude1 起点经度
 * @param Decimal $latitude1 起点纬度
 * @param Decimal $longitude2 终点经度
 * @param Decimal $latitude2 终点纬度
 * @param Int   $unit    单位 1:米 2:公里
 * @param Int   $decimal  精度 保留小数位数
 * @return Decimal
 */
function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 2, $decimal = 2)
{

    $EARTH_RADIUS = 6370.996; // 地球半径系数
    $PI           = 3.1415926;

    $radLat1 = $latitude1 * $PI / 180.0;
    $radLat2 = $latitude2 * $PI / 180.0;

    $radLng1 = $longitude1 * $PI / 180.0;
    $radLng2 = $longitude2 * $PI / 180.0;

    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;

    $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
    $distance = $distance * $EARTH_RADIUS * 1000;

    if ($unit == 2) {
        $distance = $distance / 1000;
    }

    return round($distance, $decimal);

}
