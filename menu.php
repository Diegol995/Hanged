<!DOCTYPE html>
<html>
    <head>
        <title>Ahorcado</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div id="hanged-div">
            <form action="controller.php" method="POST">
                <input type="hidden" name="action" value="1" />
                <div id="levels-div">
                    <span id="level">
                        <h3>Seleccione la dificultad:</h3>
                        <input type="radio" name="level" checked="checked" id="level_0" value="0">
                        <label for="level_0">Fácil: 10 vidas.</label><br>
                        <input type="radio" name="level" id="level_1" value="1">
                        <label for="level_1">Medio: 5 vidas.</label><br>
                        <input type="radio" name="level" id="level_2" value="2">
                        <label for="level_2">Dificil: 3 vidas.</label>
                    </span>
                </div>
                <div>
                    <input type="submit" value="Jugar!!!" id="submit-button" />
                </div>
            </form>
        </div>      
    </body>
</html>