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
```