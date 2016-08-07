<?php
namespace MCrafters\TeamPvP;

class GameManager
{
    public $reds;
    public $blues;
    public $gst;
    public $gwt;
    private $plugin;

    public function __construct(\MCrafters\TeamPvP\arena\Arena $plugin, \MCrafters\TeamPvP\Loader $m){
        $this->plugin = $plugin;
        $this->m = $m;
    }

    public function run()
    {

        $this->reds = $this->plugin->reds;
        $this->blues = $this->plugin->blues;

        if (count($this->reds) == 5 && count($this->blues) == 5) {
            $this->gst = $this->plugin->getServer()->getScheduler()->scheduleRepeatingTask(new \MCrafters\TeamPvP\Tasks\GameStartTask($this->plugin, $this->m), 20)->getTaskId();
            $this->plugin->getServer()->getScheduler()->cancelTask($this->gwt);
        } else {
            $this->gwt = $this->plugin->getServer()->getScheduler()->scheduleRepeatingTask(new \MCrafters\TeamPvP\Tasks\GameWaitingTask($this->plugin, $this->m), 15)->getTaskId();
        }
    }
}
