<?php
session_start(); //S_SESSIONを利用できるようになる。

$message = '';
try {
    $DBSERVER = 'localhost';
    $DBUSER = 'board'; //接続時のID
    $DBPASSWD = 'boardpw'; //接続時のpassword
    $DBNAME = 'board';

    /*dsn...data source name
    接続時のDSN*/
    $dsn = 'mysql:' //データベース名
        . 'host=' . $DBSERVER . ';' //ホスト名
        . ' dbname = ' . $DBNAME . '; '
        . 'charset=utf8';
    /*--
    PDOオブジェクトの作成
    第一引数...DSNの指定,第二引数...データベース接続のID, 第三引数...データベース接続のpassword
    */
    $pdo = new PDO($dsn, $DBUSER, $DBPASSWD, array(PDO::ATTR_EMULATE_PREPARES => false));
} catch (Exception $e) {
    $message = "接続に失敗しました： {$e->ggetMessage()}";
}

//入力がすべて入っていたらユーザーを作成する
if(!empty($_POST['name']) && !empty($_POST['mail']) && !empty($_POST['password'])) {
    $name = $_POST['name'];
    $mail = $_POST['mail'];
    $password = $_POST['password'];

    $sql = 'INSERT INTO `users` (name, mail, password, created, modified)';
    $sql .= ' VALUES (:name, :mail, :password, NOW(), NOW())';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $name, \PDO::PARAM_STR);
    $stmt->bindValue(':mail', $mail, \PDO::PARAM_STR);
    $stmt->bindValue(':password', $password, \PDO::PARAM_STR);
    $result = $stmt->execute();
    if($result) {
        $message = 'ユーザーを作成しました';
        $_SESSION['id'] = $pdo->lastInsertId();
        $_SESSION['name'] = $name;
        $_SESSION['mail'] = $mail;
        $_SESSION['password'] = $password;
    } else {
        $message = '登録に失敗しました';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>新規作成</title>
</head>
<body>
<header>
    <div>
        <a href = "/board/index.php">TOP</a>
        <a href = "/board/register.php">新規作成</a>
        <a href = "/board/login.php">ログイン</a>
        <a href = "/board/logout.php">ログアウト</a>
    </div>
    <h1>新規作成</h1>
</header>
<div>
    <div style = "color: red">
       <?php echo $message; ?>
    </div>
    <!--
    formタグ...お問い合わせ、メール送信のフォームがパパっと作れる。
    facebookのログイン画面、twitterのアカウント登録画面等にも使われている。
    action属性とmethod属性を指定してあげる。:
    -action属性
        必ず指定。
        フォームボタンの送信ボタンを押して送信されるデータの送信先を指定する。
        データの送信先...URI
        指定するのはデータを受け渡す処理をしてくれるサーバのCGIプログラムのURI
        CGI...Common Gateway Interface
        クライアント側のwebブラウザの要求に応じてwebサーバが外部プログラムを呼び出して、
        その実行結果がHTTpを介してクライアントのWebブラウザに送信される仕組み。
        https://www.infraexpert.com/study/tcpip16.5.html
        *CGIでできることの一つに、DBとの接続がある。
        <form action="CGIプログラムのURI">
    -method属性
        必須ではない。
        送信するときの転送方法を指定する。
        getとpostがある。
        -get
            入力したフォーム内容のデータがURIにくっついて送信される。
        -post
            入力したフォーム内容はURIとは別の場所に保管されてデータが送信される。
            内容が外側からの表示では見ることができないので安全性が高い。
    -->
    <form action = "register.php" method = "post">
        <label>メールアドレス：<input type = "email" name = "mail"/></label><br/>
        <label>パスワード：<input type = "password" name = "password"/></label><br/>
        <label>名前：<input type = "text" name = "name"/></label><br/>
        <input type="submit" value="新規登録">
    </form>
</div>
</body>
</html>