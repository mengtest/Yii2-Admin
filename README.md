<p align="center">
    <img src="https://raw.githubusercontent.com/duiying/img/master/yii2.png" height="100px">
    <h1 align="center">Yii2-Admin</h1>
    <br>
</p>

基于Yii2 Advanced开发的RBAC权限控制系统  
### 项目概览
![Yii2-Admin](https://raw.githubusercontent.com/duiying/img/master/Yii2-Admin.png)

### 在线体验
暂不支持360极速浏览器兼容模式  
[Yii2-Admin](http://106.75.117.140)


### 安装
```
1. 克隆项目到本地
2. 修改 common/config/main-local.php 文件中的数据库配置
3. 新建数据库, 导入项目根目录下的sql文件
4. 访问后台 localhost/Yii2-Admin
    超级管理员: admin 123
    游客: youke 123
```

### 基于Redis异步发送邮件实现找回密码功能
参考 [yii2-queue](https://github.com/duiying/yii2-queue)
```
1. 修改 common/config/main-local.php 文件中的邮箱和redis配置
2. 设置crontab定时任务
* * * * * /usr/bin/php /data/www/Yii2-Admin/yii mail/send > /dev/null
```

### 展示
![登录](https://raw.githubusercontent.com/duiying/img/master/yii2-admin-login.png)  
![首页](https://raw.githubusercontent.com/duiying/img/master/yii2-admin-index.png)  
![管理员管理](https://raw.githubusercontent.com/duiying/img/master/yii2-admin-admin.png)  

### Github开源项目交流群
[点击加群](https://jq.qq.com/?_wv=1027&k=55by69J)

 


