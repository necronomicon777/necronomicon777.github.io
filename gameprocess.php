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

//������� ������ �� ��������

function isTimeOut($timelastmove, $timeoutmin){
	
	$tlm_=getdate($timelastmove);
	$nowtime=strtotime("now"); 
	$nowrm_=getdate($nowtime);
	$dd=$nowrm_['minutes']-$tlm_['minutes'];
   // echo "   �� ��������   ".$dd." �����";
	if ($nowrm_['minutes']-$tlm_['minutes'] >=$timeoutmin)
		return true;
	
	
	return false;
}

// �����  �� ����������  � �� ������ ��  ���� ��  ������� ������� � �����
function isContinue(){
global $dbServer;
global $dbUser;
global $dbPass;

GLOBAL $AuthorizeSession;
$Iwin=true;
	//������� ��������� �� ��  ��
	$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
	$itsme =0;			
			/*�������� ���� ������ */
	if (mysql_select_db("shashki")) {
		
				$SQLrequest = "SELECT * FROM ONLINE_GAME WHERE OG_CODE=".$_GET['OG']." ";
				$result = mysql_query( $SQLrequest);
				$online_game = mysql_fetch_array($result, MYSQL_ASSOC);
			//	if ($online_game['OG_GAMER2']==0) return true;
				if ($AuthorizeSession['GAMER_CODE'] == $online_game['OG_GAMER1'] &
					session_id() == $online_game['OG_SID1'])
				{
					$itsme = $online_game['OG_GAMER1']; 	
				}else
					if ($AuthorizeSession['GAMER_CODE'] == $online_game['OG_GAMER2'] &
					session_id() == $online_game['OG_SID2']){
						$itsme = $online_game['OG_GAMER2']; 
					}
		
		if ($online_game['OG_ISLOSE1'] ==true  || $online_game['OG_ISLOSE2']==true ){ 
		if ($online_game['OG_ISLOSE1']==true ) //���� ��  ���� �����  ������ ����� ������ ���  �������
		{
			 //�����  �������� ��������� ������ 1
			 //�����   ������� ����
			 // �����  ���������� ������ ��  �������� ����������
			 switch ($itsme){
			 case $online_game['OG_GAMER1']:
			 {
			 $sqlgamer1 = "SELECT * FROM GAMER WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
			 $result = mysql_query( $sqlgamer1);
			 $gamer1 = mysql_fetch_array($result, MYSQL_ASSOC);
			 $gamer1['GAMER_LOSE']++;
			 $sqlgamer1= "UPDATE GAMER SET GAMER_LOSE=".$gamer1['GAMER_LOSE']." WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
			 $result = mysql_query($sqlgamer1);
			 $Iwin = false;
			}break;
			 case $online_game['OG_GAMER2']:
			 {
			 $sqlgamer2 = "SELECT * FROM GAMER WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
			 $result = mysql_query( $sqlgamer2);
			 $gamer2 = mysql_fetch_array($result, MYSQL_ASSOC);
			 $gamer2['GAMER_WINS']++;
			 $sqlgamer2= "UPDATE GAMER SET GAMER_WINS=".$gamer2['GAMER_WINS']." WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
			 $result = mysql_query($sqlgamer2);
			}break;
			}
		
	}else
		{
	
			 //�����  �������� ��������� ������ 2
			 //�����   ������� ����
			 // �����  ���������� ������ ��  �������� ����������
			 switch ($itsme){
			 case $online_game['OG_GAMER1']:
			 {
			 $sqlgamer1 = "SELECT * FROM GAMER WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
			 $result = mysql_query( $sqlgamer1);
			 $gamer1 = mysql_fetch_array($result, MYSQL_ASSOC);
			 $gamer1['GAMER_WINS']++;
			 $sqlgamer1= "UPDATE GAMER SET GAMER_WINS=".$gamer1['GAMER_WINS']." WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
			 $result = mysql_query($sqlgamer1);
			}break;
			 case $online_game['OG_GAMER2']:
			 {
			 $sqlgamer2 = "SELECT * FROM GAMER WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
			 $result = mysql_query( $sqlgamer2);
			 $gamer2 = mysql_fetch_array($result, MYSQL_ASSOC);
			 $gamer2['GAMER_LOSE']++;
			 $sqlgamer2= "UPDATE GAMER SET GAMER_LOSE=".$gamer2['GAMER_LOSE']." WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
			 $result = mysql_query($sqlgamer2);
			 $Iwin = false;
			}break;
			}
		}
		//������  ������ ���� SID
	switch ($itsme){
	case $online_game['OG_GAMER1']:{
									$online_game['OG_SID1']='0';
									$sqll = "UPDATE ONLINE_GAME SET OG_SID1='0' where OG_CODE=".$_GET['OG']." ";
									$result = mysql_query($sqll);
									}break;
	case $online_game['OG_GAMER2']:{
									$online_game['OG_SID2']='0';
									$sqll = "UPDATE ONLINE_GAME SET OG_SID2='0' where OG_CODE=".$_GET['OG']." ";
									$result = mysql_query($sqll);
									}break;
	}
	
	if ($online_game['OG_SID1']==='0' &  $online_game['OG_SID2']==='0' ){
		// �����  �������  ����
	//	ECHO "        ������� ��� sid";
		 $sqldelgame = " DELETE FROM ONLINE_GAME WHERE OG_CODE=".$_GET['OG']." ";
			 $result = mysql_query($sqldelgame);
			 if ($result){
			 echo '
		<html>

		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
		<meta http-equiv="Content-Language" content="ru">
		<meta http-equiv="Refresh" content="5; URL=game.php"> 
				 <title>���� ��������</title>
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
				<td height="197" width="319" style="font-family: Arial; font-size: 10pt; border: 3px double #C0C0C0; background-color: ';
				
				
	if 	($Iwin){
		echo  '#99FF99">
				<p align="center">�� ��������. ����������� </p>
				<p align="center"><b>������� <a href="game.php">�����</a> �����&nbsp; 
				�������� �� ������� ��������</b></td>
				<td height="197">&nbsp;</td>
			</tr>';
	}
	else
	echo  '#FF5050">
				<p align="center">���� ��������. �� ���������</p>
				<p align="center"><b>������� <a href="game.php">�����</a> �����&nbsp; 
				�������� �� ������� ��������</b></td>
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
			 
			 exit();
			 
			 
			 
			// echo "     //     ������� ����";
		
			//	echo ' 
			//		<meta http-equiv="Refresh" content="0; URL=game.php">
			//		 ';
			//		exit();
				return false;
				}
	}else{
		 echo '
		<html>

		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
		<meta http-equiv="Content-Language" content="ru">
		<meta http-equiv="Refresh" content="5; URL=game.php"> 
				 <title>���� ��������</title>
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
				<td height="197" width="319" style="font-family: Arial; font-size: 10pt; border: 3px double #C0C0C0; background-color: ';
				
				
	if 	($Iwin){
		echo  '#99FF99">
				<p align="center">�� ��������. ����������� </p>
				<p align="center"><b>������� <a href="game.php">�����</a> �����&nbsp; 
				�������� �� ������� ��������</b></td>
				<td height="197">&nbsp;</td>
			</tr>';
	}
	else
	echo  '#FF5050">
				<p align="center">���� ��������. �� ���������</p>
				<p align="center"><b>������� <a href="game.php">�����</a> �����&nbsp; 
				�������� �� ������� ��������</b></td>
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
			 
			 exit();
	//	echo ' 
		//			<meta http-equiv="Refresh" content="0; URL=game.php">
		//			 ';
			//	exit();
		return false;
		}
	
	}
	
	
	mysql_close( $link); //���������  ���� � �����
	}
	//echo "     //�� �����  �� isContinue � ������ TRUE";
	return TRUE;
}
 //1 - ����� , 2 - ������, 3 - �����  �����, 4 - ������ �����, 5 -������ �����, 6 -������ ������.
   //1.. ������� ����������  ��������� ������� ��� �����
   $A = '51515151';
   $B = '15151515';
   $C = '51515151';
   $D = '65656565';
   $E = '56565656';
   $F = '25252525';
   $G = '52525252';
   $H = '25252525';
   
  /* $A = '56565656';
   $B = '65656565';
   $C = '56565656';
   $D = '65651565';
   $E = '56525656';
   $F = '65656565';
   $G = '52565656';
   $H = '65656565';*/
  /* $A = '56565656';
   $B = '65656565';
   $C = '56565656';
   $D = '65651565';
   $E = '56525656';
   $F = '65656565';
   $G = '52565656';
   $H = '65656565';
   */
   $A_H = $A.$B.$C.$D.$E.$F.$G.$H;
   
   //�������� ������� �����
   function fillDoska(&$doska_array){
   global $A_H;
		for ($i=0,$y=0;$y<=7;$y++){
			for ($x=0;$x<=7;$x++){
				$doska_array[$x][$y] = $A_H[$i];
				$i++;
			}
		}
	}
	
	
	
	fillDoska ($doska); //�������� �����
	
	function setMove(&$doska_array,&$myselcell, $cell_num){
	//��������� ��� ����� �����
	global $online_game;
	global $gamer1;
	global $gamer2;
	global $AuthorizeSession;
		//������� ��������� �� ��  ��
		if ($AuthorizeSession['GAMER_CODE'] == $gamer1['GAMER_CODE']){
			
			
			if ($online_game['OG_GAMER1_FIG']==1){
				$myfig = 1; //�����
				$mysaska = 1; //�����
				$mydamka = 3;//�����
				$vragsaska = 2;//��������� �����
				$vragdamka = 4;//��������� �����
			}
			else {
				$myfig = 2; //������
				$mysaska = 2; //������
				$mydamka = 4;//������
				$vragsaska = 1;//��������� �����
				$vragdamka = 3;//��������� �����
			}
			
		}
		else{
			
			
			if ($online_game['OG_GAMER1_FIG']==1){
				$myfig = 2; //������
				$mysaska = 2; //������
				$mydamka = 4;//������
				$vragsaska = 1;//��������� �����
				$vragdamka = 3;//��������� �����
				
			}
			else {
				$myfig = 1; //�����
				$mysaska = 1; //�����
				$mydamka = 3;//�����
				$vragsaska = 2;//��������� �����
				$vragdamka = 4;//��������� �����
			}
		}
	
		$fromX = $myselcell[0];
		$fromY = $myselcell[1];
		$shashka = $doska_array[$fromX][$fromY];
		$toX = $cell_num[0];
		$toY = $cell_num[1];
		if ($myfig == 1) //����  ���  �����
		{
			if ($toY==7) //���� �� ����� �� ����� �����
				$doska_array[$toX][$toY] = $mydamka;
			else
				$doska_array[$toX][$toY] = $shashka;
		}else
		if ($myfig == 2) //����  ���  ������
		{
		   if ($toY==0) //���� �� ����� �� ����� �����
				$doska_array[$toX][$toY] = $mydamka;
			else
				$doska_array[$toX][$toY] = $shashka;
		}
		
		
		$doska_array[$fromX][$fromY] = 6; //������ ������ �� �� �����  ��� ������ �����
		$myselcell = ''.$toX.$toY.''; // ����������  ��������� ���������� �� � ����� �����
	}
	function isValidMove(&$doska_array, $cell_num){
		global $online_game;
		global $gamer1;
		global $gamer2;
		global $AuthorizeSession;
		
		//������� ��������� �� ��  ��
		if ($AuthorizeSession['GAMER_CODE'] == $gamer1['GAMER_CODE']){
			$itsme = $gamer1;
			$mycode = $online_game['OG_GAMER1'];
			$vragcode = $online_game['OG_GAMER2'];
			$mysid = $online_game['OG_SID1'];
			$myscore =  & $online_game['OG_SCORE1'];
			$myselcell = &$online_game['OG_SEL_CELL1'];
			
			if ($online_game['OG_GAMER1_FIG']==1){
				$myfig = 1; //�����
				$mysaska = 1; //�����
				$mydamka = 3;//�����
				$vragsaska = 2;//��������� �����
				$vragdamka = 4;//��������� �����
			}
			else {
				$myfig = 2; //������
				$mysaska = 2; //������
				$mydamka = 4;//������
				$vragsaska = 1;//��������� �����
				$vragdamka = 3;//��������� �����
			}
			
		}
		else{
			$itsme = $gamer2;
			$mycode = $online_game['OG_GAMER2'];
			$vragcode = $online_game['OG_GAMER1'];
			$mysid = $online_game['OG_SID2'];
			$myscore = & $online_game['OG_SCORE2'];
			$myselcell = &$online_game['OG_SEL_CELL2'];
			
			if ($online_game['OG_GAMER1_FIG']==1){
				$myfig = 2; //������
				$mysaska = 2; //������
				$mydamka = 4;//������
				$vragsaska = 1;//��������� �����
				$vragdamka = 3;//��������� �����
				
			}
			else {
				$myfig = 1; //�����
				$mysaska = 1; //�����
				$mydamka = 3;//�����
				$vragsaska = 2;//��������� �����
				$vragdamka = 4;//��������� �����
			}
		}
		//��������  ��� �� ���?
		// ����  ��� ��  ��� ������� � �������
		if ($online_game['OG_GAMER_MOVE']!==$AuthorizeSession['GAMER_CODE'])
			return false;
	//	echo "���������� ".$myselcell;
		//�������� ���� ��  ����� ��� ?
		// ��������  �������� ��  �������  �����
		// ����  ������ �������  �����  ��� ����   ��������� ����� �����  ��������� ��������
		$mysaski =0;
		//$vragsaski = 0;
		for ($y=0;$y<=7;$y++){
			for ($x=0;$x<=7;$x++){
				if ($doska_array[$x][$y] == $mysaska || $doska_array[$x][$y] == $mydamka){
					//��������  �������  ��  �����  ����� ���� ���� ��  4 �����������
						if ($x>0 & $y>0)
							$UP_LEFT = $doska_array[$x-1][$y-1]; //����� ������� ������  �� ��� ������� ��������.
						else
							$UP_LEFT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������) 
						if ($x>0 & $y<7)
							$DOWN_LEFT=$doska_array[$x-1][$y+1]; //������ �����  ������ �� ��� ������� ��������
						else
							$DOWN_LEFT = 5; // 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						if ($x<7 & $y>=1)
							$UP_RIGHT = $doska_array[$x+1][$y-1]; //������ �������  ������ �� ��� ������� ��������
						else
							$UP_RIGHT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						if ($x<7 & $y<7)
							$DOWN_RIGHT =$doska_array[$x+1][$y+1]; //������ ������ ������ �� ��� ������� ��������
						else
							$DOWN_RIGHT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
						if ($UP_LEFT == 6  || $UP_RIGHT == 6 ||
							$DOWN_RIGHT == 6  || $DOWN_LEFT == 6)
						$mysaski++; //���� ����� ��������  ���� ����  �����  ������   ����� ��� � ��������
					}
				}
			}
		
		if ($mysaski==0){
		
			echo ' 
						<meta http-equiv="Refresh" content="0; URL=gameprocess.php?OG='.$_GET['OG'].'&lose=1">
						';
					exit();
		}
		//�����. �������
		//���� ��� �� ������� ��������   �������� ��� ����������
		if ($myselcell!=='FF') { // ���� ���� ��� �� ��������
				if ($cell_num == $myselcell){ 
				$xx = $myselcell[0];
				$yy = $myselcell[1];
			//	echo "myselcell        ".$myselcell;
				$shashka = $doska_array[$xx][$yy];
				switch ($shashka) {//� ���� � ��� �� ������ �������� �����  ���� ����� �����
				case $mysaska:return true; // ���  �������� ��������� ��� ��� ����
				case $mydamka:return true; //���  �������� ��������� ��� ��� ����
				case 5:        //���� �������� �� ������ ����� ���
				case 6:{$myselcell='FF'; //�������� ������
					    return true; //���  ��������
					   }
				default: return false;
				}
			}else //���� ��������   �� ������� �����
			{
				$toX = $cell_num[0];
				$toY = $cell_num[1];
				$fromX = $myselcell[0];
				$fromY = $myselcell[1];
				$shashka = $doska_array[$toX][$toY];
				switch ($shashka) {//� ���� � ��� �� ������ �������� �����  ���� ����� �����
				case $mysaska:
				case $mydamka:{$myselcell = ''.$cell_num.'';
							   return true;} //���  �������� ��������  �������  �����
				case 6:{ 
				//���� �� �������� �� ������ ������ ����� �����  ���  ���������� ����
				
				
							//1.   ����� � ������  ���������� �� ���������  ��  ���������  �����  ������, �� ������� ���� ������ ������������
							//2. ����  ������  �� �������  ��  ���������   ����� ����  ��� �� ������� �� �����
						//1. ������� �������� ���� �� � ������  �����������  ����  ����   ����� ����� ���  ������ �� ������� �����  �������
						//2. � ����  �� ������  ���� �� ��������  ��������  ������� �����  �������  ������� ������  �����  ���  ���������� ����� ���	
						
						
						if ($fromX>0 & $fromY>0)
							$UP_LEFT = $doska_array[$fromX-1][$fromY-1]; //����� ������� ������  �� ��� ������� ��������.
						else
							$UP_LEFT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������) 
						if ($fromX>=2 & $fromY>=2)
							$UL_UPLEFT = $doska_array[$fromX-2][$fromY-2];////����� ������� ������  �� ����� �������
						else
							$UL_UPLEFT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
							
						if ($fromX>0 & $fromY<7)
							$DOWN_LEFT=$doska_array[$fromX-1][$fromY+1]; //������ �����  ������ �� ��� ������� ��������
						else
							$DOWN_LEFT = 5; // 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
						if ($fromX>=2 & $fromY<=5)
							$DL_DOWNLEFT=$doska_array[$fromX-2][$fromY+2]; //������ �����  ������ �� ������ �����
						else
							$DL_DOWNLEFT= 5; // 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
     					if ($fromX<7 & $fromY>=1)
							$UP_RIGHT = $doska_array[$fromX+1][$fromY-1]; //������ �������  ������ �� ��� ������� ��������
						else
							$UP_RIGHT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
						if ($fromX<=5 & $fromY>=2)
							$UR_UPRIGHT = $doska_array[$fromX+2][$fromY-2]; //������ �������  ������ ��  ������ ������� 
						else
							$UR_UPRIGHT =5 ;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
						if ($fromX<7 & $fromY<7)
							$DOWN_RIGHT =$doska_array[$fromX+1][$fromY+1]; //������ ������ ������ �� ��� ������� ��������
						else
							$DOWN_RIGHT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						if ($fromX<=5 & $fromY<=5)
							$DR_DOWNRIGHT =$doska_array[$fromX+2][$fromY+2]; //������ ������ ������ �� ������ ������
						else
							$DR_DOWNRIGHT = 5 ;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������) 
						
						if ($UP_LEFT==$vragsaska || $UP_LEFT==$vragdamka){ //���� ������������ ��  ������ ������� �������� ����� ����� ����� ��� �����
							if ($UL_UPLEFT == 6) // � ���� ��  ���  ��� �����  ������ ������ �� ����� �����
								if ($toX == $fromX-2 & $toY ==$fromY-2) //� ���� �� ��������  ������ ����  ����� ���  ���������� 
								{
									$doska_array[$fromX-1][$fromY-1]=6;// ����� �������
									$myscore++;
									setMove($doska_array,$myselcell, $cell_num);
									if (isNextMove($doska_array, $myselcell, $vragsaska,$vragdamka)) 
										$online_game['OG_GAMER_MOVE'] = $vragcode;
									return true;
								}
						}
							if ($UP_RIGHT==$vragsaska || $UP_RIGHT==$vragdamka){ //���� ������������ ��  ������ ������� �������� ����� ����� ����� ��� �����
								if ($UR_UPRIGHT == 6) // � ���� ��  ���  ��� �����  ������ ������ �� ����� �����
									if ($toX == $fromX+2 & $toY ==$fromY-2) //� ���� �� ��������  ������ ����  ����� ���  ���������� 
									{
										$doska_array[$fromX+1][$fromY-1]=6;// ����� �������
										$myscore++;
										setMove($doska_array,$myselcell, $cell_num);
										if (isNextMove($doska_array, $myselcell, $vragsaska,$vragdamka)) 
											$online_game['OG_GAMER_MOVE'] = $vragcode;
										return true;
									}
							}
								if ($DOWN_RIGHT==$vragsaska || $DOWN_RIGHT==$vragdamka){ //���� ������������ ��  ������ ������� �������� ����� ����� ����� ��� �����
									if ($DR_DOWNRIGHT == 6) // � ���� ��  ���  ��� �����  ������ ������ �� ����� �����
										if ($toX == $fromX+2 & $toY ==$fromY+2) //� ���� �� ��������  ������ ����  ����� ���  ���������� 
										{
											$doska_array[$fromX+1][$fromY+1]=6;// ����� �������
											$myscore++;
											setMove($doska_array,$myselcell, $cell_num);
											if (isNextMove($doska_array, $myselcell,$vragsaska,$vragdamka)) 
												$online_game['OG_GAMER_MOVE'] = $vragcode;
											return true;
										}
								}
									if ($DOWN_LEFT==$vragsaska || $DOWN_LEFT==$vragdamka){ //���� ������������ ��  ������ ������� �������� ����� ����� ����� ��� �����
										if ($DL_DOWNLEFT == 6) // � ���� ��  ���  ��� �����  ������ ������ �� ����� �����
											if ($toX == $fromX-2 & $toY ==$fromY+2) //� ���� �� ��������  ������ ����  ����� ���  ���������� 
											{
												$doska_array[$fromX-1][$fromY+1]=6;// ����� �������
												$myscore++;
												setMove($doska_array,$myselcell, $cell_num);
												if (isNextMove($doska_array, $myselcell, $vragsaska,$vragdamka)) 
													$online_game['OG_GAMER_MOVE'] = $vragcode;
												return true;
											}
									}
										
											//���� �� ����  ������  �� ����� .. �� ������ �� ����� ��  ������  ������� ������� ? ���� ����  � ���� �
											//***********************************************
											//� ����� ������ ��������� �� ���� �������    � �������� ��� �� ����� ���  ����� �������
				//��������� � ����������� ���������.  ���� ������� ���� �����.  ��������� ����  ��� ���� ���������� ������ ����� �� �����
				// ����  ��� �� ���� ������ �����  ��� ���������.
				// ����  ������ ����� ���� ��� ����� ������ �����  ���  ��������� ��� �����
				for ($y=0;$y<=7;$y++){
					$fromY1 = $y;
					for ($x=0;$x<=7;$x++){
						$fromX1 = $x; 
						if ($doska_array[$fromX1][$fromY1] == $vragdamka || $doska_array[$fromX1][$fromY1] == $vragsaska  ||
							$doska_array[$fromX1][$fromY1] == 5 || $doska_array[$fromX1][$fromY1] == 6	) continue;
						if ($fromX1>0 & $fromY1>0)
							$UP_LEFT = $doska_array[$fromX1-1][$fromY1-1]; //����� ������� ������  �� ��� ������� ��������.
						else
							$UP_LEFT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������) 
						if ($fromX1>=2 & $fromY1>=2)
							$UL_UPLEFT = $doska_array[$fromX1-2][$fromY1-2];////����� ������� ������  �� ����� �������
						else
							$UL_UPLEFT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
							
						if ($fromX1>0 & $fromY1<7)
							$DOWN_LEFT=$doska_array[$fromX1-1][$fromY1+1]; //������ �����  ������ �� ��� ������� ��������
						else
							$DOWN_LEFT = 5; // 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
						if ($fromX1>=2 & $fromY1<=5)
							$DL_DOWNLEFT=$doska_array[$fromX1-2][$fromY1+2]; //������ �����  ������ �� ������ �����
						else
							$DL_DOWNLEFT= 5; // 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
     					if ($fromX1<7 & $fromY1>=1)
							$UP_RIGHT = $doska_array[$fromX1+1][$fromY1-1]; //������ �������  ������ �� ��� ������� ��������
						else
							$UP_RIGHT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
						if ($fromX1<=5 & $fromY1>=2)
							$UR_UPRIGHT = $doska_array[$fromX1+2][$fromY1-2]; //������ �������  ������ ��  ������ ������� 
						else
							$UR_UPRIGHT =5 ;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
						if ($fromX1<7 & $fromY1<7)
							$DOWN_RIGHT =$doska_array[$fromX1+1][$fromY1+1]; //������ ������ ������ �� ��� ������� ��������
						else
							$DOWN_RIGHT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						if ($fromX1<=5 & $fromY1<=5)
							$DR_DOWNRIGHT =$doska_array[$fromX1+2][$fromY1+2]; //������ ������ ������ �� ������ ������
						else
							$DR_DOWNRIGHT = 5 ;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������) 
						
						if ($UP_LEFT==$vragsaska || $UP_LEFT==$vragdamka){ //���� ������������ ��  ������ ������� �������� ����� ����� ����� ��� �����
							if ($UL_UPLEFT == 6) // � ���� ��  ���  ��� �����  ������ ������ �� ����� �����
								if ($fromX1 !== $myselcell[0] & $fromY1 !==$myselcell[1]) //� ���� �� ��������  ������ ����  ����� ���  ���������� 
								{	
						//		echo "UP_LEFT ��� ����� ";								
								return false;
								}
						}
							if ($UP_RIGHT==$vragsaska || $UP_RIGHT==$vragdamka){ //���� ������������ ��  ������ ������� �������� ����� ����� ����� ��� �����
								if ($UR_UPRIGHT == 6) // � ���� ��  ���  ��� �����  ������ ������ �� ����� �����
									if ($fromX1 !== $myselcell[0] & $fromY1 !==$myselcell[1]) //� ���� �� ��������  ������ ����  ����� ���  ���������� 
									{		
										//echo "UP_RIGHT ��� ����� ";
										return false;
									}
							}
								if ($DOWN_RIGHT==$vragsaska || $DOWN_RIGHT==$vragdamka){ //���� ������������ ��  ������ ������� �������� ����� ����� ����� ��� �����
									if ($DR_DOWNRIGHT == 6) // � ���� ��  ���  ��� �����  ������ ������ �� ����� �����
										if ($fromX1 !== $myselcell[0] & $fromY1 !==$myselcell[1]) //� ���� �� ��������  ������ ����  ����� ���  ���������� 
										{		
										//echo "DOWN_RIGHT ��� ����� ";
										return false;
										}
								}
									if ($DOWN_LEFT==$vragsaska || $DOWN_LEFT==$vragdamka){ //���� ������������ ��  ������ ������� �������� ����� ����� ����� ��� �����
										if ($DL_DOWNLEFT == 6) // � ���� ��  ���  ��� �����  ������ ������ �� ����� �����
											if ($fromX1 !== $myselcell[0] & $fromY1 !==$myselcell[1]) //� ���� �� ��������  ������ ����  ����� ���  ���������� 
											{		
											//	echo "DOWN_LEFT ��� ����� ";
												return false;
											}
									}
					}
				}
				
											//***********************************************
						$fromX = $myselcell[0];
						$fromY = $myselcell[1];
						if ($fromX>0 & $fromY>0)
							$UP_LEFT = $doska_array[$fromX-1][$fromY-1]; //����� ������� ������  �� ��� ������� ��������.
						else
							$UP_LEFT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������) 
						if ($fromX>=2 & $fromY>=2)
							$UL_UPLEFT = $doska_array[$fromX-2][$fromY-2];////����� ������� ������  �� ����� �������
						else
							$UL_UPLEFT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
							
						if ($fromX>0 & $fromY<7)
							$DOWN_LEFT=$doska_array[$fromX-1][$fromY+1]; //������ �����  ������ �� ��� ������� ��������
						else
							$DOWN_LEFT = 5; // 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
						if ($fromX>=2 & $fromY<=5)
							$DL_DOWNLEFT=$doska_array[$fromX-2][$fromY+2]; //������ �����  ������ �� ������ �����
						else
							$DL_DOWNLEFT= 5; // 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
     					if ($fromX<7 & $fromY>=1)
							$UP_RIGHT = $doska_array[$fromX+1][$fromY-1]; //������ �������  ������ �� ��� ������� ��������
						else
							$UP_RIGHT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
						if ($fromX<=5 & $fromY>=2)
							$UR_UPRIGHT = $doska_array[$fromX+2][$fromY-2]; //������ �������  ������ ��  ������ ������� 
						else
							$UR_UPRIGHT =5 ;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
						if ($fromX<7 & $fromY<7)
							$DOWN_RIGHT =$doska_array[$fromX+1][$fromY+1]; //������ ������ ������ �� ��� ������� ��������
						else
							$DOWN_RIGHT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						if ($fromX<=5 & $fromY<=5)
							$DR_DOWNRIGHT =$doska_array[$fromX+2][$fromY+2]; //������ ������ ������ �� ������ ������
						else
							$DR_DOWNRIGHT = 5 ;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������) 
											//���� �� ������  ������ ����� ��  �����  ����  ����  ������� ����� �����
											switch ($myfig) {
												case 1:{		//������ ������ 
											//	echo "    ������ ������  �����  �������";
														switch ($doska_array[$fromX][$fromY]){
														case $mysaska:{
													//	echo "    ���������  �����";
															if ($DOWN_RIGHT==6){ //���� ����� ������ �����  ����� ������� 
																if ($toX == $fromX+1 & $toY ==$fromY+1){ //� ���� �� ��������  ������ ����  ����� ���  ���������� 
															//	echo "�������� DOWN RIGHT";
																	setMove($doska_array,$myselcell, $cell_num);
																//	if (isNextMove($doska_array, $myselcell, $vragsaska,$vragdamka))
																			$online_game['OG_GAMER_MOVE'] = $vragcode;
																	return true;
																
																}
															}
															if ($DOWN_LEFT== 6)//���� ����� ����� ����� ����� ����� �������
																if ($toX == $fromX-1 & $toY ==$fromY+1) //� ���� �� ��������  ������ ����  ����� ���  ���������� 
																{
															//	echo "�������� DOWN LEFT";
																	setMove($doska_array,$myselcell, $cell_num);
														//		if (isNextMove($doska_array, $myselcell, $vragsaska,$vragdamka)) 
																		$online_game['OG_GAMER_MOVE'] = $vragcode;
																	return true;
																}
														}break;
														case $mydamka:{
													//	echo "    ������ ������  ����� ��";
															//���� ����� ����� �����  ����� ������ ��� ������ ������� �� ������ � ������� �� �� ���  X � Y �� ������� �����
															if ($toX != $fromX & $toY !=$fromY) //����  �� ����� �� ��� ���� ����� �����
																if ($doska_array[$toX][$toY] == 6){ // ����  ����  ���� ����� �����
																	//�����  ���������  �� �������  �� �� �� ������  ����� ��� �� ������������  �����  ����� ���  1   ����� ��� 1 � ����� �����
																		//������� ��������� � ����� ����������� �� ��������
																		$ii=0;
																		$jj=0;
																		
																		if ($toX < $fromX & $toY <$fromY) // ������������  ����������� ����� ����� UP_LEFT
																		{
																		$sasek=0;
																			$i=$toX;
																			$j=$toY;
																			while ($i<$fromX & $j<$fromY)
																			
																				{
																					
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //����  �� ����  ����  ����� ��� �����
																					{	
																					//	echo "   ��� ������� ����  �����1";
																						return  false;
																					}
																					if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //����  ����� ����� ��� �����
																					{
																						$sasek++; // ����� ����������� ����
																						$ii=$i;
																						$jj=$j;
																					}
																					$i++;
																					$j++;
																				}
																			if ($sasek >1)	return  false; // ���� �����  0  ���  1�����  ���  ���������� 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// ����� �������
																				$myscore++;
																			}
																		}
																		if ($toX > $fromX & $toY <$fromY) // ������������  ����������� ������  ����� UP_RIGHT
																		{
																		$sasek=0;
																		
																			$i=$toX;
																			$j=$toY;
																			while ($i>$fromX & $j<$fromY)
																			
																				{
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //����  �� ����  ����  ����� ��� �����
																					{	
																					//	echo "   ��� ������� ����  �����2";
																						return  false;
																					}
																					if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //����  ����� ����� ��� �����
																					{
																						$sasek++; // ����� ����������� ����
																						$ii=$i;
																						$jj=$j;
																					}
																					$i--;
																					$j++;
																				}
																				if ($sasek >1)	return  false; // ���� �����  0  ���  1�����  ���  ���������� 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// ����� �������
																				$myscore++;
																			}
																		}
																		if ($toX > $fromX & $toY >$fromY) // ������������  ����������� ������ ����  DOWN_RIGHT
																		{
																		$sasek=0;
																		
																		//echo " toX �  TY  ��������� �".$toX.$toY;
																		$i=$toX;
																			$j=$toY;
																			
																		while ($i>$fromX & $j>$fromY)
																			
																				{
																				//	echo "    ������ toX toY ".$i.$j;
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //����  �� ����  ����  ����� ��� �����
																					{	
																						//echo "   ��� ������� ����  �����3";
																					//	echo "       xy:    ".$i.$j ; 
																						return  false;
																					}
																					if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //����  ����� ����� ��� �����
																					{
																			//		echo "\n ������� ����� ����� � ������ ".$i.$j." � ���  ����� ".$doska_array[$i][$j];
																						$sasek++; // ����� ����������� ����
																						$ii=$i;
																						$jj=$j;
																					}
																					$i--;
																					$j--;
																				}
																			if ($sasek >1){
																		//	echo "   ��� ������� �� ���� �����3     ".$sasek;
																			return  false; // ���� �����  0  ���  1�����  ���  ���������� 
																			}	
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// ����� �������
																				$myscore++;
																			}
																		}
																		if ($toX < $fromX & $toY >$fromY) // ������������  ����������� ����� ���� DOWN_LEFT
																		{
																		$sasek=0;
																		$i=$toX;
																		$j=$toY;
																		while ($i<$fromX & $j>$fromY)
																			{
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //����  �� ����  ����  ����� ��� �����
																					{	
																			//			echo "   ��� ������� ����  �����4";
																						return  false;
																					}		if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //����  ����� ����� ��� �����
																					{
																						$sasek++; // ����� ����������� ����
																						$ii=$i;
																						$jj=$j;
																					}
																					$i++;
																					$j--;
																			}
																			if ($sasek >1)	return  false; // ���� �����  0  ���  1�����  ���  ���������� 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// ����� �������
																				$myscore++;
																			}
																		}
																		// ������� ����������
																		//*************************************************
																	setMove($doska_array,$myselcell, $cell_num);
																		if (isNextMove($doska_array, $myselcell, $vragsaska,$vragdamka)) 
																			$online_game['OG_GAMER_MOVE'] = $vragcode;
																return true;
																}
														}
														}
												}break;
														
												case 2:{		//������� ������
											//	echo "�� ������ ������� ";
														switch ($doska_array[$fromX][$fromY]){
															case $mysaska:{
																if ($UP_LEFT== 6) // ���� ������ ����� ����� ����� ����� �������
																	if ($toX == $fromX-1 & $toY ==$fromY-1){ //� ���� �� ��������  ������ ����  ����� ���  ���������� 
																		setMove($doska_array,$myselcell, $cell_num);
																		$online_game['OG_GAMER_MOVE'] = $vragcode;
																		return true;
																	}
														
																if ($UP_RIGHT==6) // ���� ������ ������ ����� ����� ����� �������
																	if ($toX == $fromX+1 & $toY ==$fromY-1){
																	
																		setMove($doska_array,$myselcell, $cell_num);
																		$online_game['OG_GAMER_MOVE'] = $vragcode;
																		return true;					
																	}
															}break;
															case $mydamka:{
														//		echo "    ������ �������� ����� ��";
																//���� ����� ����� �����  ����� ������ ��� ������ ������� �� ������ � ������� �� �� ���  X � Y �� ������� �����
																if ($toX != $fromX & $toY !=$fromY) //����  �� ����� �� ��� ���� ����� �����
																	if ($doska_array[$toX][$toY] == 6){ // ����  ����  ���� ����� �����
																		//�����  ���������  �� �������  �� �� �� ������  ����� ��� �� ������������  �����  ����� ���  1   ����� ��� 1 � ����� �����
																		//������� ��������� � ����� ����������� �� ��������
																		$ii=0;
																		$jj=0;
																		if ($toX < $fromX & $toY <$fromY) // ������������  ����������� ����� ����� UP_LEFT
																		{
																		$sasek=0;
																				$i=$toX;
																				$j=$toY;
																			while ($i<$fromX & $j<$fromY)
																			
																				{
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //����  �� ����  ����  ����� ��� �����
																					{	
																		//				echo "   ��� ������� ����  �����1";
																						return  false;
																					}if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //����  ����� ����� ��� �����
																					{
																						$sasek++; // ����� ����������� ����
																						$ii=$i;
																						$jj=$j;
																					}
																					$i++;
																				$j++;
																				}
																			if ($sasek >1)	return  false; // ���� �����  0  ���  1�����  ���  ���������� 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// ����� �������
																				$myscore++;
																			}
																		}
																		if ($toX > $fromX & $toY <$fromY) // ������������  ����������� ������  ����� UP_RIGHT
																		{
																		$sasek=0;
																				$i=$toX;
																				$j=$toY;
																			while ($i>$fromX & $j<$fromY)
																			{
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //����  �� ����  ����  ����� ��� �����
																					{	
																		//				echo "   ��� ������� ����  �����2";
																						return  false;
																					}				if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //����  ����� ����� ��� �����
																					{
																						$sasek++; // ����� ����������� ����
																						$ii=$i;
																						$jj=$j;
																					}
																					$i--;
																				$j++;
																				}
																			if ($sasek >1)	return  false; // ���� �����  0  ���  1�����  ���  ���������� 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// ����� �������
																				$myscore++;
																			}
																		}
																		if ($toX > $fromX & $toY >$fromY) // ������������  ����������� ������ ����  DOWN_RIGHT
																		{
																		$sasek=0;
																			   $i=$toX;
																				$j=$toY;
																			while ($i>$fromX & $j>$fromY)
																			
																				{
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //����  �� ����  ����  ����� ��� �����
																					{	
																			//			echo "   ��� ������� ����  �����3";
																						return  false;
																					}			if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //����  ����� ����� ��� �����
																					{
																						$sasek++; // ����� ����������� ����
																						$ii=$i;
																						$jj=$j;
																					}
																					$i--;
																				$j--;
																				}
																			if ($sasek >1)	return  false; // ���� �����  0  ���  1�����  ���  ���������� 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// ����� �������
																				$myscore++;
																			}
																		}
																		if ($toX < $fromX & $toY >$fromY) // ������������  ����������� ����� ���� DOWN_LEFT
																		{
																		$sasek=0;
																		   $i=$toX;
																				$j=$toY;
																			while ($i<$fromX & $j>$fromY)
																			
																				{
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //����  �� ����  ����  ����� ��� �����
																					{	
																					//	echo "   ��� ������� ����  �����4";
																						return  false;
																					}				if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //����  ����� ����� ��� �����
																					{
																						$sasek++; // ����� ����������� ����
																						$ii=$i;
																						$jj=$j;
																					}
																					$i++;
																				$j--;
																			}
																			if ($sasek >1)	return  false; // ���� �����  0  ���  1�����  ���  ���������� 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// ����� �������
																				$myscore++;
																			}
																		}
																		// ������� ����������
																		//*************************************************
																		setMove($doska_array,$myselcell, $cell_num);
																	if (isNextMove($doska_array, $myselcell, $vragsaska,$vragdamka)) 
																		$online_game['OG_GAMER_MOVE'] = $vragcode;
																    return true;
																	}
															}break;
														}
															
												}break; 
											}
										
					
								
											
							
					   }
				default: return false;
				}
			}
		}else // ���� ������ �� ��������
		{
			    $xx = $cell_num[0];
				$yy = $cell_num[1];
				$shashka = $doska_array[$xx][$yy];
				switch ($shashka) {//� ���� � ��� �� ������ �������� �����  ���� ����� �����
				case $mysaska:
				case $mydamka:{$myselcell = ''.$cell_num.'';
							   return true;} //���  �������� ��������  �������  �����
				case 5:        //���� �������� �� ������ ����� ���
				case 6:{$myselcell='FF'; //�������� ������
					    return true; //���  ��������
					   }
				default: return false;
				}
		}
		
		
		return false;
	}
	
	function saveChanges() // ��������    ���  ������
	{
		global $dbServer;
		global $dbUser;
		global $dbPass;
		global $online_game;
		global $gamer1;
		global $gamer2;
		global $AuthorizeSession;
		global $doska;
		// ����� �������  ������� ����� �� �������� �� A �� H  � ������ � ����
		global $A_H;
		for ($i=0,$y=0;$y<=7;$y++){
			for ($x=0;$x<=7;$x++){
				$A_H[$i]=$doska[$x][$y];
				$i++;
			}
		}
		
		$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
				
			/*�������� ���� ������ */
	if (mysql_select_db("shashki")) {
		
	
	
	//echo " OG1Par     ".$online_game['OG_PARITET1'];
		//echo " OG2Par     ".$online_game['OG_PARITET2'];
		
		if ($AuthorizeSession['GAMER_CODE']==$online_game['OG_GAMER1'] ) //���� ������ ����� 
				$SQLrequest = "UPDATE ONLINE_GAME SET OG_SCORE1=".$online_game['OG_SCORE1'].",OG_PARITET1=".$online_game['OG_PARITET1'].",OG_PARITET2=".$online_game['OG_PARITET2'].", OG_GAMER_MOVE=".$online_game['OG_GAMER_MOVE'].", OG_SEL_CELL1='".$online_game['OG_SEL_CELL1']."', OG_A_H='".$A_H."' WHERE OG_CODE=".$_GET['OG']." ";
		else
			$SQLrequest = "UPDATE ONLINE_GAME SET OG_SCORE2=".$online_game['OG_SCORE2'].",OG_PARITET1=".$online_game['OG_PARITET1'].",OG_PARITET2=".$online_game['OG_PARITET2'].", OG_GAMER_MOVE=".$online_game['OG_GAMER_MOVE'].",  OG_SEL_CELL2='".$online_game['OG_SEL_CELL2']."', OG_A_H='".$A_H."' WHERE OG_CODE=".$_GET['OG']." ";
			
			
				$result = mysql_query( $SQLrequest);
		if (!$result){
			echo	' 
					<meta http-equiv="Refresh" content="0; URL=game.php">
				';
			exit();
		}
	}			
	mysql_close( $link); //���������  ���� � �����
	}
	
	function isNextMove(&$doska_array, &$myselcell, $vragsaska,$vragdamka){
		$fromX = $myselcell[0];
		$fromY = $myselcell[1];
						if ($fromX>0 & $fromY>0)
							$UP_LEFT = $doska_array[$fromX-1][$fromY-1]; //����� ������� ������  �� ��� ������� ��������.
						else
							$UP_LEFT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������) 
						if ($fromX>=2 & $fromY>=2)
							$UL_UPLEFT = $doska_array[$fromX-2][$fromY-2];////����� ������� ������  �� ����� �������
						else
							$UL_UPLEFT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
							
						if ($fromX>0 & $fromY<7)
							$DOWN_LEFT=$doska_array[$fromX-1][$fromY+1]; //������ �����  ������ �� ��� ������� ��������
						else
							$DOWN_LEFT = 5; // 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
						if ($fromX>=2 & $fromY<=5)
							$DL_DOWNLEFT=$doska_array[$fromX-2][$fromY+2]; //������ �����  ������ �� ������ �����
						else
							$DL_DOWNLEFT= 5; // 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
     					if ($fromX<7 & $fromY>=1)
							$UP_RIGHT = $doska_array[$fromX+1][$fromY-1]; //������ �������  ������ �� ��� ������� ��������
						else
							$UP_RIGHT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
						if ($fromX<=5 & $fromY>=2)
							$UR_UPRIGHT = $doska_array[$fromX+2][$fromY-2]; //������ �������  ������ ��  ������ ������� 
						else
							$UR_UPRIGHT =5 ;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						
						if ($fromX<7 & $fromY<7)
							$DOWN_RIGHT =$doska_array[$fromX+1][$fromY+1]; //������ ������ ������ �� ��� ������� ��������
						else
							$DOWN_RIGHT = 5;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������)  
						if ($fromX<=5 & $fromY<=5)
							$DR_DOWNRIGHT =$doska_array[$fromX+2][$fromY+2]; //������ ������ ������ �� ������ ������
						else
							$DR_DOWNRIGHT = 5 ;// 5 ��� �����  �� ������������ (5 ���  ����� ������ ������) 
						
						if ($UP_LEFT==$vragsaska || $UP_LEFT==$vragdamka) //���� ������������ ��  ������ ������� �������� ����� ����� ����� ��� �����
							if ($UL_UPLEFT == 6) // � ���� ��  ���  ��� �����  ������ ������ �� ����� �����
									return false;
								
						
							if ($UP_RIGHT==$vragsaska || $UP_RIGHT==$vragdamka) //���� ������������ ��  ������ ������� �������� ����� ����� ����� ��� �����
								if ($UR_UPRIGHT == 6) // � ���� ��  ���  ��� �����  ������ ������ �� ����� �����
										return false;
										
								if ($DOWN_RIGHT==$vragsaska || $DOWN_RIGHT==$vragdamka) //���� ������������ ��  ������ ������� �������� ����� ����� ����� ��� �����
									if ($DR_DOWNRIGHT == 6) // � ���� ��  ���  ��� �����  ������ ������ �� ����� �����
											return false;
							
									if ($DOWN_LEFT==$vragsaska || $DOWN_LEFT==$vragdamka) //���� ������������ ��  ������ ������� �������� ����� ����� ����� ��� �����
										if ($DL_DOWNLEFT == 6) // � ���� ��  ���  ��� �����  ������ ������ �� ����� �����
												return false;
		return true;
											
	}
	
	function produceMove(&$doska_array, $cell_num){
	//�������� ��� �������  ������ ������� ����  ....    � ������������  �����.  ����� �������  ������� isValidMove
		global $online_game;
		global $gamer1;
		global $gamer2;
		global $AuthorizeSession;
		//�� �� ��??
		//������� ��������� �� ��  ��
		if ($AuthorizeSession['GAMER_CODE'] == $gamer1['GAMER_CODE']){
			$itsme = $gamer1;
			$mycode = $online_game['OG_GAMER1'];
			$mysid = $online_game['OG_SID1'];
			$myscore = $online_game['OG_SCORE1'];
			$myselcell = &$online_game['OG_SEL_CELL1'];
			
			if ($online_game['OG_GAMER1_FIG']==1){
				$myfig = 1; //�����
				$mysaska = 1; //�����
				$mydamka = 3;//�����
				$vragsaska = 2;//��������� �����
				$vragdamka = 4;//��������� �����
			}
			else {
				$myfig = 2; //������
				$mysaska = 2; //������
				$mydamka = 4;//������
				$vragsaska = 1;//��������� �����
				$vragdamka = 3;//��������� �����
			}
			
		}
		else{
			$itsme = $gamer2;
			$mycode = $online_game['OG_GAMER2'];
			$mysid = $online_game['OG_SID2'];
			$myscore = $online_game['OG_SCORE2'];
			$myselcell = &$online_game['OG_SEL_CELL2'];
			
			if ($online_game['OG_GAMER1_FIG']==1){
				$myfig = 2; //������
				$mysaska = 2; //������
				$mydamka = 4;//������
				$vragsaska = 1;//��������� �����
				$vragdamka = 3;//��������� �����
				
			}
			else {
				$myfig = 1; //�����
				$mysaska = 1; //�����
				$mydamka = 3;//�����
				$vragsaska = 2;//��������� �����
				$vragdamka = 4;//��������� �����
			}
		}
		//�������� 
		//1. ����  ��� ������  ����� ������ ������   ������
		//2. ���� ���� ����� �������� ����������� ����������
		if ($cell_num === $myselcell){
			//echo "   ������ �� ������ ";
			return true;// ������ �� ������
		}
		
		
		
		//������ ����� ���������  ����  � ������ ���������� �����....
		//....
		//...
		
		return true;
	}

if (isset($_GET['create'])){ // ����  ������  ��������  create -  �������  ����  ����� ������� ����
//1. ����������� � ����
//2. ��������� �  ONLINE_GAME �������  ����
//3.  � ������ A...H  ������� ��������� ������� ����� (����� ������)
//4. ������� �� �������� ���������� �������� ������� ������
//5.   � �������� ���������� �� ���� ������� ������ ������
//6.  ������ ������� � ����� �� ����������,  ������� ��� ���� �� ����������
//7. �����  ������� ��� ������ �� ���� ������� �� �������   ������  �� ������
//8 .  �������� ����������� �����  ������ 20 ������

   
  
	
   $gamer1figurs = 1;
   if (isset ($_POST['sel_figuri'])) 
   	if ($_POST['sel_figuri']==='black_fig'){
			
			$gamer1figurs = 2;
			}
	
	// ��� ����� ������?
	$fstmove = 0; // ����,  ��� ��� ������ ������
	if ($gamer1figurs==1)  // ����  � ������� ������ ����� ������ �����  ������ ����� ��
		$fstmove = $AuthorizeSession['GAMER_CODE'];
	
       /*�����������  � ��������*/
				$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
				
			/*�������� ���� ������ */
				if (mysql_select_db("shashki")) {
				$nowtime=strtotime("now"); 
					$SQLrequest = "INSERT INTO ONLINE_GAME(OG_CODE,OG_GAMER1,OG_GAMER2,OG_GAMER1_FIG,OG_SID1,OG_SID2,OG_SCORE1,OG_SCORE2,OG_WAIT,
													  OG_GAMER_MOVE,OG_LST_MOVE_TIME,OG_TIMEOUT_MIN,OG_SEL_CELL1,OG_SEL_CELL2,OG_A_H)
													  VALUES (NULL,".$AuthorizeSession['GAMER_CODE'].",0,".$gamer1figurs.",'".session_id()."','0',0,0,true,".$fstmove.",'".$nowtime."',".$_POST['timeout'].",'FF','FF','".$A_H."')";
				$result = mysql_query( $SQLrequest);
					$OG_CODE = mysql_insert_id(); // ����� ������� �� �����  ���  ����
				
					
				if ($result ) {
					mysql_close( $link); //���������  ���� � �����
					//���� �� �����  ����� ����� ������������  ����� �� ������ gameprocess.php?OG=<�����  ������ ��� �������� ����>
					echo ' 
						<meta http-equiv="Refresh" content="0; URL=gameprocess.php?OG='.$OG_CODE.'">
						';
					exit();
				}
					
				else  {
					printf ("<br> Could not create new game: %s\n", mysql_error ());	
					mysql_close( $link); //���������  ���� � �����
					exit();
					}
				
				}
				
}
//���� ������  �������� JOIN  ������   �����  ���������� ����� �� ����
if (isset($_GET['join'])){ 
	//������ ���������� ������  � ���� ��  ����� � ����� ����  ������  �� ����  ������ ������ ����  �������
	// ����������� ������ �� �����  �������  ������
	//��������
	//1���� ������  ����
	//2. ���� �� ����� ����� ������� �� game.php
	//3. ���� ����� �����   ���������   ��  �������� ��   ��  ��� ��� ��� ����������� �����
	//4.  ���� ��� �����  �����
	//5.  ��������� OG_GAMER1  OG_SID2
	//6. ����    �  ������� ������  ����� ����� �����   ������� ���   OG_GAMER_MOVE ����������      ����� ��������  ������
	//7. ��������� ���������
	//8 ������������ ��  gameprocess.php?OG= ������� ����
	  
	if (!isset ($_GET['OG']))
	{
		echo	' 
					<meta http-equiv="Refresh" content="0; URL=game.php">
				';
		exit();
	}
	/*�����������  � ��������*/
	$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
	/*�������� ���� ������ */
	if (mysql_select_db("shashki")) {
	  $SQLrequest = "SELECT *  FROM ONLINE_GAME WHERE OG_CODE=".$_GET['OG']." ";
	  
		$result = mysql_query( $SQLrequest);
		if  ($result){
			$online_game = mysql_fetch_array($result, MYSQL_ASSOC);
			if ($online_game['OG_GAMER1']==$AuthorizeSession['GAMER_CODE'] || $online_game['OG_GAMER1']== session_id()){
				//��� ��� �� ����� ������ ������� �� ���� ��� ��������������
				echo ' 
						<meta http-equiv="Refresh" content="0; URL=game.php">
						';
					exit();
			}
			else{	
				if ($online_game['OG_GAMER_MOVE']==$online_game['OG_GAMER1'] ) //���� ������  ���  ���  ��  ���������  �����
					$SQLrequest = "UPDATE ONLINE_GAME SET OG_GAMER2=".$AuthorizeSession['GAMER_CODE'].",OG_SID2='".session_id()."',OG_WAIT=0 WHERE OG_CODE=".$_GET['OG']." ";
				else
					$SQLrequest = "UPDATE ONLINE_GAME SET OG_GAMER2=".$AuthorizeSession['GAMER_CODE'].",OG_SID2='".session_id()."',OG_WAIT=0,OG_GAMER_MOVE=".$AuthorizeSession['GAMER_CODE']."   WHERE OG_CODE=".$_GET['OG']." ";
			
				$result = mysql_query( $SQLrequest);
				if  ($result){
					mysql_close( $link); //���������  ���� � �����
					//���� �� �����  ����� ����� ������������  ����� �� ������ gameprocess.php?OG=<�����  ������ ��� �������� ����>
					echo ' 
						<meta http-equiv="Refresh" content="0; URL=gameprocess.php?OG='.$_GET['OG'].'">
						';
					exit();
			
				}else {
					printf ("<br> Could not join to game: %s\n", mysql_error ());	
					mysql_close( $link); //���������  ���� � �����
					exit();
				}
			}
		}else
		{
		if (!$result){
			echo	' 
					<meta http-equiv="Refresh" content="0; URL=game.php">
				';
			exit();
		}
		}
		
		
		
	}
	
}

$megameta = '<meta http-equiv="Refresh" content="10; URL=gameprocess.php?OG='.$_GET['OG'].'">';
/*************************************************/
/* ������ ������������  ������  ���������                  */
/*************************************************/

//������ ����� ����� ���������  �����  ��������� ������
//���� ?OG=XX&paritet=true/false&lose=true/false
//���� ������  paritet -  ����� ����� ����� ������� �  ������ ���� paritet1 = true  ����  ��� gamer1  � paritet2 ���� ��� gamer2
//���� ������ lose - ������. ����� ����� ������� lose1  ��� ��� gamer1   � lose2 ���� ��� gamer2
// �����  ���������  paritet  � lose  ����  ���  true  � ��������� �����  �����  ������ ���������  ��� ���������� �����  �������� �� ���?  ��� �������  ��������� ������  ��������   ����  � �������  ���� ��  ������  ���
// ����  � ����� �� �������  ������  ����� paritet_no = true �������� ����� �����  ���� true  � ���� �������  ������ ��� �  ���� �����������
// ����  �������� �� ����� ��������� paritet=true 
// ���� � �����    �����  �������   ���������  ���� ����� � �������� �� ������ game.php 
//�����  1. ����������  ��� �����������  ���� ����   ������� ��������    ��� ���� �� �����  � Gamer1  ��� Gamer2  � ���������� ����  ����� �������� ���  �� index
//2. ����  ������  ��� ���� ����  ����� ����� ���������   ���������  �� ��� SID  ���� ���  �� ���������  ����� �� ������
//3. ��   ��� �����    �� �����
//4. � �����  ����  ����������� ���� 1  �  �������   ����� ���  ����� �����
// ������: � ������� ����� � ��������� �������  ������� "�������� �������  ������"
//������  ������� �� ����������
//������ ������ ����������  � ���������� 1 �������
// ������ ������� ��� �� ���������� �  ������� � ����� ���� �� ����������
//����� ������� 

if (isset ($_GET['OG'])) { 
	
//� ������� ������� �� � ����������
/*�����������  � ��������*/
				$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
				
			/*�������� ���� ������ */
				if (mysql_select_db("shashki")) {
					$SQLrequest = "SELECT * FROM ONLINE_GAME WHERE OG_CODE=".$_GET['OG']." ";
					$result = mysql_query( $SQLrequest);
					if (mysql_num_rows($result) == 0) {
				//	echo "  ������� ������ ��� ���������22";	
					echo	' 
								<meta http-equiv="Refresh" content="0; URL=game.php">
								';
							exit();
					}
				//echo "  ����� �������";
					if ($result){
						$online_game = mysql_fetch_array($result, MYSQL_ASSOC);
						//�������� ���� �   ������ 1
						
						$gamer1SQL = "SELECT * FROM GAMER WHERE GAMER_CODE=".$online_game['OG_GAMER1']." ";
						$result = mysql_query($gamer1SQL);
						if ($result){
							$gamer1 = mysql_fetch_array($result, MYSQL_ASSOC);
						}else
							$gamer1=0;
						//�������� ���� �   ������ 2
						
						$gamer2SQL = "SELECT * FROM GAMER WHERE GAMER_CODE=".$online_game['OG_GAMER2']." ";
						$result = mysql_query($gamer2SQL);
						if ($result){
							$gamer2 = mysql_fetch_array($result, MYSQL_ASSOC);
						}
						else
						{
							//������ 2  ��� ���
							$gamer2=0;
						}
						if (isset($_GET['lose'])){
							
						if ($AuthorizeSession['GAMER_CODE'] == $gamer1['GAMER_CODE']){
							$online_game['OG_ISLOSE1']=true;
							$SQLrequest = "UPDATE ONLINE_GAME SET OG_ISLOSE1=".$online_game['OG_ISLOSE1']." WHERE OG_CODE=".$_GET['OG']." ";
							$result = mysql_query( $SQLrequest);
						}
						else{
							$online_game['OG_ISLOSE2']=true;
							$SQLrequest = "UPDATE ONLINE_GAME SET OG_ISLOSE2=".$online_game['OG_ISLOSE2']." WHERE OG_CODE=".$_GET['OG']." ";
							$result = mysql_query( $SQLrequest);
			
						}
	
						}	

						isContinue(); 
				//		echo "lastmove   ".$online_game['OG_LST_MOVE_TIME']."     ,  timoutmin   ".$online_game['OG_TIMEOUT_MIN'];
						if (isTimeOut ($online_game['OG_LST_MOVE_TIME'],$online_game['OG_TIMEOUT_MIN'])){
							// �����  �������  ����
							$link2 = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
				
								/*�������� ���� ������ */
							if (mysql_select_db("shashki")) {
							$sqldelgame = "DELETE FROM ONLINE_GAME WHERE OG_CODE=".$_GET['OG']." ";
								$result = mysql_query($sqldelgame);
								}
						
							echo	' 
								<meta http-equiv="Refresh" content="0; URL=game.php">
								';
							exit();
							//echo '���  ���� ���';
						//	exit();
						}
						//	echo "   �������� ��� ��� ";
					}
					else
						{
					//	echo "  ������� ������ ��� ���������";	
							echo	' 
								<meta http-equiv="Refresh" content="0; URL=game.php">
								';
							exit();
						}
				}else
				{
							echo	' 
								<meta http-equiv="Refresh" content="0; URL=game.php">
								';
							exit();
				}

	
	
//������  ����� ���������  ����������� ��  ������  ����    ���� ������������ �  ���  ����
// �����   ����� ��� CODE   ����������  � SESSION[Auth]  � �����  Gamer1_code � gamer2_code
// ���� �� ������ ���������� �� ������
// ���� ������  ����� ���������� ��������������� SID  � ��� ���  ����  ������ �  ������  ����  �� ����� ����� 
//���� ��� ������ ���� ���... ������� ���� ��  ������ �  ����������   ����� ��  ����.php 
$isGamer1=false; //���   ������ 1? ��� ���� ����������� ���  2
if ($AuthorizeSession['GAMER_CODE'] !== $gamer1['GAMER_CODE'])
{
	if ($AuthorizeSession['GAMER_CODE'] !== $gamer2['GAMER_CODE'])
	{
		//��� ��  �����  ����� 2  ���������� � ����
		echo	' 
				<meta http-equiv="Refresh" content="0; URL=game.php">
				';
				exit();
	}
	else{
		//��������� SID2
		if ($online_game['OG_SID2']!== session_id())
		{
			//SID2 ��  �����  �� ����� 2  ���������� � ����
			echo	' 
				<meta http-equiv="Refresh" content="0; URL=game.php">
				';
				exit();
		}
	}
}else {
		//��������� SID1
		if ($online_game['OG_SID1']!== session_id())
		{
		
			//SID1 ��  �����  �� ����� 1 ���������� � ����
			echo' 
					<meta http-equiv="Refresh" content="0; URL=game.php">
				';
			exit();
		}else
			$isGamer1 = true; //�� ��� ������  1
}

echo '
		<html>

		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
		<meta http-equiv="Content-Language" content="ru">';
		

	echo '	
		<title>����</title>
		</head>
		<body style="font-family: Arial; font-size: 10px">
		<table width="100%" id="table13" style="border-collapse: collapse">
		<tr>
			<td colspan="2" style="border-style: solid; border-width: 0; font-family:arial,sans-serif">
			<table width="100%" id="table14" style="border-collapse: collapse">
				<tr>
					<td width="151" style="font-family: arial,sans-serif">
					<img border="0" src="imgs/logo.gif" width="151" height="37"></td>
					<td style="background-color: #A4B7DB; font-family:arial,sans-serif"><b>'; 
	if ($online_game['OG_WAIT']) 
		echo '�������� �������  ������.';
	else
		if ($online_game['OG_GAMER_MOVE'] == $gamer1['GAMER_CODE'])
			echo '���  ������ '.$gamer1['GAMER_LOGIN'];
		else
			if ($online_game['OG_GAMER_MOVE'] == $gamer2['GAMER_CODE'])
				echo '���  ������ '.$gamer2['GAMER_LOGIN'];
		
		
	echo			'</b></td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td width="64%" style="font-family: arial,sans-serif">&nbsp;</td>
			<td width="35%" style="font-family: arial,sans-serif">
			&nbsp;</td>
		</tr>';
		
		
//������ 			
//���� ������  �������� CELL  ������   ��������  ��    ����� � �������
// ������� ����� ���������  
//������  �����    �������  �����  �  �������� ������ 8�8  �� ���������
//...
//...
$A_H = $online_game['OG_A_H'];
fillDoska ($doska); //�������� ����� ������� ���������

	if (isset($_GET['CELL'])){
	
		//   ����  �������  �����  ���  �� ��� ������ ���,   �����  ������ ��� ��������� � ������ �� ������
		if ($online_game['OG_GAMER_MOVE'] !== $AuthorizeSession['GAMER_CODE'] ){
			if (!$online_game['OG_WAIT']){
				echo '<tr>
				<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
				������ �� ��� ���</td>
				<td width="35%" style="font-family: arial,sans-serif">
				&nbsp;</td>
				</tr>';
			}else
			{
				echo '<tr>
					<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
					�� �� ������  ������  ���� ���  ������� ������</td>
					<td width="35%" style="font-family: arial,sans-serif">
					&nbsp;</td></tr>';	
			}
		}else {
		 
			$thismove=strtotime("now"); 
			$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
			//	echo " ���� ��� ";
			/*�������� ���� ������ */
				if (mysql_select_db("shashki")) {
					$SQLrequest = "UPDATE ONLINE_GAME SET OG_LST_MOVE_TIME='".$thismove."' WHERE OG_CODE=".$_GET['OG']." "; 
					$result = mysql_query( $SQLrequest);
					if ($result){
				//	$dt=getdate($thismove);
				//		echo "   �������� timestamp � ".$dt['minutes']." �����";
					}
				//	else
			//		echo  "������ ���������� ������ ������� ";
				}					
			//����  ���  ��� ���� ��� ����� ���������   �������� �� ��
			$cll = ''.$_GET['CELL'].'';
		///	echo "CLL: ".$cll;
			if ($online_game['OG_WAIT'] || !isValidMove($doska, $cll)){
				//���� �� �������� ����� ������ �� ������, � ������
				if ($online_game['OG_WAIT']){
				echo '<tr>
					<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
					�� �� ������  ������  ���� ���  ������� ������</td>
					<td width="35%" style="font-family: arial,sans-serif">
					&nbsp;</td></tr>';
				}else
					echo '<tr>
					<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
					������������ ���</td>
					<td width="35%" style="font-family: arial,sans-serif">
					&nbsp;</td></tr>';
			}else
			{
				produceMove($doska, $cll); //������� ���! (�������� ����� � ��������  ����������  ����� ���� ���)		 
			}
			
			
			
					
		}
	}
	
	//���  ���  �������� �����   �� ���������
	//**********************************
	$itsme =  0;
	$myparitet = 0;
	$vragparitet =0;
	if ($AuthorizeSession['GAMER_CODE'] == $online_game['OG_GAMER1']){
	//echo " �  ���� 1 � ��� ���������������������";
					$itsme =  $online_game['OG_GAMER1'];
					$myparitet =  &$online_game['OG_PARITET1'];
					$vragparitet =&$online_game['OG_PARITET2'];
				}else {
	//	echo " �  ���� 2 � ��� ���������������������";
					$itsme =  $online_game['OG_GAMER2'];
					$myparitet =   &$online_game['OG_PARITET2'];
					$vragparitet = &$online_game['OG_PARITET1'];
					
				}
		$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
		
			/*�������� ���� ������ */
	mysql_select_db("shashki");
	//// � ���  ��� ��������  ����  ���������
		   if (isset($_GET['paritet'])){ //����   ���� ��������� �����
			//	echo  "     ��������  ������� ���������  ";
		
				switch ($_GET['paritet']){
					case 2:{ //�� ���������  �����
					if ($myparitet !== 2) {
								$myparitet = 2;
								
							echo '<tr>
								<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
								������ ��������� ���� ���������� �����. ����  ��  ����������  ���� ����� ���������.</td>
								<td width="35%" style="font-family: arial,sans-serif">
								&nbsp;</td></tr>';
					}
							}break;
					case 3:{// �� �������� �� �� �����
								//����� �������� ���� 3
								$myparitet = 3;
								echo '<tr>
								<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
								�� ����������� �� �����</td>
								<td width="35%" style="font-family: arial,sans-serif">
								&nbsp;</td></tr>';
								$megameta =  '<meta http-equiv="Refresh" content="1; URL=game.php">'; //������ �����
							switch ($itsme){
								case $online_game['OG_GAMER1']:
								{
			
								 $sqlgamer1 = "SELECT * FROM GAMER WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
								 $result = mysql_query( $sqlgamer1);
								 $gamer1 = mysql_fetch_array($result, MYSQL_ASSOC);
								 $gamer1['GAMER_PARITET']++;
								 $sqlgamer1= "UPDATE GAMER SET GAMER_PARITET=".$gamer1['GAMER_PARITET']." WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
								 $result = mysql_query($sqlgamer1);
								  
								}break;
								 case $online_game['OG_GAMER2']:
								 {
							
								 $sqlgamer2 = "SELECT * FROM GAMER WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
								 $result = mysql_query( $sqlgamer2);
								 $gamer2 = mysql_fetch_array($result, MYSQL_ASSOC);
								 $gamer2['GAMER_PARITET']++;
								 $sqlgamer2= "UPDATE GAMER SET GAMER_PARITET=".$gamer2['GAMER_PARITET']." WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
								 $result = mysql_query($sqlgamer2);
								  
								}break;
									
							}
							
							}break;
					case 4:{// �� �������� ��� �� �����	
								//������ ���� ������� � 4
								$vragparitet = 1;
								$myparitet = 1;
								$_GET['paritet']=1;
								unset($_GET['paritet']);
								echo '<tr>
								<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
								�����������  ������  ��������� �� �����,  ����  ���������</td>
								<td width="35%" style="font-family: arial,sans-serif">
								&nbsp;</td></tr>';
							
							//$megameta = '<meta http-equiv="Refresh" content="0; URL=gameprocess.php?OG='.$_GET['OG'].'">';
							}break;
				}
				
			}else
			{
		
		
				switch ($vragparitet)	{
				case 1:{}break;// ��������  �� �������
				case 2:{
					$myparitet =1;// �� ��� �� ��������;
					echo '<tr>
									<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
									�������� ����������  ��� �����. �� c������� ? <a href="gameprocess.php?OG='.$_GET['OG'].'&paritet=3">��</a> / <a href="gameprocess.php?OG='.$_GET['OG'].'&paritet=4">���</a></td>
									<td width="35%" style="font-family: arial,sans-serif">
									&nbsp;</td></tr>';
							//		$megameta = '<meta http-equiv="Refresh" content="0; URL=gameprocess.php?OG='.$_GET['OG'].'">';

				}break;// ��������  �������� �����
				case 3:{
				/*	echo '
									<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
									�������� ���������� �� �����</td>
									<td width="35%" style="font-family: arial,sans-serif">
									&nbsp;</td>';*/
								$vragparitet = 1;
								$myparitet = 1;
								 //�����  �������� ��������� ������ 2
				 //�����   ������� ����
				 // �����  ���������� ������ ��  �������� ����������
				 switch ($itsme){
				 case $online_game['OG_GAMER1']:
				 {
				// echo "  �����  � ���� ��� ������ 1";
				 $sqlgamer1 = "SELECT * FROM GAMER WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
				 $result = mysql_query( $sqlgamer1);
				 $gamer1 = mysql_fetch_array($result, MYSQL_ASSOC);
				 $gamer1['GAMER_PARITET']++;
				 $sqlgamer1= "UPDATE GAMER SET GAMER_PARITET=".$gamer1['GAMER_PARITET']." WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
				 $result = mysql_query($sqlgamer1);
				 
				}break;
				 case $online_game['OG_GAMER2']:
				 {
			//	 echo "  �����  � ���� ��� ������ 1";
				 $sqlgamer2 = "SELECT * FROM GAMER WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
				 $result = mysql_query( $sqlgamer2);
				 $gamer2 = mysql_fetch_array($result, MYSQL_ASSOC);
				 $gamer2['GAMER_PARITET']++;
				 $sqlgamer2= "UPDATE GAMER SET GAMER_PARITET=".$gamer2['GAMER_PARITET']." WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
				 $result = mysql_query($sqlgamer2);
			
				}break;
				}
		//������ ����
		
				 $sqldelgame = " DELETE FROM ONLINE_GAME WHERE OG_CODE=".$_GET['OG']." ";
				 $result = mysql_query($sqldelgame);
		
				}break;// ��������  �������� �� �����
				case 4:{
					echo '<tr>
								<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
								�������� �������� ���� ����������� �� ����� </td>
								<td width="35%" style="font-family: arial,sans-serif">
								&nbsp;</td></tr>';
								$vragparitet = 1;
								$myparitet = 1;
								$megameta = '<meta http-equiv="Refresh" content="15; URL=gameprocess.php?OG='.$_GET['OG'].'">';
								
			}break;// ��������  �������� �����
				}
			
			}
			
			
			
//����� ���  ���  ������  
//������  �����  �������  �  ����   �������  �����  �   �������� ���  �������  �����
//� ��� �� ����� �������  � ���� ���  ���������� ������� ����  ������� ������ ��  ����    �������  ������  ����  ����������.  ������  ������ ���� GamerCode � SID � ���� ����
if ($AuthorizeSession['GAMER_CODE'] == $online_game['OG_GAMER1'])
	$myselcell = &$online_game['OG_SEL_CELL1'];
else
	if ($AuthorizeSession['GAMER_CODE'] == $online_game['OG_GAMER2'])
		$myselcell = &$online_game['OG_SEL_CELL2'];
	
	
	echo'	<tr>
			<td width="40%" style="font-family: arial,sans-serif" height="40">
			<div align="center">';
			//1 ������ �����
	echo 	'<table width="320" id="table19" style="border-collapse: collapse" height="320" cellpadding="0">';
				
			for ($i=0,$y=0;$y<=7;$y++){
				echo '<tr>';
				for ($x=0;$x<=7;$x++){
				  	
				$sx = $myselcell[0];
				$sy = $myselcell[1];
				
				 if ($myselcell == ''.$x.$y.'' & $doska[$x][$y] != 6 )
					echo '<td><a href="'.$_SERVER['SCRIPT_NAME'].'?OG='.$_GET['OG'].'&CELL='.$x.$y.'"><img border="0" src="imgs/'.$doska[$x][$y].'s.gif"></a></td>';
				 else
				 {
				 if ($doska[$x][$y] != 5)
						echo '<td><a href="'.$_SERVER['SCRIPT_NAME'].'?OG='.$_GET['OG'].'&CELL='.$x.$y.'"><img border="0" src="imgs/'.$doska[$x][$y].'.gif"></a></td>';
					else
						echo '<td><img border="0" src="imgs/'.$doska[$x][$y].'.gif"></td>'; //���� �����  ������ ����� ������ �� �����
				 }
				}
				echo '</tr>';
			}
			echo '
				</table>
			</div>
			</td>
			<td width="35%" style="font-family: arial,sans-serif" height="346">
			<table width="100%" id="table15">
				<tr>
					<td colspan="3" bgcolor="#CCFF99">
					<p align="center"><b>���������� �� ����</b></td>
				</tr>
				<tr>
					<td width="119" bgcolor="#A4B7DB" align="center"><b>
					<font size="2">������</font></b></td>
					<td width="84" bgcolor="#A4B7DB" align="center"><b>
					<font size="2">������</font></b></td>
					<td bgcolor="#A4B7DB" align="center"><b><font size="2">������</font></b></td>
				</tr>';
				$youcolor = '#FFCC00';
				$nonyoucolor = '#D7E3FF';
				if ($AuthorizeSession['GAMER_CODE'] == $online_game['OG_GAMER1'])
					$clr = $youcolor;
				else
					$clr =$nonyoucolor;
				echo '<tr>
					<td width="119" bgcolor="'.$clr.'"><b><span lang="en-us">
					<font size="4">';
				//����� �1	
				echo $gamer1['GAMER_LOGIN'];
				echo	'</font></span></b></td>
					<td width="84" bgcolor="'.$clr.'"><b><font size="4">';
				//����� �1 ������
				if ($online_game['OG_GAMER1_FIG']==1)
					echo '�����';
				else
					echo '������';
				
				echo '
					</font></b></td>
					<td bgcolor="'.$clr.'"><b><font size="4">';
				//������ ������ 1
				echo $online_game['OG_SCORE1'];
				echo '</font></b></td>
				</tr>';
				//���� ����  ����� 2
				if ($gamer2){
					if ($AuthorizeSession['GAMER_CODE'] == $online_game['OG_GAMER2'])
					$clr = $youcolor;
				else
					$clr =$nonyoucolor;
					
					echo '<tr>
						<td width="119" bgcolor="'.$clr.'"><b><span lang="en-us">
						<font size="4">';
					//����� ������ �2
					echo $gamer2['GAMER_LOGIN'];	
					echo	'</font></span></b></td>
						<td width="84" bgcolor="'.$clr.'"><b><font size="4">';
					//����� �2 ������
					if ($online_game['OG_GAMER1_FIG']==1)
						echo '������';
					else
						echo '�����';
					echo '</font></b></td>
						<td bgcolor="'.$clr.'"><b><font size="4">';
					//������ ������ 2
					echo $online_game['OG_SCORE2'];	
					echo'</font></b></td>
						</tr>';
				}
				echo '
				<tr>
					<td width="119">&nbsp;</td>
					<td width="84">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<p>&nbsp;</p>
			<p>&nbsp;</p>';
			if (!$online_game['OG_WAIT']){
				echo '<table width="100%" id="table16" height="60" bgcolor="#A4B7DB">
					<tr>
						<td width="283">
						<p align="center"><font size="5" color="#294069">������� ���</font></td>
						<td>
						<p align="center">
						<img border="0" src="imgs/';
						if ($online_game['OG_GAMER_MOVE'] == $online_game['OG_GAMER1']){ //����  ������� ��� ������� 1
							if ($online_game['OG_GAMER1_FIG']==1) // � ����   ����� ������� 1 ����� �����
								echo '1.gif';
							else
								echo '2.gif';
						}else 									  //����  ������� ����� ������2
							if ($online_game['OG_GAMER1_FIG']==2) // � ����   ����� ������� 2 ����� �����
								echo '1.gif';
							else
								echo '2.gif';
					echo	'" width="40" height="40"></td>
					</tr>
				</table>';
			}
		echo '</td> </tr>';
		
		if ($AuthorizeSession['GAMER_CODE'] == $online_game['OG_GAMER_MOVE'] & !$online_game['OG_WAIT'] ) {
		echo'		
			<tr>
				<td width="64%" style="font-family: arial,sans-serif">&nbsp;</td>
				<td width="35%" style="font-family: arial,sans-serif">
				<table width="100%" id="table18">
					<tr>
						<td width="46%" align="center" bgcolor="#EAEAFF"><b>
						<a href="gameprocess.php?OG='.$_GET['OG'].'&lose=1">�������</a></b></td>
						<td width="51%" align="center" bgcolor="#EAEAFF"><b>
						<a href="gameprocess.php?OG='.$_GET['OG'].'&paritet=2">�����</a></b></td>
					</tr>
				</table>
				</td>
			</tr>';
		}
		
echo '	</table>';
//echo "   �� �����  ��  ����������";
//echo " OG1Par     ".$online_game['OG_PARITET1'];
	//	echo " OG2Par     ".$online_game['OG_PARITET2'];
		
saveChanges(); // ��������    ���  ������

	
echo $megameta;
echo '</body>

	</html>';


}
		
else{
	echo ' 
			<meta http-equiv="Refresh" content="0; URL=game.php">
		';
		exit();
	}



?>