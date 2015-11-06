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

  public function onEnable(){
     //Initializing config files
     
      $this->saveResource("config.yml");
      $yml = new Config($this->getDataFolder() . "config.yml", Config::YAML);
      $this->yml = $yml->getAll();
      
      $this->getLogger()->debug("Config files have been saved!");

      $this->getServer()->getPluginManager()->registerEvents($this, $this);
      $this->getServer()->getLogger()->info(Color::BOLD . Color::GOLD . "M" . Color::AQUA . "TeamPvP " . Color::GREEN . "Enabled" . Color::RED . "!");
      }
    }
