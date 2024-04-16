<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

class CustomEnchantsManager {

    private static $instance;

    /**
     * @var array|CustomEnchant[]
     */
    private $customEnchants = [];

    private const HIGH_END = [
        CustomEnchant::RARITY_COMMON => 0,
        CustomEnchant::RARITY_UNCOMMON => 6,
        CustomEnchant::RARITY_RARE => 5,
        CustomEnchant::RARITY_VERY_RARE => 4,
        CustomEnchant::RARITY_MYTHIC => 3,
        CustomEnchant::RARITY_LEGENDARY => 1,
    ];

    public function getRandomEnchantment(bool $highEnd) : ?CustomEnchant {
        $array = [];
        foreach($this->customEnchants as $customEnchant){
            $rarity = $customEnchant->getRarity();
            if($highEnd){
                $rarity = self::HIGH_END[$rarity];
            }
            //if($rarity === 10) $rarity = 7;
            //if($rarity === 2) $rarity = 3;
            $array = array_merge($array, array_fill(0, $rarity, $customEnchant));
        }

        $enchant = $array[array_rand($array)];
        if(!empty($array)){
            return clone $enchant;
        }

        return null;
    }

    public function getRandomEnchantWithRarity(int $rarity) : ?CustomEnchant {
        $array = $this->customEnchants;
        shuffle($array);
        foreach($array as $enchant){
            if($enchant->getRarity() === $rarity){
                return clone $enchant;
            }
        }

        return null;
    }

    public function getAllIds() : array {
        $str = [];
        foreach($this->customEnchants as $enchant){
            $str[] = (string)$enchant->getId();
        }

        return $str;
    }

    public function getFromName(string $name) : ?CustomEnchant{
        foreach($this->customEnchants as $ce){
            if(strtolower($ce->getName()) === strtolower($name)) {
                return clone $ce;
            }
        }

        return null;
    }

    /**
     * @return array|CustomEnchant[]
     */
    public function getAll() : array {
        return $this->customEnchants;
    }

    public function get(int $id) : ?CustomEnchant {
        return isset($this->customEnchants[$id]) ? clone $this->customEnchants[$id] : null;
    }

    public function exist(int $id) : bool {
        return isset($this->customEnchants[$id]);
    }

    public function register(CustomEnchant $enchant) : void {
        $this->customEnchants[$enchant->getId()] = $enchant;
    }

    public function registerDefaults() : void {
        $this->register(new DrillerEnchant()); // legendary
        $this->register(new ProfitEnchant()); //
        $this->register(new FusionEnchant());
        //$this->register(new FreezeEnchant());
        //$this->register(new IceThornsEnchant());
        $this->register(new UnbreakingEnchant());
        $this->register(new StardustExtractionEnchant());
        $this->register(new ExtractionEnchant());
        $this->register(new AutoRepairEnchant());
        $this->register(new HealthEnchant());
        $this->register(new LeaperEnchant());
        $this->register(new RunnerEnchant());
        $this->register(new NightvisionEnchant());
        $this->register(new FireAspectEnchant());
        $this->register(new FireThornsEnchant());
        $this->register(new HeatShieldEnchant());
        $this->register(new DeathbringerEnchant());
        $this->register(new DamageEnchant());
        $this->register(new BlessingEnchant());
        $this->register(new PoisonEnchant());
        $this->register(new LightningEnchant());
        //$this->register(new DeflectEnchant());
        $this->register(new TankEnchant());
        $this->register(new ObsidianBreakerEnchant());
        $this->register(new LifestealEnchant());
        //$this->register(new ExplosiveEnchant());
        $this->register(new ThoughnessEnchant());
        $this->register(new XPExtractionEnchant());
        $this->register(new AerialEnchant());
        $this->register(new CriticalEnchant());
        $this->register(new InsulatorEnchant());
    }

    public static function getInstance() : self {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}