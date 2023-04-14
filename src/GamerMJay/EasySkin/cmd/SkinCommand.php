<?php

declare(strict_types=1);

namespace GamerMJay\EasySkin\cmd;


use Exception;
use Himbeer\LibSkin\SkinConverter;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;
use GamerMJay\EasySkin\Main;
use pocketmine\plugin\PluginOwned;

class SkinCommand extends Command implements PluginOwned {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("easyskin", "Open the Skin menu", "/skin", ["skin"],);
        $this->setPermission("easyskin.use");
        $this->plugin = $plugin;
    }

    public function getOwningPlugin(): Main
    {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->testPermission($sender)) {
            return;
        }
        if($sender instanceof Player) {
            $this->EasySkinMain($sender);
        } else {
            $sender->sendMessage("Run this command InGame!");
        }
    }

    public function EasySkinMain($player) {
        $form = new SimpleForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null) {
                return;
            }
            switch ($result) {
                case 0:
                    $this->openSkinList($player);
                    break;
                case 1:
                    try {
                        $baseFileName = strtolower($player->getName());
                        $imageFileName = $baseFileName . ".png";
                        $geoFileName = $baseFileName . ".json";
                        $fullImagePath = $this->getOwningPlugin()->getTempFile($imageFileName);
                        $fullGeoPath = $this->getOwningPlugin()->getTempFile($geoFileName);
                        $skinData = SkinConverter::imageToSkinDataFromPngPath($fullImagePath);
                        self::changeSkinAndGeo($player, $skinData, $fullGeoPath);
                        $player->sendMessage($this->getOwningPlugin()->cfg->getNested("messages.skin-reset"));
                    } catch (Exception $exception) {
                        $player->sendMessage("§cAn unknown error occurred!");
                    }
                    break;
                case 2:
                    $this->choiceGeo($player);
                    break;

            }
        });
        $form->setTitle($this->getOwningPlugin()->cfg->getNested("messages.forms.main-form.title"));
        $form->addButton($this->getOwningPlugin()->cfg->getNested("messages.forms.main-form.button-1"));
        $form->addButton($this->getOwningPlugin()->cfg->getNested("messages.forms.main-form.button-2"));
        $form->addButton($this->getOwningPlugin()->cfg->getNested("messages.forms.main-form.button-3"));
        $form->sendToPlayer($player);
    }

    public function openSkinList($player) {
        $skinFolder = $this->getOwningPlugin()->getDataFolder() . "SkinData/";
        $skins = array_values(array_diff(scandir($skinFolder), array('.', '..')));
        $form = new SimpleForm(function (Player $player, $data = null) use ($skins, $skinFolder) {
            if ($data === null) {
                return;
            }

            $skinName = $skins[$data];
            $skinPath = $skinFolder . $skinName;
            $skinNameWithoutExt = substr($skinName, 0, -4);
            if (is_file($skinPath)) {
                $skinData = SkinConverter::imageToSkinDataFromPngPath($skinPath);
                self::changeSkin($player, $skinData);
                $msg = $this->getOwningPlugin()->cfg->getNested("messages.skin-success");
                $msg = str_replace("{name}", $skinNameWithoutExt, $msg);
                $player->sendMessage($msg);
            } else {
                $player->sendMessage($this->getOwningPlugin()->cfg->getNested("messages.skin-not-exist"));
            }
        });
        $form->setTitle($this->getOwningPlugin()->cfg->getNested("messages.forms.skin-list-form.title"));
        $form->setContent($this->getOwningPlugin()->cfg->getNested("messages.forms.skin-list-form.description"));
        foreach ($skins as $key => $value) {
            if (str_contains($value, ".png")) {
                $form->addButton(str_replace(".png", "", $value));
            }
        }
        $form->sendToPlayer($player);

    }

    public function choiceGeo($player) {
        $form = new SimpleForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null) {
                return;
            }
            switch ($result) {
                case 0:
                    try {
                        $baseFileName = "Slim";
                        $geoFileName = $baseFileName . ".json";
                        $fullGeoPath = $this->getOwningPlugin()->getGeoFile($geoFileName);
                        self::changeGeo($player, $fullGeoPath);
                        $player->sendMessage($this->getOwningPlugin()->cfg->getNested("messages.geo-change-slim"));
                    } catch (Exception $exception) {
                        $player->sendMessage("§cAn unknown error occurred!");
                    }
                    break;
                case 1:
                    try {
                        $baseFileName = "Normal";
                        $geoFileName = $baseFileName . ".json";
                        $fullGeoPath = $this->getOwningPlugin()->getGeoFile($geoFileName);
                        self::changeGeo($player, $fullGeoPath);
                        $player->sendMessage($this->getOwningPlugin()->cfg->getNested("messages.geo-change-normal"));
                    } catch (Exception $exception) {
                        $player->sendMessage("§cAn unknown error occurred!");
                    }
            }
        });
        $form->setTitle($this->getOwningPlugin()->cfg->getNested("messages.forms.choice-geo-form.title"));
        $form->addButton($this->getOwningPlugin()->cfg->getNested("messages.forms.choice-geo-form.button-1"));
        $form->addButton($this->getOwningPlugin()->cfg->getNested("messages.forms.choice-geo-form.button-2"));
        $form->sendToPlayer($player);
    }

    private function changeSkin(Player $player, string $skinName) : void {
        try {
            $player->setSkin(new Skin($player->getSkin()->getSkinId(), $skinName));
        } catch (Exception $exception) {
            $player->sendMessage("§cAn unknown error occurred!");
        }
        $player->sendSkin();
    }

    private function changeSkinAndGeo(Player $player, string $skinData, string $fullGeoPath) : void {
        $player->setSkin($this->getOwningPlugin()->skinMetaDataFromJsonFile($fullGeoPath, $skinData));
        $player->sendSkin();
    }

    private function changeGeo(Player $player, string $fullGeoPath) : void {
        $skinData = $player->getSkin()->getSkinData();
        $player->setSkin($this->getOwningPlugin()->skinMetaDataFromJsonFile($fullGeoPath, $skinData));
        $player->sendSkin();
    }

}
