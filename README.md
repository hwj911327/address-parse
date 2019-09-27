# 收货地址智能解析

将连在一起的收获信息解析成单个信息方便录入.

项目不能保证100%解析成功，从使用情况来看，解析成功率保持在99%以上。

如果有什么问题或建议，可以
提交[Github Issue](https://github.com/hwj911327/address-parse/issues)

## 解析地址 :

### 1.调用方法
```php
$address    = '身份证号：51250119910927226x 收货地址张三收货地址：成都市武侯区美领馆路11号附2号 617000  136-3333-6666 ';
$res        = AddressParse::getDetail($address)
```

### 2.返回数组
```php
[
    'name'              => '张三',
    'mobile'            => 13633336666,
    'id_card'           => '51250119910927226X',
    'postcode'          => 617000,
    'province'          => [
        'code' => 620000,
        'name' => '甘肃省',
    ],
    'city'              => [
        'code' => 621200,
        'name' => '陇南市',
    ],
    'district'          => [
        'code' => 621221,
        'name' => '成县',
    ],
    'formatted_address' => '市武侯区美领馆路11号附2号'
];
```

## 联系作者:
* Email：hwj911327@qq.com
* QQ: 312434990
