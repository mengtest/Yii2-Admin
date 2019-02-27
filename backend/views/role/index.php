<?php
use yii\helpers\Url;
?>
<body>
<div class="x-nav">
    <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a><cite>角色管理</cite></a>
    </span>
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i>
    </a>
</div>
<div class="x-body">
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加角色','<?= Url::to(['role/create']) ?>')"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:40px">共有数据: <?= $pager->totalCount ?> 条</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th width="50">ID</th>
            <th>角色名称</th>
            <th>角色描述</th>
            <th width="50">排序</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach($list as $role): ?>
            <tr>
                <td><?= $role->role_id ?></td>
                <td><?= $role->role_name ?></td>
                <td><?= $role->role_desc ?></td>
                <td><input type="text" class="layui-input x-sort" name="order" value="<?= $role->role_sort ?>"></td>
                <td class="td-manage">
                    <button class="layui-btn layui-btn layui-btn-xs"  onclick="x_admin_show('编辑角色', '<?= Url::to(['auth/update', 'auth_id' => $role->role_id]) ?>')" ><i class="layui-icon">&#xe642;</i>编辑</button>
                    <button class="layui-btn-danger layui-btn layui-btn-xs"  onclick="del(<?= $role->role_id ?>);" href="javascript:;" ><i class="layui-icon">&#xe640;</i>删除</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="page">
        <div>
            <?php echo yii\widgets\LinkPager::widget([
                'pagination' => $pager,
                'prevPageLabel' => '&lt;&lt;',
                'nextPageLabel' => '&gt;&gt;',
            ]); ?>
        </div>
    </div>
</div>
<style type="text/css">

</style>
<script>
    function del(auth_id) {
        dialog.confirm('确定删除吗?', "<?= Url::to(['auth/del']) ?>", '', {'auth_id' : auth_id, '_csrf-backend' : '<?= Yii::$app->request->csrfToken ?>'});
    }
</script>

</body>