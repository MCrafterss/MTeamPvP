<?php
namespace MCrafters\TeamPvP;

use pocketmine\scheduler\ServerScheduler as Tasks;

class GameManager
{
    public $reds;
    public $blues;
    public $gst;
    public $gwt;

    public function run()
    {
        $a = new TeamPvP();
        $this->reds = $a->reds;
        $this->blues = $a->blues;

        if (count($this->reds) < 5 && count($this->blues) < 5) {
            $this->gst = Tasks::scheduleRepeatingTask(new GameStartTask($this), 20)->getTaskId();
            Tasks::cancelTask($this->gwt);
        } else {
            $this->gwt = Tasks::scheduleRepeatingTask(new GameWaitingTask($this), 15)->getTaskId();
        }
    }
}
