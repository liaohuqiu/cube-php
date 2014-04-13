---
layout:         "default"
title:          "规范和约定"
lead:           ""
---

#代码风格

---

### 文件

1. PHP标记仅用`<?php`

2. 代码文件UTF8编码，无BOM

3. 如果是单纯php文件，文件结尾不用`?>`符号

3. 一个文件，只放一个类。

4. 一个代码文件或者只定义符号，比如类，方法或者常量；或者只处理业务逻辑。不要同时


### 命名

命名规范为的是提高团队工作效率，提高代码可维护性。规范涵盖了大部分的情况，但只要有足够的理由，就可以处于例外。

1.  命名空间和类名首字母大写: `UserInfo`；

2.  常量全部大写，下划线分隔，包括类常量和用`define`定义的常量；比如：`STATUS_UNKNOW`；

3.  类的方法名，驼峰式: `getUserInfo()`;

4.  类的静态变量或者实例变量以及方法中的变量，驼峰和小写下划线比如：`$userInfo`, `$user_info` 都允许，但是 **要在一个范围内保持一致**。

    比如：方法范围内，类范围内，命名空间访问内等。
    
    不可使用首字母大写命名方式。对于数组中的键，建议用小写下划线分隔: `$data['begin_time']`。

5.  php中的[关键字](http://php.net/manual/en/reserved.keywords.php)和几个常量，用小写：`true`, `false`, `null`.

6.  类的私有变量，不建议用 <s>`private $_name`</s>。

### 缩进和样式

1.  缩进4个空格，不使用tab。

2.  行尾不应该留任何空格。

3.  一行不声明多个变量。

2.  保持一个空格。

    符号和变量数值之间，保持一个空格；`if` , `for` 等关键字后一个空格；方法各个参数的逗号后一个空格；

    ```php
    $a = 10;

    if ($a == 1)

    private function getUserInfo($uid, $email)
    ```

3.  各个方法之间，仅空一行，代码块之间，根据逻辑，适当空行。

4.  类和方法：

    ```php
    class UserInfo
    {
        public function getUserInfo()
        {
        }
    }
    ```

5.  条件或者循环等流程语句，以下两种都是允许的:

    ```php
    if ($age == 10) {

    }
    ```

    ```php
    foreach ($data as $k => $v)
    {

    }
    ```

    选择自己喜欢的，但是一定要 **在一个范围内保持一致**。

4.  条件判断循环等流程后，即使仅一行，也包含在花括号中。

    ```php
    if ($remain == 0) {
        $stop = true;
    }
    ```

5.  多行：

    ```
    // 方法多行
    public function aVeryLongMethodName(
            ClassTypeHint $arg1,
            &$arg2,
            array $arg3 = []
    ) {
        // 方法体
    }

    // 调用多行
    $foo->bar(
            $longArgument,
            $longerArgument,
            $muchLongerArgument
    );

    // 闭包多行
    $longArgs_longVars = function (
        $longArgument,
        $longerArgument,
        $muchLongerArgument
    ) use (
        $longVar1,
        $longerVar2,
        $muchLongerVar3
    ) {
        // 方法体
    };

#自动加载规则

---

### 命名规则

1.  顶级必须为包名，任何类都必须顶级以下：

    ```php
    \<Vendor Name>\(<Namespace>\)*<Class Name>
    ```

2.  PHP5.2 及其以下，通过 `MCore_Tool_Array`这样的形式来实现伪命名空间。

    `Cube` 用`M` 来表示顶级命名空间。

3.  类名和空间名都首字母大写。空间分隔符 `\` 以及 下划线 `_` 转化为路径分隔符 '/'：

4.  例子：

    | 命名 | 路径 |
    |---|---|
    |`\Zend\Mail\Message`| `/path/to/project/lib/vendor/Zend/Mail/Message.php`|
    |`\namespace\package\Class_Name` | `/path/to/project/lib/vendor/namespace/package/Class/Name.php`|
    |`\namespace\package_name\Class_Name` | `/path/to/project/lib/vendor/namespace/package_name/Class/Name.php`|

