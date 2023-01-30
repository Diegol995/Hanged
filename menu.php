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
                        <label for="level_0">Fácil: 10 vidas. Suma de a 1 punto.</label><br>
                        <input type="radio" name="level" id="level_1" value="1">
                        <label for="level_1">Medio: 5 vidas. Suma de a 2 puntos.</label><br>
                        <input type="radio" name="level" id="level_2" value="2">
                        <label for="level_2">Dificil: 3 vidas. Suma de a 3 puntos.</label>
                    </span>
                </div>
                <div id="name-div">
                    <label >Ingrese su nombre: </label>
                    <input type="text" name="playerName" id="playerName"/>
                    <input type="submit" value="Jugar!!!" id="submit-button"/>
                </div>
            </form>
            <table id="table-topFive">
                <h2>Top 5 mejores puntajes</h3>
                <thead>
                    <tr>
                        <th>Puntos</th>
                        <th>Jugador</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['topFive'] as $r) {?>
                    <tr>
                        <td><?php echo $r['puntos']; ?></td>
                        <td><?php echo $r['jugador']; ?></td>
                    </tr>
                </tbody>
                <?php } ?>
            </table>
        </div>      
    </body>
</html>