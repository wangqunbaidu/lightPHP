<table>
    <caption>商品列表</caption>
    <thead>
        <tr>
            <th>商品名称</th>
            <th>商品价格</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ( $product as $key => $value ): ?>
        <tr>
            <td><?= $value->name; ?></td>
            <td><?= $value->price; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<link rel="stylesheet" href="/Public/css/index.css" type="text/css" />