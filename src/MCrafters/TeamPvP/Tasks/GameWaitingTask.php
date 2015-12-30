<?php
namespace MCrafters\TeamPvP\Tasks;


use pocketmine\scheduler\PluginTask;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\tile\Sign;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\ServerScheduler;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class GameWaitingTask extends PluginTask
{

    public function __construct(\MCrafters\TeamPvP\GameManager $plugin)
    {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun($tick)
    {
        foreach ($this->plugin->reds as $r) {
            foreach ($this->plugin->blues as $b) {
                Server::getInstance()->getPlayer($r)->sendPopup("§eWaiting for players..");
                Server::getInstance()->getPlayer($b)->sendPopup("§eWaiting for players..");
            }
        }
    }
}

