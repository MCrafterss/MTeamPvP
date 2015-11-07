<?php
namespace MCrafters\TeamPvP;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as Color;
use pocketmine\utils\Config;
use pocketmine\item\item;
use pocketmine\event\Event;
use pocketmine\level\Position;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\level\Level;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
class TeamPvP extends PluginBase implements Listener {
   //Teams
    public $red = [];
    public $blue = [];
    
    
  public function onEnable(){
     //Initializing config files
     
      $this->saveResource("config.yml");
      $yml = new Config($this->getDataFolder() . "config.yml", Config::YAML);
      $this->yml = $yml->getAll();
      
      $this->getLogger()->debug("Config files have been saved!");
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
      $this->getServer()->getLogger()->info(Color::BOLD . Color::GOLD . "M" . Color::AQUA . "TeamPvP " . Color::GREEN . "Enabled" . Color::RED . "!");
    }
  public function isFriend($p1, $p2){
    if(in_array($p1, $this->red, true) && in_array($p2, $this->red, true)){
      return true;
      
    } else{
      return false;
    }
     if(in_array($p1, $this->blue, true) && in_array($p2, $this->blue, true)){
       return true;
     } else{
       return false;
     }
   }//isFriend

  public function getTeam($p){
    if(in_array($p, $this->red, true)){
      return "red";
    } elseif(in_array($p, $this->blue, true)){
      return "blue";
    }
  }

  public function setTeam($p, $team){
    $red  = array_search($p, array_keys($this->red));
    $blue = array_search($p, array_keys($this->blue));
    if(strtolower($team) === "red"){
    unset($blue);
    array_push($this->red, $p);
    }
    if(strtolower($team) === "blue"){
      unset($red);
    array_push($this->blue, $p);
    }
  }
}//Class
