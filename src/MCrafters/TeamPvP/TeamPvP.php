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

class TeamPvP extends PluginBase implements Listener
{

    // Teams
    public $red = [];
    public $blue = [];
    public $yml;

    public function onEnable()
    {
        // Initializing config files
        $this->saveResource("config.yml");
        $yml = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->yml = $yml->getAll();

        $this->getLogger()->debug("Config files have been saved!");

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
        if (in_array($p, $this->red)) {
            return "red";
        } elseif (in_array($p, $this->blue)) {
            return "blue";
        } else {
            return false;
        }
    }

    public function setTeam($p, $team)
    {
        if (strtolower($team) === "red") {
            if ($this->getTeam($p) === "blue") {
                unset($this->blue[$p]);
            }
            array_push($this->red, $p => $p);
        } elseif (strtolower($team) === "blue") {
            if ($this->getTeam($p) === "red") {
                unset($this->red[$p]);
            }
            $this->blue[$p] = $p;
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $teams = array("red", "blue");
        $b = $event->getBlock();
        if ($b->getX() === $this->yml["sign_join_x"] && $b->getY() === $this->yml["sign_join_y"] && $b->getZ() === $this->yml["sign_join_z"]) {
            if (count($this->red < 5) && count($this->blue < 5)) {
                $this->setTeam($event->getPlayer()->getName(), array_rand($teams, 1));
                $event->getPlayer()->inGame = true;
                $event->getPlayer()->teleport(new Vector3($this->yml["blue_enter_x"], $this->yml["blue_enter_y"], $this->yml["blue_enter_z"]));
            } elseif (count($this->red < 5)) {
                $this->setTeam($event->getPlayer()->getName(), "red");
                $event->getPlayer()->inGame = true;
                $event->getPlayer()->teleport(new Vector3($this->yml["red_enter_x"], $this->yml["red_enter_y"], $this->yml["red_enter_z"]));
            } elseif (count($this->blue) < 5) {
                $this->setTeam($event->getPlayer()->getName(), "blue");
                $event->getPlayer()->inGame = true;
                $event->getPlayer()->teleport(new Vector3($this->yml["blue_enter_x"], $this->yml["blue_enter_y"], $this->yml["blue_enter_z"]));
            } else {
                $event->getPlayer()->sendMessage("Teams are full");
            }
        }
    }

    public function onEntityDamage(EntityDamageEvent $event)
    {
        if ($event instanceof EntityDamageByEntityEvent) {
            if ($event->getEntity() instanceof Player) {
                if ($this->isFriend($event->getDamager()->getName(), $event->getEntity()->getName())) {
                    $event->setCancelled(true);
                    $event->getDamager()->sendMessage($event->getEntity()->getName() . " is in your team!");
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args)
    {
        $teams = array("red", "blue");
        switch ($cmd->getName()) {
            case "team": {
                switch (strtolower($args[0])) {
                    case "red": {
                        if ($sender instanceof Player) {
                            $this->setTeam($sender->getName(), "red");
                            $sender->inGame = true;
                            $sender->teleport(new Vector3($this->yml["red_enter_x"], $this->yml["red_enter_y"], $this->yml["red_enter_z"]));
                            return true;
                        } else
                            return false;
                    }
                    case "blue": {
                        if ($sender instanceof Player) {
                            $this->setTeam($sender->getName(), "blue");
                            $sender->inGame = true;
                            $sender->teleport(new Vector3($this->yml["blue_enter_x"], $this->yml["blue_enter_y"], $this->yml["blue_enter_z"]));
                            return true;
                        } else
                            return false;
                    }
                    case "var": {
                        var_dump($this->red);
                        var_dump($this->blue);
                        return true;
                    }
                    default: {
                        return false;
                    }
                }
            }
        }
    }
}//Class
