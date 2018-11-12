<?php 
class Cell{
    private $posX;
    private $posY;
    private $visited = false;

    private $WallUp = true;
    private $WallDown = true;
    private $WallLeft = true;
    private $WallRight = true;

    private $class;
    private $viableNeighbour = false;

    public function __construct($posX, $posY){
        $this->posX = $posX;
        $this->posY = $posY;
    }

    public static function Loader($posX,$posY, $WallDown, $WallLeft, $WallRight, $WallUp){
        $instance = new self($posX,$posY);
        $instance->WallUp = $WallUp;
        $instance->WallDown = $WallDown;
        $instance->WallLeft = $WallLeft;
        $instance->WallRight = $WallRight;
        return $instance;
    }

    public function GetX(){
        return $this->posX;
    }
    public function GetY(){
        return $this->posY;
    }
    public function WasVisited(){
        return $this->visited;
    }

    public function WallOff($direction){
        if($direction == 0){
            $this->WallUp = false;
        }
        if($direction == 1){
            $this->WallLeft = false;
        }
        if($direction == 2){
            $this->WallDown = false;
        }
        if($direction == 3){
            $this->WallRight = false;
        }
    }

    public function GetWall($direction){
        if($direction == 0){
            return $this->WallUp ? 1 : 0;
        }
        if($direction == 1){
            return $this->WallLeft ? 1 : 0;
        }
        if($direction == 2){
            return $this->WallDown ? 1 : 0;
        }
        if($direction == 3){
            return $this->WallRight ? 1 : 0;
        }
        return null;
    }
    
    public function TO_JSON(){
        $json_string = '{
            "WallUp": '.$this->GetWall(0).',
            "WallDown": '.$this->GetWall(2).',
            "WallLeft": '.$this->GetWall(1).',
            "WallRight": '.$this->GetWall(3).',
            "PosX": '.$this->posX.',
            "PosY": '.$this->posY.'
        }';
        return $json_string;
    }

    public function GET_CSS_CLASS(){
        $this->class = array("cell");
        if(!$this->WallRight){
            array_push($this->class, "right");
        } 
        if(!$this->WallDown){
            array_push($this->class, "bottom");
        }
        if(!$this->WallLeft){
            array_push($this->class, "left");
        }
        if(!$this->WallUp){
            array_push($this->class, "top");
        }
        if($this->visited){
            array_push($this->class, "visited");
        }
        if($this->viableNeighbour){
            array_push($this->class, "viableNeighbour");
        }
        $stringClass = "";
        foreach($this->class as &$c){
            $stringClass .= ' '.$c;
        }
        return $stringClass;
    }

    public function Visit(){
        $this->visited = true;
        $this->viableNeighbour = false;
    }

    public function ToggleAsNeighbour(){
        $this->viableNeighbour = !$this->viableNeighbour;
    }
    public function ToggleAsNeighbourOff(){
        $this->viableNeighbour = false;
    }
}

class Board{
    private $width;
    private $height;
    private $maze = array();
    private $visited = array();
    private $stepByStep;
    public function Init($height, $width,$stepByStep){
        $this->stepByStep = $stepByStep;
        $this->width = $width;
        $this->height = $height;
        $this->createBoard();
        $randX = rand(0, $this->height - 1);
        $randY = rand(0, $this->width - 1);
        if($stepByStep){
            echo '<script> setTimeout(function() { alert("Pls click ok and wait for next alert"); }, 1); </script>';
        }
        $this->recursiveBacktracker($randX, $randY);
        if($stepByStep){
            echo '<script> setTimeout(function() { alert("finished"); }, 1); </script>';
        }
        $this->printBoard();
        $this->SaveMaze();        
    }

    private function SaveMaze(){
        $json_string = "[";
        $json_string .= '
        {
         "height": '.$this->height.', 
         "width": '.$this->width.'
         },';
        for($i = 0; $i < $this->height; $i++){
            for($j = 0; $j < $this->width; $j++){
                $json_string .= $this->maze[$i][$j]->TO_JSON().',';
            }
        }
        $json_string = substr($json_string, 0, -1).']';
        file_put_contents("maze.json", $json_string);
    }

    public function LoadMaze($sourceFile){
        $json_string = file_get_contents($sourceFile);
        $this->maze = array();
        $decoded = json_decode($json_string);
        $this->height = $decoded[0]->{'height'};
        $this->width = $decoded[0]->{'width'};
        foreach($decoded as $newCell){
            if(isset($newCell->{'PosX'})){
                $posX = $newCell->{'PosX'};
                $posY = $newCell->{'PosY'};
                $WallUp = $newCell->{'WallUp'};
                $WallDown = $newCell->{'WallDown'};
                $WallLeft = $newCell->{'WallLeft'};
                $WallRight = $newCell->{'WallRight'};
                $this->maze[$posX][$posY] = Cell::Loader($posX,$posY, $WallDown, $WallLeft, $WallRight, $WallUp);
            }
        }
        $this->printBoard();
    }

    private function createBoard(){
        for($i = 0; $i < $this->height; $i++){
            for($j = 0; $j < $this->width; $j++){
                $this->maze[$i][$j] = new Cell($i,$j);
            }
        }
    }

    private function printBoard(){
        echo '<div class="board">';
        for($i = 0; $i < $this->height; $i++){
            for($j = 0; $j < $this->width; $j++){
                echo '<div class="'.$this->maze[$i][$j]->GET_CSS_CLASS().'"></div>';
                $this->maze[$i][$j]->ToogleAsNeighbourOff();
            }
            echo '<br class="clearThis" />';
        }
        echo '</div>';
    }

    private function checkNeighbours($i, $j){
        $neighbors = array();
        if($i > 0){
            $n = $this->maze[$i - 1][$j];
            if(!$n->WasVisited()){
                $n->ToogleAsNeighbour();
                array_push($neighbors, 0);
            }
        }
        if($j > 0){
            $n = $this->maze[$i][$j - 1];
            if(!$n->WasVisited()){
                $n->ToogleAsNeighbour();
                array_push($neighbors, 1);
            }
        }
        if($i < $this->height - 1){
            $n = $this->maze[$i + 1][$j];
            if(!$n->WasVisited()){
                $n->ToogleAsNeighbour();
                array_push($neighbors, 2);
            }
        }
        if($j < $this->width - 1){
            $n = $this->maze[$i][$j + 1];
            if(!$n->WasVisited()){
                $n->ToogleAsNeighbour();
                array_push($neighbors, 3);
            }
        }
        return $neighbors;
    }

    private function nextCell($direction, $x, $y){
        if($direction == 0){
            return $this->maze[$x - 1][$y];
        }
        if($direction == 1){
            return $this->maze[$x][$y - 1];
        }
        if($direction == 2){
            return $this->maze[$x + 1][$y];
        }
        if($direction == 3){
            return $this->maze[$x][$y + 1];
        }
    }

    private function recursiveBacktracker($startX, $startY){
        if($this->stepByStep){
            $this->printBoard();
            echo '<br />';
        }
        $currentCell = $this->maze[$startX][$startY];
        $currentCell->Visit();
        array_push($this->visited,$currentCell);
        $neighbors = $this->checkNeighbours($startX, $startY);
        if(count($neighbors) == 0){
            return;
        }

        $direction = $neighbors[array_rand($neighbors, 1)];
        $nextCell = $this->nextCell($direction, $startX, $startY);
        $currentCell->WallOff($direction);
        $nextCell->WallOff(($direction + 2) % 4);
        
        $this->recursiveBacktracker($nextCell->GetX(), $nextCell->GetY());
        $neighbors = $this->checkNeighbours($startX, $startY);
        if(count($neighbors) == 0){
            return;
        }
        $direction = $neighbors[array_rand($neighbors, 1)];
        $currentCell->WallOff($direction);
        $nextCell = $this->nextCell($direction, $startX, $startY);
        $nextCell->WallOff(($direction + 2) % 4);
        $this->recursiveBacktracker($nextCell->GetX(), $nextCell->GetY());
        return;
    }
}

class Maze{
    private $board;

    public function generate($height, $width, $stepByStep){
        set_time_limit(0);
        $this->board = new Board();
        $this->board->Init($height, $width, $stepByStep);
     }

     public function Load($file){
        $this->board = new Board();
        $this->board->LoadMaze($file);
     }
}
?>