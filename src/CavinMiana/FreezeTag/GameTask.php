<?php

// GameTask!

namespace CavinMiana\FreezeTag;

use pocketmine\scheduler\PluginTask;
use pocketmine\math\Vector3;
use pocketmine\entity\Effect;
use pocketmine\item\Item;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;

class GameTask extends PluginTask{
    public function __construct($plugin){
        $this->plugin = $plugin;
        parent::__construct($plugin);
		$players = $this->plugin->getServer()->getOnlinePlayers();
    }
    
    public function onRun($tick){
    $this->plugin->gameduration -= 1;
    foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
	if($this->plugin->gameduration == 612){
		$players = $this->plugin->getServer()->getOnlinePlayers();
		$effect = Effect::getEffect(1);
		$effect->setDuration(99999999);
		$effect->setAmplifier(0);
		$effect->setVisible(false);
	    $ran1 = $this->plugin->getServer()->getPlayer($this->plugin->getConfig()->get("it1"));
	    $ran2 = $this->plugin->getServer()->getPlayer($this->plugin->getConfig()->get("it2"));
		$ran3 = $this->plugin->getServer()->getPlayer($this->plugin->getConfig()->get("it3"));
		$ran1->addEffect($effect);
		$ran2->addEffect($effect);
		$ran3->addEffect($effect);
		$ran1->setNameTag(TextFormat::RED."[RUN AWAY FROM ME]");
		$ran2->setNameTag(TextFormat::RED."[RUN AWAY FROM ME]");
	    $ran3->setNameTag(TextFormat::RED."[RUN AWAY FROM ME]");
		$p->getInventory()->addItem(Item::get(280, 0, 1));
		$ran1->getInventory()->addItem(Item::get(276, 0, 1));
		$ran2->getInventory()->addItem(Item::get(276, 0, 1));
		$ran3->getInventory()->addItem(Item::get(276, 0, 1));
		$ran1->getInventory()->removeItem(Item::get(280, 0, 1));
		$ran2->getInventory()->removeItem(Item::get(280, 0, 1));
		$ran3->getInventory()->removeItem(Item::get(280, 0, 1));
		$ran3->sendMessage("Seems like your it! Tag Players with the sword.");
		$ran2->sendMessage("Seems like your it! Tag Players with the sword.");
		$ran1->sendMessage("Seems like your it! Tag Players with the sword.");
        $ran1->teleport(new Vector3(159, 4, 170));
		$ran2->teleport(new Vector3(159, 4, 170));
		$ran3->teleport(new Vector3(159, 4, 170));
		$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), "whitelist on");
	}
	elseif($this->plugin->gameduration == 599){
        $this->plugin->getServer()->getPlayer(strtolower($this->plugin->getConfig()->get("it1")))->teleport(new Vector3(153, 4, 128));
		$this->plugin->getServer()->getPlayer(strtolower($this->plugin->getConfig()->get("it2")))->teleport(new Vector3(153, 4, 128));
		$this->plugin->getServer()->getPlayer(strtolower($this->plugin->getConfig()->get("it3")))->teleport(new Vector3(153, 4, 128));
	}
	}

if($this->plugin->gameduration == 0){
	$p->kick("Game Over");
}
}
}




