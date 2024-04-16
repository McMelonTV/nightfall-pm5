<?php

declare(strict_types=1);

namespace AndreasHGK\Core\user;

class BannedUser {

    private int $expire = -1;

    private int $banDate = 0;

    private string $name;

    private string $reason = "";

    private string $banner = "the nightfall team";

    private bool $superban = false;

    public function getSuperban() : bool {
        return $this->superban;
    }

    public function setSuperban(bool $superban) : void {
        $this->superban = $superban;
    }

    public function getBanner() : string {
        return $this->banner;
    }

    public function setBanner(string $banner) : void {
        $this->banner = $banner;
    }

    public function getBanDate() : int {
        return $this->banDate;
    }

    public function getReason() : string {
        return $this->reason;
    }

    public function setReason(string $reason) : void {
        $this->reason = $reason;
    }

    public function isTempBan() : bool {
        return $this->expire !== -1;
    }

    public function getBanExpire() : int {
        return $this->expire;
    }

    public function setBanExpire(int $banExpire) : void {
        $this->expire = $banExpire;
    }

    public function getName() : string {
        return $this->name;
    }

    public function __construct(string $name, int $banDate, string $reason = "", int $expire = -1){
        $this->name = $name;
        $this->banDate = $banDate;
        $this->expire = $expire;
        $this->reason = $reason;
    }
}