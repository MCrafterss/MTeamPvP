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

public $f = 0;
    public function __construct(\MCrafters\TeamPvP\TeamPvP $plugin)
    {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun($tick)
    {
        $this->f++;
        if($f > 15){
        $a = new \MCrafters\TeamPvP\TeamPvP();
        $yml = $a->yml;
        $t = $a->getServer()->getLevelByName($yml["sign_world"])->getTile(new Vector3($yml["sign_join_x"], $yml["sign_join_y"], $yml["sign_join_z"]));

        if ($t instanceof Sign) {
            if ($a->gameStarted == true) {
                $t->setText(
                    "§l§6Team§cPvP",
                    "§l§cRed Team : " . count($a->reds),
                    "§l§bBlue Team : " . count($a->blues),
                    "§aStarted"
                );
            } elseif ($a->gameStarted == false && count($a->reds) < 5 && count($a->blues) < 5) {
                $t->setText(
                    "§l§6Team§cPvP",
                    "§l§cRed Team : " . count($a->reds),
                    "§l§bBlue Team : " . count($a->blues),
                    "§aFull"
                );
            } elseif ($a->gameStarted == false && !count($a->reds) < 5 && !count($a->blues) < 5) {
                $t->setText(
                    "§l§6Team§cPvP",
                    "§l§cRed Team : " . count($a->reds),
                    "§l§bBlue Team : " . count($a->blues),
                    "§aTap To Join!"
                );
                }
            }
        }
    }
}
