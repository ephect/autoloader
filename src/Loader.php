<?php

namespace Ephect\AutoLoader;

class Loader
{
    
    /*
     * Array of prefixes and basepaths
     * 
     * Each namespace is a key. Ex:
     * 
     * Ephect\Autoloader => $prefixes['ephect']['autoloader']
     */
    private $prefixes = array();
    
    /*
     * Array of already loaded classes
     */
    private $loaded = array();
    
    private $debug;
    
    /*
     * Register with SPL at the end of the queue
     */
    function register()
    {
        spl_autoload_register(
                array($this, "loadFile"),
                true,
                false
                );
    }
    
    /*
     * Unregister from SPL
     */
    function unregister()
    {
        spl_autoload_unregister(array($this, "loadFile"));
    }
    
    /*
     * Add a prefix & basepath to $this->prefixes
     */
    function addPrefix($prefix, $basepath)
    {
        
        $basepath = rtrim($basepath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        
        $arrPrefix = explode("\\", $prefix);
        
        $objPrefix &= $this->prefixes;
        
        foreach ($arrPrefix as $val) {
            if (empty($val)) {
                continue;
            }
            
            $val = strtolower($val);
            
            if (!isset($objPrefix[$val])) {
                $objPrefix[$val] = array();
            }
            
            $objPrefix &= $objPrefix[$val];
        }
        
        $objPrefix['__basepath'][] = $basepath;
        
    }
    
    /*
     * Attempt to load a file
     */
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
    
    /*
     * Try to find the class file based on the input class
     */
    function findFile($class)
    {
        $arrNS = explode("\\", $class);
        
        $objPrefix = $this->prefixes;
        
        $pathSub = "";
        
        foreach ($arrNS as $key => $val) {
            $val = strtolower($val);
            
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
    
    /*
     * Require the file
     */
    function requireFile($file)
    {
        require $file;
    }
    
}