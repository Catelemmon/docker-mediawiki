# 安装步骤

## 1. 删除之前的镜像
```
docker rm $(docker -qa)
```

## 2. 加载新镜像
```
unzip docker-mediawiki-org-v1.7.zip
cd docker-mediawiki-org-v1.7
gzip -d mediawiki-pkg.tar.gz
docker load mediawiki-pkg.tar
```

## 3. 新建.env文件
```
cp .env.default .env
```
填入邮箱名称
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

## 4. docker-compose 拉起镜像
```
docker-compose -f docker-compose-v1.yml up
```
然后新开一个窗口
```
docker exec -it mediawiki-wiki /script/install.sh {username} {password}
```
username和password需要自己定义，这是wiki管理员的名字和密码
代码执行后会有如下两个字段，需要将其拷贝下来，贴入.env文件对应的字段中，如下
```
SECRET_KEY=bc4d089fb344c3457a464876768c2ebd09e95721dae7d42f9ba912953f834365
UPGRADE_KEY=6828be7e1b2fdc6e
```

## 5.重启
回到刚才启动镜像的窗口，ctrl-C 停止镜像运行
```
docker-compose -f docker-compose-v1.yaml up -d
```

## 6.进入系统
wiki url 为127.0.0.1:8080
登录时使用刚刚设定的管理员用户和密码，注意不是数据库的密码
如果报错, 则执行
```
docker exec -it mediawiki-wiki /script/update.sh
```
