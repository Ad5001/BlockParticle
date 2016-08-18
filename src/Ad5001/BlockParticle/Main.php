<?php
namespace Ad5001\BlockParticle ; 
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\level\Position;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\level\particle\{AngryVillagerParticle,BubbleParticle,CriticalParticle,DestroyBlockParticle,DustParticle,EnchantmentTableParticle,EnchantParticle,EntityFlameParticle,ExplodeParticle,FlameParticle,HappyVillagerParticle,HeartParticle,InkParticle,InstantEnchantParticle,ItemBreakParticle,LavaDripParticle,LavaParticle,MobSpellParticle,PortalParticle,RainSplashParticle,RedstoneParticle,SmokeParticle,SpellParticle,SplashParticle,SporeParticle,TerrainParticle,WaterDripParticle,WaterParticle,WhiteSmokeParticle};
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Server;
use pocketmine\Player;


class Main extends PluginBase implements Listener{
    public function onEnable(){
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new ParticleTask($this), 10);
        $this->cfg = yaml_parse(file_get_contents($this->getDataFolder() . "config.yml"));
    }


    public function onDisable() {
        file_put_contents($this->getDataFolder() . "config.yml", yaml_emit($this->cfg));
    }


    public function onBlockPlace(\pocketmine\event\block\BlockPlaceEvent $event) {
        if($event->getBlock()->getId() == Item::EMERALD_BLOCK){
            $iih = $event->getPlayer()->getInventory()->getItemInHand();
            $nbt = $iih->getNamedTag();
            if(isset($nbt->isSolid) and isset($nbt->particle)) {
                if($nbt->isSolid == new StringTag("isSolid", "true")) {
                    $event->getPlayer()->getLevel()->setBlock($event->getBlock(), new Block(95, 0));
                }
                $this->cfg[$this->strFromPos($event->getBlock())] = $nbt->particle->getValue();
                $event->getPlayer()->sendMessage("ParticleBlock has been place !");
                $event->setCancelled();
            }
        }
    }
 


 public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
   switch($cmd->getName()){
    case "getparticleblock":
    if(!isset($args[0])) {
        return false;
    } else {
        if(!isset($args[1])) {
            $args[1] = "true";
        }
        switch(strtolower($args[0])) { // Did this for the next stuff so I don't need to rewrite :)
            case "angryvillager":
            case "angry_villager":
            break;
            case "bubble":
            break;
            case "critical":
            break;
            case "destroyblock":
            case "destroy_block":
            if(isset($args[2])) {
                $args[0] = strtolower($args[0]) . ":" . Item::fromString($args[2])->getId() . ":" . Item::fromString($args[2])->getDamage();
            }
            break;
            case "dust":
            break;
            case "enchantmenttable":
            case "enchantment_table":
            break;
            case "enchant":
            break;
            case "flame1":
            case "entityflame":
            case "entity_flame":
            break;
            case "flame2":
            case "flame":
            break;
            case "happy_villager";
            case "happyvillager":
            break;
            case "heart":
            break;
            case "ink":
            break;
            case "instant_enchant":
            case "instantenchant":
            break;
            case "itembreak":
            case "item_break":
            if(isset($args[2])) {
                $args[0] = strtolower($args[0]) . ":" . Item::fromString($args[2])->getId() . ":" . Item::fromString($args[2])->getDamage();
            }
            break;
            case "explode":
            break;
            case "lava_drip":
            case 'lavadrip':
            break;
            case "lava":
            break;
            case "mobspell":
            case "mob_spell":
            break;
            case "portal":
            break;
            case "rain":
            break;
            case "redstone":
            break;
            case "smoke":
            break;
            case "spell":
            break;
            case "splash":
            break;
            case "spore":
            break;
            case "town":
            case "terrain":
            if(isset($args[2])) {
                $args[0] = strtolower($args[0]) . ":" . Item::fromString($args[2])->getId() . ":" . Item::fromString($args[2])->getDamage();
            }
            break;
            case "waterdrip":
            case "water_drip":
            break;
            case "water":
            break;
            case "white_smoke":
            case "whitesmoke":
            break;
            default:
            $sender->sendMessage("No particule found with name $args[0]");
            return true;
            break;
        }
        $b = Item::get(Item::EMERALD_BLOCK, 0, 1);
        $b->setNamedTag(NBT::parseJSON("{display:{Name:\"§r§aParticleBlock " . strtolower($args[0]) . " | '". ($args[1] == "true" ? "Solid" : "Transparent") . "\"},isSolid:\"$args[1]\",particle:\"" . strtolower($args[0]) . "\"}"));
        $sender->getInventory()->addItem($b);
        $sender->sendMessage("You got yo're particle block !");
        return true;
    }
}
return false;
 }



 public function posFromStr(String $str): Position {
     $parts = explode("_", $str);
     return new Position($parts[0], $parts[1], $parts[2], $this->getServer()->getLevelByName($parts[3]));
 }



 public function strFromPos(Position $pos): String {
     return $pos->x ."_".$pos->y."_".$pos->z."_".$pos->getLevel()->getName();
 }
}


class ParticleTask extends \pocketmine\scheduler\PluginTask {

    public function __construct(Main $main) {
        $this->main = $main;
        parent::__construct($main);
    }


    public function onRun($tick) {
        try {
            unset($this->main->cfg["version"]);
        } catch(\Error $e) {}
        foreach($this->main->cfg as $spos => $particle) {
            if(strpos($spos, "_") == false) {
                return true;
            }
            $pos = $this->main->posFromStr($spos);
            if(strpos($particle, ":") !== false) {
                list($particle, $block, $damage) = explode(":", $particle);
            }
            switch($particle) {
            case "angryvillager":
            case "angry_villager":
            $particle = new AngryVillagerParticle($pos);
            break;
            case "bubble":
            $particle = new BubbleParticle($pos);
            break;
            case "critical":
            $particle = new CriticalParticle($pos);
            break;
            case "destroyblock":
            case "destroy_block":
            $particle = new DestroyBlockParticle($pos, new Block($block, $damage));
            break;
            case "dust":
            break;
            case "enchantmenttable":
            case "enchantment_table":
            $particle = new EnchantmentTableParticle($pos);
            break;
            case "enchant":
            $particle = new EnchantParticle($pos);
            break;
            case "flame1":
            case "entityflame":
            case "entity_flame":
            $particle = new EntityFlameParticle($pos);
            break;
            case "flame2":
            case "flame":
            $particle = new CFlameParticle($pos);
            break;
            case "happy_villager";
            case "happyvillager":
            $particle = new HappyVillagerParticle($pos);
            break;
            case "heart":
            $particle = new HeartParticle($pos);
            break;
            case "ink":
            $particle = new InkParticle($pos);
            break;
            case "instant_enchant":
            case "instantenchant":
            $particle = new InstantEnchantParticle($pos);
            break;
            case "itembreak":
            case "item_break":
            $particle = new ItemBreakParticle($pos, Item::get($block, $damage));
            break;
            case "explode":
            $particle = new ExplodeParticle($pos);
            break;
            case "lava_drip":
            case 'lavadrip':
            $particle = new LavaDripParticle($pos);
            break;
            case "lava":
            $particle = new LavaParticle($pos);
            break;
            case "mobspell":
            case "mob_spell":
            $particle = new MobSpellParticle($pos);
            break;
            case "portal":
            $particle = new PortalParticle($pos);
            break;
            case "rain":
            $particle = new RainSplashParticle($pos);
            break;
            case "redstone":
            $particle = new RedstoneParticle($pos);
            break;
            case "smoke":
            $particle = new SmokeParticle($pos);
            break;
            case "spell":
            $particle = new SpellParticle($pos);
            break;
            case "splash":
            $particle = new SplashParticle($pos);
            break;
            case "spore":
            $particle = new SporeParticle($pos);
            break;
            case "town":
            case "terrain":
            $particle = new TerrainParticle($pos, new Block($block, $damage));
            break;
            case "waterdrip":
            case "water_drip":
            $particle = new WaterDripParticle($pos);
            break;
            case "water":
            $particle = new WaterParticle($pos);
            break;
            case "white_smoke":
            case "whitesmoke":
            $particle = new WhiteSmokeParticle($pos);
            break;
            break;
            }
            $random = new \pocketmine\utils\Random((int) 1000 + mt_rand());
            for($i = 0; $i < 100; ++$i){
			  $particle->setComponents(
				$pos->x + (rand(0, 10)/10),
				$pos->y + (rand(0, 10)/10),
				$pos->z + (rand(0, 10)/10)
			  );
			$pos->getLevel()->addParticle($particle);
            }
        }
    }
}