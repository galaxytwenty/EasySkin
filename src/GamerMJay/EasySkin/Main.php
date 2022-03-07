<?php

declare(strict_types=1);

namespace GamerMJay\EasySkin;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {

    public $skins;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("skin", new \GamerMJay\EasySkin\cmd\SkinCommand($this));
        $this->saveResource("settings.yml");
        $this->cfg = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
        foreach ($this->cfg->get("skins") as $skins) {
            $this->saveResource("$skins.png");
        }
    }

    public function createSkin($skinName){
        $path = $this->getDataFolder()."{$skinName}.png";
        $img = @imagecreatefrompng($path);
        $bytes = '';
        $l = (int) @getimagesize($path)[1];
        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < 64; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }
}
