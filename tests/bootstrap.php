<?php
/**
 * Date: 11/21/14 3:09 PM
 *
 * ----
 *  
 * (c) Okanjo Partners Inc
 * https://okanjo.com
 * support@okanjo.com
 * 
 * https://github.com/okanjo/taxjar
 * 
 * ----
 * 
 * TL;DR? see: http://www.tldrlegal.com/license/mit-license
 * 
 * The MIT License (MIT)
 * Copyright (c) 2013 Okanjo Partners Inc.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * Class AutoLoader
 * Lovingly acquired from: http://jes.st/2011/phpunit-bootstrap-and-autoloading-classes/
 * Adapted to support name spaces
 */
class AutoLoader {

    static private $classNames = array();
    static private $namespacedClassNames = array();

    /**
     * Store the filename (sans extension) & full path of all ".php" files found
     */
    public static function registerDirectory($dirName, $namespace = '') {

        $di = new DirectoryIterator($dirName);
        foreach ($di as $file) {
            /** @var DirectoryIterator $file */
            if ($file->isDir() && !$file->isLink() && !$file->isDot()) {
                // recurse into directories other than a few special ones
                self::registerDirectory($file->getPathname(), (empty($namespace) ? '' : ($namespace.'\\')).$file->getBasename());
            } elseif (substr($file->getFilename(), -4) === '.php') {
                // save the class name / path of a .php file found
                $className = substr($file->getFilename(), 0, -4);
                AutoLoader::registerClass($className, $file->getPathname(), $namespace);
            }
        }
    }

    public static function registerClass($className, $fileName, $namespace = '') {
        AutoLoader::$classNames[$className] = $fileName;
        AutoLoader::$namespacedClassNames[$namespace.'\\'.$className] = $fileName;
    }

    public static function loadClass($className) {
        if (isset(AutoLoader::$classNames[$className])) {
            require_once(AutoLoader::$classNames[$className]);
        } else if (isset(AutoLoader::$namespacedClassNames[$className])) {
            require_once(AutoLoader::$namespacedClassNames[$className]);
        } else {
            error_log('bootstrap.php: Could not auto load class: '.$className);
        }
    }

}

// Register php auto loader
spl_autoload_register(array('AutoLoader', 'loadClass'));

// Change directory to tests (relative paths depend on this)
chdir(__DIR__);

// Load TaxJar Namespace
AutoLoader::registerDirectory('../src');

// Load UnitTest Namespace
AutoLoader::registerDirectory('.');

// Include the unit test config
require_once 'config.php';

