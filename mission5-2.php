<?php

$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

$sql = "CREATE TABLE IF NOT EXISTS TECH2"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
        . "date DATETIME,"
        . "pass TEXT"
	.");";
	$stmt = $pdo->query($sql);

//テーブル一覧
/*$sql ='SHOW TABLES';
	$result = $pdo -> query($sql);
	foreach ($result as $row){
		echo $row[0];
		echo '<br>';
	}
	echo "<hr>";*/

//テーブル中身確認
/*$sql ='SHOW CREATE TABLE TECH';
	$result = $pdo -> query($sql);
	foreach ($result as $row){
		echo $row[1];
	}
	echo "<hr>";*/

$post=date('y/m/d H:i:s');
$personal_name="";
$contents="";

//編集選択
if(!empty($_POST['editb'])) {

//定義
 $sql = 'SELECT * FROM TECH2';
 $stmt = $pdo->query($sql);
 $results = $stmt->fetchAll();
  foreach ($results as $row){ 
  $edit = $_POST['edit'];
  $passE = $_POST['passE'];
  $sql = 'update TECH2 set name=:name,comment=:comment,date=:date,pass=:pass where id=:id';
  $id = $row['id'];
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':name', $name, PDO::PARAM_STR);
  $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
  $stmt->bindParam(':date', $post, PDO::PARAM_STR);
  $stmt->bindParam(':pass', $passE, PDO::PARAM_STR);
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    //編集番号あっててパスあってれば編集フォームへ
    if($row['id'] == $edit && $row['pass'] == $passE){  
    $bango = $row['id'];          
    $Pname = $row['name'];         
    $Text = $row['comment'];   
    $Pass = $row['pass'];
    }

    //編集番号あっててパス間違ってたらエラー
    elseif($row['id'] == $edit && $row['pass'] != $passE){
    echo "パスワードが正しくありません";
    }
  }

    //編集番号なければエラー
    if(empty($edit)){
    echo "編集対象番号を入力してください。";
    }

}

//投稿フォーム
if(!empty($_POST['send'])){

//定義
$personal_name=$_POST['name'];
$contents=$_POST['text'];
$passC=$_POST['passC'];
$emode=$_POST['emode'];

 //名前コメントパスなければエラー表示
 if(empty($personal_name) or empty($contents) or empty($passC)){
 echo "未入力の項目があります。";
 }

 //名前コメント編集が空じゃない＝編集用フォーム
 elseif(!empty($emode)){


 //$rowにして処理可能に
 $sql = 'SELECT * FROM TECH2';
 $stmt = $pdo->query($sql);
 $results = $stmt->fetchAll();
      foreach ($results as $row){ 
      $id = $emode;
      $sql = 'update TECH2 set name=:name,comment=:comment,date=:date,pass=:pass where id=:id';
      $stmt = $pdo->prepare($sql);
      $stmt -> bindParam(':name', $personal_name, PDO::PARAM_STR);
      $stmt -> bindParam(':comment', $contents, PDO::PARAM_STR);
      $stmt -> bindParam(':date', $post, PDO::PARAM_STR);
      $stmt -> bindParam(':pass', $Pass, PDO::PARAM_STR);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      }
 }

 //名前あり、編集対象番号が空　=　通常投稿フォーム
  else{
  $sql = $pdo -> prepare("INSERT INTO TECH2 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
  $sql -> bindParam(':name', $personal_name, PDO::PARAM_STR);
  $sql -> bindParam(':comment', $contents, PDO::PARAM_STR);
  $sql -> bindParam(':date', $post, PDO::PARAM_STR);
  $sql -> bindParam(':pass', $passC, PDO::PARAM_STR);
  $sql -> execute();
  }

}

//削除フォーム
if(!empty($_POST['deleteb'])){

//定義
$delete=$_POST['delete'];
$passD=$_POST['passD'];
$sql = 'SELECT * FROM TECH2';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
  foreach ($results as $row){ 

    //削除対象あり、パスもあってたら削除
    if($row['id']==$delete && $row['pass']==$passD){
    $id = $row['id'];
    $sql = 'delete from TECH2 where id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    }

    //パスが間違ってたらエラー
    elseif($row['id']==$delete && $row['pass']!=$passD){
    echo "パスワードが正しくありません。";
    }

  }
    //削除対象番号なし=エラー
    if(empty($delete)){
    echo "削除対象番号を入力してください。";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
   
    <title>石原ちゃんねる_簡易掲示板</title>
</head>

<body>
<h1>石原ちゃんねる</h1>

<form action="mission5-1.php" method="POST">

投稿：<input type="text"  placeholder="名前" name="name"
value="<?php if(!empty($Pname)) {echo $Pname;} ?>" >
&nbsp;

<input type="text"  placeholder="コメント" name="text"
 value="<?php if(!empty($Text)){echo $Text;} ?>" >
&nbsp;

<input type="hidden" name="emode"
value= "<?php if(!empty($Pname)){echo $edit;}?>" >

<input type="text" placeholder="パスワード" name="passC"
value="<?php if(!empty($Pass)){echo $Pass;}?>" >

<input type="submit" value="送信" name="send">
<br><br>

削除：<input type="text"  placeholder="削除対象番号" name="delete">
&nbsp;

<input type="text" placeholder="パスワード" name="passD">

<input type="submit" value="削除" name="deleteb">
<br><br>

編集：<input type="text" placeholder="編集対象番号" name="edit">
&nbsp;

<input type="text" placeholder="パスワード" name="passE">

<input type="submit" value="編集" name="editb">
<br><br>
<hr>

</form>
</body>
</html>

<?php

//表示部分
$sql = 'SELECT * FROM TECH2';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
	echo $row['id'].' ';
	echo $row['name'].' ';
        echo $row['date'].'<br>';
        echo $row['comment'];
	echo "<hr>";
	}
?>

