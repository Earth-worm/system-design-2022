# システム概要
このシステムはslackを用いて、チームとスケジュールを共有します。

# ディレクトリ構成図
<pre>
/htdocs  
├── apps 機能ごとをまとめたフォルダ  
│   ├── auth 認証周りの機能フォルダ  
│   │   ├── signin.php アカウント作成    
│   │   ├── login.php  ログイン  
│   │   ├── logout.php ログアウト  
│   │   └── urls.php urlとphpファイルをマッピング  
│   │
│   ├── schedule スケジュール周りの機能フォルダ  
│   │   ├── createTask.php スケジュールの作成  
│   │   ├── editTask.php スケジュールの編集  
│   │   ├── taskList.php  スケジュールの表示  
│   │   └── urls.php  
│   │
│   └── slash slackのslashコマンド周りの機能  
│       ├── schedule.php スケジュールのリンクを得る  
│       ├── task.php タスクの作成  
│       └── urls.php  
│
├── cron 時間指定で実行されるプログラム  
├── statics 静的ファイル  
├── view htmlファイル(twig)  
│   ├── auth authアプリのhtmlファイル  
│   ├── schedule scheduleのhtmlファイル  
│   ├── _base.html htmlのテンプレート  
│   ├── 404.html 404ページのhtmlファイル  
│   └── home.html インデックスページのhtmlファイル  
│
├── xampp  
├── db.sqlite3 データベース  
├── home.php インデックスページ  
├── urls.php ルーティングプログラム  
├── 404.php 404のリダイレクト先  
├── utils.php 関数やクラスをまとめたファイル  
├── README.mb  
├── .env 環境変数の保存先  
└── .htaccess 内部リダイレクト設定ファイル  
</pre>
* スケジュールの表示
<img src="https://user-images.githubusercontent.com/54432132/195247053-7ddc8fc9-d0ca-426f-8448-76686e638961.jpg" width=""><p>スケジュール表示</p>
<hr>

* スケジュールの作成
<img src="https://user-images.githubusercontent.com/54432132/195247054-c7b94fde-352f-4e29-9494-09ce885e6904.jpg" width=""><p>スケジュール作成</p>
<hr>

* slackのスラッシュコマンドからスケジュールの作成
>/task {名前}<br>
>{日にち}<br>
>{休日か T or F}<br>
>{時間}<br><br>
>例
>/task 誕生日<br>
>2022-04-13<br>
>T<br>
>12:00<br>
<hr>

* slackのスラッシュコマンドからスケジュールの表示
>/schedule @user
<br>

# 機能とURL
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
* bootstrap4(CDNで読み込むため設定はありません。)

# 設定の流れ
1. xamppのインストール
2. composerのインストール
   - twig
   - ramsey/uuid
   - phpdotenv
3. プログラムのインストール
4. sqliteのインストール
5. slack apiの設定
6. ngrokのインストール
7. タイムスケジューラの設定

# 1. XAMPPのインストール
開発環境の構築にXAMPPを用います。  
アプリケーションの開発には様々なソフトウェアが必要です。XAMPPとはそれらソフトウェアをまとめて扱うことができるツールです。  
[XAMPPダウンロードページ](https://www.apachefriends.org/jp/download.html)からXAMPPのインストーラをダウンロードし、実行してください。
<img src="https://user-images.githubusercontent.com/54432132/195221899-3313dc6a-5691-4b68-b66b-03b250c92ccb.jpg" width=""><pn>XAMPPダウンロード</p>
<br>
成功するとxampp control panelが使えるようになります。<br>
<img src="https://user-images.githubusercontent.com/54432132/195582301-e6741f6e-4328-4d68-bc8e-812682c8c4cc.jpg" width="">
<p>XAMPP control panel</p><br>

# 2. composerのインストール
composerとはプログラムに使う拡張機能(パッケージ)を管理するツールで、パッケージをインストールするのに使います。  
[composerダウンロードページ](https://getcomposer.org/download/)からインストーラをダウンロードし、実行してください。
<div align="center">
<img src="https://user-images.githubusercontent.com/54432132/195222841-609fb5e3-1f80-488e-b2f5-2fa4ce4946d7.jpg" width=""><p>composer install</p>
</div><br>

# twig,uuid,phpdotenvのインストール
システムに必要な3つのパッケージをcomposerでインストールします。  
xampp control panelからshellを開き、以下のコマンドを実行。

>composer require vlucas/phpdotenv<br>
>composer require ramsey/uuid<br>
>composer require "twig/twig:~1.0"<br>

<div align="center">
<img src="https://user-images.githubusercontent.com/54432132/195228944-68831b5b-f291-478f-a5ba-dedbc44fdb96.jpg" width="80%"><p>shell</p>
</div>

次のコマンドをshellに入力すると、インストールされているパッケージの一覧を見ることができます。先ほどインストールしたパッケージを確認してみましょう。

>composer show -i

<img src="https://user-images.githubusercontent.com/54432132/195586014-cf316a07-464b-42f3-9ec7-afcd3c4d107b.jpg" width="">*パッケージ一覧*

# 3. プログラムのインストール
phpを実行する設定は終わりました。次にこのシステムをインストールします。  
[github](https://github.com/Earth-worm/system-design-2022)からzipとしてダウンロードし、xampp/htdocsフォルダに展開してください。

<img src="https://user-images.githubusercontent.com/54432132/195223688-3130d9f2-5b55-430f-8ffa-d0c05029ab62.jpg" width=""><p>git download</p>

xampp/htdocsフォルダの中身は次のようになります。2,3個関係ないファイルがありますが、気にしないでください。

<img src="https://user-images.githubusercontent.com/54432132/195588366-56117042-fa59-4408-b273-d5d11f737218.jpg" width=""><p>htdocs中身</p>

# 4. sqlite3のインストール
webアプリで扱う情報は基本的にデータベース(DB)に保管されます。このシステムではDBにsqliteを用います。  
[sqlite3ダウンロードページ](https://www.sqlite.org/download.html)からツールをダウンロードし、ダウンロードしたフォルダ内からsqlite3.exeをxampp/htdocsに設置してください。

<img src="https://user-images.githubusercontent.com/54432132/195224288-1f57f66c-e7d5-45c9-bd92-e02a648377f4.jpg" width=""><p>sqlite installer</p>

コマンドプロンプト(cmd)からhtdocsディレクトリへ移動して次のコマンドを入力し、データベースのテーブルを取得できれば成功です。

>cd {htdocsの絶対パス}&emsp;//ディレクトの移動  
>sqlite3 db.sqlite3&emsp;//DBにアクセス  
>.tables&emsp;//テーブルの取得  

<div align="center">
<img src="https://user-images.githubusercontent.com/54432132/195589998-7734181d-a630-4586-8824-17f5340ef0c2.jpg" width="50%"><p>テーブルリストの取得</p>
</div>

# 5. slack apiの設定

まずはslackのワークスペースを作成し、[slackAPIページ](https://api.slack.com/)からアプリ作成します。  
アプリの作成方法は説明が長くなるので[他のサイト](https://reffect.co.jp/html/slack)を参考にしてください。permissionから説明します。  
次にpermissionを与えます。画像のpermissionsをクリック。
<div align="center">
<img src="https://user-images.githubusercontent.com/54432132/195231251-33073db7-fe8d-46da-b431-712ba618079b.jpg" width=""><p>permission</p>
</div><br>

permissionページの下の方にScopeを追加する欄があるので、bot token scopesに次のscopeを追加します。

<div align="center">
<img src="https://user-images.githubusercontent.com/54432132/195232286-deb93759-4fe4-4159-a853-c2ed8731ee3b.jpg" width="70%">
<img src="https://user-images.githubusercontent.com/54432132/195232348-389de70b-1eb1-462a-96ae-4723427998ba.jpg" width="70%"><p>scopes</p>
</div>
<br>
この処理が終わるとslackのワークスペースにアプリ名と同じ名前のbotが追加されます。<br><br>
<div align="center">
<img src="https://user-images.githubusercontent.com/54432132/195630925-1fdfb096-15e0-4a54-ba3d-dbf2354b9bce.jpg" width=""><p>ボットの追加</p>
</div><br>

変更を保存すると、Bot User OAuth Tokenが生成されるのでそれを控えます。 <br>tokenの場所は[他のサイト](https://reffect.co.jp/html/slack)をご参照ください。  
>**Warning**  
>User OAuth Tokenと間違えると後のslashコマンドでエラーが発生します。
 
<div align="center">
<img src="https://user-images.githubusercontent.com/54432132/195232793-45100e7c-8645-4362-8c6d-09ebc08d0cb1.jpg" width="80%"><p>slack api token</p>
</div>

最後に得られたtokenをプログラム内に組み込みます。
このトークンはslack apiを用いるのに必要です。

htdocsフォルダに.envという名前でファイルを作成し、先ほど控えたtokenを記述します。.envの中身は以下のようにしてください。
>SlackToken="xoxb-xxxxxxxxxxxxxxxxx-xxxxxxxxxxxxxx"  
>Url=""

# 6. ngrokのインストール
ngrokとは開発サーバーを公開するためのツールです。slackのslash commandに使います。
[ngrokダウンロードページ](https://ngrok.com/download)からngrokをダウンロードし解凍します。解凍したフォルダ内のngrok.exeを実行し、ngrokし、xamppのサーバーを立ち上げてから以下のコマンドを実行します。

<div align="center">
<img width="492" alt="image" src="https://user-images.githubusercontent.com/54432132/196305971-f650825e-8162-4dd9-b465-7606d020e9c5.png" width="80%">
<p>サーバー立ち上げ</p>
</div>

>ngrok http {ローカルサーバーのurl}  
>例 ngrok http localhost:80

<div align="center">
<img src="https://user-images.githubusercontent.com/54432132/195240188-8f85b1b0-07e0-46b3-92e1-6d65c94c31a4.jpg" width="80%"><p>ngrok install</p>
</div>

成功すると下の画像のようにurlが発行され、開発サーバーが公開されます。  
>**Note**  
>初回にurlにアクセスすると、トークンをの入力を求められるます。ngrokの指示に従ってください。

<div align="center">
<img src="https://user-images.githubusercontent.com/54432132/195240942-a48a6aad-f925-4ef1-a433-871433fb0f32.jpg" width="80%"><p>ngrok url</p>
</div>
<br>

最後にこのurlをプログラムとslackに組み込みます。まず、先ほど設定したhtdocs/.envファイルを開き、得られたurlを記載します。
>SlackToken="xoxb-xxxxxxxxxxxxxxxxxx-xxxxxxxxxxxxxx"
>Url="https://xxxxxxxxxxxxxxxxxxxx.jp.ngrok.io"

次にslackAPIのslashコマンドを設定します。[slackAPIページ](https://api.slack.com/lang/ja-jp)からyour appの先ほど作成したアプリを開き、左端のfeature欄からslash commandをクリックしてください。
<img src="https://user-images.githubusercontent.com/54432132/195242030-8bd74997-a3b1-4284-a011-c2829e224333.jpg" width="">*manage app*

create new commandから次の二つのコマンドを作成します。{URL}にはngrokのurlを記載してください。

<div align="center">
<img src="https://user-images.githubusercontent.com/54432132/195242159-c82a4c5c-1ee2-4328-9019-e65950bfd970.jpg" width="70%">

<img src="https://user-images.githubusercontent.com/54432132/195242166-1c87271b-2962-4d43-a5d2-278c734f7f27.jpg" width="70%">
<p>スラッシュコマンド</p>
</div>
<br>

# 7. タスクスケジューラの設定
この設定はユーザへのスケジュールの通知とワンタイムurlに有効期限設定のために行ないます。もし設定ができなくても他の機能には影響しません。
>**Warning**  
>プロジェクトを削除する時に一緒に削除してください。

htdocs/cronフォルダ内に以下のファイルにファイルのパスを記載する場所があるので目的のファイルのパスを記載してください。
* batch.vbs
* command.bat
* deleteTokenBatch.vbs
* deleteTokenCommand.bat

次にタスクスケジューラを設定します。特定のプログラムを指定した時間に実行するためにあるwindows標準の機能です。
次の二つのプログラムをタスクスケジューラで設定します。
* batch.vbs
* deleteTokenBatch.vbs
<hr>
アプリ検索欄からタスクスケジューラを開き、操作ウィンドウの基本タスクの生成を押します。
<br>

<img src="https://user-images.githubusercontent.com/54432132/195638039-ca312672-7f8c-484f-8290-c4464fd181bf.jpg" width=""><p>タスクスケジューラ</p>

各プログラムは次のように設定します。
* batch.vbs
名前:notification<br>
トリガー:毎日<br>
開始:次の朝7時<br>
間隔:1日<br>
操作:プログラムの実行<br>
プログラム:wscript<br>
引数:batch.vbsのパス<br>

* deleteTokenBatch.vbs
名前:deleteToken<br>
トリガー:パソコン起動時<br>
操作:プログラムの実行<br>
プログラム:wscript<br>
引数:deleteTokenBatch.vbsのパス<br>
完了を押した後、タスクを右クリック->プロパティ->トリガー->実行間隔を1分に設定。
<br>


<div align="center">
<img src="https://user-images.githubusercontent.com/54432132/195244969-4329baa8-8166-4b5a-bdcc-a4983cd5382d.jpg" width="70%" alt="タスク名設定"><p>タスク名設定</p>
<img src="https://user-images.githubusercontent.com/54432132/195244977-d30e8b75-cb76-4248-bb43-6dffa185cffd.jpg" width="70%"><p>トリガー設定</p>
<img src="https://user-images.githubusercontent.com/54432132/195245010-dff8716c-4a3a-4c20-b70b-6c74b4e24b89.jpg" width="70%"><p>開始プログラム設定</p>
<img src="https://user-images.githubusercontent.com/54432132/195245022-ccaba1cf-29e5-4ca1-ae7c-5b33ca19ff1f.jpg" width="70%"><p>プログラム詳細設定</p>
<img src="https://user-images.githubusercontent.com/54432132/195639232-447d0e54-1974-47f8-9dbe-f9b12bfe92e8.jpg" width="70%"><p>プロパティ</p>
</div>
