<?php
/**
 * Created by PhpStorm.
 * User: hwj
 * Date: 2019/9/26
 * Time: 14:38
 */

require_once './vendor/autoload.php';

//$address = "身份证号：51250119910927226x 收货地址张三收货地址：成都市武侯区美领馆路11号附2号 617000 136-3333-6666 ";
//$address = '成都市高新区天府软件园B区科技大楼';
//$address = '双流县郑通路社保局区52050号';
//$address = '岳市岳阳楼区南湖求索路碧灏花园A座1101';
//$address = '四川省南充市阆中市公园路25号';
//$address = '四川省阆中市公园路25号';
//$address = '四川省 凉山州美姑县xxx小区18号院';
//$address = '重庆攀枝花市东区机场路3中学校';
//$address = '渝北区渝北中学51200街道地址';
//$address = '天津天津市红桥区水木天成1区临湾路9-3-1101';
//$address = '四川省阿坝藏族羌族自治州理县***路9-3-1101';
//$address = '上海市上海城区宝山区**路9-3-1101';
//$address = '四川省成都市都江堰市**路9-3-1101';
//$address = '四川成都都江堰幸福路****号';
$address = '河南-济源市-济源市-沁园街道济源大道学苑小区';
//$address = '“广东省-深圳市-龙岗区-****小区-*栋*单元_19C”, "张三".158-6622-5533 ';
$address = '云南 昆明市 呈贡县 七甸街道 七兴街***号 ';

$sss = \AddressParse\AddressParse::getDetail($address);

print_r($sss);

