<?php
    // DB接続設定
    $dsn = 'データベース';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)); 
    
    $sql = "CREATE TABLE IF NOT EXISTS miss5" //もしまだこのテーブルが存在しないなら
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY," //自動で登録されているナンバリング。
    . "name char(32),"  //名前を入れる。文字列、半角英数で32文字。
    . "comment TEXT,"  //コメントを入れる。文字列、長めの文章も入る。
    . "date TEXT,"
    . "password char(20)"
    .");";
    $stmt = $pdo->query($sql); 
    
    //変数定義
           if(isset($_POST["name"])){
               $name=$_POST["name"];
           }
           
           if(isset($_POST["DelNum"])){
               $DelNum=$_POST["DelNum"];
           }
           if(isset($_POST["UpdateNum"])){
                $UpdateNum=$_POST["UpdateNum"];
            }
           
          if(isset($_POST["NewNum"])){
              $NewNum=$_POST["NewNum"];
          }
          if(isset($_POST["comment"])){
              $comment=$_POST["comment"];
          }
        //パスワード
           if(isset($_POST["password"])){
               $password=$_POST["password"];
           }
           
           if(isset($_POST["DelPass"])){
               $DelPass=$_POST["DelPass"];
           }
           if(isset($_POST["UpPass"])){
               $UpPass=$_POST["UpPass"];
           }
           
    //新規投稿
    //INSERT文 で、データ（レコード）を登録してみましょう。
      //prepare関数。関数内の()がプリペアドステートメント(クエリを実行できる機能)によるSQLで、実行するプリペアドステートメントのSQLをセット。
      //INSERT INTO テーブル名 (列名)VALUES(値)。
      if(!empty($name) && !empty($comment) && !empty($password) && empty($NewNum)){
          $sql = $pdo -> prepare("INSERT INTO miss5 (name, comment, date, password) VALUES (:name, :comment, :date, :password)"); 
    //bindParam関数。プリペアードステートメントで使用するSQL文の中で、プレイスホルダーに値をバインド。
    //bindParam($パラメーターID,$バインドする変数,$PDOデータ型定数)。
          $sql -> bindParam(':name', $sqlname, PDO::PARAM_STR); //$PDOデータ型定数はデフォルトのもの。
          $sql -> bindParam(':comment', $sqlcomment, PDO::PARAM_STR);
          $sql -> bindParam(':date',$date, PDO::PARAM_STR);
          $sql -> bindParam(':password',$sqlpassword, PDO::PARAM_STR);
          $sqlname = $name;
          $sqlcomment = $comment; 
          $date=date("Y-m-d- H:i:s") ;
          $sqlpassword = $password;
          //executeでクエリを実行
          $sql -> execute(); 
      }
      
    //削除機能
             if(!empty($DelNum)){
                   //削除したい行のパスワード取得
                   $id = $DelNum; // idがこの値のデータだけを抽出したい、とする
                   $sql = 'SELECT * FROM miss5 WHERE id=:id ';
                   $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                   $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                   $stmt->execute();                             // ←SQLを実行する。
                   $results = $stmt->fetchAll(); 
                   foreach ($results as $row){
                       $dpass = $row['password'];
                   }
                   //パスワードが一致したとき
                   if($DelPass == $dpass){
                        //データベースのテーブルに登録したデータレコードは、DELETE文 で削除する事が可能
                        $id = $DelNum;
                        //DELETE FROM テーブル名 where 主キー項目=値;
                        $sql = 'DELETE FROM miss5 where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                     //パスワードが不一致のとき
                   }elseif($dpass != $DelPass){
                       echo "パスワードが間違っています"."<br>";
                   }
               }
               
    //編集で起こるエラーをなくすための定義
               $upname="";
               $upcomment="";
               $epass="";  
               
    //編集機能
               if(!empty($UpdateNum)){
                   //編集したい行のパスワードの変数定義
                  $id = $UpdateNum; // idがこの値のデータだけを抽出したい、とする
                  $sql = 'SELECT * FROM miss5 WHERE id=:id ';
                  $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                  $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                  $stmt->execute();                             // ←SQLを実行する。
                  $results = $stmt->fetchAll(); 
                  foreach ($results as $row){
                      $upass = $row['password'];
                      
                  }
                  //パスワードが一致するとき
                  if($UpPass == $upass){
                      $id = $UpdateNum; // idがこの値のデータだけを抽出したい、とする
                      $sql = 'SELECT * FROM miss5 WHERE id=:id ';
                      $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                      $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                      $stmt->execute();                             // ←SQLを実行する。
                      $results = $stmt->fetchAll(); 
                      foreach ($results as $row){
                      $upname = $row['name'];
                      $upcomment = $row['comment'];
                      $epass = $row['password'];
                      }
                  }elseif($UpPass != $upass){
                     echo "パスワードが間違っています"."<br>";
                   }
               }
               
    //名前とコメントの変更           
    if(!empty($name) && !empty($comment) && !empty($NewNum)){                  
        $id=$_POST["NewNum"]; //変更する投稿番号
        $sqlname = $_POST["name"];
        $sqlcomment = $_POST["comment"];
        $date=date("Y-m-d- H:i:s") ;
        $sqlpassword=$_POST["password"];
    
    //UPDATE テーブル名 SET カラム1=値1 WHERE 条件;
        $sql = 'UPDATE miss5 SET name=:name,comment=:comment,date=:date,password=:password WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $sqlname, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $sqlcomment, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':password', $sqlpassword, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
    }
      
    //ブラウザ表示    
    //SELECTで表示させる機能 も記述し、表示もさせる。
     
    $sql = 'SELECT * FROM miss5'; 
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date'].'<br>';
    echo "<hr>";
    }
     
        ?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>mission_5-2.php</title>
    </head>
    <body>
         <b>テーマ「好きなお菓子は？」（パスワードは20字以内でお願いします！）</b><br><br>        
        <form action="" method="post">
            <form action="" method="post">
            <!--名前とコメントのフォーム-->                                                            
            <input type="text" name="name" placeholder="名前" value="<?php echo $upname; ?>">
            <input type="text" name="comment" placeholder="コメント" value="<?php echo $upcomment; ?>">
            <input type="password" name="password" placeholder="パスワードの入力" value="<?php echo $epass; ?>">
            <input type="submit" name="submit"><br><br>
            <!--削除ホーム--> 
            <input type="number" name="DelNum" placeholder="削除対象番号">
            <input type="password" name="DelPass" placeholder="パスワードの入力">
            <input type="submit" name="submit" value="削除"><br><br>
             <!--編集フォーム-->
             <input type="number" name="UpdateNum" placeholder="編集対象番号">
             <input type="password" name="UpPass" placeholder="パスワードの入力">
             <input type="submit" name="submit" value="編集"><br>
             <input type="hidden" name="NewNum" value="<?php if(isset($UpdateNum) != 0){echo $UpdateNum;} ?>">
        </form>
    </body>
</html>