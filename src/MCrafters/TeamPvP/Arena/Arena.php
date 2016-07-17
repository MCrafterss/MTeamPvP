<?php
namespace MCrafters\TeamPvP\Arena;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\TextFormat as Color;
use pocketmine\utils\Config;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\math\Vector3;

use MCrafters\TeamPvP\Loader;
use MCrafters\TeamPvP\GameManager;
use MCrafters\TeamPvP\Tasks\SignUpdaterTask;

class Arena implements Listener {

	public $name;
	private $plugin;
	public $yml;
	public $reds = [];
    public $blues = [];
    public $gameStarted = false;
	

	public function __construct(string $name, Loader $plugin){
		$this->name = $name;
		$this->plugin = $plugin;
		$this->yml = $plugin->arenas[$name];

        $level = $this->yml["sign_world"];

        if(!$this->getServer()->isLevelLoaded($level)){
            $this->getServer()->loadLevel($level);
        }

		$plugin->getServer()->loadLevel($this->yml["world"]);

		$plugin->getServer()->getScheduler()->scheduleRepeatingTask(new SignUpdaterTask($this, $this->plugin), 5);

	}

 public function isFriend($p1, $p2) : bool
    {
        if ($this->getTeam($p1) === $this->getTeam($p2) && $this->getTeam($p1) !== false) {
            return true;
        } else {
            return false;
        }
    }

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

    public function setTeam(Player $p, $team)
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
                $this->getServer()->getPlayer($p)->setNameTag(Color::BOLD . Color::RED . $p->getName());
                $this->getServer()->getPlayer($p)->teleport(new Vector3($this->yml["waiting_x"], $this->yml["waiting_y"], $this->yml["waiting_z"]));
                return true;
            } 
            elseif (count($this->blues) < 5) {
                $this->setTeam($p, "blue");
            }
        }
        if (strtolower($team) === "blue") {
            if (count($this->blues) < 5) {
                if ($this->getTeam($p) === "red") {
                    unset($this->reds{
                    array_search(
                                $p
                                 , 
                                 $this->reds)});
                }
                array_push($this->blues, $p);
                $this->getServer()->getPlayer($p)->setNameTag(Color::BOLD . Color::AQUA . $p->getName());
                $this->getServer()->getPlayer($p)->teleport(new Vector3($this->yml["waiting_x"], $this->yml["waiting_y"], $this->yml["waiting_z"]));
                return true;
            } elseif (count($this->reds) < 5) {
                $this->setTeam($p, "red");
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
                $this->setTeam($p, $teams{
                    array_rand(
                    $teams, 1)
                });
                $s = new GameManager($this, $this->plugin);
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
                    $event->getDamager()->sendMessage(str_replace("{player}", $event->getPlayer()->getName(), $this->yml["teammate_message"]));
                }

                if ($this->isFriend($event->getDamager()->getName(), $event->getEntity()->getName())) {
                    $event->setCancelled(true);
                }
            }
        }
    }
    
    public function onQuit(PlayerQuitEvent $event)
    {
     if ($this->getTeam($event->getPlayer()->getName()) == "red" || $this->getTeam($event->getPlayer()->getName()) == "blue" && $this->gameStarted == true) {
      $this->checkForEnd($event->getPlayer());
     }
    }
    
    public function onDeath(PlayerDeathEvent $event)
    {
     if ($this->getTeam($event->getEntity()->getName()) == "red" || $this->getTeam($event->getEntity()->getName()) == "blue" && $this->gameStarted == true) {
      $this->checkForEnd($event->getEntity());
     }
    }


    public function checkForEnd(Player $player) : bool
    {
        $a = [];
        
        if ($this->getTeam($player->getName()) == "red" && $this->gameStarted == true) {
            $this->removeFromTeam($player->getName(), "red");
            $player->teleport($this->getServer()->getLevelByName($this->yml["spawn_level"])->getSafeSpawn());
        } elseif ($this->getTeam($player->getName()) == "blue" && $this->gameStarted == true) {
            $this->removeFromTeam($player->getName(), "blue");
            $player->teleport($this->getServer()->getLevelByName($this->yml["spawn_level"])->getSafeSpawn());
        }
        foreach ($this->blues as $b) {
            foreach ($this->reds as $r) {
                if (count($this->reds) == 0 && $this->gameStarted == true) {
                    $a[] = "BLUE";
                }
                if (count($this->blues) == 0 && $this->gameStarted == true) {
                    $a[] = "RED";
                }
                if($a[0] == "BLUE"){
                    $this->removeFromTeam($b, "blue");
                    $this->getServer()->getPlayer($b)->teleport($this->getServer()->getLevelByName($this->yml["spawn_level"])->getSafeSpawn());
                    $this->gameStarted = false;
                    $this->getServer()->broadcastMessage("Blue Team won the game!");
                $a[] = false;
                
                }else{
                    return false;
                }
                if ($a[0] == "RED"){
                    $this->removeFromTeam($r, "red");
                    $this->getServer()->getPlayer($r)->teleport($this->getServer()->getLevelByName($this->yml["spawn_level"])->getSafeSpawn());
                    $this->gameStarted = false;
                    $this->getServer()->broadcastMessage("Red Team won the game!");
                $a[] = false;
                }else{
                    return false;
                }
                if($a[1] == false){
                    return;
                }
            }
        }
    }

    public function getServer(){
    	return $this->plugin->getServer();
    }
}
