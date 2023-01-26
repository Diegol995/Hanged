<?php
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
        $_SESSION['actualPoints'] = 0;
        $_SESSION['record'] = file('puntos.txt');
        /*$levels = [0 => 'Fácil, 10 vidas.',
                   1 => 'Medio, 5 vidas.',
                   2 => 'Dificil, 3 vidas.'];*/
        require 'menu.php';
        break;

    case 1:
        //Se abre el archivo que contiene las palabras
        $lines = file('palabras.txt');
        //Se selecciona de maneara aleatoria un número entre 0 y la cantidad de lineas del archivo
        //para tomarlo como referencia del array y seleccionar una palabra
        $word = $lines[rand(0, count($lines))];
        $word = substr($word, 0, strlen($word) - 1);

        $_SESSION['word'] = trim($word);
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

        for($i = 0; $i < strlen($word)-1; $i++)
	    {
	        $blankWord .= (substr($word,$i,1) != ' ' ? '<span class="guessed-letter">_</span>' : ' ');	
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
                    $_SESSION['win'] = false;
                    if ($_SESSION['actualPoints'] > $_SESSION['record'][0]){
                        $data = fopen('puntos.txt', 'w');
                        fwrite($data, strval($_SESSION['actualPoints']));
                        fclose($data);
                        $_SESSION['record'] = file('puntos.txt');
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
                //significa que el usuario ganó
                if ($i == strlen($_SESSION['word']))
                {
                    $_SESSION['win'] = true;
                    $_SESSION['actualPoints']++;
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