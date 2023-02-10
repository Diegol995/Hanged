<?php
//Se establece la conexión a la base de datos
require_once 'config.php';
function conectar(){
    $pdo = "mysql:host=".host.";dbname=".db;
    try {
        $conn = new PDO($pdo, user, pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        return $conn;
        // echo "Conexion exitosa";
    }catch (PDOException $e)
    {
        echo "Error en la conexión ".$e->getMessage();
    }
}
//action controla la acción del usuario
//por defecto es 0 ya que ésta signifca que está en el menú de selección de dificultad
session_start();
if (isset($_GET['action']))
    $action = $_GET['action'];
else if(isset($_POST['action']))
    $action = $_POST['action'];
else
    $action = 0;

switch ($action)
{
    case 0:
        $_SESSION['name'] = false;
        $_SESSION['actualPoints'] = 0;
        $_SESSION['alert'] = "display-none";
        //Se consulta y se guarda el record de puntos
        $conn = conectar();
        $sql = "SELECT MAX(puntos) as record FROM records";
        $result = $conn->prepare($sql);
        $result->execute();
        $_SESSION['record'] = $result->fetch(PDO::FETCH_ASSOC)['record'];
        if ($_SESSION['record'] == null)
        {
            $_SESSION['record'] = 0;
        }

        $sql = "SELECT puntos,jugador FROM records order by puntos desc limit 5";
        $result = $conn->prepare($sql);
        $result->execute();
        $_SESSION['topFive'] = $result->fetchAll(PDO::FETCH_ASSOC);
        require 'menu.php';
        break;

    case 1:
        if ($_SESSION['name'] == false){
            if (isset($_POST['playerName']) && $_POST['playerName'] != "")
            {
                $name = $_POST['playerName'];
                $conn = conectar();
                $sql = "SELECT jugador FROM records WHERE jugador = '$name'";
                $result = $conn->prepare($sql);
                $result->execute();
                $matchName = $result->fetch(PDO::FETCH_ASSOC);
                if ($matchName){
                    $_SESSION['alert'] = '';
                    require 'menu.php';
                    break;
                }else{
                    $_SESSION['playerName'] = $_POST['playerName'];
                    $_SESSION['name'] = true;
                }
            }
            else
            {
                $_SESSION['playerName'] = "Unknown";
            }
        }
        
        $conn = conectar();
        //Se cuenta la cantidad de palabras en la base de datos
        $sql = 'SELECT COUNT(id) as cant FROM palabras';
        $result = $conn->prepare($sql);
        $result->execute();
        $cant = $result->fetch(PDO::FETCH_ASSOC)['cant'];

        //Se selecciona de maneara aleatoria un número entre 1 y la cantidad de palabras registradas
        //para tomarlo como referencia de id y seleccionar una palabra
        $rand = rand(1, $cant);
        $sql = "SELECT palabra FROM palabras WHERE id = '$rand'";
        $result = $conn->prepare($sql);
        $result->execute();
        $_SESSION['word'] = $result->fetch(PDO::FETCH_ASSOC)['palabra'];

        $_SESSION['foundLetters'] = '';
        //Mientras esté en null, significa que se está jugando
        $_SESSION['win'] = null;

        //nivel de dificultad seleccionado desde el menú
        if ($_SESSION['actualPoints'] == 0){
            $level = 0;
            if(isset($_POST['level']))
                $level = $_POST['level'];
            $_SESSION['level'] = $level;
    
            switch($level)
            {
                case 0:
                    $_SESSION['lives'] = 10;
                    break;
                case 1:
                    $_SESSION['lives'] = 5;
                    break;
                case 2:
                    $_SESSION['lives'] = 3;
                    break;                
            }
    
            //Imagen con la que se comienza
            $_SESSION['image'] = 0;
        }
        
        //Espacios que ocupa la palabra seleccionada
        $blankWord =  '';

        for($i = 0; $i < strlen($_SESSION['word']); $i++)
	    {
	        $blankWord .= (substr($_SESSION['word'],$i,1) != ' ' ? '<span class="guessed-letter">_</span>' : ' ');	
	    }
		
    require 'start.php';
        
    break;

    case 2:
        $response = [];

        if ($_SESSION['win'] == null)
        {
            $letter = strtolower($_POST['letter']);
            //Compara si la letra ingresada se encuentra en la palabra
            if (strpos(strtolower($_SESSION['word']), $letter) === false)
            {
                //Si no existe, se resta una vida
                $_SESSION['lives']--;

                //Según el nivel de dificultad que se haya seleccionado
                //se agrega la imagen correspondiente
                switch($_SESSION['level'])
                {
                    case 0:
                        $_SESSION['image']++;
                        break;
                    case 1:
                        $_SESSION['image'] += 2;
                        break;
                    case 2:
                        if($_SESSION['image'] == 0)
                                $_SESSION['image'] = 3;
                            elseif($_SESSION['image'] == 3)
                                $_SESSION['image'] = 6;
                            else
                                $_SESSION['image'] = 10;
                        break;
                }
                //Se guarda la ruta con la imagen correspondiente
                $response['image'] = 'images/' . $_SESSION['image'] . '.jpg';

                //Valida si le quedan vidas para así revelar la palabra
                //y dar por finalizada la partida
                if($_SESSION['lives'] == 0)
                {
                    $response['newRecord'] = false;
                    $response['playerName'] = $_SESSION['playerName'];
                    if ($_SESSION['actualPoints'] > $_SESSION['record'])
                    {
                        $response['newRecord'] = true;
                    }

                    $_SESSION['win'] = false;
                    if ($_SESSION['actualPoints'] > 0){
                        $conn = conectar();
                        $actualPoints = $_SESSION['actualPoints'];
                        $playerName = $_SESSION['playerName'];
                        $sql = "INSERT INTO records(puntos,jugador) VALUES($actualPoints,'$playerName')";
                        $conn->query($sql);
                    }
                    $response['word'] = 'La palabra era: <b>' . $_SESSION['word'] . '</b>';
                }   
            }
            else
            {
                //Si existe se van guardando las letras
                $_SESSION['foundLetters'] .= $letter;
                //Se cuentan las coincidencias
                $i = 0;
                //Se convierte la palabra y las letras que coinciden en arrays
                $wordLetters = str_split($_SESSION['word']);
                $foundLetters = str_split($_SESSION['foundLetters']);

                foreach ($wordLetters as $wLetters)
                {
                    $found = false;

                    foreach ($foundLetters as $fLetters)
                    {
                        if (strtolower($wLetters) == strtolower($fLetters))
                        {
                            $found = true;
                            break;
                        }
                    }
                    if($found)
                        $i++;
                }
                //Si la cantidad de coincidencias es igual al largo de la palabra
                //significa que el usuario acertó
                if ($i == strlen($_SESSION['word']))
                {
                    $_SESSION['win'] = true;

                    switch($_SESSION['level'])
                    {
                        case 0:
                            $_SESSION['actualPoints']++;
                            break;
                        case 1:
                            $_SESSION['actualPoints'] += 2;
                            break;
                        case 2:
                            $_SESSION['actualPoints'] += 3;
                            break;
                    }
                }
            }
        }

        $wordLetters = str_split($_SESSION['word']);
        $foundLetters = str_split($_SESSION['foundLetters']);
        $guessedWord = '';

        foreach($wordLetters as $wLetter)
        {
            $found = false;
            
            foreach($foundLetters as $fLetter)
            {
                if(strtolower($wLetter) == strtolower($fLetter))
                {
                    $found = true;
                    break;
                }
            }
        
            //Se van revelando las letras que coincidan en la palabra
	        if($found)
		        $guessedWord .= $wLetter;
	        elseif($wLetter != ' ')
		        $guessedWord .= '<span class="guessed-letter">_</span>';
	        else
		        $guessedWord .= ' ';
        }  

        $response['win'] = $_SESSION['win'];
        $response['lives'] = $_SESSION['lives'];
        $response['guessedWord'] = $guessedWord;
        
        //Se envia la respuesta como array a script.js
        echo json_encode($response, JSON_UNESCAPED_UNICODE);  
           
        break; 
}
?>