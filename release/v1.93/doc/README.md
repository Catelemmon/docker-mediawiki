# 简介

## mediawiki 简介

mediawiki是一个开源的wiki引擎，其驱动的维基百科是目前最为著名的wiki网络应用，
当前这个版本是一系列迭代后最后可用，以及功能相对完善的wiki版本，目前需要补充大量
针对普通用户的使用文档，后续会持续更新1.93的文档。

## 主体提供功能
 
 - 基于docker镜像，方便部署，一次打包，到处部署。
 - 基于php7.3-fpm，增强php的并发性能，方便后续二次开发
 - 基于mediawiki1.34.2 较为成熟的一个mediawiki版本
 - supervisor组件拉起子服务，在一个docker镜像内实现多个服务同时运行
 - supervisor可以作为内部容器的日志展示和管理器存在
 - 独立安装mysql服务器，数据备份到外部，方便备份和管理
 - 内部使用nginx启动web服务
 - mediawiki原生自带功能（关键词条或者url高亮，内容历史记录）
 - visual editor(可视化编辑器),带有数学公式插入的功能，excel表格可以直接拷贝进入其表格组件，word同样也可以拷贝进去
 - parsoid mediawiki的接口增强服务，既方便后续二次开发，同时提供visual editor 基础功能
 - 提供视频嵌入播放功能
 - 提供分数贡献展示功能
 - 提供pdf嵌入展示

## 系统要求

### 硬件配置 （以下均为最低配置，各项配置按词条数量，上传文件大小数量，期望使用年限酌情提高）
  
 10人以下团队需要配置
 
 - 4核以上
 - 4G内存以上
 - 100G磁盘以上
 
 10-50人团队需要配置
 
 - 8核以上
 - 16G内存以上
 - 200G磁盘以上
 
 50-100人团队需要配置
 
 - 12核以上
 - 32G内存以上
 - 300G磁盘以上

### 系统要求（docker-ce版本目前仅兼容rhel，centos，ubuntu）

 - Red Hat Enterprise Linux 7 64位
 - centos 7 64位
 - ubuntu (推荐使用18.04)
    - Zesty 17.04
    - Xenial 16.04 (LTS)
    - Trusty 14.04 (LTS)
    - Bionic 18.04 (LTS)