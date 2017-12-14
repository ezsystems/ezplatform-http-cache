<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\ContextProvider;

use eZ\Publish\API\Repository\Repository;
use FOS\HttpCache\UserContext\ContextProviderInterface;
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
class RoleIdentify implements ContextProviderInterface
{
    /**
     * @var \eZ\Publish\Core\Repository\Repository
     */
    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function updateUserContext(UserContext $context)
    {
        $user = $this->repository->getCurrentUser();
        /** @var \eZ\Publish\API\Repository\Values\User\RoleAssignment[] $roleAssignments */
        $roleAssignments = $this->repository->sudo(
            function (Repository $repository) use ($user) {
                return $repository->getRoleService()->getRoleAssignmentsForUser($user, true);
            }
        );

        $roleIds = array();
        $limitationValues = array();
        /** @var \eZ\Publish\API\Repository\Values\User\UserRoleAssignment $roleAssignment */
        foreach ($roleAssignments as $roleAssignment) {
            $roleId = $roleAssignment->getRole()->id;
            $roleIds[] = $roleId;
            $limitation = $roleAssignment->getRoleLimitation();
            // If a limitation is present, store the limitation values by roleId
            if ($limitation !== null) {
                $limitationValuesKey = sprintf('%s-%s', $roleId, $limitation->getIdentifier());
                $limitationValues[$limitationValuesKey] = array();
                foreach ($limitation->limitationValues as $value) {
                    $limitationValues[$limitationValuesKey][] = $value;
                }
            }
        }

        $context->addParameter('roleIdList', $roleIds);
        $context->addParameter('roleLimitationList', $limitationValues);
    }
}
