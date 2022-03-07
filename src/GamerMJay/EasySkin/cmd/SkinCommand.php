<?php

declare(strict_types=1);

namespace GamerMJay\EasySkin\cmd;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;
use GamerMJay\EasySkin\Main;

class SkinCommand extends Command {

    public function __construct(Main $plugin) {
        parent::__construct("skin", "Open the Skin menu", "/skin", ["skin"]);
        $this->setPermission("skin.use");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->testPermission($sender)) {
            return false;
        }
        if($sender instanceof Player) {
            $this->openList($sender);
        } else {
            $sender->sendMessage("Run this command InGame!");
        }
        return false;
    }

    public function openList($player) {
        $form = new SimpleForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    $this->openSkins($player);
                    break;
                case 1:
                    $player->setSkin(new Skin("Standard_Custom", $this->plugin->skins[$player->getName()]));
                    $player->sendSkin();
                    $player->sendMessage($this->plugin->cfg->getNested("messages.skin-reset"));
            }
        });
        $form->setTitle($this->plugin->cfg->getNested("messages.forms.main-form.title"));
        $form->addButton($this->plugin->cfg->getNested("messages.forms.main-form.button-1"));
        $form->addButton($this->plugin->cfg->getNested("messages.forms.main-form.button-2"));
        $form->sendToPlayer($player);
    }

    public function openSkins($player) {
        $form = new SimpleForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            $skin = $data;
            if(file_exists($this->plugin->getDataFolder() . $data . ".png")) {
                $player->setSkin(new Skin("Standard_Custom", $this->plugin->createSkin($skin)));
                $player->sendSkin();
                $msg = $this->plugin->cfg->getNested("messages.skin-success");
                $msg = str_replace("{name}", $skin, $msg);
                $player->sendMessage($msg);
            } else {
                $player->sendMessage($this->plugin->cfg->getNested("messages.skin-not-exist"));
            }
        });
        $form->setTitle($this->plugin->cfg->getNested("messages.forms.skin-list-form.title"));
        $form->setContent($this->plugin->cfg->getNested("messages.forms.skin-list-form.description"));
        foreach ($this->plugin->cfg->get("skins") as $skin) {
            $form->addButton("$skin", -1, "", $skin);
        }
        $form->sendToPlayer($player);
    }
}
