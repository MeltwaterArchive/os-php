<?php
/**
 * Copyright (c) 2013-present Mediasift Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   Os
 * @author    Michael Heap <michael.heap@datasift.com>
 * @copyright 2013-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://github.com/datasift/os-php
 */

namespace Datasift\Os;

use Datasift\Os\OsInterface;
use Datasift\Os\OsException;

/**
 * Base
 *
 * @author Michael Heap <michael.heap@datasift.com>
 */
class Base implements OsInterface {

    /**
     * commandTranslations
     *
     * A key=>val list of commands to translate
     * e.g. ifconfig => /sbin/ifconfig
     *
     * @var array
     */
    protected $commandTranslations = array();

    /**
     * name
     *
     * @var string The name of the OS
     */
    private $name;

    /**
     * version
     *
     * @var string The version of the OS
     */
    private $version;

    /**
     * setName
     *
     * @param string $name The name of the OS
     *
     * @return void
     */
    public function setName($name){
        $this->name = $name;
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /**
     * setVersion
     *
     * @param string $name The version of the OS
     *
     * @return void
     */
    public function setVersion($version){
        $this->version = $version;
    }

    /**
     * getVersion
     *
     * @return string
     */
    public function getVersion(){
        return $this->version;
    }

    /**
     * runCommand
     *
     * @param string $command Shell command to run
     *
     * @return string
     */
    public function runCommand($command){

        // Normalise it for our lookup
        $command = trim($command);

        // Split it into a binary + params
        $parts = explode(" ", $command, 2);
        $bin = $parts[0];

        // We might not have any params
        if (isset($parts[1])){
            $params = $parts[1];
        } else {
            $params = '';
        }

        // Do we translate this internally?
        if (isset($this->commandTranslations[$bin])){
            $bin = $this->commandTranslations[$bin];
        }

        // Run the command and return the results
        return `$bin $params`;
    }

    /**
     * getPossibleClassNames
     *
     * Get the possible class names for the current OS
     *
     * @return array
     */
    public function getPossibleClassNames(){
        return static::generatePossibleClassNames($this->getName(), $this->getVersion());
    }

    /**
     * fromDistribution
     *
     * @param mixed $name OS Name
     * @param mixed $version OS Version
     *
     * @return Datasift\Os\OsInterface;
     */
    public static function fromDistribution($name, $version='', $className = null){

        // If we were provided a class name, use that one
        if ($className){
            $possibleClasses = array($className);
        // Otherwise, try and generate them
        } else {
            // Which possible classes could we use?
            $possibleClasses = static::generatePossibleClassNames($name, $version);
        }

        // Loop over them and see which we know about
        foreach ($possibleClasses as $className){

            // Make sure we try every OS type
            foreach (array("Linux", "Darwin") as $osType){
                // Make sure the class name is fully qualified
                $fullClass = "Datasift\\Os\\".$osType."\\".$className;

                // If it exists, create a new instance of it
                // And set the relevant instance params
                if (class_exists($fullClass)) {
                    $os = new $fullClass;
                    $os->setName($name);
                    $os->setVersion($version);
                    return $os;
                }
            }
        }

        throw new OsException("Unable to create OS from distribution");

    }

    /**
     * fromIssueFile
     *
     * @param mixed $contents The contents of the issue file
     *
     * @return Datasift\Os\OsInterface;
     */
    public static function fromIssueFile($contents){

        // Get the details of the OS
        $details = static::parseIssueFile($contents);

        try {
            return static::fromDistribution($details['name'], $details['version']);
        } catch(Exception $e){
            throw new OsException("Unable to create OS from issue file");
        }


    }

    /**
     * matchesIssueFile
     *
     * @param mixed $contents The contents of the issue file
     *
     * @return bool
     */
    public static function matchesIssueFile($contents){

        // Can we parse the issue file?
        if (static::parseIssueFile($contents)){
            return true;
        }

        return false;
    }

    /**
     * generatePossibleClassNames
     *
     * @param mixed $name OS name
     * @param mixed $version OS Version
     *
     * @return array Array of possible class names for the provided OS
     */
    private static function generatePossibleClassNames($name, $version){

        // Which classes do we want to search? None by default
        $possibleNames = array();

        // Do we have a exact version
        $fullVersion = str_replace(".","", $version);
        $possibleNames[] = $name.$fullVersion;

        // Or just a major version?
        $versionParts = explode(".", $version);
        $majorVersion = $versionParts[0];
        $possibleNames[] = $name.$majorVersion;

        // Or just the distro name?
        $possibleNames[] = $name;

        // Return a list of possible classes to check, most specific first
        return $possibleNames;
    }

}
