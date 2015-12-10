<?php
namespace MCrafters\TeamPvP\Tasks;


use pocketmine\scheduler\PluginTask;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\Server;
use pocketmine\tile\Sign;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\ServerScheduler as Tasks;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class GameStartTask extends PluginTask
{

    public $seconds = 15;

    public function __construct(\MCrafters\TeamPvP\TeamPvP $n)
    {

    }

    public function onRun($tick)
    {
        $this->seconds = $this->seconds - 1;

        $a = new \MCrafters\TeamPvP\TeamPvP();
        foreach ($a->reds as $r) {
            foreach ($a->blues as $b) {
                foreach ($a->yml["items"] as $i) {
                    Server::getInstance()->getPlayer($r)->sendPopup("§eThe game will start in $this->seconds second(s)!");
                    Server::getInstance()->getPlayer($b)->sendPopup("§eThe game will start in $this->seconds second(s)!");

                    if ($this->seconds == 1) {
                        Server::getInstance()->getPlayer($r)->teleport(new Vector3($a->yml["red_enter_x"], $a->yml["red_enter_y"], $a->yml["red_enter_z"]));
                        Server::getInstance()->getPlayer($b)->teleport(new Vector3($a->yml["blue_enter_x"], $a->yml["blue_enter_y"], $a->yml["blue_enter_z"]));
                        Server::getInstance()->getPlayer($r)->getInventory()->addItem(Item::get($i));
                        Server::getInstance()->getPlayer($b)->getInventory()->addItem(Item::get($i));
                        $a->gameStarted = true;
                        $this->seconds = 15;
                        Tasks::cancelTask($this->getTaskId());
                    }
                }
            }
        }
    }
}
