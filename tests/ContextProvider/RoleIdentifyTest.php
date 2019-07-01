<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\ContextProvider;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation;
use eZ\Publish\API\Repository\Values\User\Role;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use eZ\Publish\API\Repository\Values\User\UserReference;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Helper\LimitationService;
use eZ\Publish\Core\Repository\Helper\RoleDomainMapper;
use eZ\Publish\Core\Repository\Permission\PermissionResolver;
use eZ\Publish\Core\Repository\Values\User\UserRoleAssignment;
use eZ\Publish\SPI\Persistence\User\Handler as SPIUserHandler;
use EzSystems\PlatformHttpCacheBundle\ContextProvider\RoleIdentify;
use FOS\HttpCache\UserContext\UserContext;
use PHPUnit\Framework\TestCase;

/**
 * Class RoleIdentify test.
 */
class RoleIdentifyTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\Repository
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\RoleService
     */
    private $roleServiceMock;

    protected function setUp()
    {
        parent::setUp();
        $this->repositoryMock = $this
            ->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getRoleService', 'getCurrentUser', 'getPermissionResolver'))
            ->getMock();

        $this->roleServiceMock = $this->createMock(RoleService::class);

        $this->repositoryMock
            ->expects($this->any())
            ->method('getRoleService')
            ->willReturn($this->roleServiceMock);
        $this->repositoryMock
            ->expects($this->any())
            ->method('getPermissionResolver')
            ->willReturn($this->getPermissionResolverMock());
    }

    public function testSetIdentity()
    {
        $user = $this->createMock(APIUser::class);
        $userContext = new UserContext();

        $this->repositoryMock
            ->expects($this->once())
            ->method('getCurrentUser')
            ->willReturn($user);

        $roleId1 = 123;
        $roleId2 = 456;
        $roleId3 = 789;
        $limitationForRole2 = $this->generateLimitationMock(
            array(
                'limitationValues' => array('/1/2', '/1/2/43'),
            )
        );
        $limitationForRole3 = $this->generateLimitationMock(
            array(
                'limitationValues' => array('foo', 'bar'),
            )
        );
        $returnedRoleAssignments = array(
            $this->generateRoleAssignmentMock(
                array(
                    'role' => $this->generateRoleMock(
                        array(
                            'id' => $roleId1,
                        )
                    ),
                )
            ),
            $this->generateRoleAssignmentMock(
                array(
                    'role' => $this->generateRoleMock(
                        array(
                            'id' => $roleId2,
                        )
                    ),
                    'limitation' => $limitationForRole2,
                )
            ),
            $this->generateRoleAssignmentMock(
                array(
                    'role' => $this->generateRoleMock(
                        array(
                            'id' => $roleId3,
                        )
                    ),
                    'limitation' => $limitationForRole3,
                )
            ),
        );

        $this->roleServiceMock
            ->expects($this->once())
            ->method('getRoleAssignmentsForUser')
            ->with($user, true)
            ->willReturn($returnedRoleAssignments);

        $this->assertSame(array(), $userContext->getParameters());
        $contextProvider = new RoleIdentify($this->repositoryMock);
        $contextProvider->updateUserContext($userContext);
        $userContextParams = $userContext->getParameters();
        $this->assertArrayHasKey('roleIdList', $userContextParams);
        $this->assertSame(array($roleId1, $roleId2, $roleId3), $userContextParams['roleIdList']);
        $this->assertArrayHasKey('roleLimitationList', $userContextParams);
        $limitationIdentifierForRole2 = \get_class($limitationForRole2);
        $limitationIdentifierForRole3 = \get_class($limitationForRole3);
        $this->assertSame(
            array(
                "$roleId2-$limitationIdentifierForRole2" => array('/1/2', '/1/2/43'),
                "$roleId3-$limitationIdentifierForRole3" => array('foo', 'bar'),
            ),
            $userContextParams['roleLimitationList']
        );
    }

    private function generateRoleAssignmentMock(array $properties = array())
    {
        return $this
            ->getMockBuilder(UserRoleAssignment::class)
            ->setConstructorArgs(array($properties))
            ->getMockForAbstractClass();
    }

    private function generateRoleMock(array $properties = array())
    {
        return $this
            ->getMockBuilder(Role::class)
            ->setConstructorArgs(array($properties))
            ->getMockForAbstractClass();
    }

    private function generateLimitationMock(array $properties = array())
    {
        $limitationMock = $this
            ->getMockBuilder(RoleLimitation::class)
            ->setConstructorArgs(array($properties))
            ->getMockForAbstractClass();
        $limitationMock
            ->expects($this->any())
            ->method('getIdentifier')
            ->willReturn(\get_class($limitationMock));

        return $limitationMock;
    }

    protected function getPermissionResolverMock()
    {
        return $this
            ->getMockBuilder(PermissionResolver::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    $this->createMock(RoleDomainMapper::class),
                    $this->createMock(LimitationService::class),
                    $this->createMock(SPIUserHandler::class),
                    $this->createMock(UserReference::class),
                ]
            )
            ->getMock();
    }
}
