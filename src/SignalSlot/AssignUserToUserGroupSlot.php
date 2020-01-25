<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A slot handling AssignUserToUserGroupSignal.
 */
class AssignUserToUserGroupSlot extends AbstractContentSlot
{
    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\UserService\AssignUserToUserGroupSignal $signal
     */
    protected function generateTags(Signal $signal)
    {
        return ['c' . $signal->userId, 'c' . $signal->userGroupId, 'ez-user-context-hash'];
    }

    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\UserService\AssignUserToUserGroupSignal;
    }
}
