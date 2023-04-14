<?php

declare(strict_types=1);

namespace GamerMJay\EasySkin;

use Exception;
use GamerMJay\EasySkin\cmd\SkinCommand;
use pocketmine\entity\Skin;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {

    public Config $cfg;

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("skin", new SkinCommand($this));
        $this->saveResources();
        $this->cfg = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
    }

    public function saveResources(){
        $skinDir = $this->getDataFolder() . "SkinData/";
        $this->saveResource("settings.yml");
        $this->saveResource("AbgegrieftHD.png");
        $this->saveResource("GommeHD.png");
        $this->saveResource("Normal.json");
        $this->saveResource("Slim.json");
        if (!is_dir($skinDir)) {
            mkdir($skinDir);
        }
        if (!is_dir($this->getDataFolder() . "temp/")) {
            mkdir($this->getDataFolder() . "temp/");
        }
        if (!is_dir($this->getDataFolder() . "Geometry/")) {
            mkdir($this->getDataFolder() . "Geometry/");
        }
        $source1 = $this->getDataFolder()."AbgegrieftHD.png";
        $destination1 = $skinDir."AbgegrieftHD.png";
        $source2 = $this->getDataFolder()."GommeHD.png";
        $destination2 = $skinDir."GommeHD.png";

        $source3 = $this->getDataFolder()."Normal.json";
        $destination3 = $this->getDataFolder() . "Geometry/"."Normal.json";
        $source4 = $this->getDataFolder()."Slim.json";
        $destination4 = $this->getDataFolder() . "Geometry/"."Slim.json";
        if (!rename($source1, $destination1) || !rename($source2, $destination2)) {
            throw new Exception("Error trying to move Skin files!");
        }
        if (!rename($source3, $destination3) || !rename($source4, $destination4)) {
            throw new Exception("Error trying to move Geometry files!");
        }
        $skinDir = $this->getDataFolder() . "SkinData/";
        if (file_exists($skinDir."AbgegrieftHD.png")) {
            if (file_exists($this->getDataFolder() ."AbgegrieftHD.png")) {
                unlink($this->getDataFolder() . "AbgegrieftHD.png");
            }
        }

        if (file_exists($skinDir."GommeHD.png")) {
            if (file_exists($this->getDataFolder() ."GommeHD.png")) {
                unlink($this->getDataFolder() . "GommeHD.png");
            }
        }

        if (file_exists($this->getDataFolder()."Geometry/"."Normal.json")) {
            if (file_exists($this->getDataFolder() . "Normal.json")) {
                unlink($this->getDataFolder() . "Normal.json");
            }
        }

        if (file_exists($this->getDataFolder()."Geometry/"."Slim.json")) {
            if (file_exists($this->getDataFolder() . "Slim.json")) {
                unlink($this->getDataFolder() . "Slim.json");
            }
        }
    }

    public static function skinMetaDataToJsonSave(string $skinId, string $geometryName, string $geometryData, string $savePath) : void {
        $jsonData = json_encode([
            "skinId" => $skinId,
            "geometryName" => $geometryName,
            "geometryData" => $geometryData
        ]);
        if ($jsonData === false) {
            throw new Exception("JSON encoding failed!");
        }
        if (file_put_contents($savePath, $jsonData) === false) {
            throw new Exception("Saving JSON file failed!");
        }
    }

    public static function skinMetaDataFromJsonFile(string $savePath, string $skinData) : Skin {
        $jsonData = file_get_contents($savePath);
        if ($jsonData === false) {
            throw new Exception("Reading file failed!");
        }
        $parsedData = json_decode($jsonData, true);
        if ($parsedData === null) {
            throw new Exception("JSON decoding failed!");
        }
        $skinId = $parsedData["skinId"];
        $geometryName = $parsedData["geometryName"];
        $geometryData = $parsedData["geometryData"];
        return new Skin($skinId, $skinData, "", $geometryName, $geometryData);
    }

    public function getTempFile(string $fileName) : string {
        return $this->getDataFolder() . "temp/" . $fileName;
    }

    public function getGeoFile(string $fileName) : string {
        return $this->getDataFolder() . "Geometry/" . $fileName;
    }
}
