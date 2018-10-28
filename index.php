<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="maze.css">
    <title>Document</title>
</head>
<body>
    <main>
        <?php 
            include("mazeGenerator.php"); 
            $maze = new Maze();
            echo '<form action="" method="get">
            <input type="text" name="x" placeholder="height"/> 
            <input type="text" name="y" placeholder="width"/>
            <input type="radio" name="steps" value="1" > Step by step </input>
            <input type="radio" name="steps" value="0" > Only result </input>
            <input type="submit" value="Submit"/> 
            </form>
            <form action="" method="get">
            <input type="radio" name="steps" value="1" > Step by step </input>
            <input type="radio" name="steps" value="0" > Only result </input>
            <input type="submit" value="Random 2-30" name="random"/> 
            </form>
            ';
            echo '<br /> <br />';
            if(isset($_GET['x']) && isset($_GET['y']) && isset($_GET['steps'])){
                $x = $_GET['x'];
                $y = $_GET['y'];
                $steps = $_GET['steps'];
                if(is_numeric($x) && is_numeric($y)){
                    $maze->generate($x,$y,$steps);
                }else 
                echo 'Fill the form with integer values';                
            } else if(isset($_GET['random']) && isset($_GET['steps'])){
                $x = rand(2,30);
                $y = rand(2,30);
                $steps = $_GET['steps'];
                $maze->generate($x,$y, $steps);
            }
            else
            {
                echo 'fill the form';
            }
        ?>
    </main>
</body>
</html>