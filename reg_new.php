
<?php
require_once 'dbSettings.php';
//��������� ��� �� ��������� ������
if ($_POST['regsubmit'] == '����������������') {
	//���������� �����������
	//���� ������ � ������������� �� ��������� ����� ���������� ����� �� �������� �����������
	if ($_POST['passw'] !== $_POST['confirm']) {
		echo ' 
			<meta http-equiv="Refresh" content="0; URL=registration.html">
		';
		exit();
	}
	//����� �����������  �� ����  � �����  ������ � �����  �� ������.  ���� ������� ����� ����� �����  �������� ��  � �������� ����������� �����
	/*�����������  � ��������*/
	$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
    /*�������� ���� ������ */
	if (mysql_select_db("shashki")) {
		$SQLrequest = "SELECT GAMER_CODE from GAMER where GAMER_LOGIN='".mysql_escape_string($_POST['uname'])."'";
		
		if ($result = mysql_query( $SQLrequest)) { // ����������� ��  ����  ��� ������  ������� ����  �����  �� ���
			$logincount = mysql_num_rows($result);
			if (!$logincount) { //������  ������ ���  ������� ��������� � ���� ������  �����
				$SQLrequest = "INSERT INTO GAMER (GAMER_CODE,GAMER_LOGIN,GAMER_PASSWORD,GAMER_WINS,GAMER_LOSE) 
								VALUES (NULL,'".mysql_escape_string($_POST['uname'])."','".mysql_escape_string($_POST['passw'])."',0,0)";
				if ( $result = mysql_query( $SQLrequest)) {
					echo ' 
						<meta http-equiv="Refresh" content="0; URL=reg_Result.html">
						 ';
					exit();
				}else
					printf ("<br> Could not registrate new  GAMER_LOGIN: %s\n", mysql_error ());	
			}
			else { //�����  �����  ��� ����������  �� ������ ��������  �� ���� �����
			echo ' 
					<meta http-equiv="Refresh" content="0; URL=registration.html">
					 ';
				exit();
			}
		}else //������   ��� ���������� �������  Select
			printf ("<br> Could not select from GAMER: %s\n", mysql_error ());	
	}else //���� �� ������� ������� ���� ������
		printf ("<br> Could not select shashki database: %s\n", mysql_error ());	
	mysql_close( $link); //���������  ���� � �����
	
}
?>
