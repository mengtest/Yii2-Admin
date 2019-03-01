<?php
    use yii\helpers\Url;
?>
<body>
<div class="x-body">
    <form class="layui-form" id="dataSet" onsubmit="return present();">
        <input name="_csrf-backend" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">

        <div class="layui-form-item">
            <label class="layui-form-label">用户名</label>
            <div class="layui-input-inline">
                <input type="text" name="admin_name" lay-verify="required" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">必填</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                角色
            </label>
            <div class="layui-input-inline">
                <select  name="role_id" class="valid">
                    <?php foreach ($roleList as $role) : ?>
                        <option value="<?= $role->role_id ?>"><?= $role->role_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">密码</label>
            <div class="layui-input-inline">
                <input type="password" name="admin_pass" lay-verify="required" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">
                必填
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">确认密码</label>
            <div class="layui-input-inline">
                <input type="password" name="rePass" lay-verify="required" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">
                必填
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
            </label>
            <button  class="layui-btn" lay-submit="">
                提交
            </button>
        </div>
    </form>
</div>
<script>
    /**
     * 数据提交
     *
     * @returns {boolean}
     */
    function present() {
        dialog.presentForm('<?= Url::to(['admin/create']) ?>');
        return false;
    }
</script>
</body>

