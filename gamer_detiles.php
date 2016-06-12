<?php
session_start();

$AuthorizeSession = & $_SESSION['Auth']; 
require_once 'dbSettings.php';
if (!isset($AuthorizeSession['isAutharize'])){
	echo ' 
			<meta http-equiv="Refresh" content="0; URL=index.html">
		';
		exit();
} 
if ($AuthorizeSession['isAutharize']==false){
	echo ' 
			<meta http-equiv="Refresh" content="0; URL=index.html">
		';
		exit();
}
if (isset ($_GET['gamer'])){
		$link = mysql_connect($dbServer,$dbUser,$dbPass) or exit("Connection is fall");
		if (mysql_select_db("shashki")) {
			$SQLrequest = "SELECT * from GAMER where GAMER_CODE=".$_GET['gamer']."";
			if ($result = mysql_query( $SQLrequest)) { // вытаскиваем из  базы  запись  юзера
						$gamerinfo = mysql_fetch_array($result, MYSQL_ASSOC);
			
		
echo'
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<meta http-equiv="Refresh" content="55; URL=game.php">

<title>Информация о пользователе</title>
</head>

<body>
<table width="100%" id="table1" height="488">
	<tr>
		<td height="149" width="296">&nbsp;</td>
		<td height="149" width="325">&nbsp;</td>
		<td height="149">&nbsp;</td>
	</tr>
	<tr>
		<td height="197" width="296">&nbsp;</td>
		<td height="197" width="319" style="font-family: Arial; font-size: 10pt; border: 3px double #C0C0C0; background-color: #FFCC00">
		<p align="center"><b><span lang="en-us">&nbsp;</span>Информация о 
		пользователе</b></p>
		<p align="center"><b><font size="3">';
echo 	$gamerinfo['GAMER_LOGIN'];
echo	'</font></b></p>
		<table width="100%" id="table2">
			<tr>
				<td colspan="2" align="center" bgcolor="#FFCC99"><b>История игр</b></td>
			</tr>
			<tr>
				<td width="54%">Побед</td>
				<td width="43%">';
	    echo 	$gamerinfo['GAMER_WINS'];
echo            '</td>
			</tr>
			<tr>
				<td width="54%">Поражений</td>
				<td width="43%">';
		echo 	$gamerinfo['GAMER_LOSE'];
echo			'</td>
			</tr>
			<tr>
				<td width="54%">В ничью</td>
				<td width="43%">';
		echo 	$gamerinfo['GAMER_PARITET'];
		echo 	'</td>
			</tr>
		</table>
		<p align="center"><b>Нажмите <a href="game.php">здесь</a> чтобы&nbsp; 
		вернуться в игру</b><p align="center">&nbsp;</td>
		<td height="197">&nbsp;</td>
	</tr>
	<tr>
		<td width="296">&nbsp;</td>
		<td width="325">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
</table>
</body>

</html>';
}
else{
	echo '<meta http-equiv="Refresh" content="0; URL=game.php">';
	exit();
}
}else{
	echo '<meta http-equiv="Refresh" content="0; URL=game.php">';
	exit();
}
}
else{
	echo '<meta http-equiv="Refresh" content="0; URL=game.php">';
	exit();
}
?>