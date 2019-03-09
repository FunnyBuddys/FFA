<?php
/**
 * Created by PhpStorm.
 * User: Samuel
 * Date: 03.03.2019
 * Time: 16:18
 */

namespace FunnyBuddys\FFA\Listener;


use FunnyBuddys\FFA\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\Config;

class QuitListener implements Listener {

    /** @var Main  */
    private $main;

    public function __construct(Main $main) {
        $this->main = $main;
    }

    public function onQuit(PlayerQuitEvent $event){
        $config = new Config($this->main->getDataFolder() . "config.yml", Config::YAML);

        $replace = str_replace("{PLAYER}", $event->getPlayer()->getNameTag(), $config->get("LeaveMessage"));
        $event->setQuitMessage($config->get("Prefix") . $replace);
    }

}