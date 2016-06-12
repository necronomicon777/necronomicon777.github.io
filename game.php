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

echo '<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<meta http-equiv="Refresh" content="25; URL=game.php">

<title>Шашки-онлайн</title>
</head>

<body style="font-family: Arial; font-size: 10px">

<table width="100%" id="table6" style="border-collapse: collapse">
	<tr>
		<td colspan="2" style="border-style: solid; border-width: 0; font-family:arial,sans-serif">
		<table width="100%" id="table7" style="border-collapse: collapse">
			<tr>
				<td width="151" style="font-family: arial,sans-serif">
				<img border="0" src="imgs/logo.gif" width="151" height="37"></td>
				<td style="background-color: #A4B7DB; font-family:arial,sans-serif"><b>&nbsp; Добро пожаловать '.$AuthorizeSession['GAMER_LOGIN'].'</b></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
	<td width="54%" style="font-family: arial,sans-serif">

		<p align="center"><b><font size="4" color="#A4B7DB">
		<a href="top10.php">ТОП10&nbsp; лучших&nbsp; игроков</a></font></b></td>
			<td width="45%" style="font-family: arial,sans-serif">
		<table width="100%" id="table11" bgcolor="#CCFF99">
			<tr>
				<td>
				<form method="POST" enctype="windows-1251" action="gameprocess.php?create=1">
					<p align="center"><b><font face="Arial">Создайте свою&nbsp; 
					игру</font></b></p>
					<table width="100%" id="table12">
						<tr>
							<td width="168">
							<p align="right"><font size="2" face="Arial">Ваши 
							фигуры<span lang="en-us">:</span></font></td>
							<td><font face="Arial">
							<select size="1" name="sel_figuri" style="font-family: Arial; font-size: 10px">
							<option selected value="white_fig">белые</option>
							<option value="black_fig">черные</option>
							</select></font></td>
						</tr>
						<tr>
							<td width="168">
							<p align="right"><font size="2">Таймаут игры</font></td>
							<td><select size="1" name="timeout" style="font-family: Arial; font-size: 10px">
							<option value="3">3</option>
							<option value="5" selected>5</option>
							<option value="10">10</option>
							
							</select><font size="2"> минут неактивноси</font></td>
						</tr>
						<tr>
							<td width="168">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td width="168">&nbsp;</td>
							<td><font face="Arial">
							<input type="submit" value="создать игру" name="create_game" style="font-family: Arial; font-size: 10px"></font></td>
						</tr>
					</table>
				</form></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td width="100%" style="font-family: arial,sans-serif" colspan="2">
		<div align="center">
			<table width="55%" id="table8">
				<tr>
					<td colspan="3" bgcolor="#ECECFF">
					<p align="center">Игры&nbsp; доступные на сервере</td>
				</tr>
				<tr>
					<td width="38%" bgcolor="#A4B7DB"><font size="2">Хозяин  игры</font></td>
					<td width="33%" bgcolor="#A4B7DB"><font size="2">Шашки хозяина игры</font></td>
					<td width="25%" bgcolor="#A4B7DB"><font size="2">
					Присоединиться</font></td>
				</tr>';
			   /*Соединяемся  с сервером*/
				$link = mysql_connect($dbServer,$dbUser,$dbPass) 	or exit("Connection is fall");
	
			/*Выбираем базу данных */
				if (mysql_select_db("shashki")) {
					$SQLrequest = "SELECT t1.OG_CODE,  t1.OG_GAMER1_FIG,  t2.GAMER_LOGIN,  t2.GAMER_CODE FROM GAMER AS t2, ONLINE_GAME AS t1 
										WHERE t2.GAMER_CODE =t1.OG_GAMER1 and t1.OG_WAIT = true";
					$result = mysql_query( $SQLrequest);
				    $colorflag=true;
					$gamer_fig='Белые';
					while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
						if ($colorflag)
							$color = '#ECECFF';
						else
							$color = '#DFDFFF';
						if ($line['OG_GAMER1_FIG'] == 2) 
							$gamer_fig = 'Черные';
						else
							$gamer_fig = 'Белые';
							
						$href_join = 'gameprocess.php?OG='.$line['OG_CODE'].'&join=1';
						
						
						print '<tr>';
					
						print '<td width="38%" bgcolor="'.$color.'"><a href="gamer_detiles.php?gamer='.$line['GAMER_CODE'].'">
						'.$line['GAMER_LOGIN'].'</a></td>';
						print '<td width="33%" bgcolor="'.$color.'">'.$gamer_fig.'</td>';
						print '<td width="25%" bgcolor="'.$color.'"><a href="'.$href_join.'">
							<img border="0" src="imgs/join.gif" width="137" height="20"></a></td>';
						print '</tr>';
						$colorflag=!$colorflag;
					}
				}
    	   
				
					
				
echo '
			</table>
		</div>
		</td>
	</tr>
	<tr>
		<td width="57%" style="font-family: arial,sans-serif">&nbsp;</td>
		<td width="43%" style="font-family: arial,sans-serif">&nbsp;</td>
	</tr>
</table>

</body>

</html>';
?>