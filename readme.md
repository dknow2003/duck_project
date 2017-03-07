# 后台

## 环境要求

- PHP >= 5.5.9

## 部署

Git 克隆仓库到本地。

复制 `.env.example` 为 `.env` 预设环境变量，并将其中的数据库信息替换成自己的。

安装依赖：

```shell
$ composer install
```

生成密钥：

```shell
$ php artisan key:generate
```

迁移新的数据库结构：

```shell
$ php artisan migrate
```

新增超级管理员，通过命令行执行。
```shell
$ php artisan admin:create username your@mail.com password
```

超级管理员的操作命令还包括：
```shell
$ // 删除特定的超级管理员
$ php artisan admin:delete username
$ // 修改特定管理员的密码 
$ php artisan admin:password username new-password
$ // 修改特定管理员的邮箱地址
$ php artisan admin:email username new-email
```

每天零点同步前一天数据，加入以下内容到 cron，替换 php 和 artisan 为源码目录。
```shell
* * * * * php /path/to/artisan schedule:run 1>> /dev/null 2>&1
```

如果任务意外中断，导致数据有缺失，命令行执行以下命令补全所有日期、所有服务器数据：
```shell
$ php artisan sync:data --all
```
如果去掉 `--all` 选项，则仅仅同步前一天的。


