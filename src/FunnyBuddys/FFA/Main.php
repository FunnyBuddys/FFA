<?php

namespace FunnyBuddys\FFA;

use FunnyBuddys\FFA\Listener\JoinListener;
use FunnyBuddys\FFA\Listener\KillListener;
use FunnyBuddys\FFA\Listener\QuitListener;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Main extends PluginBase{

    public function onEnable() {
        $this->getLogger()->info(TextFormat::GREEN . "FFA has been loaded successfully!");
        $this->getLogger()->warning(TextFormat::YELLOW . base64_decode("VGhlIHBsdWdpbiB3YXMgZGV2ZWxvcGVkIGJ5IEZ1bm55QnVkZHlz"));

        /* --- Loading Listener --- */
        $this->getServer()->getPluginManager()->registerEvents(new JoinListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new QuitListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new KillListener($this), $this);

        @mkdir($this->getDataFolder() . "player");

        if(!file_exists($this->getDataFolder() . "config.yml")){
            $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

            /* --- Armor --- */
            $armor = array(Item::IRON_HELMET, Item::IRON_CHESTPLATE, Item::IRON_LEGGINGS, Item::IRON_BOOTS);

            /* --- Items --- */
            $items = array(
                array(
                    Item::IRON_SWORD, 0, 1
                ),
                array(
                    Item::GOLDEN_APPLE, 0, 5
                )
            );

            /* --- Configuation sets and save --- */
            $config->set("Prefix", "§a§lFFA§r §8| §f");
            $config->set("Armor", $armor);
            $config->set("Items", $items);

            /* --- Messages --- */
            $config->set("JoinMessage", "§e{PLAYER}§a joined the game.");
            $config->set("LeaveMessage", "§e{PLAYER}§c left the game.");
            $config->set("KillMessage", "§aYou killed §c{VICTIM}§a!");
            $config->set("KilledMessage", "§cYou were killed by §e{KILLER}§c!");
            $config->set("DeathMessage", "§cYou died!");
            $config->set("Title", "§l§aFFA");
            $config->set("Subtitle", "§eWelcome to the game!");
            $config->set("SpawnRadius", 4);
            $config->set("ProtectionMessage", "You're too close to the spawn to fight with other players.");
            $config->save();
        }
    }
}
