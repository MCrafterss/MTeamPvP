<?php
namespace MCrafters\TeamPvP;

/** 
 * @author  MCrafters Team
 * @version 3.5
**/

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as Color;

use MCrafters\TeamPvP\command\TeamPvPCmd;
use MCrafters\TeamPvP\arena\Arena;

class Loader extends PluginBase {

	public $arenas = [];

	public function onEnable(){
		if(!is_dir($this->getDataFolder()))mkdir($this->getDataFolder());
		if(!is_dir($this->getDataFolder() . "Arena")) mkdir($this->getDataFolder() . "/Arena");

		$this->getServer()->getLogger()->info(Color::BOLD . Color::GOLD . "M" . Color::AQUA . "TeamPvP " . Color::GREEN . "Enabled" . Color::RED . "!");

		$this->getServer()->getCommandMap()->register("mteampvp", new TeamPvPCmd($this));
		$this->runArenas();
	}

	public function addArena(string $name) : bool{
		file_put_contents($this->getDataFolder() . "Arena". DIRECTORY_SEPARATOR ."$name.yml", $this->getDefaultArenaFile());
		return true;
	}

	public function removeArena(string $name) : bool{
		unlink($this->getDataFolder() . "Arena". DIRECTORY_SEPARATOR ."$name.yml");
		return true;
	}

	public function arenaExists(string $name) : bool{
		return file_exists($this->getDataFolder() . "Arena". DIRECTORY_SEPARATOR ."$name.yml");
	}

	public function getDefaultArenaFile() : string{
		return "---

#the positions of the join sign
sign_join_x: 
sign_join_y: 
sign_join_z:
sign_world: 'world'

#the positions of the team's enter places.
blue_enter_x: 
blue_enter_y: 
blue_enter_z: 

red_enter_x: 
red_enter_y: 
red_enter_z: 

#the place when players teleport when the game is going to start soon
waiting_x: 
waiting_y: 
waiting_z: 

#the item ids will be given to the players when the game starts
items:
 - 276
 - 3

#the message will be sent to the player when he's hitting his team mate
teammate_message: '{player} is in your team!'

#the message will be sent to the player when the teams are full
teams_are_full_message: 'Teams are full'

#the level that we'll get it's spawn and teleport the player to it when the game ends
spawn_level: 'world'
world: 'world'
...";
	}

	public function runArenas(){
		foreach(glob($this->getDataFolder() . "Arena". DIRECTORY_SEPARATOR ."*.yml") as $file){
			$c = new Config($file, Config::YAML);
			$this->arenas[basename($file, ".yml")] = $c->getAll();
			$this->getServer()->getPluginManager()->registerEvents(new Arena(basename($file, ".yml"), $this), $this);
		}
	}
}
