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
use pocketmine\event\entity\EntityDamageEvent;
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
       if(isset($this->blue[$blue])){
    unset($this->blue[$blue]);
       }
    array_push($this->red, $p);
    }
    
    if(strtolower($team) === "blue"){
       if(isset($this->red[$red])){
      unset($this->red[$red]);
       }
    array_push($this->blue, $p);
    }
  }


  public function Interact(PlayerInteractEvent $event){
    $teams = array("red", "blue");
    $b = $event->getBlock();
    if($b->getX() === $this->yml["sign_join_x"] && $b->getY() === $this->yml["sign_join_y"] && $b->getZ() === $this->yml["sign_join_z"]){
      if(count($this->red < 5) && count($this->blue < 5)){
    $this->setTeam($event->getPlayer()->getName(), array_rand($teams, 1));

      }elseif(count($this->red < 5)){
      $this->setTeam($event->getPlayer()->getName(), "red");
      $event->getPlayer()->inGame = true;
      $event->getPlayer()->teleport(new Vector3($this->yml["red_enter_x"], $this->yml["red_enter_y"], $this->yml["red_enter_z"]));
    } elseif(count($this->blue) < 5){
      $this->setTeam($event->getPlayer()->getName(), "blue");
      $event->getPlayer()->inGame = true;
      $event->getPlayer()->teleport(new Vector3($this->yml["blue_enter_x"], $this->yml["blue_enter_y"], $this->yml["blue_enter_z"]));
    } else {
      $event->getPlayer()->sendMessage("Teams are full");
    }
    }
  }

public function edbee(EntityDamageEvent $event){
   if($event instanceof EntityDamageByEntityEvent){
  if(!isset($event->getPlayer()->inGame) && !isset($event->getAttacker()->inGame) && $this->isFriend($event->getAttacker()->getName(), $event->getPlayer()->getName())){
    $event->setCancelled(true);
    $event->getAttacker()->sendMessage($event->getPlayer()->getName() . " is in your team!");
  }
}
}

}//Class
