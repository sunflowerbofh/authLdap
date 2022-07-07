<?php

declare(strict_types=1);

/**
 * Copyright (c) Andreas Heigl<andreas@heigl.org>
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @since     19.07.2020
 * @link      http://github.com/heiglandreas/authLDAP
 */

namespace Org_Heigl\AuthLdap;

use Org_Heigl\AuthLdap\Exception\InvalidLdapUri;
use function getenv;
use function preg_replace;
use function urlencode;

final class LdapUri
{
    private $uri;

    private function __construct(string $uri)
    {
        if (! preg_match('/^(ldap|ldaps|env)/', $uri)) {
            throw InvalidLdapUri::fromLdapUriString($uri);
        }
        $this->uri = $uri;
    }

    public static function fromString(string $uri): LdapUri
    {
        return new LdapUri($uri);
    }

    public function toString(): string
    {
        $uri = $this->uri;
        if (0 === strpos($uri, 'env:')) {
            $uri = getenv(substr($this->uri, 4));
        }

        $uri = preg_replace_callback('/%env:([^%]+)%/', function (array $matches) {
            return rawurlencode(getenv($matches[1]));
        }, $uri);

        return $uri;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
