<?php
/***************************************************************************
 * Copyright (C) 1999-2012 Gadz.org                                        *
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
namespace Gorg\Bundle\UserBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class for a User
 *
 * @category Authentication
 * @package  UserBundle
 * @author   Mathieu GOULIN <mathieu.goulin@gadz.org>
 * @license  GNU General Public License
 * @ORM\Entity
 * @ORM\Table(name="CAS_USER_CACHE",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="user_unique",columns={"username"})},
 *      indexes={@ORM\Index(name="search_username", columns={"username"})})
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $usernameAttribute = null; /* set null if no username attribute */

    /**
     * @ORM\Column(type="array")
     */
    private $attributes;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * Build a User object
     * 
     * @param String $username          the unique username
     * @param Array  $attributes        the array of attribute of user
     * @param String $usernameAttribute the attribute name for username
     */
    public function __construct($username, $attributes, $roleAttribute, $usernameAttribute = null)
    {
        $this->username          = $username;
        $this->attributes        = $attributes;
        $this->usernameAttribute = $usernameAttribute;
	if($usernameAttribute)
        {
            if($attributes[$usernameAttribute] != $username)
            {
                throw new \InvalidArgumentException(sprintf('The attribute username value (%s) is not the same as username (%s)', $attributes[$usernameAttribute], $username));
            }
        }
	$this->roles = array();
        $this->addRole('user');
	$this->addRole($attributes[$roleAttribute]);
    }

    /**
     * Add Role on user
     */
    private function addRole($roleName)
    {
	if(preg_match('/^ROLE_/', $roleName))
        {
            $this->roles[] = strtoupper($roleName);
            return;
        }
        $this->roles[] = 'ROLE_' . strtoupper($roleName);
    }

    /**
     * Update attribute
     * @param Array $attributes the new attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function equals(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Call non existant method
     */
    public function __call($name, $arguments)
    {
        if(preg_match('/^get/', $name))
        {
            if(count($arguments) > 0)
            {
                throw new \BadMethodCallException();
            }
            $varNameWithUpperLetter =  preg_replace('/^get/','', $name);
            $varName = preg_replace('/\B([A-Z])/', '_$1', $varNameWithUpperLetter);
            if(!isset($this->attributes[$varName]))
            {
                throw new \BadMethodCallException();
            }
            return $this->attributes[$varName];
        }
    }

    public function serialize() {
        return serialize(array(
            'username'          => $this->username,
            'usernameAttribute' => $this->usernameAttribute,
            'attributes'        => $this->attributes,
            'roles'             => $this->roles,
        ));
    }

    public function unserialize($data) {
        $data = unserialize($data);
        $this->username          = $data['username'];
        $this->usernameAttribute = $data['usernameAttribute'];
        $this->attributes        = $data['attributes'];
        $this->roles             = $data['roles'];
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Set usernameAttribute
     *
     * @param string $usernameAttribute
     */
    public function setUsernameAttribute($usernameAttribute)
    {
        $this->usernameAttribute = $usernameAttribute;
    }

    /**
     * Get usernameAttribute
     *
     * @return string 
     */
    public function getUsernameAttribute()
    {
        return $this->usernameAttribute;
    }

    /**
     * Get attributes
     *
     * @return array 
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set roles
     *
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
}
