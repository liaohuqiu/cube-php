---
layout:         "default"
title:          "Cube"
lead:           ""
---

# 特点

1.  简单，容易上手。

2.  容易扩展。

3.  定制了一套规范和一套工具，方便开发。

4.  设计之初就准备着为大访问量准备的。

    这套架构是经过实践严格检验的。

# 目录结构

*   如下:

    ```bash
    +--+
       |
       +- app/                      控制台程序
       +- boot.php                  初始化文件
       +- config/                   配置文件
       +- cube/                     框架文件
       +- cube-admin/               管理后台
       |    |
       |    +- app/                 控制台工具
       |    +- cube-admin-boot.php
       |    +- htdocs/              管理后台web入口
       |    +- include/             php代码文件
       |    +- template/            管理后台模板
       |
       +- htdocs                    web可访问文件
       +- htdocs_res                js/css/image
       +- include                   php代码文件
       +- template                  模板路径
       +- writable                  日志文件，系统运行生成的临时文件
    ```

# 配置和运行

---


> TO BE CONTINUED.
