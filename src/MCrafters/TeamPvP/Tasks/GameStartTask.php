<?php
namespace MCrafters\TeamPvP\Tasks;


use pocketmine\scheduler\PluginTask;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\item\Item;
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
        $this->seconds -= 1;

        foreach ($this->plugin->reds as $r) {
            foreach ($this->plugin->blues as $b) {
                foreach ($this->plugin->yml["items"] as $i) {
                    $this->plugin->getServer()->getPlayer($r)->sendPopup("§eThe game will start in {$this->seconds} ".($this->seconds<=1?"second":"seconds"));
                    $this->plugin->getServer()->getPlayer($b)->sendPopup("§eThe game will start in {$this->seconds} ".($this->seconds<=1?"second":"seconds"));

                    if ($this->seconds == 1) {
                        getPlayer($r)->teleport(new Vector3($this->plugin->yml["red_enter_x"], $this->plugin->yml["red_enter_y"], $this->plugin->yml["red_enter_z"]));
                        $this->plugin->getServer()->getPlayer($b)->teleport(new Vector3($this->plugin->yml["blue_enter_x"], $this->plugin->yml["blue_enter_y"], $this->plugin->yml["blue_enter_z"]));
                        $this->plugin->getServer()->getPlayer($r)->getInventory()->addItem(Item::fromString($i));
                        $this->plugin->getServer()->getPlayer($b)->getInventory()->addItem(Item::fromString($i));
                        $this->plugin->gameStarted = true;
                        $this->seconds = 15;
                        Tasks::cancelTask($this->getTaskId());
                    }
                }
            }
        }
    }
}
