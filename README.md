# 收货地址智能解析

将连在一起的收获信息解析成单个信息方便录入.

项目不能保证100%解析成功，从生使用情况来看，解析成功率保持在99%以上。

如果有什么问题或建议，可以
提交[Github Issue](https://github.com/hwj911327/address-parse/issues)

## 解析地址 

```php
$address    = '身份证号：51250119910927226x 收货地址张三收货地址：成都市武侯区美领馆路11号附2号 617000  136-3333-6666 ';
$res        = AddressParse::getDetail($address)
```

返回：
```php
array(5) {
  ["name"]=>
  string(6) "张三"
  ["mobile"]=>
  string(11) "13633336666"
  ["postcode"]=>
  string(6) "617000"
  ["idno"]=>
  string(18) "51250119910927226X"
  ["detail"]=>
  string(42) "成都市武侯区美领馆路11号附2号"
}
```


## 2. 把收货地址解析成省、市、区县、街道小区地址
使用parse_detail.php文件中的$obj = AddressDetail::detail_parse($str)方法，该静态方法接受字符串，同样返回数组。但该文件要配合项目的的地址库 area.sql 才能使用，如：
```php
AddressDetail::detail_parse('成都市高新区天府软件园B区科技大楼');
```

返回数组
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

## 3. 联系作者
* Email：hwj911327@qq.com
* QQ: 312434990
