<?php

namespace MCrafters\TeamPvP;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as Color;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\block\WallSign;
use pocketmine\block\PostSign;
use pocketmine\scheduler\ServerScheduler;

class TeamPvP extends PluginBase implements Listener
{

    // Teams
    public $reds = [];
    public $blues = [];
    public $gameStarted = false;
    public $yml;


    public function onEnable()
    {
        // Initializing config files
        $this->saveResource("config.yml");
        $yml = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->yml = $yml->getAll();

        $this->getLogger()->debug("Config files have been saved!");
        
    $level = $this->yml["sign_world"];
    
    if(!$this->getServer()->isLevelGenerated($level)){
      $this->getLogger()->error("The level you used on the config ( " . $level . " ) doesn't exist! stopping plugin...");
      $this->getServer()->getPluginManager()->disablePlugin($this->getServer()->getPluginManager()->getPlugin("MTeamPvP"));
    }
    
    if(!$this->getServer()->isLevelLoaded($level)){
      $this->getServer()->loadLevel($level);
    }

        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Tasks\SignUpdaterTask($this), 15);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getLogger()->info(Color::BOLD . Color::GOLD . "M" . Color::AQUA . "TeamPvP " . Color::GREEN . "Enabled" . Color::RED . "!");
    }

    public function isFriend($p1, $p2)
    {
        if ($this->getTeam($p1) === $this->getTeam($p2) && $this->getTeam($p1) !== false) {
            return true;
        } else {
            return false;
        }
    }

    // isFriend
    public function getTeam($p)
    {
        if (in_array($p, $this->reds)) {
            return "red";
        } elseif (in_array($p, $this->blues)) {
            return "blue";
        } else {
            return false;
        }
    }

    public function setTeam($p, $team)
    {
        if (strtolower($team) === "red") {
            if (count($this->reds) < 5) {
                if ($this->getTeam($p) === "blue") {
                    unset($this->blues{
                    array_search(
                                $p
                                , 
                                $this->blues)
                                
                    });
                }
                array_push($this->reds, $p);
                $this->getServer()->getPlayer($p)->setNameTag("§c§l" . $p);
                $this->getServer()->getPlayer($p)->teleport(new Vector3($this->yml["waiting_x"], $this->yml["waiting_y"], $this->yml["waiting_z"]));
                return true;
            } elseif (count($this->blues) < 5) {
                $this->setTeam($p, "blue");
            } else {
                return false;
            }
        } elseif (strtolower($team) === "blue") {
            if (count($this->blues) < 5) {
                if ($this->getTeam($p) === "red") {
                    unset($this->reds{
                    array_search(
                                $p
                                 , 
                                 $this->reds)
                            
                    }
                    );
                }
                array_push($this->blues, $p);
                $this->getServer()->getPlayer($p)->setNameTag("§b§l" . $p);
                $this->getServer()->getPlayer($p)->teleport(new Vector3($this->yml["waiting_x"], $this->yml["waiting_y"], $this->yml["waiting_z"]));
                return true;
            } elseif (count($this->reds) < 5) {
                $this->setTeam($p, "red");
            } else {
                return false;
            }
        }
    }

    public function removeFromTeam($p, $team)
    {
        if (strtolower($team) == "red") {
            unset($this->reds{array_search(
                $p
                , 
                $this->reds)
                
            }
            );
            return true;
        } elseif (strtolower($team) == "blue") {
            unset($this->blues{array_search(
            $p
            ,
            $this->blues)
                
            }
            );
            return true;
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $p = $event->getPlayer();
        $teams = array("red", "blue");
        if ($event->getBlock()->getX() === $this->yml["sign_join_x"] && $event->getBlock()->getY() === $this->yml["sign_join_y"] && $event->getBlock()->getZ() === $this->yml["sign_join_z"]) {
            if (count($this->blues) <= 5 and count($this->reds) <= 5) {
                $this->setTeam($p->getName(), $teams{
                    array_rand(
                    $teams, 1)
                });
                $s = new GameManager();
                $s->run();
            } else {
                $p->sendMessage($this->yml["teams_are_full_message"]);
            }
        }
    }

    public function onEntityDamage(EntityDamageEvent $event)
    {
        if ($event instanceof EntityDamageByEntityEvent) {
            if ($event->getEntity() instanceof Player) {
                if ($this->isFriend($event->getDamager()->getName(), $event->getEntity()->getName()) && $this->gameStarted == true) {
                    $event->setCancelled(true);
                    $event->getDamager()->sendMessage(str_replace("{player}", $event->getPlayer()->getName(), $this->yml["hit_same_team_message"]));
                }

                if ($this->isFriend($event->getDamager()->getName(), $event->getEntity()->getName())) {
                    $event->setCancelled(true);
                }
            }
        }
    }


    public function onDeath(PlayerDeathEvent $event)
    {
        $a = array();
        
        if ($this->getTeam($event->getEntity()->getName()) == "red" && $this->gameStarted == true) {
            $this->removeFromTeam($event->getEntity()->getName(), "red");
            $event->getEntity()->teleport($this->getServer()->getLevelByName($this->yml["spawn_level"])->getSafeSpawn());
        } elseif ($this->getTeam($event->getEntity()->getName()) == "blue" && $this->gameStarted == true) {
            $this->removeFromTeam($event->getEntity()->getName(), "blue");
            $event->getEntity()->teleport($this->getServer()->getLevelByName($this->yml["spawn_level"])->getSafeSpawn());
        }
        foreach ($this->blues as $b) {
            foreach ($this->reds as $r) {
                if (count($this->reds) == 0 && $this->gameStarted == true) {
                    $a{
                        "WON"
                        
                    } = "BLUE";
                }
                if (count($this->blues) == 0 && $this->gameStarted == true) {
                    $a{
                        "WON"
                        
                    } = "RED";
                }
                if($a[0] == "BLUE"){
                    $this->removeFromTeam($b, "blue");
                    $this->getServer()->getPlayer($b)->teleport($this->getServer()->getLevelByName($this->yml["spawn_level"])->getSafeSpawn());
                    $this->gameStarted = false;
                    $this->getServer()->broadcastMessage("Blue Team won TeamPvP!");
                $a{
                    "WON"
                    
                } = "False";
                
                }else{
                    return FALSE;
                }
                if ($a[0] == "RED"){
                    $this->removeFromTeam($r, "red");
                    $this->getServer()->getPlayer($r)->teleport($this->getServer()->getLevelByName($this->yml["spawn_level"])->getSafeSpawn());
                    $this->gameStarted = false;
                    $this->getServer()->broadcastMessage("Red Team won TeamPvP!");
                $a{
                    "WON"
                    
                } = "False";
                }else{
                    return FALSE;
                }
                if($a[0] == "False"){
                    return;
                }
            }
        }
    }
}//class
