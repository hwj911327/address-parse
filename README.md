# 收货地址智能解析

将连在一起的收获信息解析成单个信息方便录入.

项目不能保证100%解析成功，从使用情况来看，解析成功率保持在99%以上。

如果有什么问题或建议，可以
提交[Github Issue](https://github.com/hwj911327/address-parse/issues)

## 解析地址 :

### 1.调用方法
```php
$address    = '身份证号：51250119910927226x 收货地址张三收货地址：成都市武侯区美领馆路11号附2号 617000  136-3333-6666 ';
$gd_key     = '******' //高德开放平台(https://lbs.amap.com/)key 非必填 (使用后可提高识别精度)
$res        = AddressParse::getDetail($address,$gd_key)
```

### 2.返回数组
```php
[
    'name'              => '张三',
    'mobile'            => 13633336666,
    'id_card'           => '51250119910927226X',
    'zip_code'          => 617000,
    'province'          => [
        'code' => 510000,
        'name' => '四川省',
    ],
    'city'              => [
        'code' => 510100,
        'name' => '成都市',
    ],
    'district'          => [
        'code' => 510107,
        'name' => '武侯区',
    ],
    'formatted_address' => '美领馆路11号附2号'
];
```

## 联系作者:
* Email：hwj911327@qq.com
* QQ: 312434990
