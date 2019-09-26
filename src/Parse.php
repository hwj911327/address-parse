<?php
/**
 * Created by PhpStorm.
 * User: hwj91
 * Date: 2019/9/25
 * Time: 18:50
 */

namespace AddressParse;

class Parse
{
    public function __construct()
    {
    }


    public static function hello()
    {
        echo 'hello word';
    }


    public static function smartParse($address)
    {
        //解析结果
        $parse = [
            'name'     => '',
            'mobile'   => '',
            'idno'     => '',
            'postcode' => '',
            'detail'   => '',
        ];
        //1. 过滤掉收货地址中的常用说明字符，排除干扰词
        $search  = ['收货地址', '地址', '收货人', '收件人', '收货', '邮编', '电话', '身份证号码', '身份证号', '身份证', '：', ':', '；', ';', '，', ',', '。',];
        $replace = [' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '];
        $address = str_replace($search, $replace, $address);
        //2. 把空白字符(包括空格\r\n\t)都换成一个空格
        $address = preg_replace('/\s{1,}/', ' ', $address);
        //3. 去除手机号码中的短横线 如136-3333-6666 主要针对苹果手机
        $address = preg_replace('/0-|0?(\d{3})-(\d{4})-(\d{4})/', '$1$2$3', $address);
        //4. 提取中国境内身份证号码
        preg_match('/\d{18}|\d{17}X/i', $address, $match);
        if ($match && $match[0]) {
            $parse['idno'] = strtoupper($match[0]);
            $address       = str_replace($match[0], '', $address);
        }
        //5. 提取11位手机号码或者7位以上座机号
        preg_match('/\d{7,11}|\d{3,4}-\d{6,8}/', $address, $match);
        if ($match && $match[0]) {
            $parse['mobile'] = $match[0];
            $address         = str_replace($match[0], '', $address);
        }
        //6. 提取6位邮编 邮编也可用后面解析出的省市区地址从数据库匹配出
        preg_match('/\d{6}/', $address, $match);
        if ($match && $match[0]) {
            $parse['postcode'] = $match[0];
            $address           = str_replace($match[0], '', $address);
        }
        //再次把2个及其以上的空格合并成一个，并首位TRIM
        $address = trim(preg_replace('/ {2,}/', ' ', $address));
        //按照空格切分 长度长的为地址 短的为姓名 因为不是基于自然语言分析，所以采取统计学上高概率的方案
        $split_arr = explode(' ', $address);
        if (count($split_arr) > 1) {
            $parse['name'] = $split_arr[0];
            foreach ($split_arr as $value) {
                if (strlen($value) < strlen($parse['name'])) {
                    $parse['name'] = $value;
                }
            }
            $address = trim(str_replace($parse['name'], '', $address));
        }
        $parse['detail'] = $address;

        return $parse;
    }
}