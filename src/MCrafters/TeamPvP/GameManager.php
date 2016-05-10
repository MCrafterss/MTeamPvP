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

        $this->reds = $team->reds;
        $this->blues = $team->blues;
        if (count($this->reds) == 5 && count($this->blues) == 5) {
            $this->gst = Server::getInstance()->getScheduler()->scheduleRepeatingTask(new \MCrafters\TeamPvP\Tasks\GameStartTask($team), 20)->getTaskId();
            Server::getInstance()->getScheduler()->cancelTask($this->gwt);
        } else {
            $this->gwt = Server::getInstance()->getScheduler()->scheduleRepeatingTask(new \MCrafters\TeamPvP\Tasks\GameWaitingTask($team), 15)->getTaskId();
        }
    }
}
