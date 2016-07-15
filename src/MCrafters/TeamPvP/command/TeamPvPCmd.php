<?php
namespace MCrafters\TeamPvP\command;

use MCrafters\TeamPvP\Loader;
use MCrafters\TeamPvP\arena\Arena;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;

class TeamPvPCmd extends Command{

    private $plugin;

    public function __construct(Loader $plugin){
        parent::__construct("mteampvp", "MTeamPvP Main Command", null, ["mtp", "tpvp"]);
        $this->plugin = $plugin;
    }
    
    public function execute(CommandSender $sender, $label, array $args){
        if(!(isset($args[0]))) return false;
        switch($args[0]){
            case "add":
            case "create":
            case "new":
            case "make":
                if(!(isset($args[1]))) return false;

                if(!$this->plugin->arenaExists($args[1])){
                    $this->plugin->addArena($args[1]);
                    $c = new Config($this->plugin->getDataFolder() . "Arena". DIRECTORY_SEPARATOR ."$args[1].yml", Config::YAML);
                    $this->plugin->arenas[$args[1]] = $c->getAll();
                    unset($c);
                    $this->plugin->getServer()->getPluginManager()->registerEvents(new Arena($args[1], $this->plugin), $this->plugin);
                    $sender->sendMessage("[MTeamPvP] Arena successfully created!");
                    return true;
                }

                $sender->sendMessage("[MTeamPvP] Arena already exists.");
            break;

            case "remove":
            case "delete":
            case "del":
            case "rm":
                if(!(isset($args[1]))) return false;

                if($this->plugin->arenaExists($args[1])){
                    $this->plugin->removeArena($args[1]);
                    unset($this->plugin->arenas[$args[1]]);
                    $sender->sendMessage("[MTeamPvP] Arena successfully deleted!");
                    return true;
                }
                $sender->sendMessage("[MTeamPvP] Arena does not exist.");

            break;
        }
    }
}