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

    public function __construct(\MCrafters\TeamPvP\TeamPvP $plugin)
    {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun($tick)
    {
        $this->seconds = $this->seconds-1;

        foreach ($this->plugin->reds as $r) {
            foreach ($this->plugin->blues as $b) {
                foreach ($this->plugin->yml["items"] as $i) {
                    Server::getInstance()->getPlayer($r)->sendPopup("§eThe game will start in $this->seconds second(s)!");
                    Server::getInstance()->getPlayer($b)->sendPopup("§eThe game will start in $this->seconds second(s)!");

                    if ($this->seconds == 1) {
                        Server::getInstance()->getPlayer($r)->teleport(new Vector3($this->plugin->yml["red_enter_x"], $this->plugin->yml["red_enter_y"], $this->plugin->yml["red_enter_z"]));
                        Server::getInstance()->getPlayer($b)->teleport(new Vector3($this->plugin->yml["blue_enter_x"], $this->plugin->yml["blue_enter_y"], $this->plugin->yml["blue_enter_z"]));
                        Server::getInstance()->getPlayer($r)->getInventory()->addItem(Item::get($i));
                        Server::getInstance()->getPlayer($b)->getInventory()->addItem(Item::get($i));
                        $this->plugin->gameStarted = true;
                        $this->seconds = 15;
                        Tasks::cancelTask($this->getTaskId());
                    }
                }
            }
        }
    }
}
