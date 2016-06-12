
<?php
require_once 'dbSettings.php';
//проверяем все ли параметры пришли
if ($_POST['regsubmit'] == 'Зарегистрировать') {
	//инициируем регистрацию
	//если пароль и подтверждение не совпадают тогда выкидываем снова на страницу регистрации
	if ($_POST['passw'] !== $_POST['confirm']) {
		echo ' 
			<meta http-equiv="Refresh" content="0; URL=registration.html">
		';
		exit();
	}
	//нужно прошариться  по базе  и найти  юзеров с таким  же именем.  елси таковые будут тогда нужно  выкинуть на  с страницу регистрации снова
	/*Соединяемся  с сервером*/
	$link = mysql_connect($dbServer,$dbUser,$dbPass)	or exit("Connection is fall");
    /*Выбираем базу данных */
	if (mysql_select_db("shashki")) {
		$SQLrequest = "SELECT GAMER_CODE from GAMER where GAMER_LOGIN='".mysql_escape_string($_POST['uname'])."'";
		
		if ($result = mysql_query( $SQLrequest)) { // вытаскиваем из  базы  все записи  которые имею  такое  же имя
			$logincount = mysql_num_rows($result);
			if (!$logincount) { //такого  логина нет  поэтому добавляем в базу нового  юзера
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
			else { //такой  логин  уже существует  мы должны сообщить  об этом узеру
			echo ' 
					<meta http-equiv="Refresh" content="0; URL=registration.html">
					 ';
				exit();
			}
		}else //ошибка   при выполнении запроса  Select
			printf ("<br> Could not select from GAMER: %s\n", mysql_error ());	
	}else //Если не удалось выбрать базу данных
		printf ("<br> Could not select shashki database: %s\n", mysql_error ());	
	mysql_close( $link); //закрываем  линк с базой
	
}
?>
