<?php

namespace Mrchlldev\Nickname;

use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\ModalForm;
use jojoe77777\FormAPI\CustomForm;

class Nick extends PluginBase {

    public function onEnable(): void {
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        if(!(is_numeric((float)(int)$this->config->get("min-nick"))) || !(is_numeric((int)$this->config->get("max-nick")))){
            $this->getServer()->getLogger()->warning("The config plugin of min-nick or max-nick is not numeric! disable plugin.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if($command->getName() === "nickname"){
            if($sender instanceof Player){
                $this->sendFirstForm($sender);
            } else {
                $sender->sendMessage("§cUse this command in-game only!");
            }
        }
        return true;
    }

   public function sendFirstForm(Player $player){
       $form = new SimpleForm(function(Player $player, $data){
           if($data === null){
               return;
           }
           switch($data){
               case 0:
                 if($player->hasPermission("nick.use")){
                     $this->sendCustomNickMenu($player);
                 } else {
                   $player->sendMessage($this->config->get("no-permission-msg"));
                 }
               break;
               case 1:
                 if($player->hasPermission("nick.color.use")){
                     $this->sendNickColorMenu($player);
                 } else {
                   $player->sendMessage($this->config->get("no-permission-msg"));
                 }
               break;
               case 2:
                 $name = $player->getName();
                 $player->setDisplayName(TextFormat::WHITE . $name);
                 $player->setNameTag(TextFormat::WHITE . $name);
               break;
           }
       });
       $form->setTitle("Nickname");
       $form->setContent("Change nickname or change nickname color here");
       if($player->hasPermission("nick.use")){
           $form->addButton("Custom Nickname");
       } else {
           $form->addButton("Custom Nickname\n§l§c!! LOCKED !!");
       }
       if($player->hasPermission("nick.color.use")){
           $form->addButton("Nickname Color");
       } else {
           $form->addButton("Nickname Color\n§l§c!! LOCKED !!");
       }
       $form->addButton("Reset");
       $form->addButton("Exit", 0, "textures/blocks/barrier");
       $player->sendForm($form);
   }

   public function sendCustomNickMenu(Player $player){
       $form = new CustomForm(function(Player $player, $data){
           if($data === null){
               $this->sendFirstForm($player);
               return;
           }
           if(!isset($data[0])){
               $player->sendMessage("§cPlease fill in the fields!");
             return true;
           }
           if($data === $player->getName()){
               $player->sendMessage("§cYou can't fill the field with your name!");
             return true;
           }
           if(strlen($data[0]) >= $this->config->get("max-nick")){
               $player->sendMessage("§cYour nickname is long. Maximum of nickname is 15.");
             return true;
           }
           if(strlen($data[0]) <= $this->config->get("min-nick")){
               $player->sendMessage("§cYour name is short. Minimum of nickname is 3.");
             return true;
           }
           $player->setDisplayName($data[0]);
           $player->setNameTag($data[0]);
           $player->sendMessage("§aSuccesfully change your player name to: " . $data[0]);
       });
       $form->setTitle("Custom Nickname");
       $form->addInput("Enter your custom name", "Mrchlldev");
       $player->sendForm($form);
   }

   public function sendNickColorMenu(Player $player){
       $form = new SimpleForm(function(Player $player, $data){
           if($data === null){
               $this->sendFirstForm($player);
               return;
           }
           switch($data){
               case 0:
                 $player->setNameTag(TextFormat::BLUE . $player->getDisplayName() . TextFormat::RESET);
                 $player->setDisplayName(TextFormat::BLUE . $player->getDisplayName() . TextFormat::RESET);
                 $player->sendMessage("§aSuccesfully changed your nickname to: " . TextFormat::BLUE . $player->getDisplayName());
               break;
               case 1:
                 $player->setNameTag(TextFormat::RED . $player->getDisplayName() . TextFormat::RESET);
                 $player->setDisplayName(TextFormat::RED . $player->getDisplayName() . TextFormat::RESET);
                 $player->sendMessage("§aSuccesfully changed your nickname to: " . TextFormat::RED . $player->getDisplayName() . TextFormat::RESET);
               break;
               case 2:
                 $player->setNameTag(TextFormat::YELLOW . $player->getDisplayName() . TextFormat::RESET);
                 $player->setDisplayName(TextFormat::YELLOW . $player->getDisplayName() . TextFormat::RESET);
                 $player->sendMessage("§aSuccesfully changed your nickname to: " . TextFormat::YELLOW . $player->getDisplayName());
               break;
               case 3:
                 $player->setNameTag(TextFormat::GREEN . $player->getDisplayName() . TextFormat::RESET);
                 $player->setDisplayName(TextFormat::GREEN . $player->getDisplayName() . TextFormat::RESET);
                 $player->sendMessage("§aSuccesfully changed your nickname to: " . TextFormat::GREEN . $player->getDisplayName());
               break;
               case 4:
                 $player->setNameTag(TextFormat::BLUE . $player->getDisplayName() . TextFormat::RESET);
                 $player->setDisplayName(TextFormat::LIGHT_PURPLE . $player->getDisplayName() . TextFormat::RESET);
                 $player->sendMessage("§aSuccesfully changed your nickname to: " . TextFormat::LIGHT_PURPLE . $player->getDisplayName());
               break;
               case 0:
                 $player->setNameTag(TextFormat::AQUA . $player->getDisplayName() . TextFormat::RESET);
                 $player->setDisplayName(TextFormat::AQUA . $player->getDisplayName() . TextFormat::RESET);
                 $player->sendMessage("§aSuccesfully changed your nickname to: " . TextFormat::AQUA . $player->getDisplayName());
               break;
           }
       });
       $form->setTitle("Nickname Color");
       $form->setContent("Select the nickname color that you will use");
       $form->addButton("Blue\n" . TextFormat::DARK_GRAY . "(" . TextFormat::BLUE . $player->getDisplayName() . TextFormat::DARK_GRAY . ")");
       $form->addButton("Red\n" . TextFormat::DARK_GRAY . "(" . TextFormat::RED . $player->getDisplayName() . TextFormat::DARK_GRAY . ")");
       $form->addButton("Yellow\n" . TextFormat::DARK_GRAY . "(" . TextFormat::YELLOW . $player->getDisplayName() . TextFormat::DARK_GRAY . ")");
       $form->addButton("Green\n" . TextFormat::DARK_GRAY . "(" . TextFormat::GREEN . $player->getDisplayName() . TextFormat::DARK_GRAY . ")");
       $form->addButton("Light Purple\n" . TextFormat::DARK_GRAY . "(" . TextFormat::LIGHT_PURPLE . $player->getDisplayName() . TextFormat::DARK_GRAY . ")");
       $form->addButton("Aqua\n" . TextFormat::DARK_GRAY . "(" . TextFormat::AQUA . $player->getDisplayName() . TextFormat::DARK_GRAY . ")");
       $player->sendForm($form);
   }

}