<?php
namespace MCrafters\TeamPvP;


use pocketmine\scheduler\PluginTask;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\tile\Sign;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\ServerScheduler;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class SignUpdater extends PluginTask {
  
    public function __construct(TeamPvP $n) {
  
}

  public function onRun($tick) {
  $yml = $this->getOwner()->yml;
  $t = $this->plugin->getServer()->getLevelByName($yml["sign_world")->getTile(new Vector3($yml["sign_join_x"], $yml["sign_join_y"], $yml["sign_join_z"]));
  
  if($t instanceof Sign){
  $t->setText(
  "§l§6Team§cWars"
  
  );
  }
  }
  }
