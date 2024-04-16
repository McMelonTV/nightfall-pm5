<?php

declare(strict_types=1);

namespace AndreasHGK\Core\utils;

final class FileUtils {

    private function __construct(){
        //NOOP
    }

    public static function MakeJSON(string $file) : string {
        return $file.".json";
    }

    public static function MakeYAML(string $file) : string {
        return $file.".yml";
    }

    public static function isFolderEmpty(string $dir) : bool {
        return count(scandir($dir)) <= 2;
    }

    public static function deleteFolder(string $dir) : void {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach($files as $file){
            if(is_dir("$dir/$file")){
                if(!self::isFolderEmpty("$dir/$file")) self::deleteFolder("$dir/$file");
                rmdir("$dir/$file");
            }else{
                unlink("$dir/$file");
            }
        }
    }

}