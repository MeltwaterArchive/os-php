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

namespace Datasift;

/**
 * Os
 *
 * Factory class used for detecting current OS and/or
 * returning an object that represents the current OS
 *
 * @author Michael Heap <michael.heap@datasift.com>
 */
class Os {

    /**
     * getOs
     *
     * @param string $osName The name of the OS to create
     * @param string $osVersion The version of the OS to create
     *
     * @return Datasift\Os
     */
    public static function getOs($osName = null, $osVersion = null){

        // If they provided an OS, use it
        if ($osName){
            return Os\Base::fromDistribution($osName, $osVersion);
        }

        // Otherwise, try and detect it automatically
        return static::detectOs();
    }

    /**
     * detectOs
     *
     * @return string The name and version of the current OS
     */
    public static function detectOs(){

        // Is it a linux machine?
        if ($os = Os\Linux::detect()){
            return $os;
        }

        // Is it an OSX machine?
        if ($os = Os\Darwin::detect()){
            return $os;
        }

        throw new \Exception("Unable to detect OS");
    }
}
