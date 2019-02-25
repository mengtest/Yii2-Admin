<?php
    use yii\helpers\Url;
?>
<body class="login-bg">
<div class="login layui-anim layui-anim-up">
    <div class="message">Yii2-Admin</div>
    <div id="darkbannerwrap"></div>

    <form class="layui-form" action="badiu.com" id="dataSet" onsubmit="return present();">
        <input name="_csrf-backend" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">

        <input name="admin_name" placeholder="用户名"  type="text" required class="layui-input" >
        <hr class="hr15">
        <input name="admin_pass" required placeholder="密码"  type="password" class="layui-input">
        <hr class="hr15">
        <input value="登录" style="width:100%;" type="submit">
        <hr class="hr20" >
    </form>
</div>

<script>
    /**
     * 数据提交
     *
     * @returns {boolean}
     */
    function present() {
        dialog.presentForm('<?= Url::to(['login/login']) ?>', '<?= Url::to(['index/index']) ?>');
        return false;
    }
</script>
<!-- 底部结束 -->
</body>