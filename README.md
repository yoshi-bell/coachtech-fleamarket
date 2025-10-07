# アプリケーション名
フリーマーケットアプリ

## 環境構築

### 前提条件
- Git
- Docker
>コンビニ支払いのテストをする場合は以下の条件も満たす必要があります。
- Stripeアカウントを持っていること。
- StripeダッシュボードからAPIキー（公開可能キーとシークレットキー）を取得できること。

### Dockerビルド
- `git clone git@github.com:yoshi-bell/coachtech-fleamarket.git`
- `cd coachtech-fleamarket`
- `docker-compose up -d --build`

> MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせて`docker-compose.yml`ファイルを編集してください。

### Laravel環境構築

- **`php`コンテナに入る**
  - `docker-compose exec php bash`

  > これ以降のコマンドは、コンテナ内で実行します。

- **パッケージのインストール**
  - `composer install --dev`
- **`.env.example` ファイルから `.env` ファイルを作成し環境変数を変更**
  - `cp .env.example .env`
- **アプリケーションキーの生成**
  - `php artisan key:generate`
- **データベースのマイグレーション**
  - `php artisan migrate`
- **ダミー画像のコピー**
  - `php artisan setup:copy-images`
  > このコマンドにより、初期データ投入（シーディング）で使用されるダミーの画像ファイルを、適切なディレクトリにコピー。
- **データベースの初期データ投入**
  - `php artisan db:seed`
  > シーディングにより、商品のダミーデータ10種類、ユーザーのダミーデータ5件がデータベースに入力されます。
- **ストレージのシンボリックリンク作成**
  - `php artisan storage:link`

- **（任意）`storage`ディレクトリの権限設定**
  - "The stream or file could not be opened"エラーが発生した場合に実行します。
  - `chmod -R 777 storage`
  > **注意:** `777`は全てのユーザーに読み書き実行を許可する最も緩い権限設定です。これはローカル開発環境での権限問題を簡易的に解決するためのもので、本番環境では使用しないでください。

### 決済サービスstripeによるテスト環境構築
- **Stripe APIキーの設定**
  - Stripeダッシュボード（開発者設定 -> APIキー）から取得した以下のキーを`.env`ファイルに設定。
    - `STRIPE_KEY="pk_test_..."`
    - `STRIPE_SECRET="sk_test_..."`
  > 以上のの設定でクレジット支払いでの決済テストが可能。コンビニ払い決済テストの設定は以下。

- **Dockerを再ビルドし`.env`ファイルの変更を反映**
  - `docker-compose down`
  - `docker-compose up -d`

- **Stripe CLIをインストール**
  - 使用OSに合わせて、以下の公式ドキュメントを参考にインストール。
    - (https://stripe.com/docs/stripe-cli)

- **決済機能テスト**
  - 後述の「決済機能テスト」を参照。

## アプリケーションの機能
このアプリケーションは、フリマサイトを管理するためのシステムです。主な機能は以下の通りです。

- **会員登録・ログイン機能**:
    -   ユーザーはメールアドレスとパスワードで会員登録・ログインが可能です。
    -   **メール認証機能**: 新規会員登録時にメール認証を行い、未認証ユーザーは保護されたページにアクセスできません。
    -   認証メールの再送機能も備えています。
- **商品一覧表示**:
    -   全商品の表示、商品画像、商品名、価格の表示。
    -   購入済み商品は「Sold」と表示されます。
    -   いいねした商品や購入した商品の一覧も確認できます。
- **商品検索機能**:
    -   商品名での部分一致検索が可能です。
- **商品詳細の確認**:
    -   商品画像、商品名、ブランド名、価格、いいね数、コメント数、商品説明、商品情報（カテゴリ、商品の状態）などを確認できます。
    -   **いいね機能**: 商品に「いいね」を付けたり解除したりできます。
    -   **コメント機能**: ログインユーザーのみがコメントを送信でき、コメントのバリデーションも実装されています。
- **商品購入機能**:
    -   **Stripe決済**: クレジットカード決済とコンビニ決済に対応しています。
    -   **コンビニ決済**: 非同期処理に対応し、Webhookを通じて支払い完了を検知し、購入記録をデータベースに保存します。
    -   配送先住所の変更も可能です。
    -   購入した商品は「Sold」と表示され、マイページの購入履歴に追加されます。
- **プロフィール管理**:
    -   ユーザーは自身のプロフィール（画像、ユーザー名、出品商品、購入商品）を確認できます。
    -   プロフィール画像、ユーザー名、郵便番号、住所、建物名の編集が可能です。
- **商品出品機能**:
    -   商品画像、カテゴリ（複数選択可）、商品の状態、商品名、ブランド名、商品説明、販売価格を登録できます。
    -   商品画像のアップロード機能も備えています。

## 使用技術
- PHP 8.1.33
- Laravel 8.75
- MySQL 8.0.26
- Laravel Fortify (認証基盤)
- mailhog (メールテスト)
- Stripe (決済API)
- Stripe CLI (Webhookテスト)

## ER図

![ER図](ER.drawio.png)

## URL
- 開発環境トップページ: `http://localhost/`
- 会員登録ページ: `http://localhost/register`
- ログインページ: `http://localhost/login`
- マイページ: `http://localhost/mypage`
- 商品出品ページ: `http://localhost/sell`
- メール認証誘導画面: `http://localhost/email/verify`
- phpMyAdmin: `http://localhost:8080/`
- MailHog: `http://localhost:8025`
- Stripe公式サイト: `https://stripe.com/jp`
- Stripeテストダッシュボード: `https://dashboard.stripe.com/test/dashboard`


## 機能テスト（PHPunit）

アプリケーションの各機能が、サーバーサイドで正しく動作するかを検証します。

### テスト環境
- **データベース:**
  - テスト実行時には、`phpunit.xml`の設定に基づき、**インメモリのSQLiteデータベース** (`:memory:`) が使用されます。、開発用のデータベース（`laravel_db`）に影響を与えません。
- **データのリセット:**
  - 各テストメソッドの実行後、`RefreshDatabase`トレイトの機能により、データベースへの変更はすべて自動的にロールバック（リセット）され、他のテストに影響を与えないようになっています。

### 実行方法
- **`php`コンテナに入る**

  - `docker-compose exec php bash`

  > これ以降のコマンドは、コンテナ内で実行します。

- **すべてのテストを実行:**
  - `php artisan test`

- **特定のファイルのみを実行:**
  - `php artisan test tests/Feature/Auth/LoginTest.php`

- **特定のディレクトリのみを実行:**
  -  `php artisan test tests/Feature/Item`


## ブラウザテスト (Laravel Dusk)

このプロジェクトには、JavaScriptによる動的なフロントエンドの挙動をテストするための、ブラウザテスト（Laravel Dusk）も含まれています。

### 初回セットアップ手順

- **ChromeDriverの準備**
- ChromeDriverの実行に必要なライブラリをインストール。
  - `sudo apt-get update && sudo apt-get install -y libnss3`

- **`php`コンテナに入る**
  - `docker-compose exec php bash`

  > これ以降のコマンドは、コンテナ内で実行します。

- **ChromeDriverのインストール**
  - `php artisan dusk:chrome-driver --detect`

- **Dusk用環境ファイル`.env.dusk.local` ファイルを`.env.dusk.local.example` ファイルから 作成。**
  - `cp .env.dusk.local.example .env.dusk.local`
  - その後`.env.dusk.local`に`.env`ファイルの`APP_KEY`をコピー。

- **Dusk用データベースファイルの作成**
  - `touch database/database.sqlite`

### テストの実行

- セットアップ完了後、以下のコマンドでキャッシュをクリアし、続いてDuskテストを実行。

   > Laravelの**設定キャッシュ**が残っていると、この設定が読み込まれず、**開発用のデータベースを意図せず変更してしまう**ことがあります。

  - `php artisan config:clear`
  - `php artisan dusk`

  > もし誤って開発用データベースを変更してしまった場合は、`php artisan migrate:fresh --seed` を実行することで、データベースを初期状態に戻すことができます。（注意：データベース内の全データがリセットされます）

## 決済機能テスト
stripeを用いた、購入時の決済テストが行えます。
### テスト用アカウント

- シーディングによって作成されるダミーユーザーのパスワードは、全ユーザー共通で以下に設定されています。

  - **パスワード:** `usertest`

- 出品商品のダミーデータは、主に以下の2ユーザーに紐づけられています。
  - `test1@example.com`
  - `test2@example.com`

### クレジットカード決済

- Stripeのテスト環境では、実際のカード情報なしで決済フローをシミュレートできます。テスト用のクレジットカードとして、以下の情報を使用してください。

  - **カード番号:** `4242 4242 4242 4242` (Visaのテストカード)
  - **有効期限:** 未来の日付 (例: `12/25`)
  - **CVC:** 任意の3桁の数字 (例: `123`)
  - **名前:** 任意 (例: `TARO TEST`)

### コンビニ決済

- **Strip CLIにログイン**
  - 新規ターミナルにて下記のコマンドを実行し、表示されたURLよりブラウザによる認証を行いログイン。
  - `stripe login`
- **Stripe WebhookのセットアップしStripe CLIの「リスニングモード」実行**
    - `stripe listen --forward-to http://localhost/api/webhook/stripe`
    - 表示されたWebhookシークレット (`whsec_...`) を`.env`ファイルに設定。
      - `STRIPE_WEBHOOK_SECRET="whsec_..."`
    - Stripe CLIをこのまま「リスニングモード」実行中にしターミナルを開いておくことで、StripeからWebhookイベント（例：決済成功、返金、顧客作成など）を監視し、ローカルサーバーに転送。

    > **注意:** `stripe listen` を再実行すると新しいシークレットキーが発行されます。その際は改めて.envを更新してください。このシークレットキーはstripe listenコマンドを実行するたびに変わるため、開発セッションごとに更新が必要です。

- **Dockerを再ビルドし`.env`ファイルの変更を反映**
  - Stripe CLIを実行しているのとは別のターミナルで、プロジェクトのルートディレクトリ`coachtech-fleamarket`にてDockerを再起動する。
  - `docker-compose down`
  - `docker-compose up -d`

- stripeの支払い情報入力画面で**以下のテスト用メールアドレスを使用する**ことで、支払いを即座に成功させ、購入を完了するシミュレーションが可能です。

-   **テスト用メールアドレス:** `succeed_immediately@example.com`

- このメールアドレスを入力して支払いを確定すると、Stripeは即座に支払い成功の通知（Webhook）をアプリケーションに送信します。これにより、実際にコンビニで支払うことなく、購入完了のロジックをテストできます。

  > stripeの支払い完了後、ブラウザの戻るボタンを使用にアプリケーションに戻る必要があります。

