<?php
namespace MCrafters\TeamPvP;
use pocketmine\scheduler\ServerScheduler as Tasks;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;

class GameManager extends PluginBase
{
    public $reds;
    public $blues;
    public $gst;
    public $gwt;
     
    public function run()
    {
        $team = new \MCrafters\TeamPvP\TeamPvP();

        $this->plugin = $team;
        $this->reds = $this->plugin->reds;
        $this->blues = $this->plugin->blues;
        if (count($this->reds) == 5 && count($this->blues) == 5) {
            $this->gst = Server::getInstance()->getScheduler()->scheduleRepeatingTask(new \MCrafters\TeamPvP\Tasks\GameStartTask($this), 20)->getTaskId();
            Server::getInstance()->getScheduler()->cancelTask($this->gwt);
        } else {
            $this->gwt = Server::getInstance()->getScheduler()->scheduleRepeatingTask(new \MCrafters\TeamPvP\Tasks\GameWaitingTask($this), 15)->getTaskId();
        }
    }
}
