<?php
namespace MCrafters\TeamPvP;

use pocketmine\scheduler\ServerScheduler as Tasks;

class GameManager
{
    public $reds;
    public $blues;
    public $gst;
    public $gwt;

   public function __construct(\MCrafters\TeamPvP\TeamPvP $plugin)
    {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }
    
    
    public function run()
    {
        $this->reds = $this->plugin->reds;
        $this->blues = $this->plugin->blues;

        if (count($this->reds) < 5 && count($this->blues) < 5) {
            $this->gst = Tasks::scheduleRepeatingTask(new Tasks\GameStartTask($this), 20)->getTaskId();
            Tasks::cancelTask($this->gwt);
        } else {
            $this->gwt = Tasks::scheduleRepeatingTask(new Tasks\GameWaitingTask($this), 15)->getTaskId();
        }
    }
}
