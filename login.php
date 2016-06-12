<?php
//ini_set("session.use_trans_sid",true); 
session_start(); //стартуем сессию
require_once 'dbSettings.php';

// 1.нужно проверить   пришедшее имя юзера и пароль
// 2. если они валидны тогда  указываем isAutharized в сессии равным true и открываем страницу с игрой а также вписываем GAMER_CODE в SESSION gamer_code
	//(если на странице с игрой is Authorized не установлен просто перекидываем на index.html)
// 3. если нет тогда выдаем страницу с предупреждением и возвращаем снова на ввод на логина и пароля
//если  мы нажали войти
if ($_POST['enter'] == 'Войти') {
	if (!$_POST['uname']  || !$_POST['passw']){ //если не введено имя или пароль тогда ничего не делаем а снова скидываем на страницу логина
		echo ' 
			<meta http-equiv="Refresh" content="0; URL=index.html">
		';
		exit();
	}else //если всеё введено  проверям есть ли такой юзверь в базе
	{
		//1. коннектимся к  базе
		//2.  извлекаем из  Gamer   юзера с пришедшеми параметрами
		//3. если что то извлекли значит SESIONAutorized = true и Session gamercode =gamer.gamer_code
		//4. если ничего не извлекли сообщаем о неверном праоле или логине и опять кидавем на страницу логина
		//1.../*Соединяемся  с сервером*/
		$link = mysql_connect($dbServer,$dbUser,$dbPass) or exit("Connection is fall");
		/*Выбираем базу данных */
		if (mysql_select_db("shashki")) {
		//2...
			$SQLrequest = "SELECT * from GAMER where GAMER_LOGIN='".mysql_escape_string($_POST['uname'])."' and GAMER_PASSWORD='".mysql_escape_string($_POST['passw'])."'";
		
			if ($result = mysql_query( $SQLrequest)) { // вытаскиваем из  базы  запись  юзера
				$countusers = mysql_num_rows($result);
				if (!$countusers) 
				{ //такого  логина нет  поэтому  сообщаем юзеру
					$_SESSION['Auth']['isAutharize']=false; //не авторизован
				require_once "login_result.php";				
				/*echo ' 
						<meta http-equiv="Refresh" content="0; URL=login_result.php">
						';
					exit();*/
				}
				else 
				{
					$_SESSION['Auth']['isAutharize']=true; //авторизован
					$_SESSION['Auth']['GAMER_LOGIN']=mysql_result($result,0,'GAMER_LOGIN'); //имя пользователя
					$_SESSION['Auth']['GAMER_CODE']=mysql_result($result,0,'GAMER_CODE'); //код  пользователя
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