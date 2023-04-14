<?php

namespace GamerMJay\EasySkin;

use Exception;
use Himbeer\LibSkin\SkinConverter;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class EventListener implements Listener {

    private Main $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $skinData = $player->getSkin()->getSkinData();
        $baseFileName = strtolower($player->getName());
        $imageFileName = $baseFileName . ".png";
        $geoFileName = $baseFileName . ".json";
        $fullImagePath = $this->plugin->getTempFile($imageFileName);
        $fullGeoPath = $this->plugin->getTempFile($geoFileName);
        try {
            SkinConverter::skinDataToImageSave($skinData, $fullImagePath);
            $this->plugin->skinMetaDataToJsonSave($player->getSkin()->getSkinId(), $player->getSkin()->getGeometryName(), $player->getSkin()->getGeometryData(), $fullGeoPath);
        } catch (Exception) {
            $player->sendMessage("§cAn unknown error occurred!");
        }
    }

    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        $baseFileName = strtolower($player->getName());
        $imageFileName = $baseFileName . ".png";
        $geoFileName = $baseFileName . ".json";
        $imageFile = $this->plugin->getTempFile($imageFileName);
        $jasonFile = $this->plugin->getTempFile($geoFileName);
        unlink($imageFile);
        unlink($jasonFile);
    }
}
