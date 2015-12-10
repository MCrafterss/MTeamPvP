<?php

namespace MCrafters\TeamPvP;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as Color;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\block\WallSign;
use pocketmine\block\PostSign;

class TeamPvP extends PluginBase implements Listener
{

    // Teams
    public $reds = [];
    public $blues = [];
    public $yml;

    public function onEnable()
    {
        // Initializing config files
        $this->saveResource("config.yml");
        $yml = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->yml = $yml->getAll();

        $this->getLogger()->debug("Config files have been saved!");

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getLogger()->info(Color::BOLD . Color::GOLD . "M" . Color::AQUA . "TeamWars " . Color::GREEN . "Enabled" . Color::RED . "!");
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
                    unset($this->blues[$p]);
                }
                $this->reds[$p] = $p;
                $this->getServer()->getPlayer($p)->teleport(new Vector3($this->yml["red_enter_x"], $this->yml["red_enter_y"], $this->yml["red_enter_z"]));
                return true;
            } elseif (count($this->blues) < 5) {
                $this->setTeam($p, "blue");
            } else {
                return false;
            }
        } elseif (strtolower($team) === "blue") {
            if (count($this->blues) < 5) {
                if ($this->getTeam($p) === "red") {
                    unset($this->reds[$p]);
                }
                $this->blues[$p] = $p;
                $this->getServer()->getPlayer($p)->teleport(new Vector3($this->yml["blue_enter_x"], $this->yml["blue_enter_y"], $this->yml["blue_enter_z"]));
                return true;
            } elseif (count($this->reds) < 5) {
                $this->setTeam($p, "red");
            } else {
                return false;
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $p = $event->getPlayer();
        $teams = array("red", "blue");
        $b = $event->getBlock();
        if ($b->getX() === $this->yml["sign_join_x"] && $b->getY() === $this->yml["sign_join_y"] && $b->getZ() === $this->yml["sign_join_z"] && $b->getLevel()->getName() == $this->yml["sign_world"]) {
            if ($b instanceof WallSign || $b instanceof PostSign) {
                if (count($this->blues) < 5 && count($this->reds) < 5) {
                    $this->setTeam($p, array_rand($teams, 1));
                }
            }
        }
    }

    public function onEntityDamage(EntityDamageEvent $event)
    {
        if ($event instanceof EntityDamageByEntityEvent) {
            if ($event->getEntity() instanceof Player) {
                if ($this->isFriend($event->getDamager()->getName(), $event->getEntity()->getName())) {
                    $event->setCancelled(true);
                    $event->getDamager()->sendMessage(str_replace("{player}", $event->getPlayer()->getName(), $this->yml["hit_same_team_message"]));
                }
            }
        }
    }
}//Class
