<?php
// talvez mude um monte de coisa quando eu colocar no proprio servidor
// $host = 'localhost';
// $dbname = 'home_server';
// $usuario = 'root';
// // provavelmente vou criar uma senha depois
// $senha = ''; 

// try {
//     $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $usuario, $senha);
//     $pdo->setAtrribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// }
// catch (PDOException){
//     die('deu erro quando fui conectar no banco manito: ' . $e->getMessage());
// }

// config da planilha de gastos
define('planilha_resumo', 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQ2Q_81Gt0ZmOBGmeivr0YLLf8yCAIJqv9DZ7VvL3h2i0Zgjm1dHRf9iQCLexT66qoX-fDnUz6bseAP/pub?gid=3053895&single=true&output=csv');
?>