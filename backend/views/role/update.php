<?php
    use yii\helpers\Url;
?>
<body>
<div class="x-body">
    <form action="" method="post" class="layui-form layui-form-pane" id="dataSet" onsubmit="return present();">
        <input name="_csrf-backend" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
        <input type="hidden" name="role_id" value="<?= $model->role_id ?>">
        <div class="layui-form-item">
            <label class="layui-form-label">
                角色名称
            </label>
            <div class="layui-input-inline">
                <input type="text" name="role_name" required="" lay-verify="required" class="layui-input" value="<?= $model->role_name ?>">
            </div>
            <div class="layui-form-mid layui-word-aux">必填</div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">
                分配权限
            </label>
            <table  class="layui-table layui-input-block">
                <tbody>
                <?php foreach($authList as $auth): ?>
                <tr>
                    <td>
                        <input type="checkbox" name="auth_id[]" lay-skin="primary" title="<b><?= $auth['auth_name'] ?></b>" value="<?= $auth['auth_id'] ?>" <?php if (in_array($auth['auth_id'], $itemList)) {echo 'checked';} ?>>
                    </td>
                    <td>
                        <?php foreach($auth['child'] as $child): ?>
                        <div class="layui-input-block">
                            <input type="checkbox" name="auth_id[]" lay-skin="primary" title="<b><?= $child['auth_name'] ?></b>" value="<?= $child['auth_id'] ?>" <?php if (in_array($child['auth_id'], $itemList)) {echo 'checked';} ?>>
                            <?php foreach($child['child'] as $childChild): ?>
                                <input name="auth_id[]" lay-skin="primary" type="checkbox" title="<?= $childChild['auth_name'] ?>" value="<?= $childChild['auth_id'] ?>" <?php if (in_array($childChild['auth_id'], $itemList)) {echo 'checked';} ?>>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">
                描述
            </label>
            <div class="layui-input-block">
                <textarea placeholder="请输入内容" name="role_desc" class="layui-textarea"><?= $model->role_desc ?></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">排序</label>
            <div class="layui-input-inline">
                <input type="text" name="role_sort" value="<?= $model->role_sort ?>" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">数值越小, 排序越靠前, 默认值为10</div>
        </div>
        <div class="layui-form-item">
            <button class="layui-btn" lay-submit="">提交</button>
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
        dialog.presentForm('<?= Url::to(['role/update']) ?>');
        return false;
    }
</script>
</body>
