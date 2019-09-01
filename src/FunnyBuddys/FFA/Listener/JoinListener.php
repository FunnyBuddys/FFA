<?php
/**
 * Created by PhpStorm.
 * User: Samuel
 * Date: 02.03.2019
 * Time: 20:00
 */


namespace FunnyBuddys\FFA\Listener;

use FunnyBuddys\FFA\Main;
use FunnyBuddys\FFA\Scheduler\Scoreboard;
use FunnyBuddys\FFA\Scheduler\TitleScheduler;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\utils\Config;

class JoinListener implements Listener {

    /** @var Main  */
    private $main;

    public function __construct(Main $main) {
        $this->main = $main;
    }

    public function onJoin(PlayerJoinEvent $event){

        $config = new Config($this->main->getDataFolder() . "config.yml", Config::YAML);

        $replace = str_replace("{PLAYER}", $event->getPlayer()->getNameTag(), $config->get("JoinMessage"));
        $event->setJoinMessage($config->get("Prefix") . $replace);

        $event->getPlayer()->getInventory()->clearAll();
        $event->getPlayer()->getArmorInventory()->clearAll();

        $this->main->getScheduler()->scheduleDelayedTask(new TitleScheduler($this->main, $event->getPlayer()), 10);

        $armor = $config->get("Armor");
        $items = $config->get("Items");
        $event->getPlayer()->getArmorInventory()->setHelmet(Item::get($armor[0]));
        $event->getPlayer()->getArmorInventory()->setChestplate(Item::get($armor[1]));
        $event->getPlayer()->getArmorInventory()->setLeggings(Item::get($armor[2]));
        $event->getPlayer()->getArmorInventory()->setBoots(Item::get($armor[3]));

        for($i = 0; $i < count($items); $i++) {
            $itemArray = $items[$i];

            $event->getPlayer()->getInventory()->addItem(Item::get($itemArray[0], $itemArray[1], $itemArray[2]));
        }

        if(!file_exists($this->main->getDataFolder() . "player/" . $event->getPlayer()->getName() . ".yml")){
            $playerconfig = new Config($this->main->getDataFolder() . "player/" . $event->getPlayer()->getName() . ".yml", Config::YAML);
            $playerconfig->set("Kills", 0);
            $playerconfig->set("Deaths", 0);
            $playerconfig->set("KD", "NaN");
            $playerconfig->set("Streak", 0);
            $playerconfig->save();
        }
    }

    public function onBuild(BlockPlaceEvent $event){
        if(!$event->getPlayer()->isOp()){
            $event->setCancelled();
        }
    }

    public function onBreak(BlockBreakEvent $event){
        if(!$event->getPlayer()->isOp()){
            $event->setCancelled();
        }
    }

    public function onPickUp(InventoryPickupItemEvent $event) {
        $event->setCancelled();
    }

    public function onExhaust(PlayerExhaustEvent $event){
        $event->setCancelled();
    }

}