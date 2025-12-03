<!DOCTYPE html>
<html>

<head>
    <title>取引完了のお知らせ</title>
</head>

<body>
    <p>{{ $item->seller->name }}様</p>

    <p>購入者の{{ $rater->name }}様が、以下の商品の取引を完了しました。<br>
    チャット画面より、購入者の評価を行ってください。</p>

    <p>
        <strong>商品名:</strong> {{ $item->name }}<br>
        <strong>価格:</strong> &yen;{{ number_format($item->price) }}
    </p>

    <p style="text-align: center;">
        <a href="{{ route('chat.index', $item->id) }}" style="
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff5555;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        ">▼チャット画面で評価を行う</a>
    </p>

    <p>今後ともcoachtechフリマをよろしくお願いいたします。</p>
</body>

</html>