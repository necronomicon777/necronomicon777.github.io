<?php
//session_start(); //�������� ������

if (isset($_SESSION['Auth']['isAutharize'])){
	echo '
		<html>

		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
		<meta http-equiv="Content-Language" content="ru">
		<meta http-equiv="Refresh" content="5; URL=';
		
		
		if ($_SESSION['Auth']['isAutharize']==false){ 
			echo 'index.html"> 
				 <title>������ �����������</title>
				 </head>
				 <body>
				 ';
		}
		else{
			echo 'game.php"> 
			<title>�������� �����������</title>
			</head>
			<body>
			';
		}
		
		
	echo '	<table width="100%" id="table1" height="488">
			<tr>
				<td height="149" width="296">&nbsp;</td>
				<td height="149" width="325">&nbsp;</td>
				<td height="149">&nbsp;</td>
			</tr>
			<tr>
				<td height="197" width="296">&nbsp;</td>
				<td height="197" width="319" style="font-family: Arial; font-size: 10pt; border: 3px double #C0C0C0; background-color: ';
				
				
	if 	($_SESSION['Auth']['isAutharize']==false){
		echo  '#FF5050">
				<p align="center">�� �������&nbsp; ����� ��� ������������ ��� ������ </p>
				<p align="center"><b>������� <a href="index.html">�����</a> �����&nbsp; 
				������ �����������&nbsp; ��������</b></td>
				<td height="197">&nbsp;</td>
			</tr>';
	}
	else
		echo  '#99FF99">
				<p align="center">�� ������������ ��� '.$_SESSION['Auth']['GAMER_LOGIN'].' </p>
				<p align="center">������ �� ������ � ���� ������������� </p>
				<p align="center"><b>������� <a href="game.php">�����</a> �����&nbsp; 
				����� � ���� ����������</b></td>
				<td height="197">&nbsp;</td>
			</tr>';
	
	echo  '	<tr>
				<td width="296">&nbsp;</td>
				<td width="325">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			</table>
			</body>
        	</html>';
}else{
	echo ' 
			<meta http-equiv="Refresh" content="0; URL=index.html">
		';
		exit();
	}
?>
