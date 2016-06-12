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
$link = mysql_connect($dbServer,$dbUser,$dbPass) or exit("Connection is fall");
		if (mysql_select_db("shashki")) {
			$SQLrequest = "SELECT * from GAMER where GAMER_WINS>0 OR  GAMER_PARITET>0  ORDER BY GAMER_WINS DESC, GAMER_LOSE, GAMER_PARITET LIMIT 10";
			if ($result = mysql_query( $SQLrequest)) { // вытаскиваем из  базы  запись  юзера
			//$gamerinfo = mysql_fetch_array($result, MYSQL_ASSOC);
	echo '		<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<meta http-equiv="Refresh" content="55; URL=game.php">

<title>ШАШКИ-ОНЛАЙН: Десятка лучшех</title>
</head>

<body>
<table width="100%" id="table1" height="488">
	<tr>
		<td height="67" width="269">&nbsp;</td>
		<td height="67" width="380">&nbsp;</td>
		<td height="67">&nbsp;</td>
	</tr>
	<tr>
		<td height="279" width="269">&nbsp;</td>
		<td height="279" width="374" style="font-family: Arial; font-size: 10pt; border: 3px double #C0C0C0; background-color: #9999FF">
		<table width="100%" id="table3" bgcolor="#D7E3FF">
			<tr>
				<td width="96%" height="38" bgcolor="#EAEAFF">
				<img border="0" src="imgs/logo.gif" width="98" height="26" style="border: 1px dotted #D7E3FF" alt="SHASHKI"></td>
			</tr>
			<tr>
				<td>
				<p align="center"><b><font color="#333399">Десятка&nbsp; лучших&nbsp; 
				игроков</font></b></td>
			</tr>
		</table>
		<p align="center">&nbsp;</p>
		<table width="100%" id="table2" style="border: 1px dotted #D7E3FF">
			<tr>
				<td width="28%" align="center" style="border: 1px dotted #D7E3FF">
				<b><font size="2">место</font></b></td>
				<td width="36%" align="center" style="border: 1px dotted #D7E3FF">
				<b><font size="2">имя</font></b></td>
				<td width="33%" align="center" style="border: 1px dotted #D7E3FF">
				<b><font size="2">побед</font></b></td>
			</tr>
			';
			$i=1;
			while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
											
				echo '<tr> <td width="28%" style="border: 1px dotted #D7E3FF">'.$i.'</td>
				<td width="36%" style="border: 1px dotted #D7E3FF"><a href="gamer_detiles.php?gamer='.$line['GAMER_CODE'].'">
						'.$line['GAMER_LOGIN'].'</a></td>
				<td width="33%" style="border: 1px dotted #D7E3FF">'.$line['GAMER_WINS'].'</td></tr>';
						
				$i++;
					}
			echo'

			
				
			
			</table>
		<p align="center">&nbsp;<p align="center"><b>Нажмите <a href="game.php">здесь</a> чтобы&nbsp; 
		вернуться в игру</b><p align="center">&nbsp;</td>
		<td height="279">&nbsp;</td>
	</tr>
	<tr>
		<td width="269">&nbsp;</td>
		<td width="380">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
</table>
</body>

</html>';
}else {
	echo '<meta http-equiv="Refresh" content="0; URL=game.php">';
	exit();
}
}else{
	echo '<meta http-equiv="Refresh" content="0; URL=game.php">';
	exit();
}
?>