<?php
/***************************************************************************
 * Copyright (C) 1999-2011 Gadz.org                                        *
 * http://opensource.gadz.org/                                             *
 *                                                                         *
 * This program is free software; you can redistribute it and/or modify    *
 * it under the terms of the GNU General Public License as published by    *
 * the Free Software Foundation; either version 2 of the License, or       *
 * (at your option) any later version.                                     *
 *                                                                         *
 * This program is distributed in the hope that it will be useful,         *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of          *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the            *
 * GNU General Public License for more details.                            *
 *                                                                         *
 * You should have received a copy of the GNU General Public License       *
 * along with this program; if not, write to the Free Software             *
 * Foundation, Inc.,                                                       *
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA                   *
 ***************************************************************************/

namespace Gorg\Bundle\UserBundle\Security;


use Gorg\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider implements UserProviderInterface
{
    private $logger;
    private $buzz;
    private $apiUser;
    private $apiPassword;
    private $apiPath;
    private $apiServer;
    
    public function __construct($logger, $buzz, $apiUser, $apiPassword, $apiPath, $apiServer)
    {
        $this->logger      = $logger;
        $this->buzz        = $buzz;
        $this->apiUser     = $apiUser;
        $this->apiPassword = $apiPassword;
        $this->apiPath     = $apiPath;
        $this->apiServer   = $apiServer;
    }
    
    public function loadUserByUsername($hruid)
    {
       
       $request = new \Buzz\Message\Request('GET', $this->apiPath . '/accounts/' . $hruid . '/accounts.json', $this->apiServer);
       $response = new \Buzz\Message\Response();

       $request->addHeader('Authorization: Basic '.base64_encode($this->apiUser.':'.$this->apiPassword));
       
       $this->buzz->send($request, $response);
       $findUsers = json_decode($response->getContent());
       $userStdClass = $findUsers[0];
       return User::buildFromStdClass($userStdClass);
    }

    public function loadUserByUid($uid)
    {

        $user = User::buildFromRawData($this->webServiceConnection->retriveUserDataFromUid($uid));

        return $user;
    }

    public function refreshUser(UserInterface $user) {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class === 'Gorg\Bundle\UserBundle\Entity\User';
    }
}


/* vim:set et sw=4 sts=4 ts=4: */
