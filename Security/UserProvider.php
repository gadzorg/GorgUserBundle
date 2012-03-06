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

use Doctrine\Tests\ORM\Mapping\User;

use Gorg\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $entityManager;
    
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function loadUserByUsername($hruid)
    {
        $userExist = $this->entityManager->getRepository('User')->isRegistered($hruid);
        if($userExist != 1)
        {
            throw new UsernameNotFoundException('Could not find user. Sorry!');
        }
        
        $user = new User();
        $user->setHruid($hruid);
        
        return $user;
    }

    public function refreshUser(User $user) {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class === 'Gorg\Bundle\UserBundle\Entity\User';
    }
}


/* vim:set et sw=4 sts=4 ts=4: */