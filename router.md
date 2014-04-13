---
layout:         "default"
title:          "路由"
lead:           ""
---

#路由规则
---

### 1.基本规则

*   如下：

    | 路径 | 类 |
    | --- | ---|
    | /admin/server/info              | MApps_Admin_Server_Info             |
    | /admin/server/info/             | MApps_Admin_Server_Info             |
    | /admin/server/info.php          | MApps_Admin_Server_Info             |
    | /admin/server/info.html         | MApps_Admin_Server_Info             |
    | /admin/server/offline-list      | MApps_Admin_Server_OfflineList      |

*   带参数的静态永久链接

    | 路径 | 类 |
    | --- | ---|
    |/admin/server/info/341894/38.htm      | MApps_Admin_Server_Info           |

*   需要加规则配置：

    ```php
    array('/admin/server/info', array('server_id', 'group_id'));
    ```

    这样会获得 `server_id` 和 `group_id` 的值。

### 2. dialog / ajax

*   路径后最后加 `-ajax` 或者 `-dialog`即可：

    | 路径 | 类 |
    | --- | ---|
    |/admin/server/info-ajax         |  MApps_Admin_Server_InfoAjax         |
    |/admin/server/info-dialog       |  MApps_Admin_Server_InfoDialog       |

### 3. API

*   API 需要分版本, 高版本不存在，使用低版本, 配置存在的版本列表:

    ```php
    $api_class_list = array(
        'MApis_Admin_Server_InfoV1' => 1,
        'MApis_Admin_Server_InfoV8' => 1,
        'MApis_Admin_Server_InfoV10' => 1,
    );
    MCore_Web_Router::addApiClassList($api_class_list);
    ```

*   规则：

    | 路径 | 类 |
    | --- | --- |
    | /api/admin/server/info          | MApis_Admin_Server_Info      |
    | /api/admin/server/info.json     | MApis_Admin_Server_Info      |
    | /api/admin/server/info?v=1      | MApis_Admin_Server_InfoV1    |
    | /api/admin/server/info?v=2      | MApis_Admin_Server_InfoV1    |
    | /api/admin/server/info?v=9      | MApis_Admin_Server_InfoV8    |
    | /api/admin/server/info?v=20     | MApis_Admin_Server_InfoV10   |


### 4. 路径映射

*   配置路径映射，允许多个路径指向一个页面:

    ```php
    $list = array(
            '/admin' => 'admin/index',
            );

    MCore_Web_Router::setPathMapList($list);
    ```

