# 开发者安装文档

PS: 目前用户一键安装脚本正在适配，后续会有相关文档和教程

## 正式安装步骤

PS: 以下命令均可去掉sudo,在root用户下执行

### 安装docker及其附属软件

进入static文件夹，解压docker 套件包
```
cd statics
tar -xzvf docker-tool-pkg.tar.gz
cd ubuntu-docker-pkgs
```

安装docker

```bash
sudo dpkg -i containerd.io_1.3.7-1_amd64.deb
sudo dpkg -i docker-ce-cli_19.03.9_3-0_ubuntu-bionic_amd64.deb
sudo dpkg -i docker-ce_19.03.9_3-0_ubuntu-bionic_amd64.deb
```

配置指定用户到docker用户组（一直使用root用户操作可忽略），生效需要重启电脑

```bash
sudo usermod -aG docker $USER 
```

安装docker-compose

PS: 当docker-compose报错的时候可以删除docker-compose，执行`rm -rf/usr/local/bin/docker-compose`，并按下列步骤重装

```
cp docker-compose-Linux-x86_64 /usr/local/bin/docker-compose
sudo chmod 755 /usr/local/bin/docker-compose
docker-compose --version # 监测docker-compose 是否安装成功
```

### 正式进入安装mediawiki环节

首先进入statics目录加载mediawiki_wiki所需要的镜像

```bash
gunzip -c mediawiki_pack.tar.gz | docker load
```

新建.env文件
```bash
cp .env.default .env
```

填入邮箱名称(如下)
```
EMERGENCY_CONTACT=1713856662a@gmail.com
PASSWORD_SENDER=1713856662a@gmail.com
```

填入需要创建的数据库名称，用户和密码
```
DB_NAME=mediawiki_db
DB_USER=cicada
DB_PASSWORD=123456
```

第一次拉起镜像
```bash
docker-compose -f docker-compose.yml up
```

新开一个终端
```bash
docker exec -it mediawiki-wiki /script/install.sh {username} {password}
```
username和password需要自己定义，这是wiki管理员的名字和密码
代码执行后会有如下两个字段，需要将其拷贝下来，贴入.env文件对应的字段中，如下

```
SECRET_KEY=bc4d089fb344c3457a464876768c2ebd09e95721dae7d42f9ba912953f834365
UPGRADE_KEY=6828be7e1b2fdc6e
```

回到刚才启动镜像的窗口，ctrl-C 停止镜像运行
```
docker exec -it mediawiki-wiki /script/update.sh # 更新mediawiki插件
docker-compose -f docker-compose.yaml up -d
```

进入系统
wiki url地址为127.0.0.1:8080
登录时使用刚刚设定的管理员用户和密码，注意不是数据库的密码