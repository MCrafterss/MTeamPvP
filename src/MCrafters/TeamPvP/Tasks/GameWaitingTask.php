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

    public function __construct(\MCrafters\TeamPvP\TeamPvP $plugin)
    {
        parent::__construct($plugin, $player);
    }

    public function onRun($tick)
    {
        $a = new \MCrafters\TeamPvP\TeamPvP();
        foreach ($a->reds as $r) {
            foreach ($a->blues as $b) {
                Server::getInstance()->getPlayer($r)->sendPopup("§eWaiting for players..");
                Server::getInstance()->getPlayer($b)->sendPopup("§eWaiting for players..");
            }
        }
    }
}

