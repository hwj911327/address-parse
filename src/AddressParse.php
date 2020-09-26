<?php
/**
 * Created by PhpStorm.
 * User: hwj91
 * Date: 2019/9/25
 * Time: 18:50
 */

namespace AddressParse;

use GuzzleHttp\Client;

class AddressParse
{
    public static function getDetail($address, $gd_key = '')
    {
        //解析用户信息
        $userDetail = self::_getUserDetail($address);

        //解析地址详情
        $addressDetail = self::_getAddressDetail($userDetail['address'], $gd_key);

        return array_merge($userDetail, $addressDetail);
    }

    protected static function _getUserDetail($address)
    {
        //解析结果
        $detail = [
            'name'     => '',
            'mobile'   => '',
            'id_card'  => '',
            'zip_code' => '',
        ];

        //1. 过滤掉收货地址中的常用说明字符，排除干扰词
        $address = preg_replace(
            "/收货地址|地址|收货人|收件人|收货|邮编|电话|身份证号码|身份证号|身份证|：|:|；|;|，|,|。|\.|“|”|\"/",
            ' ',
            $address
        );

        //2. 把空白字符(包括空格\r\n\t)都换成一个空格,去除首位空格
        $address = trim(preg_replace('/\s{1,}/', ' ', $address));

        //3. 去除手机号码中的短横线 如136-3333-6666 主要针对苹果手机
        $address = preg_replace('/0-|0?(\d{3})-(\d{4})-(\d{4})/', '$1$2$3', $address);

        //4. 提取中国境内身份证号码
        preg_match('/\d{18}|\d{17}X/i', $address, $match);
        if ($match && $match[0]) {
            $detail['id_card'] = strtoupper($match[0]);
            $address           = str_replace($match[0], '', $address);
        }

        //5. 提取11位手机号码或者7位以上座机号
        preg_match('/\d{7,11}|\d{3,4}-\d{6,8}/', $address, $match);
        if ($match && $match[0]) {
            $detail['mobile'] = $match[0];
            $address          = str_replace($match[0], '', $address);
        }

        //6. 提取6位邮编 邮编也可用后面解析出的省市区地址从数据库匹配出
        preg_match('/\d{6}/', $address, $match);
        if ($match && $match[0]) {
            $detail['postcode'] = $match[0];
            $address            = str_replace($match[0], '', $address);
        }

        //再次把2个及其以上的空格合并成一个，并首位TRIM
        $address = trim(preg_replace('/ {2,}/', ' ', $address));
        //按照空格切分 长度长的为地址 短的为姓名 因为不是基于自然语言分析，所以采取统计学上高概率的方案
        $split_arr = explode(' ', $address);
        if (count($split_arr) > 1) {
            $detail['name'] = $split_arr[0];
            foreach ($split_arr as $value) {
                if (strlen($value) < strlen($detail['name'])) {
                    $detail['name'] = $value;
                }
            }
            $address = trim(str_replace($detail['name'], '', $address));
        }
        $detail['address'] = $address;

        return $detail;
    }

    protected static function _getAddressDetail($address, $gd_key = '')
    {

        $detail            = [
            'province'          => ['code' => '', 'name' => ''],
            'city'              => ['code' => '', 'name' => ''],
            'district'          => ['code' => '', 'name' => ''],
            'formatted_address' => '',
        ];
        $address           = preg_replace('/-|_/', '', $address);
        $formatted_address = preg_replace('/^(\D+?)(市)/', '', $address);
        $formatted_address = preg_replace('/^(\D+?)(区|县|旗)/', '', $formatted_address);


        //1. 过滤干扰字段
        $area = include 'area.php';

        //匹配 三级地址 这里将【县，区，旗，市】去掉,都江堰市->都江堰
        $arr = [];
        foreach ($area as $value) {
            if (mb_strstr($address, mb_substr(mb_substr($value[2], 0, -1), 7))) {
                array_push($arr, $value);
            }
        }

        //二级地址 过滤 将没匹配上的剔除
        if ($arr && count($arr) > 1) {
            $arr2 = [];
            foreach ($arr as $k => $value) {
                if (mb_strstr($address, mb_substr(mb_substr($value[1], 0, -1), 7))) {
                    $arr2[] = $value;
                }
            }
            $arr2 and $arr = $arr2;
        }

        //一级级地址 过滤
        if ($arr && count($arr) > 1) {
            $arr2 = [];
            foreach ($arr as $k => $value) {
                if (mb_strstr($address, mb_substr(mb_substr($value[0], 0, -1), 7))) {
                    $arr2[] = $value;
                }
            }
            $arr2 and $arr = $arr2;
        }

        //多个情况带上【县，区，旗，市】 在匹配一次 如果能匹配上使用新的
        if ($arr && count($arr) > 1) {
            $arr2 = [];
            foreach ($arr as $k => $value) {
                if (mb_strstr($address, mb_substr($value[2], 7))) {
                    $arr2[] = $value;
                }
            }
            $arr2 and $arr = $arr2;
        }

        //基本走到这里 过滤的差不多了 只剩一个了  目前没发现多个 如果有 后续修改
        if ($arr) {
            //如果还有多个(情感上是不存在的) 就返回第一个把.....
            $arr    = current($arr);
            $detail = [
                'province'          => [
                    'code' => mb_substr($arr[0], 0, 6),
                    'name' => mb_substr($arr[0], 7)
                ],
                'city'              => [
                    'code' => mb_substr($arr[1], 0, 6),
                    'name' => mb_substr($arr[1], 7)
                ],
                'district'          => [
                    'code' => mb_substr($arr[2], 0, 6),
                    'name' => mb_substr($arr[2], 7)
                ],
                'formatted_address' => trim($formatted_address),
            ];
        }


        //使用高德地图进一步分析
        if ($gd_key) {
            $url      = "https://restapi.amap.com/v3/geocode/geo?key={$gd_key}&s=rsv3&batch=true&address={$address}";
            $client   = new Client();
            $response = $client->get($url, ['http_errors' => false]);
            $code     = $response->getStatusCode();

            if ($code == 200) {
                $body = json_decode($response->getBody()->getContents(), true);
                if (isset($body['geocodes'][0])) {
                    $body = $body['geocodes'][0];
                    if ($detail['province']['code'] == mb_substr($body['adcode'], 0, 2) . '0000') {
                        $detail['formatted_address'] = $formatted_address ?: $body['formatted_address'];
                    } else {
                        $detail = [
                            'province'          => [
                                'code' => mb_substr($body['adcode'], 0, 2) . '0000',
                                'name' => $body['province'] ?: ''
                            ],
                            'city'              => [
                                'code' => $body['city'] ? mb_substr($body['adcode'], 0, 4) . '00' : '',
                                'name' => $body['city'] ?: ''
                            ],
                            'district'          => [
                                'code' => $body['district'] ? $body['adcode'] : '',
                                'name' => $body['district'] ?: ''
                            ],
                            'formatted_address' => $formatted_address ?: $body['formatted_address'],
                        ];
                    }
                }
            }
        }


        return $detail;
    }
}