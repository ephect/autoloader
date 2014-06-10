<?php

namespace Ephect\AutoLoader;

class AutoLoader
{
    
    private $prefixes = array();
    
    private $loaded = array();
    
    private $debug;
    
    function register()
    {
        spl_autoload_register(
                array($this, "loadFile"),
                true,
                false
                );
    }
    
    function unregister()
    {
        spl_autoload_unregister(array($this, "loadFile"));
    }
    
    function addPrefix($prefix, $basepath)
    {
        
        $basepath = rtrim($basepath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        
        $arrPrefix = explode("\\", $prefix);
        
        $objPrefix &= $this->prefixes;
        
        foreach ($arrPrefix as $val) {
            if (empty($val)) {
                continue;
            }
            
            if (!isset($objPrefix[$val])) {
                $objPrefix[$val] = array();
            }
            
            $objPrefix &= $objPrefix[$val];
        }
        
        $objPrefix['__basepath'][] = $basepath;
        
    }
    
    function loadFile($class)
    {
        if (isset($this->loaded[$class])) {
            return true;
        }
        
        if ($this->findFile($class)) {
            $this->loaded[$class] = true;
            return true;
        }
        
        return false;
    }
    
    function findFile($class)
    {
        $arrNS = explode("\\", $class);
        
        $objPrefix = $this->prefixes;
        
        $pathSub = "";
        
        foreach ($arrNS as $key => $val) {
            if (!isset($objPrefix[$val])) {
                break;
            }
            
            $prefix[$key] = $objPrefix[$val];
            $curr = $key;
            $pathSub .= $val."\\";
            $pathLeft[$key] = $pathSub;
        }
        
        $i = $curr+1;
        
        while ($i--) {
            $k = $i-1;
            if (isset($prefix[$k]['__basepath'])) {
                $basepath = $prefix[$k]['__basepath'];
                $append = str_replace($pathLeft[$k], "", $class);
                $filepath = $basepath.str_replace("\\", DIRECTORY_SEPARATOR, $append).".php";
                if (file_exists($filepath)) {
                    $this->requireFile($filepath);
                    return true;
                }
            }
        }
        
        $this->debug[] = 'No basepath for class '.$class;
        return false;
        
    }
    
    function requireFile($file)
    {
        require $file;
    }
    
}