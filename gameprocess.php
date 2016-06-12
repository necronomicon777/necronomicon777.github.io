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

//функция выхода по таймауту

function isTimeOut($timelastmove, $timeoutmin){
	
	$tlm_=getdate($timelastmove);
	$nowtime=strtotime("now"); 
	$nowrm_=getdate($nowtime);
	$dd=$nowrm_['minutes']-$tlm_['minutes'];
   // echo "   до таймаута   ".$dd." минут";
	if ($nowrm_['minutes']-$tlm_['minutes'] >=$timeoutmin)
		return true;
	
	
	return false;
}

// стоит  ли продолжать  и не сдался ли  один из  игроков провери м сразу
function isContinue(){
global $dbServer;
global $dbUser;
global $dbPass;

GLOBAL $AuthorizeSession;
$Iwin=true;
	//сначала определим ху из  ху
	$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
	$itsme =0;			
			/*Выбираем базу данных */
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
		if ($online_game['OG_ISLOSE1']==true ) //если из  игры вышел  первый игрок заявив что  сдается
		{
			 //нужно  записать проигрышь игроку 1
			 //нужно   удалить игру
			 // нужно  перекинуть юзеров на  страницу информации
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
	
			 //нужно  записать проигрышь игроку 2
			 //нужно   удалить игру
			 // нужно  перекинуть юзеров на  страницу информации
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
		//теперь  удалим свой SID
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
		// можно  удалять  игру
	//	ECHO "        УДАЛЕНЫ ВСЕ sid";
		 $sqldelgame = " DELETE FROM ONLINE_GAME WHERE OG_CODE=".$_GET['OG']." ";
			 $result = mysql_query($sqldelgame);
			 if ($result){
			 echo '
		<html>

		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
		<meta http-equiv="Content-Language" content="ru">
		<meta http-equiv="Refresh" content="5; URL=game.php"> 
				 <title>Игра окончена</title>
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
				<p align="center">Вы победили. Поздравляем </p>
				<p align="center"><b>Нажмите <a href="game.php">здесь</a> чтобы&nbsp; 
				пререйти на главную страницу</b></td>
				<td height="197">&nbsp;</td>
			</tr>';
	}
	else
	echo  '#FF5050">
				<p align="center">Игра окончена. Вы проиграли</p>
				<p align="center"><b>Нажмите <a href="game.php">здесь</a> чтобы&nbsp; 
				пререйти на главную страницу</b></td>
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
			 
			 
			 
			// echo "     //     Удалили игру";
		
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
				 <title>Игра окончена</title>
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
				<p align="center">Вы победили. Поздравляем </p>
				<p align="center"><b>Нажмите <a href="game.php">здесь</a> чтобы&nbsp; 
				пререйти на главную страницу</b></td>
				<td height="197">&nbsp;</td>
			</tr>';
	}
	else
	echo  '#FF5050">
				<p align="center">Игра окончена. Вы проиграли</p>
				<p align="center"><b>Нажмите <a href="game.php">здесь</a> чтобы&nbsp; 
				пререйти на главную страницу</b></td>
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
	
	
	mysql_close( $link); //закрываем  линк с базой
	}
	//echo "     //Мы вышли  из isContinue с флагом TRUE";
	return TRUE;
}
 //1 - белая , 2 - черная, 3 - белая  дамка, 4 - черная дамка, 5 -пустая белая, 6 -пустая черная.
   //1.. сначала подготовим  начальные строчки для доски
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
   
   //заполним расклад доски
   function fillDoska(&$doska_array){
   global $A_H;
		for ($i=0,$y=0;$y<=7;$y++){
			for ($x=0;$x<=7;$x++){
				$doska_array[$x][$y] = $A_H[$i];
				$i++;
			}
		}
	}
	
	
	
	fillDoska ($doska); //заполним сразу
	
	function setMove(&$doska_array,&$myselcell, $cell_num){
	//оприделим чья какая дамка
	global $online_game;
	global $gamer1;
	global $gamer2;
	global $AuthorizeSession;
		//сначала определим ху из  ху
		if ($AuthorizeSession['GAMER_CODE'] == $gamer1['GAMER_CODE']){
			
			
			if ($online_game['OG_GAMER1_FIG']==1){
				$myfig = 1; //белые
				$mysaska = 1; //белая
				$mydamka = 3;//белая
				$vragsaska = 2;//вражеская шашка
				$vragdamka = 4;//вражеская дамка
			}
			else {
				$myfig = 2; //черные
				$mysaska = 2; //черная
				$mydamka = 4;//черная
				$vragsaska = 1;//вражеская шашка
				$vragdamka = 3;//вражеская дамка
			}
			
		}
		else{
			
			
			if ($online_game['OG_GAMER1_FIG']==1){
				$myfig = 2; //черная
				$mysaska = 2; //черная
				$mydamka = 4;//черная
				$vragsaska = 1;//вражеская шашка
				$vragdamka = 3;//вражеская дамка
				
			}
			else {
				$myfig = 1; //белая
				$mysaska = 1; //белая
				$mydamka = 3;//белая
				$vragsaska = 2;//вражеская шашка
				$vragdamka = 4;//вражеская дамка
			}
		}
	
		$fromX = $myselcell[0];
		$fromY = $myselcell[1];
		$shashka = $doska_array[$fromX][$fromY];
		$toX = $cell_num[0];
		$toY = $cell_num[1];
		if ($myfig == 1) //если  мои  белые
		{
			if ($toY==7) //если мы дошли до конца тогда
				$doska_array[$toX][$toY] = $mydamka;
			else
				$doska_array[$toX][$toY] = $shashka;
		}else
		if ($myfig == 2) //если  мои  черные
		{
		   if ($toY==0) //если мы дошли до конца тогда
				$doska_array[$toX][$toY] = $mydamka;
			else
				$doska_array[$toX][$toY] = $shashka;
		}
		
		
		$doska_array[$fromX][$fromY] = 6; //пустая черная на то место  где стояла шашка
		$myselcell = ''.$toX.$toY.''; // выделенную  оставляем выделенной но в новом месте
	}
	function isValidMove(&$doska_array, $cell_num){
		global $online_game;
		global $gamer1;
		global $gamer2;
		global $AuthorizeSession;
		
		//сначала определим ху из  ху
		if ($AuthorizeSession['GAMER_CODE'] == $gamer1['GAMER_CODE']){
			$itsme = $gamer1;
			$mycode = $online_game['OG_GAMER1'];
			$vragcode = $online_game['OG_GAMER2'];
			$mysid = $online_game['OG_SID1'];
			$myscore =  & $online_game['OG_SCORE1'];
			$myselcell = &$online_game['OG_SEL_CELL1'];
			
			if ($online_game['OG_GAMER1_FIG']==1){
				$myfig = 1; //белые
				$mysaska = 1; //белая
				$mydamka = 3;//белая
				$vragsaska = 2;//вражеская шашка
				$vragdamka = 4;//вражеская дамка
			}
			else {
				$myfig = 2; //черные
				$mysaska = 2; //черная
				$mydamka = 4;//черная
				$vragsaska = 1;//вражеская шашка
				$vragdamka = 3;//вражеская дамка
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
				$myfig = 2; //черная
				$mysaska = 2; //черная
				$mydamka = 4;//черная
				$vragsaska = 1;//вражеская шашка
				$vragdamka = 3;//вражеская дамка
				
			}
			else {
				$myfig = 1; //белая
				$mysaska = 1; //белая
				$mydamka = 3;//белая
				$vragsaska = 2;//вражеская шашка
				$vragdamka = 4;//вражеская дамка
			}
		}
		//проверим  его ли ход?
		// если  ход не  его выходим с фальшем
		if ($online_game['OG_GAMER_MOVE']!==$AuthorizeSession['GAMER_CODE'])
			return false;
	//	echo "выделенная ".$myselcell;
		//проверим есть ли  шашки еще ?
		// проверим  мозможно ли  шашками  ходит
		// если  второе условие  ложно  для всех   имеющихся шашек тогда  проигрышь засчитан
		$mysaski =0;
		//$vragsaski = 0;
		for ($y=0;$y<=7;$y++){
			for ($x=0;$x<=7;$x++){
				if ($doska_array[$x][$y] == $mysaska || $doska_array[$x][$y] == $mydamka){
					//проверим  реально  ли  шашке  пойти куда нить из  4 направлений
						if ($x>0 & $y>0)
							$UP_LEFT = $doska_array[$x-1][$y-1]; //левая верхняя клетка  от той которая выделена.
						else
							$UP_LEFT = 5;// 5 все равно  не используется (5 это  белая пустая клетка) 
						if ($x>0 & $y<7)
							$DOWN_LEFT=$doska_array[$x-1][$y+1]; //нижняя левая  клетка от той которая выделена
						else
							$DOWN_LEFT = 5; // 5 все равно  не используется (5 это  белая пустая клетка)  
						if ($x<7 & $y>=1)
							$UP_RIGHT = $doska_array[$x+1][$y-1]; //правая верхняя  клетка от той которая выделена
						else
							$UP_RIGHT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
						if ($x<7 & $y<7)
							$DOWN_RIGHT =$doska_array[$x+1][$y+1]; //нижняя правая клетка от той которая выделена
						else
							$DOWN_RIGHT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
						
						if ($UP_LEFT == 6  || $UP_RIGHT == 6 ||
							$DOWN_RIGHT == 6  || $DOWN_LEFT == 6)
						$mysaski++; //если шашка способна  куда нить  пойти  значит   тогда все в шоколаде
					}
				}
			}
		
		if ($mysaski==0){
		
			echo ' 
						<meta http-equiv="Refresh" content="0; URL=gameprocess.php?OG='.$_GET['OG'].'&lose=1">
						';
					exit();
		}
		//иначе. смотрим
		//если той на которой щелкнули   является уже выделенной
		if ($myselcell!=='FF') { // если хоть что то выделено
				if ($cell_num == $myselcell){ 
				$xx = $myselcell[0];
				$yy = $myselcell[1];
			//	echo "myselcell        ".$myselcell;
				$shashka = $doska_array[$xx][$yy];
				switch ($shashka) {//и если в той на кторую щелкнули стоит  своя шашка тогда
				case $mysaska:return true; // ход  допустим оставляем все как есть
				case $mydamka:return true; //ход  допустим оставляем все как есть
				case 5:        //если щелкнули на пустом месте уже
				case 6:{$myselcell='FF'; //выделяем ничего
					    return true; //ход  выполним
					   }
				default: return false;
				}
			}else //если щелкнули   по другому месту
			{
				$toX = $cell_num[0];
				$toY = $cell_num[1];
				$fromX = $myselcell[0];
				$fromY = $myselcell[1];
				$shashka = $doska_array[$toX][$toY];
				switch ($shashka) {//и если в той на кторую щелкнули стоит  своя шашка тогда
				case $mysaska:
				case $mydamka:{$myselcell = ''.$cell_num.'';
							   return true;} //ход  допустим выделяем  нажатую  шашку
				case 6:{ 
				//если мы щелкнули на черную клетку пусту тогда  ход  осуществим если
				
				
							//1.   рядом к клетке  выделенной по диагонали  не  примыкает  чужая  клетка, за которой есть пустое пространство
							//2. если  клетка  на которую  мы  переходим   стоит ниже  той на которой мы стоим
						//1. сначала проверим есть ли в округе  чужиеклетки  если  есть   тогда среди них  найдем те которые можно  срубить
						//2. и если  та клетка  куда мы щелкнули  является  именной такой  клеткой  которая срубит  тогда  ход  осуществим иначе нет	
						
						
						if ($fromX>0 & $fromY>0)
							$UP_LEFT = $doska_array[$fromX-1][$fromY-1]; //левая верхняя клетка  от той которая выделена.
						else
							$UP_LEFT = 5;// 5 все равно  не используется (5 это  белая пустая клетка) 
						if ($fromX>=2 & $fromY>=2)
							$UL_UPLEFT = $doska_array[$fromX-2][$fromY-2];////левая верхняя клетка  от Левой верхней
						else
							$UL_UPLEFT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
							
						if ($fromX>0 & $fromY<7)
							$DOWN_LEFT=$doska_array[$fromX-1][$fromY+1]; //нижняя левая  клетка от той которая выделена
						else
							$DOWN_LEFT = 5; // 5 все равно  не используется (5 это  белая пустая клетка)  
						
						if ($fromX>=2 & $fromY<=5)
							$DL_DOWNLEFT=$doska_array[$fromX-2][$fromY+2]; //нижняя левая  клетка от нижней левой
						else
							$DL_DOWNLEFT= 5; // 5 все равно  не используется (5 это  белая пустая клетка)  
						
     					if ($fromX<7 & $fromY>=1)
							$UP_RIGHT = $doska_array[$fromX+1][$fromY-1]; //правая верхняя  клетка от той которая выделена
						else
							$UP_RIGHT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
						
						if ($fromX<=5 & $fromY>=2)
							$UR_UPRIGHT = $doska_array[$fromX+2][$fromY-2]; //правая верхняя  клетка от  правой верхней 
						else
							$UR_UPRIGHT =5 ;// 5 все равно  не используется (5 это  белая пустая клетка)  
						
						if ($fromX<7 & $fromY<7)
							$DOWN_RIGHT =$doska_array[$fromX+1][$fromY+1]; //нижняя правая клетка от той которая выделена
						else
							$DOWN_RIGHT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
						if ($fromX<=5 & $fromY<=5)
							$DR_DOWNRIGHT =$doska_array[$fromX+2][$fromY+2]; //нижняя правая клетка от нижней правой
						else
							$DR_DOWNRIGHT = 5 ;// 5 все равно  не используется (5 это  белая пустая клетка) 
						
						if ($UP_LEFT==$vragsaska || $UP_LEFT==$vragdamka){ //если справавверху от  клетки которая выделена стоит чужая дамка или шашка
							if ($UL_UPLEFT == 6) // и если от  нее  уже стоит  пустая клетка по курсу тогда
								if ($toX == $fromX-2 & $toY ==$fromY-2) //и если мы щелкнули  именно туда  тогда ход  осуществим 
								{
									$doska_array[$fromX-1][$fromY-1]=6;// пусто срубили
									$myscore++;
									setMove($doska_array,$myselcell, $cell_num);
									if (isNextMove($doska_array, $myselcell, $vragsaska,$vragdamka)) 
										$online_game['OG_GAMER_MOVE'] = $vragcode;
									return true;
								}
						}
							if ($UP_RIGHT==$vragsaska || $UP_RIGHT==$vragdamka){ //если справавверху от  клетки которая выделена стоит чужая дамка или шашка
								if ($UR_UPRIGHT == 6) // и если от  нее  уже стоит  пустая клетка по курсу тогда
									if ($toX == $fromX+2 & $toY ==$fromY-2) //и если мы щелкнули  именно туда  тогда ход  осуществим 
									{
										$doska_array[$fromX+1][$fromY-1]=6;// пусто срубили
										$myscore++;
										setMove($doska_array,$myselcell, $cell_num);
										if (isNextMove($doska_array, $myselcell, $vragsaska,$vragdamka)) 
											$online_game['OG_GAMER_MOVE'] = $vragcode;
										return true;
									}
							}
								if ($DOWN_RIGHT==$vragsaska || $DOWN_RIGHT==$vragdamka){ //если справавверху от  клетки которая выделена стоит чужая дамка или шашка
									if ($DR_DOWNRIGHT == 6) // и если от  нее  уже стоит  пустая клетка по курсу тогда
										if ($toX == $fromX+2 & $toY ==$fromY+2) //и если мы щелкнули  именно туда  тогда ход  осуществим 
										{
											$doska_array[$fromX+1][$fromY+1]=6;// пусто срубили
											$myscore++;
											setMove($doska_array,$myselcell, $cell_num);
											if (isNextMove($doska_array, $myselcell,$vragsaska,$vragdamka)) 
												$online_game['OG_GAMER_MOVE'] = $vragcode;
											return true;
										}
								}
									if ($DOWN_LEFT==$vragsaska || $DOWN_LEFT==$vragdamka){ //если справавверху от  клетки которая выделена стоит чужая дамка или шашка
										if ($DL_DOWNLEFT == 6) // и если от  нее  уже стоит  пустая клетка по курсу тогда
											if ($toX == $fromX-2 & $toY ==$fromY+2) //и если мы щелкнули  именно туда  тогда ход  осуществим 
											{
												$doska_array[$fromX-1][$fromY+1]=6;// пусто срубили
												$myscore++;
												setMove($doska_array,$myselcell, $cell_num);
												if (isNextMove($doska_array, $myselcell, $vragsaska,$vragdamka)) 
													$online_game['OG_GAMER_MOVE'] = $vragcode;
												return true;
											}
									}
										
											//если во круг  ничего  не стоит .. Мы должны не нужно ли  рубить  другими шашками ? если надо  и если э
											//***********************************************
											//В самом начале пройдемся по всем клеткам    и проверим нет ли среди них  такие которые
				//нуждаются в срубываниии вражеских.  если таковые есть тогда.  проверяем если  это наша выделенная клетка тогда всё ровно
				// если  это не наша клетка тогда  ход запрещаем.
				// если  помимо нашей есть еще какая нибудь тогда  ход  разрешаем все равно
				for ($y=0;$y<=7;$y++){
					$fromY1 = $y;
					for ($x=0;$x<=7;$x++){
						$fromX1 = $x; 
						if ($doska_array[$fromX1][$fromY1] == $vragdamka || $doska_array[$fromX1][$fromY1] == $vragsaska  ||
							$doska_array[$fromX1][$fromY1] == 5 || $doska_array[$fromX1][$fromY1] == 6	) continue;
						if ($fromX1>0 & $fromY1>0)
							$UP_LEFT = $doska_array[$fromX1-1][$fromY1-1]; //левая верхняя клетка  от той которая выделена.
						else
							$UP_LEFT = 5;// 5 все равно  не используется (5 это  белая пустая клетка) 
						if ($fromX1>=2 & $fromY1>=2)
							$UL_UPLEFT = $doska_array[$fromX1-2][$fromY1-2];////левая верхняя клетка  от Левой верхней
						else
							$UL_UPLEFT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
							
						if ($fromX1>0 & $fromY1<7)
							$DOWN_LEFT=$doska_array[$fromX1-1][$fromY1+1]; //нижняя левая  клетка от той которая выделена
						else
							$DOWN_LEFT = 5; // 5 все равно  не используется (5 это  белая пустая клетка)  
						
						if ($fromX1>=2 & $fromY1<=5)
							$DL_DOWNLEFT=$doska_array[$fromX1-2][$fromY1+2]; //нижняя левая  клетка от нижней левой
						else
							$DL_DOWNLEFT= 5; // 5 все равно  не используется (5 это  белая пустая клетка)  
						
     					if ($fromX1<7 & $fromY1>=1)
							$UP_RIGHT = $doska_array[$fromX1+1][$fromY1-1]; //правая верхняя  клетка от той которая выделена
						else
							$UP_RIGHT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
						
						if ($fromX1<=5 & $fromY1>=2)
							$UR_UPRIGHT = $doska_array[$fromX1+2][$fromY1-2]; //правая верхняя  клетка от  правой верхней 
						else
							$UR_UPRIGHT =5 ;// 5 все равно  не используется (5 это  белая пустая клетка)  
						
						if ($fromX1<7 & $fromY1<7)
							$DOWN_RIGHT =$doska_array[$fromX1+1][$fromY1+1]; //нижняя правая клетка от той которая выделена
						else
							$DOWN_RIGHT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
						if ($fromX1<=5 & $fromY1<=5)
							$DR_DOWNRIGHT =$doska_array[$fromX1+2][$fromY1+2]; //нижняя правая клетка от нижней правой
						else
							$DR_DOWNRIGHT = 5 ;// 5 все равно  не используется (5 это  белая пустая клетка) 
						
						if ($UP_LEFT==$vragsaska || $UP_LEFT==$vragdamka){ //если справавверху от  клетки которая выделена стоит чужая дамка или шашка
							if ($UL_UPLEFT == 6) // и если от  нее  уже стоит  пустая клетка по курсу тогда
								if ($fromX1 !== $myselcell[0] & $fromY1 !==$myselcell[1]) //и если мы щелкнули  именно туда  тогда ход  осуществим 
								{	
						//		echo "UP_LEFT нас вышеб ";								
								return false;
								}
						}
							if ($UP_RIGHT==$vragsaska || $UP_RIGHT==$vragdamka){ //если справавверху от  клетки которая выделена стоит чужая дамка или шашка
								if ($UR_UPRIGHT == 6) // и если от  нее  уже стоит  пустая клетка по курсу тогда
									if ($fromX1 !== $myselcell[0] & $fromY1 !==$myselcell[1]) //и если мы щелкнули  именно туда  тогда ход  осуществим 
									{		
										//echo "UP_RIGHT нас вышеб ";
										return false;
									}
							}
								if ($DOWN_RIGHT==$vragsaska || $DOWN_RIGHT==$vragdamka){ //если справавверху от  клетки которая выделена стоит чужая дамка или шашка
									if ($DR_DOWNRIGHT == 6) // и если от  нее  уже стоит  пустая клетка по курсу тогда
										if ($fromX1 !== $myselcell[0] & $fromY1 !==$myselcell[1]) //и если мы щелкнули  именно туда  тогда ход  осуществим 
										{		
										//echo "DOWN_RIGHT нас вышеб ";
										return false;
										}
								}
									if ($DOWN_LEFT==$vragsaska || $DOWN_LEFT==$vragdamka){ //если справавверху от  клетки которая выделена стоит чужая дамка или шашка
										if ($DL_DOWNLEFT == 6) // и если от  нее  уже стоит  пустая клетка по курсу тогда
											if ($fromX1 !== $myselcell[0] & $fromY1 !==$myselcell[1]) //и если мы щелкнули  именно туда  тогда ход  осуществим 
											{		
											//	echo "DOWN_LEFT нас вышеб ";
												return false;
											}
									}
					}
				}
				
											//***********************************************
						$fromX = $myselcell[0];
						$fromY = $myselcell[1];
						if ($fromX>0 & $fromY>0)
							$UP_LEFT = $doska_array[$fromX-1][$fromY-1]; //левая верхняя клетка  от той которая выделена.
						else
							$UP_LEFT = 5;// 5 все равно  не используется (5 это  белая пустая клетка) 
						if ($fromX>=2 & $fromY>=2)
							$UL_UPLEFT = $doska_array[$fromX-2][$fromY-2];////левая верхняя клетка  от Левой верхней
						else
							$UL_UPLEFT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
							
						if ($fromX>0 & $fromY<7)
							$DOWN_LEFT=$doska_array[$fromX-1][$fromY+1]; //нижняя левая  клетка от той которая выделена
						else
							$DOWN_LEFT = 5; // 5 все равно  не используется (5 это  белая пустая клетка)  
						
						if ($fromX>=2 & $fromY<=5)
							$DL_DOWNLEFT=$doska_array[$fromX-2][$fromY+2]; //нижняя левая  клетка от нижней левой
						else
							$DL_DOWNLEFT= 5; // 5 все равно  не используется (5 это  белая пустая клетка)  
						
     					if ($fromX<7 & $fromY>=1)
							$UP_RIGHT = $doska_array[$fromX+1][$fromY-1]; //правая верхняя  клетка от той которая выделена
						else
							$UP_RIGHT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
						
						if ($fromX<=5 & $fromY>=2)
							$UR_UPRIGHT = $doska_array[$fromX+2][$fromY-2]; //правая верхняя  клетка от  правой верхней 
						else
							$UR_UPRIGHT =5 ;// 5 все равно  не используется (5 это  белая пустая клетка)  
						
						if ($fromX<7 & $fromY<7)
							$DOWN_RIGHT =$doska_array[$fromX+1][$fromY+1]; //нижняя правая клетка от той которая выделена
						else
							$DOWN_RIGHT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
						if ($fromX<=5 & $fromY<=5)
							$DR_DOWNRIGHT =$doska_array[$fromX+2][$fromY+2]; //нижняя правая клетка от нижней правой
						else
							$DR_DOWNRIGHT = 5 ;// 5 все равно  не используется (5 это  белая пустая клетка) 
											//если мы играем  Белыми тогда мы  ходим  вниз  если  черными тогда вверх
											switch ($myfig) {
												case 1:{		//белыми играем 
											//	echo "    играем белыми  перед  свитчем";
														switch ($doska_array[$fromX][$fromY]){
														case $mysaska:{
													//	echo "    сработала  шашка";
															if ($DOWN_RIGHT==6){ //если снизу справа пусто  можно ставить 
																if ($toX == $fromX+1 & $toY ==$fromY+1){ //и если мы щелкнули  именно туда  тогда ход  осуществим 
															//	echo "сработал DOWN RIGHT";
																	setMove($doska_array,$myselcell, $cell_num);
																//	if (isNextMove($doska_array, $myselcell, $vragsaska,$vragdamka))
																			$online_game['OG_GAMER_MOVE'] = $vragcode;
																	return true;
																
																}
															}
															if ($DOWN_LEFT== 6)//если снизу слева пусто тогда можно ставить
																if ($toX == $fromX-1 & $toY ==$fromY+1) //и если мы щелкнули  именно туда  тогда ход  осуществим 
																{
															//	echo "сработал DOWN LEFT";
																	setMove($doska_array,$myselcell, $cell_num);
														//		if (isNextMove($doska_array, $myselcell, $vragsaska,$vragdamka)) 
																		$online_game['OG_GAMER_MOVE'] = $vragcode;
																	return true;
																}
														}break;
														case $mydamka:{
													//	echo "    играем белыми  Дамка да";
															//если дамка тогда дамка  может ходить как угодно главное по черным и главное не на тот  X и Y на котором стоит
															if ($toX != $fromX & $toY !=$fromY) //если  мы стоим не там куда хотим пойти
																if ($doska_array[$toX][$toY] == 6){ // если  туда  куда хотим пусто
																	//нужно  проверить  не срубили  ли мы по дороге  шашку или не перепрыгнули  через  более чем  1   чужую или 1 и более своих
																		//сначала определим в каком направлении мы прыгнули
																		$ii=0;
																		$jj=0;
																		
																		if ($toX < $fromX & $toY <$fromY) // обрабатываем  направление влево вверх UP_LEFT
																		{
																		$sasek=0;
																			$i=$toX;
																			$j=$toY;
																			while ($i<$fromX & $j<$fromY)
																			
																				{
																					
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //если  на пути  своя  шашка или дамка
																					{	
																					//	echo "   нас вышебла своя  шашка1";
																						return  false;
																					}
																					if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //если  чужая дамка или шашка
																					{
																						$sasek++; // тогда увеличиваем флаг
																						$ii=$i;
																						$jj=$j;
																					}
																					$i++;
																					$j++;
																				}
																			if ($sasek >1)	return  false; // если шашек  0  или  1тогда  ход  осуществим 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// пусто срубили
																				$myscore++;
																			}
																		}
																		if ($toX > $fromX & $toY <$fromY) // обрабатываем  направление ВПРАВО  вверх UP_RIGHT
																		{
																		$sasek=0;
																		
																			$i=$toX;
																			$j=$toY;
																			while ($i>$fromX & $j<$fromY)
																			
																				{
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //если  на пути  своя  шашка или дамка
																					{	
																					//	echo "   нас вышебла своя  шашка2";
																						return  false;
																					}
																					if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //если  чужая дамка или шашка
																					{
																						$sasek++; // тогда увеличиваем флаг
																						$ii=$i;
																						$jj=$j;
																					}
																					$i--;
																					$j++;
																				}
																				if ($sasek >1)	return  false; // если шашек  0  или  1тогда  ход  осуществим 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// пусто срубили
																				$myscore++;
																			}
																		}
																		if ($toX > $fromX & $toY >$fromY) // обрабатываем  направление вправо вниз  DOWN_RIGHT
																		{
																		$sasek=0;
																		
																		//echo " toX и  TY  находится в".$toX.$toY;
																		$i=$toX;
																			$j=$toY;
																			
																		while ($i>$fromX & $j>$fromY)
																			
																				{
																				//	echo "    сейчас toX toY ".$i.$j;
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //если  на пути  своя  шашка или дамка
																					{	
																						//echo "   нас вышебла своя  шашка3";
																					//	echo "       xy:    ".$i.$j ; 
																						return  false;
																					}
																					if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //если  чужая дамка или шашка
																					{
																			//		echo "\n найдена шашка врага в клетке ".$i.$j." и это  шашка ".$doska_array[$i][$j];
																						$sasek++; // тогда увеличиваем флаг
																						$ii=$i;
																						$jj=$j;
																					}
																					$i--;
																					$j--;
																				}
																			if ($sasek >1){
																		//	echo "   нас вышебла не своя шашка3     ".$sasek;
																			return  false; // если шашек  0  или  1тогда  ход  осуществим 
																			}	
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// пусто срубили
																				$myscore++;
																			}
																		}
																		if ($toX < $fromX & $toY >$fromY) // обрабатываем  направление ВЛЕВО ВНИЗ DOWN_LEFT
																		{
																		$sasek=0;
																		$i=$toX;
																		$j=$toY;
																		while ($i<$fromX & $j>$fromY)
																			{
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //если  на пути  своя  шашка или дамка
																					{	
																			//			echo "   нас вышебла своя  шашка4";
																						return  false;
																					}		if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //если  чужая дамка или шашка
																					{
																						$sasek++; // тогда увеличиваем флаг
																						$ii=$i;
																						$jj=$j;
																					}
																					$i++;
																					$j--;
																			}
																			if ($sasek >1)	return  false; // если шашек  0  или  1тогда  ход  осуществим 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// пусто срубили
																				$myscore++;
																			}
																		}
																		// сначала просмотрим
																		//*************************************************
																	setMove($doska_array,$myselcell, $cell_num);
																		if (isNextMove($doska_array, $myselcell, $vragsaska,$vragdamka)) 
																			$online_game['OG_GAMER_MOVE'] = $vragcode;
																return true;
																}
														}
														}
												}break;
														
												case 2:{		//черными играем
											//	echo "мы играем черными ";
														switch ($doska_array[$fromX][$fromY]){
															case $mysaska:{
																if ($UP_LEFT== 6) // если сверху слева пусто тогда можно ставить
																	if ($toX == $fromX-1 & $toY ==$fromY-1){ //и если мы щелкнули  именно туда  тогда ход  осуществим 
																		setMove($doska_array,$myselcell, $cell_num);
																		$online_game['OG_GAMER_MOVE'] = $vragcode;
																		return true;
																	}
														
																if ($UP_RIGHT==6) // если справа сверху пусто тогда можно ставить
																	if ($toX == $fromX+1 & $toY ==$fromY-1){
																	
																		setMove($doska_array,$myselcell, $cell_num);
																		$online_game['OG_GAMER_MOVE'] = $vragcode;
																		return true;					
																	}
															}break;
															case $mydamka:{
														//		echo "    играем черныими Дамка да";
																//если дамка тогда дамка  может ходить как угодно главное по черным и главное не на тот  X и Y на котором стоит
																if ($toX != $fromX & $toY !=$fromY) //если  мы стоим не там куда хотим пойти
																	if ($doska_array[$toX][$toY] == 6){ // если  туда  куда хотим пусто
																		//нужно  проверить  не срубили  ли мы по дороге  шашку или не перепрыгнули  через  более чем  1   чужую или 1 и более своих
																		//сначала определим в каком направлении мы прыгнули
																		$ii=0;
																		$jj=0;
																		if ($toX < $fromX & $toY <$fromY) // обрабатываем  направление влево вверх UP_LEFT
																		{
																		$sasek=0;
																				$i=$toX;
																				$j=$toY;
																			while ($i<$fromX & $j<$fromY)
																			
																				{
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //если  на пути  своя  шашка или дамка
																					{	
																		//				echo "   нас вышебла своя  шашка1";
																						return  false;
																					}if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //если  чужая дамка или шашка
																					{
																						$sasek++; // тогда увеличиваем флаг
																						$ii=$i;
																						$jj=$j;
																					}
																					$i++;
																				$j++;
																				}
																			if ($sasek >1)	return  false; // если шашек  0  или  1тогда  ход  осуществим 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// пусто срубили
																				$myscore++;
																			}
																		}
																		if ($toX > $fromX & $toY <$fromY) // обрабатываем  направление ВПРАВО  вверх UP_RIGHT
																		{
																		$sasek=0;
																				$i=$toX;
																				$j=$toY;
																			while ($i>$fromX & $j<$fromY)
																			{
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //если  на пути  своя  шашка или дамка
																					{	
																		//				echo "   нас вышебла своя  шашка2";
																						return  false;
																					}				if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //если  чужая дамка или шашка
																					{
																						$sasek++; // тогда увеличиваем флаг
																						$ii=$i;
																						$jj=$j;
																					}
																					$i--;
																				$j++;
																				}
																			if ($sasek >1)	return  false; // если шашек  0  или  1тогда  ход  осуществим 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// пусто срубили
																				$myscore++;
																			}
																		}
																		if ($toX > $fromX & $toY >$fromY) // обрабатываем  направление вправо вниз  DOWN_RIGHT
																		{
																		$sasek=0;
																			   $i=$toX;
																				$j=$toY;
																			while ($i>$fromX & $j>$fromY)
																			
																				{
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //если  на пути  своя  шашка или дамка
																					{	
																			//			echo "   нас вышебла своя  шашка3";
																						return  false;
																					}			if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //если  чужая дамка или шашка
																					{
																						$sasek++; // тогда увеличиваем флаг
																						$ii=$i;
																						$jj=$j;
																					}
																					$i--;
																				$j--;
																				}
																			if ($sasek >1)	return  false; // если шашек  0  или  1тогда  ход  осуществим 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// пусто срубили
																				$myscore++;
																			}
																		}
																		if ($toX < $fromX & $toY >$fromY) // обрабатываем  направление ВЛЕВО ВНИЗ DOWN_LEFT
																		{
																		$sasek=0;
																		   $i=$toX;
																				$j=$toY;
																			while ($i<$fromX & $j>$fromY)
																			
																				{
																					if ($doska_array[$i][$j]== $mysaska || $doska_array[$i][$j]== $mydamka) //если  на пути  своя  шашка или дамка
																					{	
																					//	echo "   нас вышебла своя  шашка4";
																						return  false;
																					}				if ($doska_array[$i][$j]== $vragsaska || $doska_array[$i][$j]== $vragdamka)	 //если  чужая дамка или шашка
																					{
																						$sasek++; // тогда увеличиваем флаг
																						$ii=$i;
																						$jj=$j;
																					}
																					$i++;
																				$j--;
																			}
																			if ($sasek >1)	return  false; // если шашек  0  или  1тогда  ход  осуществим 
																			if ($sasek == 1)
																			{
																				$doska_array[$ii][$jj]=6;// пусто срубили
																				$myscore++;
																			}
																		}
																		// сначала просмотрим
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
		}else // если ничего не выделено
		{
			    $xx = $cell_num[0];
				$yy = $cell_num[1];
				$shashka = $doska_array[$xx][$yy];
				switch ($shashka) {//и если в той на кторую щелкнули стоит  своя шашка тогда
				case $mysaska:
				case $mydamka:{$myselcell = ''.$cell_num.'';
							   return true;} //ход  допустим выделяем  нажатую  шашку
				case 5:        //если щелкнули на пустом месте уже
				case 6:{$myselcell='FF'; //выделяем ничего
					    return true; //ход  выполним
					   }
				default: return false;
				}
		}
		
		
		return false;
	}
	
	function saveChanges() // сохраним    что  меняли
	{
		global $dbServer;
		global $dbUser;
		global $dbPass;
		global $online_game;
		global $gamer1;
		global $gamer2;
		global $AuthorizeSession;
		global $doska;
		// нужно расклад  разбить снова по строчкам от A до H  и внести в базу
		global $A_H;
		for ($i=0,$y=0;$y<=7;$y++){
			for ($x=0;$x<=7;$x++){
				$A_H[$i]=$doska[$x][$y];
				$i++;
			}
		}
		
		$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
				
			/*Выбираем базу данных */
	if (mysql_select_db("shashki")) {
		
	
	
	//echo " OG1Par     ".$online_game['OG_PARITET1'];
		//echo " OG2Par     ".$online_game['OG_PARITET2'];
		
		if ($AuthorizeSession['GAMER_CODE']==$online_game['OG_GAMER1'] ) //если первый игрок 
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
	mysql_close( $link); //закрываем  линк с базой
	}
	
	function isNextMove(&$doska_array, &$myselcell, $vragsaska,$vragdamka){
		$fromX = $myselcell[0];
		$fromY = $myselcell[1];
						if ($fromX>0 & $fromY>0)
							$UP_LEFT = $doska_array[$fromX-1][$fromY-1]; //левая верхняя клетка  от той которая выделена.
						else
							$UP_LEFT = 5;// 5 все равно  не используется (5 это  белая пустая клетка) 
						if ($fromX>=2 & $fromY>=2)
							$UL_UPLEFT = $doska_array[$fromX-2][$fromY-2];////левая верхняя клетка  от Левой верхней
						else
							$UL_UPLEFT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
							
						if ($fromX>0 & $fromY<7)
							$DOWN_LEFT=$doska_array[$fromX-1][$fromY+1]; //нижняя левая  клетка от той которая выделена
						else
							$DOWN_LEFT = 5; // 5 все равно  не используется (5 это  белая пустая клетка)  
						
						if ($fromX>=2 & $fromY<=5)
							$DL_DOWNLEFT=$doska_array[$fromX-2][$fromY+2]; //нижняя левая  клетка от нижней левой
						else
							$DL_DOWNLEFT= 5; // 5 все равно  не используется (5 это  белая пустая клетка)  
						
     					if ($fromX<7 & $fromY>=1)
							$UP_RIGHT = $doska_array[$fromX+1][$fromY-1]; //правая верхняя  клетка от той которая выделена
						else
							$UP_RIGHT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
						
						if ($fromX<=5 & $fromY>=2)
							$UR_UPRIGHT = $doska_array[$fromX+2][$fromY-2]; //правая верхняя  клетка от  правой верхней 
						else
							$UR_UPRIGHT =5 ;// 5 все равно  не используется (5 это  белая пустая клетка)  
						
						if ($fromX<7 & $fromY<7)
							$DOWN_RIGHT =$doska_array[$fromX+1][$fromY+1]; //нижняя правая клетка от той которая выделена
						else
							$DOWN_RIGHT = 5;// 5 все равно  не используется (5 это  белая пустая клетка)  
						if ($fromX<=5 & $fromY<=5)
							$DR_DOWNRIGHT =$doska_array[$fromX+2][$fromY+2]; //нижняя правая клетка от нижней правой
						else
							$DR_DOWNRIGHT = 5 ;// 5 все равно  не используется (5 это  белая пустая клетка) 
						
						if ($UP_LEFT==$vragsaska || $UP_LEFT==$vragdamka) //если справавверху от  клетки которая выделена стоит чужая дамка или шашка
							if ($UL_UPLEFT == 6) // и если от  нее  уже стоит  пустая клетка по курсу тогда
									return false;
								
						
							if ($UP_RIGHT==$vragsaska || $UP_RIGHT==$vragdamka) //если справавверху от  клетки которая выделена стоит чужая дамка или шашка
								if ($UR_UPRIGHT == 6) // и если от  нее  уже стоит  пустая клетка по курсу тогда
										return false;
										
								if ($DOWN_RIGHT==$vragsaska || $DOWN_RIGHT==$vragdamka) //если справавверху от  клетки которая выделена стоит чужая дамка или шашка
									if ($DR_DOWNRIGHT == 6) // и если от  нее  уже стоит  пустая клетка по курсу тогда
											return false;
							
									if ($DOWN_LEFT==$vragsaska || $DOWN_LEFT==$vragdamka) //если справавверху от  клетки которая выделена стоит чужая дамка или шашка
										if ($DL_DOWNLEFT == 6) // и если от  нее  уже стоит  пустая клетка по курсу тогда
												return false;
		return true;
											
	}
	
	function produceMove(&$doska_array, $cell_num){
	//ВНИМАНИЕ ЭТА ФУНКЦИЯ  ТОЛЬКО считает очки  ....    и переставляет  шашку.  шашки удаляет  функция isValidMove
		global $online_game;
		global $gamer1;
		global $gamer2;
		global $AuthorizeSession;
		//Ху из ху??
		//сначала определим ху из  ху
		if ($AuthorizeSession['GAMER_CODE'] == $gamer1['GAMER_CODE']){
			$itsme = $gamer1;
			$mycode = $online_game['OG_GAMER1'];
			$mysid = $online_game['OG_SID1'];
			$myscore = $online_game['OG_SCORE1'];
			$myselcell = &$online_game['OG_SEL_CELL1'];
			
			if ($online_game['OG_GAMER1_FIG']==1){
				$myfig = 1; //белые
				$mysaska = 1; //белая
				$mydamka = 3;//белая
				$vragsaska = 2;//вражеская шашка
				$vragdamka = 4;//вражеская дамка
			}
			else {
				$myfig = 2; //черные
				$mysaska = 2; //черная
				$mydamka = 4;//черная
				$vragsaska = 1;//вражеская шашка
				$vragdamka = 3;//вражеская дамка
			}
			
		}
		else{
			$itsme = $gamer2;
			$mycode = $online_game['OG_GAMER2'];
			$mysid = $online_game['OG_SID2'];
			$myscore = $online_game['OG_SCORE2'];
			$myselcell = &$online_game['OG_SEL_CELL2'];
			
			if ($online_game['OG_GAMER1_FIG']==1){
				$myfig = 2; //черная
				$mysaska = 2; //черная
				$mydamka = 4;//черная
				$vragsaska = 1;//вражеская шашка
				$vragdamka = 3;//вражеская дамка
				
			}
			else {
				$myfig = 1; //белая
				$mysaska = 1; //белая
				$mydamka = 3;//белая
				$vragsaska = 2;//вражеская шашка
				$vragdamka = 4;//вражеская дамка
			}
		}
		//Алгоритм 
		//1. туда  где стояла  шашка ставим пустую   черную
		//2. туда куда шашку поставим прописываем выделенную
		if ($cell_num === $myselcell){
			//echo "   ничего не меняли ";
			return true;// ничего не меняем
		}
		
		
		
		//Теперь нужно посчитать  очки  и убрать срубленные шашки....
		//....
		//...
		
		return true;
	}

if (isset($_GET['create'])){ // если  пришел  параметр  create -  создать  игру  тогда создаем игру
//1. коннектимся к базе
//2. добавляем в  ONLINE_GAME строчку  игры
//3.  в строки A...H  вбиваем начальный расклад фигур (белые сверху)
//4. выводим на страницу зааголовок Ожидание второго игрока
//5.   в табличку информация по игре выносим список юзеров
//6.  кнопки сдаться и ничья не показываем,  текущий ход тоже не показываем
//7. доску  выводим без ссылок то есть нажатия на клетках   ничего  не делают
//8 .  страница обнавляется через  каждые 20 секунд

   
  
	
   $gamer1figurs = 1;
   if (isset ($_POST['sel_figuri'])) 
   	if ($_POST['sel_figuri']==='black_fig'){
			
			$gamer1figurs = 2;
			}
	
	// кто ходит первым?
	$fstmove = 0; // пока,  тот кто придет вторым
	if ($gamer1figurs==1)  // если  у первого игрока белые фигуры тогда  первым ходит он
		$fstmove = $AuthorizeSession['GAMER_CODE'];
	
       /*Соединяемся  с сервером*/
				$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
				
			/*Выбираем базу данных */
				if (mysql_select_db("shashki")) {
				$nowtime=strtotime("now"); 
					$SQLrequest = "INSERT INTO ONLINE_GAME(OG_CODE,OG_GAMER1,OG_GAMER2,OG_GAMER1_FIG,OG_SID1,OG_SID2,OG_SCORE1,OG_SCORE2,OG_WAIT,
													  OG_GAMER_MOVE,OG_LST_MOVE_TIME,OG_TIMEOUT_MIN,OG_SEL_CELL1,OG_SEL_CELL2,OG_A_H)
													  VALUES (NULL,".$AuthorizeSession['GAMER_CODE'].",0,".$gamer1figurs.",'".session_id()."','0',0,0,true,".$fstmove.",'".$nowtime."',".$_POST['timeout'].",'FF','FF','".$A_H."')";
				$result = mysql_query( $SQLrequest);
					$OG_CODE = mysql_insert_id(); // после вставки мы знаем  код  игры
				
					
				if ($result ) {
					mysql_close( $link); //закрываем  линк с базой
					//если всё ровно  тогда можно перекидывать  юзера на скрипт gameprocess.php?OG=<Номер  только что созданой игры>
					echo ' 
						<meta http-equiv="Refresh" content="0; URL=gameprocess.php?OG='.$OG_CODE.'">
						';
					exit();
				}
					
				else  {
					printf ("<br> Could not create new game: %s\n", mysql_error ());	
					mysql_close( $link); //закрываем  линк с базой
					exit();
					}
				
				}
				
}
//Если пришел  параметр JOIN  значит   нужно  прикрепить юзера на игру
if (isset($_GET['join'])){ 
	//нельзя прикрепить одного  и того же  юзера к одной игре  дважды  то есть  игроки должны быть  разными
	// прикрепляем только на место  второго  игрока
	//Алгоритм
	//1Ищем запись  игры
	//2. если не нашли тогда выходим на game.php
	//3. если нашли тогда   проверяем   не  является ли   он  уже тем кто подключился ранее
	//4.  если все ровно  тогда
	//5.  добавляем OG_GAMER1  OG_SID2
	//6. если    у  второго игрока  белые шашки тогда   текущий ход   OG_GAMER_MOVE становится      ходом дляэтого  игрока
	//7. сохраняем параметры
	//8 перекидываем на  gameprocess.php?OG= текущая игра
	  
	if (!isset ($_GET['OG']))
	{
		echo	' 
					<meta http-equiv="Refresh" content="0; URL=game.php">
				';
		exit();
	}
	/*Соединяемся  с сервером*/
	$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
	/*Выбираем базу данных */
	if (mysql_select_db("shashki")) {
	  $SQLrequest = "SELECT *  FROM ONLINE_GAME WHERE OG_CODE=".$_GET['OG']." ";
	  
		$result = mysql_query( $SQLrequest);
		if  ($result){
			$online_game = mysql_fetch_array($result, MYSQL_ASSOC);
			if ($online_game['OG_GAMER1']==$AuthorizeSession['GAMER_CODE'] || $online_game['OG_GAMER1']== session_id()){
				//это тот же самый юзверь поэтому не даем ему приконнектится
				echo ' 
						<meta http-equiv="Refresh" content="0; URL=game.php">
						';
					exit();
			}
			else{	
				if ($online_game['OG_GAMER_MOVE']==$online_game['OG_GAMER1'] ) //если первый  ход  еще  не  определен  тогда
					$SQLrequest = "UPDATE ONLINE_GAME SET OG_GAMER2=".$AuthorizeSession['GAMER_CODE'].",OG_SID2='".session_id()."',OG_WAIT=0 WHERE OG_CODE=".$_GET['OG']." ";
				else
					$SQLrequest = "UPDATE ONLINE_GAME SET OG_GAMER2=".$AuthorizeSession['GAMER_CODE'].",OG_SID2='".session_id()."',OG_WAIT=0,OG_GAMER_MOVE=".$AuthorizeSession['GAMER_CODE']."   WHERE OG_CODE=".$_GET['OG']." ";
			
				$result = mysql_query( $SQLrequest);
				if  ($result){
					mysql_close( $link); //закрываем  линк с базой
					//если всё ровно  тогда можно перекидывать  юзера на скрипт gameprocess.php?OG=<Номер  только что созданой игры>
					echo ' 
						<meta http-equiv="Refresh" content="0; URL=gameprocess.php?OG='.$_GET['OG'].'">
						';
					exit();
			
				}else {
					printf ("<br> Could not join to game: %s\n", mysql_error ());	
					mysql_close( $link); //закрываем  линк с базой
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
/* Теперь обрабатываем  другие  параметры                  */
/*************************************************/

//Первым делом нужно проверить  какие  параметры пришли
//если ?OG=XX&paritet=true/false&lose=true/false
//если пришел  paritet -  ничья тогда нужно вписать в  записи игры paritet1 = true  если  это gamer1  и paritet2 если это gamer2
//если пришел lose - сдаюсь. тогда нужно вписать lose1  ели это gamer1   и lose2 если это gamer2
// нужно  проверить  paritet  и lose  если  они  true  у оппонента тогда  юзера  выдать сообщение  Вам предложили ничью  согласны да нет?  или вывести  противник сдался  записать   очки  и удалить  игру из  списка  игр
// если  в ответ на паритет  придет  ответ paritet_no = true параметр тогда снять  флаг true  с поля паритет  оппоне нта и  игра продолжится
// если  ответить да тогда отправить paritet=true 
// если у обоих    стоит  паритет   выставить  себе ничью и отвалить на скрипт game.php 
//Нужно  1. Определить  кто коннектится  Если юзер   который запросил    эту игру не будет  в Gamer1  или Gamer2  в запрошеной игре  тогда посылаем его  на index
//2. если  юзверь  все таик есть  тогда нужно проверить   совпадает  ли его SID  если сид  не совпадает  тогда на индекс
//3. ну   щас точно    он самый
//4. в общем  если  коннектится юзер 1  и  второго   юзера нет  тогда нужно
// кратко: в область рядом с логотипом вывести  надпись "Ожидание второго  игрока"
//Желтую  полоску не показываем
//Список юзеров показываем  в количестве 1 человек
// кнопки текущий ход не показываем и  сдаться и ничья тоже не показываем
//доску выводим 

if (isset ($_GET['OG'])) { 
	
//с таблицы считаем всё в переменные
/*Соединяемся  с сервером*/
				$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
				
			/*Выбираем базу данных */
				if (mysql_select_db("shashki")) {
					$SQLrequest = "SELECT * FROM ONLINE_GAME WHERE OG_CODE=".$_GET['OG']." ";
					$result = mysql_query( $SQLrequest);
					if (mysql_num_rows($result) == 0) {
				//	echo "  запроса запрос был фальшивым22";	
					echo	' 
								<meta http-equiv="Refresh" content="0; URL=game.php">
								';
							exit();
					}
				//echo "  После запроса";
					if ($result){
						$online_game = mysql_fetch_array($result, MYSQL_ASSOC);
						//получаем инфу о   гемере 1
						
						$gamer1SQL = "SELECT * FROM GAMER WHERE GAMER_CODE=".$online_game['OG_GAMER1']." ";
						$result = mysql_query($gamer1SQL);
						if ($result){
							$gamer1 = mysql_fetch_array($result, MYSQL_ASSOC);
						}else
							$gamer1=0;
						//получаем инфу о   гемере 2
						
						$gamer2SQL = "SELECT * FROM GAMER WHERE GAMER_CODE=".$online_game['OG_GAMER2']." ";
						$result = mysql_query($gamer2SQL);
						if ($result){
							$gamer2 = mysql_fetch_array($result, MYSQL_ASSOC);
						}
						else
						{
							//гамера 2  еще нет
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
							// можно  удалять  игру
							$link2 = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
				
								/*Выбираем базу данных */
							if (mysql_select_db("shashki")) {
							$sqldelgame = "DELETE FROM ONLINE_GAME WHERE OG_CODE=".$_GET['OG']." ";
								$result = mysql_query($sqldelgame);
								}
						
							echo	' 
								<meta http-equiv="Refresh" content="0; URL=game.php">
								';
							exit();
							//echo 'Это  тайм аут';
						//	exit();
						}
						//	echo "   таймаута еще нет ";
					}
					else
						{
					//	echo "  запроса запрос был фальшивым";	
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

	
	
//теперь  нужно проверить  авторизован ли  вообще  юзер    быть подключенным в  эту  игру
// нужно   найти его CODE   занесенный  в SESSION[Auth]  в полях  Gamer1_code и gamer2_code
// если не найден выкидываем на индекс
// если найден  тогда сравниваем соответствующий SID  с тем что  есть  сейчас у  гамера  если  не равны тогда 
//Если это хозяин игры был... Удаляем игру из  списка и  выкидываем   юзера на  гаме.php 
$isGamer1=false; //это   геймер 1? нет пока предположим что  2
if ($AuthorizeSession['GAMER_CODE'] !== $gamer1['GAMER_CODE'])
{
	if ($AuthorizeSession['GAMER_CODE'] !== $gamer2['GAMER_CODE'])
	{
		//Код не  равен  юзеру 2  выкидываем с игры
		echo	' 
				<meta http-equiv="Refresh" content="0; URL=game.php">
				';
				exit();
	}
	else{
		//проверяем SID2
		if ($online_game['OG_SID2']!== session_id())
		{
			//SID2 не  равен  ни юзеру 2  выкидываем с игры
			echo	' 
				<meta http-equiv="Refresh" content="0; URL=game.php">
				';
				exit();
		}
	}
}else {
		//проверяем SID1
		if ($online_game['OG_SID1']!== session_id())
		{
		
			//SID1 не  равен  ни юзеру 1 выкидываем с игры
			echo' 
					<meta http-equiv="Refresh" content="0; URL=game.php">
				';
			exit();
		}else
			$isGamer1 = true; //да это геймер  1
}

echo '
		<html>

		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
		<meta http-equiv="Content-Language" content="ru">';
		

	echo '	
		<title>Игра</title>
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
		echo 'Ожидание второго  игрока.';
	else
		if ($online_game['OG_GAMER_MOVE'] == $gamer1['GAMER_CODE'])
			echo 'Ход  игрока '.$gamer1['GAMER_LOGIN'];
		else
			if ($online_game['OG_GAMER_MOVE'] == $gamer2['GAMER_CODE'])
				echo 'Ход  игрока '.$gamer2['GAMER_LOGIN'];
		
		
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
		
		
//теперь 			
//Если пришел  параметр CELL  значит   щелкнули  по    доске с шашками
// поэтому нужно проверить  
//теперь  нужно    извлечь  доску  в  дмуерный массив 8х8  по символьно
//...
//...
$A_H = $online_game['OG_A_H'];
fillDoska ($doska); //заполним ДОСКУ ТЕКУЩИМ РАСКЛАДОМ

	if (isset($_GET['CELL'])){
	
		//   если  щелкнул  чувак  тот  не чей сейчас ход,   тогда  выдаем ему сообщение и ничего не меняем
		if ($online_game['OG_GAMER_MOVE'] !== $AuthorizeSession['GAMER_CODE'] ){
			if (!$online_game['OG_WAIT']){
				echo '<tr>
				<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
				Сейчас не ваш ход</td>
				<td width="35%" style="font-family: arial,sans-serif">
				&nbsp;</td>
				</tr>';
			}else
			{
				echo '<tr>
					<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
					Вы не можете  ходить  пока нет  второго игрока</td>
					<td width="35%" style="font-family: arial,sans-serif">
					&nbsp;</td></tr>';	
			}
		}else {
		 
			$thismove=strtotime("now"); 
			$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
			//	echo " номы тут ";
			/*Выбираем базу данных */
				if (mysql_select_db("shashki")) {
					$SQLrequest = "UPDATE ONLINE_GAME SET OG_LST_MOVE_TIME='".$thismove."' WHERE OG_CODE=".$_GET['OG']." "; 
					$result = mysql_query( $SQLrequest);
					if ($result){
				//	$dt=getdate($thismove);
				//		echo "   обновили timestamp в ".$dt['minutes']." минут";
					}
				//	else
			//		echo  "ошибка обновления штампа времени ";
				}					
			//если  ход  все таки его нужно проверить   допустим ли он
			$cll = ''.$_GET['CELL'].'';
		///	echo "CLL: ".$cll;
			if ($online_game['OG_WAIT'] || !isValidMove($doska, $cll)){
				//если не допустим тогда ничего не меняем, а выдаем
				if ($online_game['OG_WAIT']){
				echo '<tr>
					<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
					Вы не можете  ходить  пока нет  второго игрока</td>
					<td width="35%" style="font-family: arial,sans-serif">
					&nbsp;</td></tr>';
				}else
					echo '<tr>
					<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
					Недопустимый ход</td>
					<td width="35%" style="font-family: arial,sans-serif">
					&nbsp;</td></tr>';
			}else
			{
				produceMove($doska, $cll); //сделать ход! (Изменяет доску и изменяет  выделенную  шашку если что)		 
			}
			
			
			
					
		}
	}
	
	//вот  тут  проверим ничью   от соперника
	//**********************************
	$itsme =  0;
	$myparitet = 0;
	$vragparitet =0;
	if ($AuthorizeSession['GAMER_CODE'] == $online_game['OG_GAMER1']){
	//echo " я  юзер 1 и все проинициализировалось";
					$itsme =  $online_game['OG_GAMER1'];
					$myparitet =  &$online_game['OG_PARITET1'];
					$vragparitet =&$online_game['OG_PARITET2'];
				}else {
	//	echo " я  юзер 2 и все проинициализировалось";
					$itsme =  $online_game['OG_GAMER2'];
					$myparitet =   &$online_game['OG_PARITET2'];
					$vragparitet = &$online_game['OG_PARITET1'];
					
				}
		$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
		
			/*Выбираем базу данных */
	mysql_select_db("shashki");
	//// а тут  уже проверим  свои  параметры
		   if (isset($_GET['paritet'])){ //если   нами запрошена ничья
			//	echo  "     параметр  паритет устновлен  ";
		
				switch ($_GET['paritet']){
					case 2:{ //мы запросили  ничью
					if ($myparitet !== 2) {
								$myparitet = 2;
								
							echo '<tr>
								<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
								Вашему сопернику была предложена ничья. Если  он  согласится  игра будет завершена.</td>
								<td width="35%" style="font-family: arial,sans-serif">
								&nbsp;</td></tr>';
					}
							}break;
					case 3:{// мы ответили ДА на ничью
								//нужно записать себе 3
								$myparitet = 3;
								echo '<tr>
								<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
								Вы согласились на ничью</td>
								<td width="35%" style="font-family: arial,sans-serif">
								&nbsp;</td></tr>';
								$megameta =  '<meta http-equiv="Refresh" content="1; URL=game.php">'; //уходим сразу
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
					case 4:{// мы ответили нет на ничью	
								//ставим свой паритет в 4
								$vragparitet = 1;
								$myparitet = 1;
								$_GET['paritet']=1;
								unset($_GET['paritet']);
								echo '<tr>
								<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
								Предложение  Вашего  соперника на ничью,  было  отклонено</td>
								<td width="35%" style="font-family: arial,sans-serif">
								&nbsp;</td></tr>';
							
							//$megameta = '<meta http-equiv="Refresh" content="0; URL=gameprocess.php?OG='.$_GET['OG'].'">';
							}break;
				}
				
			}else
			{
		
		
				switch ($vragparitet)	{
				case 1:{}break;// оппонент  не отвечал
				case 2:{
					$myparitet =1;// мы еще не отвечали;
					echo '<tr>
									<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
									Соперник предлагает  Вам ничью. Вы cогласны ? <a href="gameprocess.php?OG='.$_GET['OG'].'&paritet=3">Да</a> / <a href="gameprocess.php?OG='.$_GET['OG'].'&paritet=4">Нет</a></td>
									<td width="35%" style="font-family: arial,sans-serif">
									&nbsp;</td></tr>';
							//		$megameta = '<meta http-equiv="Refresh" content="0; URL=gameprocess.php?OG='.$_GET['OG'].'">';

				}break;// оппонент  запросил ничью
				case 3:{
				/*	echo '
									<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
									Соперник согласился на ничью</td>
									<td width="35%" style="font-family: arial,sans-serif">
									&nbsp;</td>';*/
								$vragparitet = 1;
								$myparitet = 1;
								 //нужно  записать проигрышь игроку 2
				 //нужно   удалить игру
				 // нужно  перекинуть юзеров на  страницу информации
				 switch ($itsme){
				 case $online_game['OG_GAMER1']:
				 {
				// echo "  вошли  в кейс для гемера 1";
				 $sqlgamer1 = "SELECT * FROM GAMER WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
				 $result = mysql_query( $sqlgamer1);
				 $gamer1 = mysql_fetch_array($result, MYSQL_ASSOC);
				 $gamer1['GAMER_PARITET']++;
				 $sqlgamer1= "UPDATE GAMER SET GAMER_PARITET=".$gamer1['GAMER_PARITET']." WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
				 $result = mysql_query($sqlgamer1);
				 
				}break;
				 case $online_game['OG_GAMER2']:
				 {
			//	 echo "  вошли  в кейс для гемера 1";
				 $sqlgamer2 = "SELECT * FROM GAMER WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
				 $result = mysql_query( $sqlgamer2);
				 $gamer2 = mysql_fetch_array($result, MYSQL_ASSOC);
				 $gamer2['GAMER_PARITET']++;
				 $sqlgamer2= "UPDATE GAMER SET GAMER_PARITET=".$gamer2['GAMER_PARITET']." WHERE GAMER_CODE=".$AuthorizeSession['GAMER_CODE']." ";
				 $result = mysql_query($sqlgamer2);
			
				}break;
				}
		//удалим игру
		
				 $sqldelgame = " DELETE FROM ONLINE_GAME WHERE OG_CODE=".$_GET['OG']." ";
				 $result = mysql_query($sqldelgame);
		
				}break;// оппонент  согласен на ничью
				case 4:{
					echo '<tr>
								<td width="64%" style="font-family: arial,sans-serif" bgcolor="#FFFF99">
								Соперник отклонил Ваше предложение на ничью </td>
								<td width="35%" style="font-family: arial,sans-serif">
								&nbsp;</td></tr>';
								$vragparitet = 1;
								$myparitet = 1;
								$megameta = '<meta http-equiv="Refresh" content="15; URL=gameprocess.php?OG='.$_GET['OG'].'">';
								
			}break;// оппонент  отклонил ничью
				}
			
			}
			
			
			
//Далее уже  ход  сделан  
//теперь  нужно  вписать  в  базу   расклад  доски  и   передать ход  другому  юзеру
//а так же нужно вписать  в базу все  переменные которые были  считаны юзером из  базы    изменяя  только  свои  переменные.  тоесть  только свой GamerCode и SID и свои очки
if ($AuthorizeSession['GAMER_CODE'] == $online_game['OG_GAMER1'])
	$myselcell = &$online_game['OG_SEL_CELL1'];
else
	if ($AuthorizeSession['GAMER_CODE'] == $online_game['OG_GAMER2'])
		$myselcell = &$online_game['OG_SEL_CELL2'];
	
	
	echo'	<tr>
			<td width="40%" style="font-family: arial,sans-serif" height="40">
			<div align="center">';
			//1 рисуем доску
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
						echo '<td><img border="0" src="imgs/'.$doska[$x][$y].'.gif"></td>'; //если белая  клетка тогда ссылку не нужно
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
					<p align="center"><b>Информация по игре</b></td>
				</tr>
				<tr>
					<td width="119" bgcolor="#A4B7DB" align="center"><b>
					<font size="2">игроки</font></b></td>
					<td width="84" bgcolor="#A4B7DB" align="center"><b>
					<font size="2">фигуры</font></b></td>
					<td bgcolor="#A4B7DB" align="center"><b><font size="2">взятий</font></b></td>
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
				//Игрок №1	
				echo $gamer1['GAMER_LOGIN'];
				echo	'</font></span></b></td>
					<td width="84" bgcolor="'.$clr.'"><b><font size="4">';
				//Игрок №1 Фигуры
				if ($online_game['OG_GAMER1_FIG']==1)
					echo 'Белые';
				else
					echo 'Черные';
				
				echo '
					</font></b></td>
					<td bgcolor="'.$clr.'"><b><font size="4">';
				//Взятия игрока 1
				echo $online_game['OG_SCORE1'];
				echo '</font></b></td>
				</tr>';
				//Если есть  игрок 2
				if ($gamer2){
					if ($AuthorizeSession['GAMER_CODE'] == $online_game['OG_GAMER2'])
					$clr = $youcolor;
				else
					$clr =$nonyoucolor;
					
					echo '<tr>
						<td width="119" bgcolor="'.$clr.'"><b><span lang="en-us">
						<font size="4">';
					//логин игрока №2
					echo $gamer2['GAMER_LOGIN'];	
					echo	'</font></span></b></td>
						<td width="84" bgcolor="'.$clr.'"><b><font size="4">';
					//Игрок №2 Фигуры
					if ($online_game['OG_GAMER1_FIG']==1)
						echo 'Черные';
					else
						echo 'Белые';
					echo '</font></b></td>
						<td bgcolor="'.$clr.'"><b><font size="4">';
					//Взятия игрока 2
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
						<p align="center"><font size="5" color="#294069">текущий ход</font></td>
						<td>
						<p align="center">
						<img border="0" src="imgs/';
						if ($online_game['OG_GAMER_MOVE'] == $online_game['OG_GAMER1']){ //если  текущий ход геймера 1
							if ($online_game['OG_GAMER1_FIG']==1) // и если   шашки геймера 1 белые тогда
								echo '1.gif';
							else
								echo '2.gif';
						}else 									  //если  текуший игрок геймер2
							if ($online_game['OG_GAMER1_FIG']==2) // и если   шашки геймера 2 белые тогда
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
						<a href="gameprocess.php?OG='.$_GET['OG'].'&lose=1">сдаться</a></b></td>
						<td width="51%" align="center" bgcolor="#EAEAFF"><b>
						<a href="gameprocess.php?OG='.$_GET['OG'].'&paritet=2">ничья</a></b></td>
					</tr>
				</table>
				</td>
			</tr>';
		}
		
echo '	</table>';
//echo "   мы дошли  до  сохранения";
//echo " OG1Par     ".$online_game['OG_PARITET1'];
	//	echo " OG2Par     ".$online_game['OG_PARITET2'];
		
saveChanges(); // сохраним    что  меняли

	
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