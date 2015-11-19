<?php

// Enjoy

namespace CavinMiana\FreezeTag;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\entity\Effect;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\level\particle\DustParticle;
use pocketmine\level\sound\FizzSound;
use pocketmine\level\Position;
use pocketmine\event\entity\EntityTeleportEvent;

use Main\BeginTask;

class Main extends PluginBase implements Listener{
public $begin3 = 10;
public $end = 15;
public $begin2 = 30;
public $gameduration = 642;
public $gameduration2 = 9;
public $sec1 = 60;
public $sec2 = 60;
public $sec3 = 60;
public $sec4 = 60;
public $sec5 = 60;
public $sec6 = 60;
public $sec7 = 60;
public $sec8 = 60;
public $sec9 = 60;
public $sec10= 60;

public function onEnable(){
    $this->getServer()->getPluginManager()->registerEvents($this,$this);
    $this->saveDefaultConfig();
	$this->reloadConfig();
	$this->getServer()->dispatchCommand(new ConsoleCommandSender(), "whitelist off");
}

public function onHold(PlayerItemHeldEvent $event){
    if($event->getItem()->getId() == 276){
        $event->getPlayer()->sendPopup(TextFormat:: AQUA . "Freezing Wand");
    }
    if($event->getItem()->getId() == 280){
        $event->getPlayer()->sendPopup("¬ßdMelting Wand");
    }
}

public function onBreak(BlockBreakEvent $event){
	$event->setCancelled(true);
}
public function onPlace(BlockPlaceEvent $event){
	$event->setCancelled(true);
}

public function EntityDamageByEvent(EntityDamageEvent $event){
    if($event instanceof EntityDamageByEntityEvent and $event->getDamager() instanceof Player and $event->getEntity() instanceof Player){
        if($event->getDamager()->getInventory()->getItemInHand()->getId() == 276){
			if(!$event->getEntity()->hasEffect(2) and !$event->getEntity()->hasEffect(1)){
			$frozen = Effect::getEffect(2);
            $frozen->setDuration(99999999);
            $frozen->setAmplifier(9999999);
			$event->getEntity()->addEffect($frozen);
		    $froze = $this->getConfig()->get("frozenplayers");
            $this->getConfig()->set("frozenplayers",++$froze);
			$event->getDamager()->sendMessage("‚úî ".$event->getEntity()->getName(). " has been frozen!");
			$event->getEntity()->setNameTag(TextFormat:: AQUA ."[FROZEN]");
			$event->setCancelled(true);
					$onp = count($this->getServer()->getOnlinePlayers());
		            $t = 3;
		           $tot = $onp - $t;
		           if($this->getConfig()->get("frozenplayers") == $tot){
			$this->getServer()->getScheduler()->scheduleDelayedRepeatingTask((new EndTask($this)),20 * 20, 20 * 1);
			$this->getServer()->broadcastMessage("--------------------");
			$this->getServer()->broadcastMessage("    FREEZERS WIN    ");
			$this->getServer()->broadcastMessage("--------------------");
			$this->getServer()->broadcastMessage("PREPARE TO BE KICKED");
		}
			}		
			if($event->getEntity()->hasEffect(1)){
				$event->getDamager()->sendMessage($event->getEntity()->getName()." is on your team!");
				$event->setCancelled(true);
			}
		}
        if($event->getDamager()->getInventory()->getItemInHand()->getId() == 280 and $event->getEntity()->hasEffect(2)){
            $event->getEntity()->removeEffect(2);
			$froze = $this->getConfig()->get("frozenplayers");
            $this->getConfig()->set("frozenplayers",--$froze);
			$event->getDamager()->sendMessage("‚úî ".$event->getEntity()->getName(). "has been unfrozen");
			$event->getEntity()->setNameTag(null);
            $event->setCancelled(true);
        }
		else{
			$event->setCancelled(true);
		}
    }
	$event->setCancelled(true);
}

public function onJoin(PlayerJoinEvent $event){
	$event->getPlayer()->setNameTag(null);
	$event->getPlayer()->removeAllEffects();
	$event->getPlayer()->getInventory()->clearAll();
    if(count($this->getServer()->getOnlinePlayers()) == 1){
		$this->getConfig()->set("start", 0);
		$this->getConfig()->save();
        $this->getServer()->getScheduler()->scheduleRepeatingTask((new TipTask ($this)), 25); //25
    }
	if(count($this->getServer()->getOnlinePlayers()) == 3){
		$this->getConfig()->set("it1", $event->getPlayer()->getName());
	}
	if(count($this->getServer()->getOnlinePlayers()) == 5){
		$this->getConfig()->set("it2", $event->getPlayer()->getName());
	}
	if(count($this->getServer()->getOnlinePlayers()) == 9){
		$this->getConfig()->set("it3", $event->getPlayer()->getName());
	}
    if(count($this->getServer()->getOnlinePlayers()) == 12){                                   //-----------------------------------------
        $this->getServer()->getScheduler()->scheduleRepeatingTask((new GameTask ($this)), 25);// THESE TWO TASKS MUST HAVE THE SAME VALUES!!!
				$this->getServer()->getScheduler()->scheduleRepeatingTask((new TimerTask ($this)), 25);//
    }                                                                                            //------------------------------------------
    $p = $event->getPlayer();
    $event->setJoinMessage($p->getName()." has joined the match");
}
public function onQuit(PlayerQuitEvent $event){
	if($event->getPlayer()->getName() == $this->getConfig()->get("it1")){
		$players = $this->getServer()->getOnlinePlayers();
		$this->getServer()->broadcastMessage("Seems a Tagger has left.  Searching for another.");
		$this->getConfig()->set("it1", $players[array_rand($players)]->getName());
		if($this->getConfig()->get("it1") == $this->plugin->get("it2") or $this->getConfig()->get("it1") == $this->getConfig()->get("it3")){
		$players = $this->getServer()->getOnlinePlayers();
		$this->getConfig()->set("it1", $players[array_rand($players)]->getName());
		$p = $this->getServer()->getPlayer($this->getConfig()->get("it1"));
		$effect = Effect::getEffect(1);
		$effect->setDuration(999999999);
		$effect->setVisible(false);
		$p->addEffect($effect);
		$p->getInventory()->addItem(Item::get(276, 0, 1));
		$p->sendMessage("WOO! A Tagger left and you've been chosen to take their place. Go get em.");
		}
	}
	if($event->getPlayer()->getName() == $this->getConfig()->get("it2")){
		$players = $this->getServer()->getOnlinePlayers();
		$this->getServer()->broadcastMessage("Seems a Tagger has left.  Searching for another.");
		$this->getConfig()->set("it2", $players[array_rand($players)]->getName());
		if($this->getConfig()->get("it2") == $this->plugin->get("it1") or $this->getConfig()->get("it2") == $this->getConfig()->get("it3")){
		$players = $this->getServer()->getOnlinePlayers();
		$this->getConfig()->set("it2", $players[array_rand($players)]->getName());
		$p = $this->getServer()->getPlayer($this->getConfig()->get("it2"));
		$effect = Effect::getEffect(1);
		$effect->setDuration(999999999);
		$effect->setVisible(false);
		$p->addEffect($effect);
		$p->getInventory()->addItem(Item::get(276, 0, 1));
		$p->sendMessage("WOO! A Tagger left and you've been chosen to take their place. Go get em.");
		}
	}
	if($event->getPlayer()->getName() == $this->getConfig()->get("it3")){
		$players = $this->getServer()->getOnlinePlayers();
		$this->getServer()->broadcastMessage("Seems a Tagger has left.  Searching for another.");
		$this->getConfig()->set("it3", $players[array_rand($players)]->getName());
		if($this->getConfig()->get("it3") == $this->plugin->get("it2") or $this->getConfig()->get("it3") == $this->getConfig()->get("it1")){
		$players = $this->getServer()->getOnlinePlayers();
		$this->getConfig()->set("it3", $players[array_rand($players)]->getName());
		$p = $this->getServer()->getPlayer($this->getConfig()->get("it3"));
		$effect = Effect::getEffect(1);
		$effect->setDuration(999999999);
		$effect->setVisible(false);
		$p->addEffect($effect);
		$p->getInventory()->addItem(Item::get(276, 0, 1));
		$p->sendMessage("WOO! A Tagger left and you've been chosen to take their place. Go get em.");
		}
	}
}
}


namespace CavinMiana\FreezeTag;

use pocketmine\scheduler\PluginTask;
use CavinMiana\FreezeTag\Main;
use pocketmine\Plugin;
use pocketmine\level\sound\ClickSound;
use pocektmine\math\Vector3;

class TipTask extends PluginTask{
    public $plugin;
    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        parent::__construct($plugin);
}
public function onRun($tick){
	foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
		$onp = count($this->plugin->getServer()->getOnlinePlayers());
		$three = 3;
		$t = $onp - $three;
	if($this->plugin->getConfig()->get("frozenplayers") == $t){
		$this->plugin->getServer()->getScheduler()->cancelTask($this->getTaskId());
	}
    if($this->plugin->gameduration == 642){
		$onp = count($this->plugin->getServer()->getOnlinePlayers());
		$need = 12;
		$total = $need - $onp;
		$t2 = $onp - $need;
         $p->sendTip("Players Online: ".count($this->plugin->getServer()->getOnlinePlayers())."/16");
		 $p->sendPopup("Waiting for ".$total. " more player(s)");
	}
	//Begin Tip Timer
	elseif($this->plugin->gameduration <= 641 and $this->plugin->gameduration > 611 and $this->plugin->begin2 >= 10){
			   	$p->sendTip("Game Begins in: 0:".$this->plugin->begin2);
		   }
		   elseif($this->plugin->gameduration > 611 and $this->plugin->begin2 <= 10){
			   $p->sendTip("Game Begins in: 0:0".$this->plugin->begin2);
			   $p->getLevel()->addSound(new ClickSound($p));
		   }
		   elseif($this->plugin->gameduration < 611 and $this->plugin->gameduration > 598 and $this->plugin->begin3 >= 0){
			   $p->sendTip("Freezers will be released in: ".$this->plugin->begin3);
			   $p->getLevel()->addSound(new ClickSound($p));
		   }
		   // 10
		   elseif($this->plugin->gameduration <= 600 and $this->plugin->sec1 >= 10 and $this->plugin->sec1 < 60){
			   		$onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
			   $p->sendTip("Time:  9:".$this->plugin->sec1."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
		   }
		   elseif($this->plugin->sec1 < 10 and $this->plugin->sec1 >= 0){
			   $onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
			   $p->sendTip("Time:  9:0".$this->plugin->sec1."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
		   }
		   //end 10 start 9
		   elseif($this->plugin->sec2 >= 10 and $this->plugin->sec2 < 60){
			   $onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
			   $p->sendTip("Time:  8:".$this->plugin->sec2."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
		   }
		   elseif($this->plugin->sec2 < 10 and $this->plugin->sec2 >= 0){
			   $onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
			   $p->sendTip("Time:  8:0".$this->plugin->sec2."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
		   }
		   elseif($this->plugin->sec3 >= 10 and $this->plugin->sec3 < 60){
			   $onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
			   $p->sendTip("Time:  7:".$this->plugin->sec3."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
		   }
		   elseif($this->plugin->sec3 < 10 and $this->plugin->sec3 >= 0){
			   $onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
			   $p->sendTip("Time:  7:0".$this->plugin->sec3."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
		   }
		   elseif($this->plugin->sec4 >= 10 and $this->plugin->sec4 < 60){
			   $onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
			   $p->sendTip("Time:  6:".$this->plugin->sec4."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
		   }
           elseif($this->plugin->sec4 < 10 and $this->plugin->sec4 >= 0){
			   $onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
			   $p->sendTip("Time:  6:0".$this->plugin->sec4."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
		   }	
           elseif($this->plugin->sec5 >= 10 and $this->plugin->sec5 < 60){
			   $onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
			   $p->sendTip("Time:  5:".$this->plugin->sec5."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
		   }		   
		   elseif($this->plugin->sec5 < 10 and $this->plugin->sec5 >= 0){
			   $onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
			   $p->sendTip("Time:  5:0".$this->plugin->sec5."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
}
elseif($this->plugin->sec6 >= 10 and $this->plugin->sec6 < 60){
	$onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
	$p->sendTip("Time:  4:".$this->plugin->sec6."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
}
elseif($this->plugin->sec6 < 10 and $this->plugin->sec6 >= 0){
	$onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
	$p->sendTip("Time:  4:0".$this->plugin->sec6."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
}
elseif($this->plugin->sec7 >= 10 and $this->plugin->sec7 < 60){
	$onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
	$p->sendTip("Time:  3:".$this->plugin->sec7."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
}
elseif($this->plugin->sec7 < 10 and $this->plugin->sec7 >= 0){
	$onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
	$p->sendTip("Time:  3:0".$this->plugin->sec7."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
}
elseif($this->plugin->sec8 >= 10 and $this->plugin->sec8 < 60){
	$onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
	$p->sendTip("Time:  2:".$this->plugin->sec8."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
}
elseif($this->plugin->sec8 < 10 and $this->plugin->sec8 >= 0){
	$onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
	$p->sendTip("Time:  2:0".$this->plugin->sec8."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
}
elseif($this->plugin->sec9 >= 10 and $this->plugin->sec9 < 60){
	$onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
	$p->sendTip("Time:  1:".$this->plugin->sec9."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
}
elseif($this->plugin->sec9 < 10 and $this->plugin->sec9 >= 0){
	$onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
	$p->sendTip("Time:  1:0".$this->plugin->sec9."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
}
elseif($this->plugin->sec10 >= 10 and $this->plugin->sec10 < 60){
	$onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
	$p->sendTip("Time:  0:".$this->plugin->sec10."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
}
elseif($this->plugin->sec10 < 10 and $this->plugin->sec10 >= 0){
	$onp = count($this->plugin->getServer()->getOnlinePlayers());
		            $need = 3;
		            $total = $onp - $need;
	$p->sendTip("Time:  0:0".$this->plugin->sec10."  |  Frozen: ".$this->plugin->getConfig()->get("frozenplayers")."/".$total);
}
}
}
}
namespace CavinMiana\FreezeTag;

use pocketmine\scheduler\PluginTask;
use CavinMiana\FreezeTag\Main;
use pocketmine\Plugin;

class TimerTask extends PluginTask{
    public $plugin;
    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        parent::__construct($plugin);
}

public function onRun($tick){
	//Begin Timer
if($this->plugin->gameduration < 642 and $this->plugin->gameduration > 610){
	 $this->plugin->begin2 -= 1;
}
elseif($this->plugin->gameduration < 610 and $this->plugin->gameduration > 595){
	$this->plugin->begin3 -= 1;
}
// First Min
elseif($this->plugin->gameduration < 603 and $this->plugin->gameduration >= 530){
	$this->plugin->sec1 -=1;
}
elseif($this->plugin->sec1 <= 0 and $this->plugin->sec2 >= 0){
	$this->plugin->sec2 -=1;
}
elseif($this->plugin->sec2 <= 0 and $this->plugin->sec3 >= 0){
	$this->plugin->sec3 -=1;
}
elseif($this->plugin->sec3 <= 0 and $this->plugin->sec4 >= 0){
	$this->plugin->sec4 -=1;
}
elseif($this->plugin->sec4 <= 0 and $this->plugin->sec5 >= 0){
	$this->plugin->sec5 -=1;
}
elseif($this->plugin->sec5 <= 0 and $this->plugin->sec6 >= 0){
	$this->plugin->sec6 -=1;
}
elseif($this->plugin->sec6 <= 0 and $this->plugin->sec7 >= 0){
	$this->plugin->sec7 -=1;
}
elseif($this->plugin->sec7 <= 0 and $this->plugin->sec8 >= 0){
	$this->plugin->sec8 -=1;
}
elseif($this->plugin->sec8 <= 0 and $this->plugin->sec9 >= 0){
	$this->plugin->sec9 -=1;
}
elseif($this->plugin->sec9 <= 0 and $this->plugin->sec10 >= -15){
	$this->plugin->sec10 -=1;
}
elseif($this->plugin->sec10 == 0){
	foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
		$p->sendMessage("-------------------");
		$p->sendMessage("      GAME OVER    ");
		$p->sendMessage("-------------------");
		$p->sendMessage("     HIDERS WIN   ");
		$p->sendMessage("-------------------");
	}
}
elseif($this->plugin->sec10 == -2){
	$p->sendMessage("Prepare to be kicked in 10 seconds");
}
elseif($this->plugin->sec10 == -10){
	$this->plugin->getServer()->shutdown();
}
}
}
namespace CavinMiana\FreezeTag;

use pocketmine\scheduler\PluginTask;
use pocketmine\Plugin;

class EndTask extends PluginTask{
	
	public function __construct($plugin){
		$this->plugin = $plugin;
		parent::__construct($plugin);
	}
	public function onRun($tick){
		foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
			$this->plugin->end -=1;
			if($this->plugin->end == 14){
				$p->sendMessage("Prepare to be kicked in 10 Seconds");
			}
			elseif($this->plugin->end == 4){
				$p->kick("Match Ended");
			}
			elseif($this->plugin->end == 1){
				$this->plugin->getServer()->shutdown();
			}
		}
	}
}
