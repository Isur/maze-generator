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

    public function ToogleAsNeighbour(){
        $this->viableNeighbour = !$this->viableNeighbour;
    }
    public function ToogleAsNeighbourOff(){
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
        //$this->printBoard();
        $randX = rand(0, $this->height - 1);
        $randY = rand(0, $this->width - 1);
        // echo ' '.$randX.' '.$randY;
        $this->recursiveBacktracker($randX, $randY);
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
            echo '</board><br class="clearThis" />';
        }
    }

    private function checkNeighbours($i,$j){
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
        // echo ''.$startX.' '.$startY.' '.$direction;
        $nextCell->WallOff(($direction + 2) % 4);
        
        $this->recursiveBacktracker($nextCell->GetX(), $nextCell->GetY());
        $neighbors = $this->checkNeighbours($startX, $startY);
        if(count($neighbors) == 0){
            return;
        }
        $direction = $neighbors[array_rand($neighbors, 1)];
        // echo ''.$startX.' '.$startY.' '.$direction;
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
        $this->board = new Board();
        $this->board->Init($height, $width, $stepByStep);
     }

}


?>