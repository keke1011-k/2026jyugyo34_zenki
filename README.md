# 掲示板サービス 構築手順書

本リポジトリのソースコードを用いて、初期状態のAmazon Linux EC2インスタンス上にWeb掲示板サービスを構築するための手順を記述する。

## 1. 前提条件
- OS: Amazon Linux (EC2インスタンス)
- 必要なソフトウェア: Git, Docker, Docker Compose

## 2. パッケージの更新とDockerのインストール
初期状態のインスタンスに接続後、システムを最新化し、必要なパッケージをインストールする。

```bash
# システムのアップデート
sudo dnf update -y

# GitとDockerのインストール
sudo dnf install -y git docker

# Dockerの起動と自動起動設定
sudo systemctl start docker
sudo systemctl enable docker

# ec2-userでsudoなしでDockerコマンドを実行できるようにする
sudo usermod -aG docker ec2-user
3.Doker ComposeおよびBulidxの最新化
# Docker Composeの最新版インストール
sudo curl -SL https://github.com/docker/compose/releases/download/v2.29.0/docker-compose-linux-x86_64 -o /usr/libexec/docker/cli-plugins/docker-compose
sudo chmod +x /usr/libexec/docker/cli-plugins/docker-compose

# Buildxの最新版インストール
sudo curl -SL https://github.com/docker/buildx/releases/download/v0.17.1/buildx-v0.17.1.linux-amd64 -o /usr/libexec/docker/cli-plugins/docker-buildx
sudo chmod +x /usr/libexec/docker/cli-plugins/docker-buildx

4.リポジトリのクローンと起動
# リポジトリのクローン
git clone https://github.com/keke1011-k/2026jyugyo34_zenki.git
cd 2026jyugyo34_zenki

# コンテナのビルドとバックグラウンド起動
docker compose up -d


5. データベース（MySQL）のテーブル作成
   コンテナ起動後、MySQLコンテナにログインし、投稿データを保存するためのテーブル作成する。

# MySQLコンテナへログイン
docker compose exec mysql mysql -u root example_db
MySQLのプロンプト（mysql>）が表示されたら、以下のSQLを実行する。

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

6. データベース構造（テーブル定義：posts テーブル）本システムでは、掲示板のデータを保存するために以下の構造のテーブルを使用している。
カラム名データ型制約説明idINTPRIMARY KEY, AUTO_INCREMENT投稿の固有番号（キー）。自動で1から連番が振られます。nameVARCHAR(255)NOT NULL投稿者の名前（必須）。bodyTEXTNOT NULL投稿の本文（必須）。image_pathVARCHAR(255)なしアップロードされた画像の保存先ファイルパス。画像がない場合は空になります。created_atTIMESTAMPDEFAULT CURRENT_TIMESTAMP投稿日時。データが追加された瞬間の時間が自動で記録されます。

7. AWSセキュリティグループの設定ブラウザからWebサイトにアクセスするため、AWSのEC2管理画面からセキュリティグループ（インバウンドルール）に以下の設定を追加する。タイプポート範囲ソース用途SSH220.0.0.0/0サーバー管理用（Tera Term等での接続）HTTP800.0.0.0/0Webサイト閲覧用

### 【補足】テーブル定義（postsテーブル）
本システムでは、掲示板のデータを保存するために以下の構造のテーブルを使用しています。


