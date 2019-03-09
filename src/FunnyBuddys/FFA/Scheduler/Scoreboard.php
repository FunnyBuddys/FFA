<?php
/**
 * Created by PhpStorm.
 * User: Samuel
 * Date: 25.09.2018
 * Time: 14:53
 */

namespace FunnyBuddys\FFA\Scheduler;

use FunnyBuddys\FFA\Main;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;


class Scoreboard extends Task {

    /** @var Player */
    private $player;

    /** @var Main */
    private $main;

    public function __construct(Main $main, Player $player) {
        $this->main = $main;
        $this->player = $player;
    }

    public function numberPacket(Player $player, $score = 1, $msg = ""): void {
        $entrie = new ScorePacketEntry();

        $entrie->objectiveName = "test";
        $entrie->type = 3;
        $entrie->customName = str_repeat("", 5) . $msg . str_repeat(" ", 1);
        $entrie->score = $score;
        $entrie->scoreboardId = $score;

        $pk = new SetScorePacket();

        $pk->type = 1;
        $pk->entries[] = $entrie;
        $player->sendDataPacket($pk);
        $pk2 = new SetScorePacket();


        $pk2->entries[] = $entrie;
        $pk2->type = 0;
        $player->sendDataPacket($pk2);
    }


    public function onRun($tick) {

        $config = new Config($this->main->getDataFolder() . "config.yml", Config::YAML);

        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = "sidebar";
        $pk->objectiveName = "test";
        $pk->displayName = $config->get("Title");
        $pk->criteriaName = "dummy";
        $pk->sortOrder = 0;
        $this->player->sendDataPacket($pk);

        $playerconfig = new Config($this->main->getDataFolder() . "player/" . $this->player->getName() . ".yml", Config::YAML);

        $this->numberPacket($this->player, 2, "§7");

        $this->numberPacket($this->player, 3, "Name§7:");
        $this->numberPacket($this->player, 4, "§8» §6" . $this->player->getNameTag());

        $this->numberPacket($this->player, 5, "§1");

        $this->numberPacket($this->player, 6, "Kills§7:");
        $this->numberPacket($this->player, 7, "§8» §a" . $playerconfig->get("Kills"));

        $this->numberPacket($this->player, 8, "§2");

        $this->numberPacket($this->player, 9, "Deaths§7:");
        $this->numberPacket($this->player, 10, "§8» §c" . $playerconfig->get("Deaths"));

        $this->numberPacket($this->player, 11, "§3");

        if($playerconfig->get("Deaths") >= 0 and $playerconfig->get("Kills") === 0){
            $this->numberPacket($this->player, 12, "KD§7:");
            $this->numberPacket($this->player, 13, "§8» §e0");
        }elseif($playerconfig->get("Deaths") === 0 and $playerconfig->get("Kills") > 0){
            $this->numberPacket($this->player, 12, "KD§7:");
            $this->numberPacket($this->player, 13, "§8» §e100");
        }else{
            $kd = $playerconfig->get("Kills") / $playerconfig->get("Deaths");
            $this->numberPacket($this->player, 12, "KD§7:");
            $this->numberPacket($this->player, 13, "§8» §e" . round($kd, 2));
        }



    }


}