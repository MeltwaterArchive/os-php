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

use Datasift\Os\Base;
use DirectoryIterator;

/**
 * Darwin
 *
 * @author Michael Heap <michael.heap@datasift.com>
 */
class Darwin extends Base {

    /**
     * detect
     *
     * Detect the current OS
     *
     * @return Datasift\Os\OsInterface|false An instance of the OS that we're on. False if no match
     */
    public static function detect(){
        // To see if we're on OSX, we use uname -s
        $os = php_uname("s");

        // If we're not on a Darwin machine, return false
        if ($os != "Darwin"){
            return false;
        }

        // If we are on a Darwin machine, try and work out which version
        $currentVersion = php_uname("r");

        // We have the complete version, but we only want major.minor
        $versionParts = explode(".", $currentVersion);
        $currentVersion = $versionParts[0].'.'.$versionParts[1];


        // Let's see if we have a match
        $availableDistributions = array();

        // Look at all the versions of OSX we know about
        foreach (new DirectoryIterator(__DIR__.'/Darwin') as $fileInfo) {
            // If it's . or .., skip it
            if ($fileInfo->isDot()){
                continue;
            }

            // Otherwise, add it to our list of available distributions
            $availableDistributions[] = $fileInfo->getBasename(".php");
        }

        // For each of the available distributions, check if the
        // data in $releaseFile matches what the distro knows
        foreach ($availableDistributions as $distribution){
            $className = "Datasift\\Os\\Darwin\\".$distribution;

            // If the current OS matches the version in the potential
            // class, create a new instance of it
            if ($className::VERSION == $currentVersion){
                return $className::fromDistribution($os, $currentVersion, $distribution);
            }
        }

        // If we get this far, we're using a version of OSX, but we're not sure
        // which one it is
        return Darwin\Generic::fromDistribution($os, '', $distribution);

        return false;
    }

}
