<?php
//ini_set("session.use_trans_sid",true); 
session_start(); //�������� ������
require_once 'dbSettings.php';

// 1.����� ���������   ��������� ��� ����� � ������
// 2. ���� ��� ������� �����  ��������� isAutharized � ������ ������ true � ��������� �������� � ����� � ����� ��������� GAMER_CODE � SESSION gamer_code
	//(���� �� �������� � ����� is Authorized �� ���������� ������ ������������ �� index.html)
// 3. ���� ��� ����� ������ �������� � ��������������� � ���������� ����� �� ���� �� ������ � ������
//����  �� ������ �����
if ($_POST['enter'] == '�����') {
	if (!$_POST['uname']  || !$_POST['passw']){ //���� �� ������� ��� ��� ������ ����� ������ �� ������ � ����� ��������� �� �������� ������
		echo ' 
			<meta http-equiv="Refresh" content="0; URL=index.html">
		';
		exit();
	}else //���� ��� �������  �������� ���� �� ����� ������ � ����
	{
		//1. ����������� �  ����
		//2.  ��������� ��  Gamer   ����� � ���������� �����������
		//3. ���� ��� �� �������� ������ SESIONAutorized = true � Session gamercode =gamer.gamer_code
		//4. ���� ������ �� �������� �������� � �������� ������ ��� ������ � ����� ������� �� �������� ������
		//1.../*�����������  � ��������*/
		$link = mysql_connect($dbServer,$dbUser,$dbPass) or exit("Connection is fall");
		/*�������� ���� ������ */
		if (mysql_select_db("shashki")) {
		//2...
			$SQLrequest = "SELECT * from GAMER where GAMER_LOGIN='".mysql_escape_string($_POST['uname'])."' and GAMER_PASSWORD='".mysql_escape_string($_POST['passw'])."'";
		
			if ($result = mysql_query( $SQLrequest)) { // ����������� ��  ����  ������  �����
				$countusers = mysql_num_rows($result);
				if (!$countusers) 
				{ //������  ������ ���  �������  �������� �����
					$_SESSION['Auth']['isAutharize']=false; //�� �����������
				require_once "login_result.php";				
				/*echo ' 
						<meta http-equiv="Refresh" content="0; URL=login_result.php">
						';
					exit();*/
				}
				else 
				{
					$_SESSION['Auth']['isAutharize']=true; //�����������
					$_SESSION['Auth']['GAMER_LOGIN']=mysql_result($result,0,'GAMER_LOGIN'); //��� ������������
					$_SESSION['Auth']['GAMER_CODE']=mysql_result($result,0,'GAMER_CODE'); //���  ������������
					//require_once "login_result.php";
					echo ' 
						<meta http-equiv="Refresh" content="0; URL=game.php">
						';
					exit();
				}
			}
		}
	}
}
?>