# システム設計2022


# 必要なもの
* windows10
* xampp 3.3.0
* sqlite3 3.39.3
* ngrok
* composer
* twig
* ramesy/uuid
* phpdotenv

# 環境設定の流れ
- xamppのインストール
- composerのインストール
   - twig
   - ramsey/uuid
   - phpdotenv
-　プログラムのインストール
- sqliteのインストール
- プログラムのインストール
- slack apiの設定
- ngrokのインストール
- タイムスケジューラの設定

## xamppのインストール
[xamppダウンロードページ](https://www.apachefriends.org/jp/download.html)

![xampphp](https://user-images.githubusercontent.com/54432132/195221899-3313dc6a-5691-4b68-b66b-03b250c92ccb.jpg)

## composerのインストール
[composerダウンロードページ](https://getcomposer.org/download/)

![compsoer](https://user-images.githubusercontent.com/54432132/195222841-609fb5e3-1f80-488e-b2f5-2fa4ce4946d7.jpg)

## プログラムのインストール
[github](https://github.com/Earth-worm/system-design-2022)からzipとしてダウントールし、xampp/htdocsフォルダに展開してください。

![git](https://user-images.githubusercontent.com/54432132/195223688-3130d9f2-5b55-430f-8ffa-d0c05029ab62.jpg)

##  sqlite3のインストール
[sqlite3ダウンロードページ](https://www.sqlite.org/download.html)からツールをダウンロードし、sqlite3.exeをxampp/htdocsに設置、もしくはpathを通す。
xampp/htdocsフォルダ内でsqlite3 db.sqlite3と実行し、dbにアクセスできれば成功です。

![sqlite](https://user-images.githubusercontent.com/54432132/195224288-1f57f66c-e7d5-45c9-bd92-e02a648377f4.jpg)

成功例
> C:\xampp\htdocs>sqlite3 db.sqlite3<br>
> SQLite version 3.39.3 2022-09-05 11:02:23<br>
> Enter ".help" for usage hints.<br>
> sqlite> .tables<br>
> task   token  user<br>
> sqlite><br>

