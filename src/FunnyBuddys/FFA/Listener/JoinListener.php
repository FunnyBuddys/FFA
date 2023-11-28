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
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\ArmorMaterial;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaArmorMaterials;
use pocketmine\item\VanillaItems;
use pocketmine\utils\Config;

class JoinListener implements Listener {

    /** @var Main  */
    private $main;

    public function __construct(Main $main) {
        $this->main = $main;
    }

    function getArmorItem($type) {
        switch ($type) {
            // Netherite Rüstung
            case "netherite_helmet":
                return VanillaItems::NETHERITE_HELMET();
            case "netherite_chestplate":
                return VanillaItems::NETHERITE_CHESTPLATE();
            case "netherite_leggings":
                return VanillaItems::NETHERITE_LEGGINGS();
            case "netherite_boots":
                return VanillaItems::NETHERITE_BOOTS();

            // Diamant Rüstung
            case "diamond_helmet":
                return VanillaItems::DIAMOND_HELMET();
            case "diamond_chestplate":
                return VanillaItems::DIAMOND_CHESTPLATE();
            case "diamond_leggings":
                return VanillaItems::DIAMOND_LEGGINGS();
            case "diamond_boots":
                return VanillaItems::DIAMOND_BOOTS();

            // Eisen Rüstung
            case "iron_helmet":
                return VanillaItems::IRON_HELMET();
            case "iron_chestplate":
                return VanillaItems::IRON_CHESTPLATE();
            case "iron_leggings":
                return VanillaItems::IRON_LEGGINGS();
            case "iron_boots":
                return VanillaItems::IRON_BOOTS();

            // Gold Rüstung
            case "gold_helmet":
                return VanillaItems::GOLDEN_HELMET();
            case "gold_chestplate":
                return VanillaItems::GOLDEN_CHESTPLATE();
            case "gold_leggings":
                return VanillaItems::GOLDEN_LEGGINGS();
            case "gold_boots":
                return VanillaItems::GOLDEN_BOOTS();

            // Leder Rüstung
            case "leather_helmet":
                return VanillaItems::LEATHER_CAP();
            case "leather_chestplate":
                return VanillaItems::LEATHER_TUNIC();
            case "leather_leggings":
                return VanillaItems::LEATHER_PANTS();
            case "leather_boots":
                return VanillaItems::LEATHER_BOOTS();

            // Ketten Rüstung
            case "chainmail_helmet":
                return VanillaItems::CHAINMAIL_HELMET();
            case "chainmail_chestplate":
                return VanillaItems::CHAINMAIL_CHESTPLATE();
            case "chainmail_leggings":
                return VanillaItems::CHAINMAIL_LEGGINGS();
            case "chainmail_boots":
                return VanillaItems::CHAINMAIL_BOOTS();

            case "turtle_helmet":
                return VanillaItems::TURTLE_HELMET();

            default:
                return null;
        }
    }


    public function onJoin(PlayerJoinEvent $event){

        $config = new Config($this->main->getDataFolder() . "config.yml", Config::YAML);

        $replace = str_replace("{PLAYER}", $event->getPlayer()->getNameTag(), $config->get("JoinMessage"));
        $event->setJoinMessage($config->get("Prefix") . $replace);

        $event->getPlayer()->getInventory()->clearAll();
        $event->getPlayer()->getArmorInventory()->clearAll();

        $event->getPlayer()->sendTitle($config->get("Title"), $config->get("Subtitle"));

        $armor = $config->get("Armor");
        $items = $config->get("Items");

        $event->getPlayer()->getArmorInventory()->setHelmet($this->getArmorItem($armor[0]));
        $event->getPlayer()->getArmorInventory()->setChestplate($this->getArmorItem($armor[1]));
        $event->getPlayer()->getArmorInventory()->setLeggings($this->getArmorItem($armor[2]));
        $event->getPlayer()->getArmorInventory()->setBoots($this->getArmorItem($armor[3]));

        for($i = 0; $i < count($items); $i++) {
            $itemArray = $items[$i];

            $item = StringToItemParser::getInstance()->parse($itemArray[0]);
            $item->setCount($itemArray[1]);

            $event->getPlayer()->getInventory()->addItem($item);
        }

        if(!file_exists($this->main->getDataFolder() . "player/" . $event->getPlayer()->getName() . ".yml")){
            $playerconfig = new Config($this->main->getDataFolder() . "player/" . $event->getPlayer()->getName() . ".yml", Config::YAML);
            $playerconfig->set("Kills", 0);
            $playerconfig->set("Deaths", 0);
            $playerconfig->set("KD", "NaN");
            $playerconfig->set("Streak", 0);
            $playerconfig->save();
        }
        $this->main->getScheduler()->scheduleRepeatingTask(new Scoreboard($this->main, $event->getPlayer()), 10);

    }

    public function onBuild(BlockPlaceEvent $event){
        if(!$event->getPlayer()->hasPermission("op")){
            $event->cancel();
        }
    }

    public function onBreak(BlockBreakEvent $event){
        if(!$event->getPlayer()->hasPermission("op")){
            $event->cancel();
        }
    }

    public function onPickUp(EntityItemPickupEvent $event) {
        $event->cancel();
    }

    public function onExhaust(PlayerExhaustEvent $event){
        $event->cancel();
    }

}