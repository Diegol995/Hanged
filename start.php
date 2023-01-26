<html>
    <head>
        <title>Ahorcado</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div id="hanged-div2">
            <div>
                Seleccione una letra:
                <div id="letters">
                    <?php
                    $abc = range('A', 'Z');
                    $abc[26] = 'ñ';
                    foreach ($abc as $char) {
                        echo '<span class="letter">'. $char .'</span>';
                    }
                    ?>
                    <div class="clear"></div>
                </div>
                <div id="lives-left-div">
                    Vidas restantes: <span id="lives-left"><?= $_SESSION['lives'] ?></span>
                </div>
            </div>
            <div>
                <img src="images/<?php echo $_SESSION['image'] ?>.jpg" id="hanged" alt="hanged"/>
            </div>
            <div>
                <div id="guessed-word-div">
                    <?= $blankWord ?>
                </div>
                <div id="the-word-was-div" class="display-none"></div>
                <div id="play-again-div" class="display-none">
                    <a href="index.php">Jugar otra partida</a>
                </div>
                <div id="actual-points-div">
                    Puntos: <span id="actual-points"><?= $_SESSION['actualPoints'] ?></span>
                </div>
                <div id="record-points-div">
                    Record: <span id="record-points"><?= $_SESSION['record'][0] ?></span>
                </div>
                <form action="controller.php" method="POST">
                <input type="hidden" name="action" value="1" />
                <input type="hidden" name="level" value="<?php echo $_SESSION['level'] ?>">
                    <div id="next-word-div" class="display-none">
                        <input type="submit" value="Siguiente palabra">
                    </div>
                </form>
            </div>
        </div>
        <script src="js/jquery-2.1.3.min.js"></script>
        <script src="js/script.js"></script> 
    </body>
</html>