# プログラム概要
slackを用いて、チームとスケジュールを共有するアプリ。

スケジュールの表示
![suke](https://user-images.githubusercontent.com/54432132/195247053-7ddc8fc9-d0ca-426f-8448-76686e638961.jpg)

スケジュールの作成
![createtask](https://user-images.githubusercontent.com/54432132/195247054-c7b94fde-352f-4e29-9494-09ce885e6904.jpg)

slackのスラッシュコマンドからスケジュールの作成
>/task {名前}<br>
>{日にち}<br>
>{休日か T or F}<br>
>{時間}<br>

slackのスラッシュコマンドからスケジュールの表示
>/schedule @user


機能とパス
* /auth/signin サインイン
* /auth/login ログイン
* /auth/logout ログアウト
* /schedule/createtask タスクの作成
* /schedule/edittask タスクの編集
* /schedule/tasklist  スケジュールの表示


# 必要なもの
* windows10
* xampp 3.3.0
* sqlite3 3.39.3
* ngrok
* composer
* twig
* ramesy/uuid
* phpdotenv

# 設定の流れ
1. xamppのインストール
2. composerのインストール
   - twig
   - ramsey/uuid
   - phpdotenv
3. プログラムのインストール
4. sqliteのインストール
5. プログラムのインストール
6. slack apiの設定
7. ngrokのインストール
8. タイムスケジューラの設定

## 1. XAMPPのインストール
開発環境の構築にXAMPPを用います。  
アプリケーションの開発には様々なソフトウェアが必要です。XAMPPとはそれらソフトウェアをまとめて扱うことができるツールです。  
[XAMPPダウンロードページ](https://www.apachefriends.org/jp/download.html)からXAMPPのインストーラをダウンロードし、実行してください。

![xampphp](https://user-images.githubusercontent.com/54432132/195221899-3313dc6a-5691-4b68-b66b-03b250c92ccb.jpg)
*XAMPPダウンロード*
<br>

成功するとxampp control panelが使えるようになります。  

![xamppcontrol](https://user-images.githubusercontent.com/54432132/195582301-e6741f6e-4328-4d68-bc8e-812682c8c4cc.jpg)
*XAMPP control panel*


## 2. composerのインストール
composerとはプログラムに使う拡張機能(パッケージ)を管理するツールで、パッケージをインストールするのに使います。  
[composerダウンロードページ](https://getcomposer.org/download/)からインストーラをダウンロードし、実行してください。

![compsoer](https://user-images.githubusercontent.com/54432132/195222841-609fb5e3-1f80-488e-b2f5-2fa4ce4946d7.jpg)
*composer install*

## twig,uuid,phpdotenvのインストール
システムに必要な3つのパッケージをcomposerでインストールします。  
xampp control panelからshellを開き、以下のコマンドを実行。

>composer require vlucas/phpdotenv<br>
>composer require ramsey/uuid<br>
>composer require "twig/twig:~1.0"<br>

![shell](https://user-images.githubusercontent.com/54432132/195228944-68831b5b-f291-478f-a5ba-dedbc44fdb96.jpg)
*shell*

次のコマンドをshellに入力すると、インストールされているパッケージの一覧を見ることができます。先ほどインストールしたパッケージを確認してみましょう。

>composer show -i

![compsoerlist](https://user-images.githubusercontent.com/54432132/195586014-cf316a07-464b-42f3-9ec7-afcd3c4d107b.jpg)
*パッケージ一覧*

## 3. プログラムのインストール
phpを実行する設定は終わりました。次にこのシステムをインストールします。  
[github](https://github.com/Earth-worm/system-design-2022)からzipとしてダウンロードし、xampp/htdocsフォルダに展開してください。

![git](https://user-images.githubusercontent.com/54432132/195223688-3130d9f2-5b55-430f-8ffa-d0c05029ab62.jpg)

xampp/htdocsフォルダの中身は次のようになります。2,3個関係ないファイルがありますが、気にしないでください。

![htdocs](https://user-images.githubusercontent.com/54432132/195588366-56117042-fa59-4408-b273-d5d11f737218.jpg)
*htdocs中身*

##  sqlite3のインストール
webアプリで扱う情報は基本的にデータベース(DB)に保管されます。このシステムではDBにsqliteを用います。  
[sqlite3ダウンロードページ](https://www.sqlite.org/download.html)からツールをダウンロードし、ダウンロードしたフォルダ内からsqlite3.exeをxampp/htdocsに設置してください。

![sqlite](https://user-images.githubusercontent.com/54432132/195224288-1f57f66c-e7d5-45c9-bd92-e02a648377f4.jpg)

コマンドプロンプト(cmd)からhtdocsディレクトリへ移動して次のコマンドを入力し、データベースのテーブルを取得できれば成功です。

>cd {htdocsの絶対パス}&emsp;//ディレクトの移動  
>sqlite3 db.sqlite3&emsp;//DBにアクセス  
>.tables&emsp;//テーブルの取得  

![sqlitea](https://user-images.githubusercontent.com/54432132/195589998-7734181d-a630-4586-8824-17f5340ef0c2.jpg)
*テーブルリストの取得*

## slack apiの設定

まずはslackのワークスペースを作成し、[slackAPIページ](https://api.slack.com/)からアプリ作成します。  
アプリの作成方法は説明が長くなるので[他のサイト](https://reffect.co.jp/html/slack)を参考にしてください。permissionから説明します。  
次にpermissionを与えます。画像のpermissionをクリック。

![SharedScreenshot](https://user-images.githubusercontent.com/54432132/195231251-33073db7-fe8d-46da-b431-712ba618079b.jpg)
*permission*

permissionページの下の方にScopeを追加する欄があるので、bot token scopesに次のscopeを追加します。

![SharedScreenshot2](https://user-images.githubusercontent.com/54432132/195232286-deb93759-4fe4-4159-a853-c2ed8731ee3b.jpg)
![SharedScreenshot3](https://user-images.githubusercontent.com/54432132/195232348-389de70b-1eb1-462a-96ae-4723427998ba.jpg)
*scopes*

この処理が終わるとslackのワークスペースにアプリ名と同じ名前のbotが追加されます。

![bot](https://user-images.githubusercontent.com/54432132/195630925-1fdfb096-15e0-4a54-ba3d-dbf2354b9bce.jpg)
*botの追加*

変更を保存すると、Bot User Outh Tokenが生成されるのでそれを控えます。 
tokenの場所は[他のサイト](https://reffect.co.jp/html/slack)を参照ください。
:::note warm
User Outh Tokenと間違えると後のslashコマンドでエラーが発生します。 
:::

![token](https://user-images.githubusercontent.com/54432132/195232793-45100e7c-8645-4362-8c6d-09ebc08d0cb1.jpg)

最後に得られたtokenをプログラム内に組み込みます。
このトークンはslack apiを用いるのに必要です。

htdocsフォルダに.envという名前でファイルを作成し、先ほど控えたtokenを記述します。.envの中身は以下のようにしてください。
>SlackToken="xoxb-xxxxxxxxxxxxxxxxx-xxxxxxxxxxxxxx"  
>Url=""

## ngrokのインストール
ngrokとは開発サーバーを公開するためのツールです。slackのslash commandに使います。
[ngrokダウンロードページ]()からngrokをダウンロードし解凍します。解凍したフォルダ内のngrok.exeを実行し、ngrokし、以下のコマンドを実行します。

>ngrok http {ローカルサーバのurl}  
>例 ngrok http localhost:8080

![ngrok](https://user-images.githubusercontent.com/54432132/195240188-8f85b1b0-07e0-46b3-92e1-6d65c94c31a4.jpg)
*ngrok install*

成功すると下の画像のようにurlが発行され、開発サーバーが公開されます。  
:::note warm
urlに初回でアクセスすると、トークンをの入力を求められるので指示に従ってください。
::: 

![ngrokurl](https://user-images.githubusercontent.com/54432132/195240942-a48a6aad-f925-4ef1-a433-871433fb0f32.jpg)


最後にこのurlをプログラムとslackに組み込みます。まず、先ほど設定したhtdocs/.envファイルを開き、得られたurlを記載します。
>SlackToken="xoxb-xxxxxxxxxxxxxxxxxx-xxxxxxxxxxxxxx"
>Url="https://xxxxxxxxxxxxxxxxxxxx.jp.ngrok.io"

次にslackAPIのslashコマンドを設定します。[slackAPIページ](https://api.slack.com/lang/ja-jp)からyour appの先ほど作成したアプリを開き、左端のfeature欄からslash commandをクリックしてください。
![slackwebap](https://user-images.githubusercontent.com/54432132/195242030-8bd74997-a3b1-4284-a011-c2829e224333.jpg)

create new commandから次の二つのコマンドを作成します。{URL}にはngrokのurlを記載してください。

![SharedScreenshot4](https://user-images.githubusercontent.com/54432132/195242159-c82a4c5c-1ee2-4328-9019-e65950bfd970.jpg)
![SharedScreenshot5](https://user-images.githubusercontent.com/54432132/195242166-1c87271b-2962-4d43-a5d2-278c734f7f27.jpg)


## タスクスケジューラの設定
この設定は朝七時にスケジュールをユーザーへ通知する機能と生成されたワンタイムurlに有効期限をつける機能のために行ないます。もし設定がうまくいかなくとも他の機能には影響しません。最終的にタスクは削除してください。

htdocs/cronフォルダ内に以下のファイルにファイルのパスを記載する場所があるので目的のファイルのパスを記載してください。
* batch.vbs
* command.bat
* deleteTokenBatch.vbs
* deleteTokenCommand.bat

次にタスクスケジューラを設定します。特定のプログラムを指定した時間に実行するためにあるwindows標準の機能です。
次の二つのプログラムをタスクスケジューラで設定します。
* batch.vbs
* deleteTokenBatch.vbs

アプリ検索欄からタスクスケジューラを開き、操作ウィンドウの基本タスクの生成を押します。次のように設定します。

* batch.vbs
名前:通知
トリガー:毎日
開始:次の朝7時
間隔:1日
操作:プログラムの実行
プログラム:wscript
引数:batch.vbsのパス

* deleteTokenBatch.vbs
名前:トークン削除
トリガー:パソコン起動時
操作:プログラムの実行
プログラム:wscript
引数:deleteTokenBatch.vbsのパス
完了を押した後、プロパティからトリガーの実行間隔を1分に設定。

![taskname](https://user-images.githubusercontent.com/54432132/195244969-4329baa8-8166-4b5a-bdcc-a4983cd5382d.jpg)
![kidouji](https://user-images.githubusercontent.com/54432132/195244977-d30e8b75-cb76-4248-bb43-6dffa185cffd.jpg)
![puroguramunokaisi](https://user-images.githubusercontent.com/54432132/195245010-dff8716c-4a3a-4c20-b70b-6c74b4e24b89.jpg)
![puroguramusyousai](https://user-images.githubusercontent.com/54432132/195245022-ccaba1cf-29e5-4ca1-ae7c-5b33ca19ff1f.jpg)