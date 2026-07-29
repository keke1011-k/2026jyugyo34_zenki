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
