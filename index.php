<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>App TaskList</title>
</head>
<body>
	<h1>TaskList</h1>
	<form method="POST" action="index.php">
		<label>Nueva tarea:</label>
		<input type="text" name="introducir">
		<button>Agregar tarea</button>
	</form>
	<?php
		$db = parse_url(getenv("DATABASE_URL"));
		$pdo = new PDO("pgsql:" . sprintf("host=%s;port=%s;user=%s;password=%s;dbname=%s",
		$db["host"],
		$db["port"],
		$db["user"],
		$db["pass"],
		ltrim($db["path"], "/")
		));
		if(isset($_GET['hecho'])) {
			$value = $_GET['hecho'];
			$cambiar = $pdo->exec("update tareas set pendientes=true where id = '$value'");
		}
		if(isset($_GET['sinHacer'])) {
			$value = $_GET['sinHacer'];
			$cambiar = $pdo->exec("update tareas set pendientes=false where id = '$value'");
		}
		if(isset($_GET['borrar'])) {
			$value = $_GET['borrar'];
			$borrar = $pdo->exec("delete from tareas where id = '$value'");
		}
		if (isset($_POST["introducir"])) {
			$value = $_POST["introducir"];
			$query = $pdo->prepare("insert into tareas (lista_tareas, pendientes) values ('$value',false)");
			$query->execute();
		}
		echo "<br><br>";
		$query = $pdo->prepare("select * FROM tareas");
		$query->execute();
		$query2 = $pdo->prepare("select * FROM tareas");
		$query2->execute();
		echo "<h3>Cosas pendientes</h3> <br>";
		foreach ($query as $row) {
			  if ($row['pendientes'] == 0) {
				  $idprimaria = $row['id'];
				  echo $row['lista_tareas'] ."\t"."<a href='?hecho=$idprimaria'>Hecho</a>"."\t"."<a href='?borrar=$idprimaria'>Borrar</a>". "<br>";
			  }
		  }
		echo "<br><br>";
		echo "<h3>Cosas no pendientes</h3> <br>";
		foreach ($query2 as $row) {
			if ($row['pendientes'] == 1) {
				  $idprimaria = $row['id'];
				  echo $row['lista_tareas'] ."\t"."<a href='?sinHacer=$idprimaria'>Sin hacer</a>"."\t"."<a href='?borrar=$idprimaria'>Borrar</a>". "<br>";
			  }
		}

		$e= $query->errorInfo();
		if ($e[0]!='00000') {
			echo "\nPDO::errorInfo():\n";
			die("Error accedint a dades: " . $e[2]);
		}
 ?>
</body>
</html>
