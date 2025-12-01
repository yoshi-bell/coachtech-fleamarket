<!DOCTYPE html>
<html>

<head>
    <title>取引完了のお知らせ</title>
</head>

<body>
    <h1>取引完了のお知らせ</h1>
    <p>{{ $item->seller->name }}様</p>
    <p>以下の商品の取引が完了しました。</p>
    <p>商品名: {{ $item->name }}</p>
    <p>購入者: {{ $rater->name }}</p>
    <p>評価が送信されました。マイページよりご確認ください。</p>
</body>

</html>