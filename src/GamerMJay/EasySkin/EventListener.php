<?php

namespace GamerMJay\EasySkin;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class EventListener implements Listener {

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $skins = $this->plugin->skins;
        $skins[$player->getName()] = $player->getSkin()->getSkinData();
        $this->plugin->skins = $skins;
    }

    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        $skins = $this->plugin->skins;
        unset($skins[$player->getName()]);
        $this->plugin->skins = $skins;
    }
}
