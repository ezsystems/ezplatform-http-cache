<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\ContextProvider;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\UserService;
use FOS\HttpCache\UserContext\ContextProvider;
use FOS\HttpCache\UserContext\UserContext;

/**
 * Identity definer based on current user role ids and role limitations.
 *
 * This will make sure user context hash is unique for all users that share same rights.
 *
 * Note:
 * If you need to vary by user this could be done with own vary by header logic to be able to vary by session id.
 * For user unique policies like Owner limitation, make sure to handle this in controller/view layer, in
 * the future there might be a way in api to give hints to view/controllers about this more cleanly.
 */
class RoleIdentify implements ContextProvider
{
    /** @var \eZ\Publish\Core\Repository\Repository */
    protected $repository;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    public function __construct(
        Repository $repository,
        PermissionResolver $permissionResolver,
        UserService $userService
    ) {
        $this->repository = $repository;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
    }

    public function updateUserContext(UserContext $context)
    {
        $user = $this->userService->loadUser(
            $this->permissionResolver->getCurrentUserReference()->getUserId()
        );

        /** @var \eZ\Publish\API\Repository\Values\User\RoleAssignment[] $roleAssignments */
        $roleAssignments = $this->repository->sudo(
            function (Repository $repository) use ($user) {
                return $repository->getRoleService()->getRoleAssignmentsForUser($user, true);
            }
        );

        $roleIds = [];
        $limitationValues = [];
        /** @var \eZ\Publish\API\Repository\Values\User\UserRoleAssignment $roleAssignment */
        foreach ($roleAssignments as $roleAssignment) {
            $roleId = $roleAssignment->getRole()->id;
            $roleIds[] = $roleId;
            $limitation = $roleAssignment->getRoleLimitation();
            // If a limitation is present, store the limitation values by roleId
            if ($limitation !== null) {
                $limitationValuesKey = sprintf('%s-%s', $roleId, $limitation->getIdentifier());
                $limitationValues[$limitationValuesKey] = [];
                foreach ($limitation->limitationValues as $value) {
                    $limitationValues[$limitationValuesKey][] = $value;
                }
            }
        }

        $context->addParameter('roleIdList', $roleIds);
        $context->addParameter('roleLimitationList', $limitationValues);
    }
}
