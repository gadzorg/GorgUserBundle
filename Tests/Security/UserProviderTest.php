<?php

namespace Gorg\Bundle\UserBundle\Tests\Security;

use Gorg\Bundle\UserBundle\Security\UserProvider;
use Gorg\Bundle\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Gorg\Bundle\RemoteServiceBundle\Services\PlatalConnection;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserProviderTest extends WebTestCase
{
     private $object;

     public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->base = "https://pl.int.gadz.org";
        $this->user = "api_k100_sf2";
        $this->pass = "api_k100_sf2";


	$this->plc = new PlatalConnection($this->base, $this->user, $this->pass);
	$this->up = new UserProvider($this->plc);
    }

    public function testLoadUserByUsername()
    {
	$validHruid    = "mathieu.goulin.2008";
	$nonValidHruid = "toto.tata.blablabla";
	try {
		$this->up->loadUserByUsername($nonValidHruid);
		$this->assertTrue(false);
	} catch (UsernameNotFoundException $e) {
		$this->assertTrue(true);
	}
	$user = $this->up->loadUserByUsername($validHruid);
	$this->assertInstanceOf("Gorg\\Bundle\\UserBundle\\Entity\\User", $user);
	$this->assertEquals($user->hruid, $validHruid);
	$this->assertEquals($user->buque_txt, "Kapable");
    }
}
