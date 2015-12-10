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

class SignUpdaterTask extends PluginTask
{

    public function __construct(\MCrafters\TeamPvP\TeamPvP $n)
    {

    }

    public function onRun($tick)
    {
        $yml = $this->getOwner()->yml;
        $t = $this->getOwner()->getServer()->getLevelByName($yml["sign_world"])->getTile(new Vector3($yml["sign_join_x"], $yml["sign_join_y"], $yml["sign_join_z"]));

        if ($t instanceof Sign) {
            if ($this->getOwner()->gameStarted == true) {
                $t->setText(
                    "§l§6Team§cPvP",
                    "§l§cRed Team : " . count($this->getOwner()->reds),
                    "§l§bBlue Team : " . count($this->getOwner()->blues),
                    "§aStarted"
                );
            } elseif ($this->getOwner()->gameStarted == false && count($this->getOwner()->reds) < 5 && count($this->getOwner()->blues) < 5) {
                $t->setText(
                    "§l§6Team§cPvP",
                    "§l§cRed Team : " . count($this->getOwner()->reds),
                    "§l§bBlue Team : " . count($this->getOwner()->blues),
                    "§aFull"
                );
            } elseif ($this->getOwner()->gameStarted == false && !count($this->getOwner()->reds) < 5 && !count($this->getOwner()->blues) < 5) {
                $t->setText(
                    "§l§6Team§cPvP",
                    "§l§cRed Team : " . count($this->getOwner()->reds),
                    "§l§bBlue Team : " . count($this->getOwner()->blues),
                    "§aTap To Join!"
                );
            }
        }
    }
}
