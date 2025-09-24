# アプリケーション名
フリーマーケットアプリ

## 環境構築

```
前提条件
* Git
* Docker

Dockerビルド
1.git clone git@github.com:yoshi-bell/onoe-kadai01.git
2.docker-compose up -d --build

＊MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせてdocker-compose.ymlファイルを編集してください。

Laravel環境構築
1.docker-compose exec php bash
2.composer install
3..env.exampleファイルから.envを作成し、環境変数を変更
4.php artisan key:generate
5.php artisan migrate
6.php artisan db:seed
7.stripeアカウント作成しAPIキーを取得(※要修正
8..envファイルにAPIキーを設定
＊シーディングにより、「〇〇の種類」〇種類のデータ、〇〇のダミーデータ〇件がデータベースに入力されます。

＊"The stream or file could not be opened"エラーが発生した場合
srcディレクトリにあるstorageディレクトリに権限を設定

chmod -R 777 storage

```
## アプリケーションの機能
```
このアプリケーションは、フリマサイトを管理するためのシステムです。
主な機能は以下の通りです。


-機能
-一覧表示
-絞り込み機能
-詳細の確認と削除機能
```
## 使用技術
```
・PHP 8.1.33
・Laravel 8.75
・MySQL 8.0.26
・mailhog
```
## ER図

![ER図](ER.drawio.png)

## URL
```
・開発環境：http://localhost/
・〇〇ページ：/
・〇〇ページ：/confirm
・〇〇ページ：/thanks
・管理画面：/admin
・ユーザ登録ページ：/register
・ログインページ：/login
・phpMyAdmin：http://localhost:8080/
・mailhog：http://localhost:8025
・stripe公式サイト：https://stripe.com/jp

```
ディスクがWebからアクセスできるようにシンボリックリンクを作成してください。

   1     php artisan storage:link


ダミーデータユーザー
ユーザーメールアドレス
test1@example.com
test2@example.com
ユーザーパスワード
全ユーザー：usertest

* カード番号:
      4242 4242 4242 4242
      ( 4242 を4回繰り返します。これはテスト用のVisaカードとして認識されます。)


   * 有効期限:
      未来の日付であれば、何でも構いません。
      (例: 12 / 25)

   * CVC（セキュリティコード）:
      3桁の数字であれば、何でも構いません。
      (例: 123)


   * 名前:
      アルファベットにて任意の名前で構いません。
      (例: TEST TARO)



ngrokについて
アカウント作成
認証トークンを取得
.envの
NGROK_AUTHTOKEN=
に続いて認証トークンを入力

ngrokが正常に機能しているかは、docker-compose
  psでngrokコンテナのSTATUSがUpになっていること、そしてブラウザでhttp://localhost:4040にアクセスしてWebイン
  ターフェースが表示され、公開URLが発行されていることで確認できます。

  2. Laravelのキャッシュをクリアするコマンドを再度実行します。

   1     docker-compose exec php php artisan config:clear
   2     docker-compose exec php php artisan cache:clear
   3     docker-compose exec php php artisan view:clear

   http://localhost:4040にアクセス
   公開urlを確認ご.envの
   APP_URL=
   に記述



   mailhog http://localhost:8025