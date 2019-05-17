<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A slot handling UnAssignUserFromUserGroupSignal.
 */
class UnassignUserFromUserGroupSlot extends AbstractContentSlot
{
    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\UserService\UnAssignUserFromUserGroupSignal $signal
     * @return array
     */
    protected function generateTags(Signal $signal)
    {
        return [
            $this->tagProvider->getTagForContentId($signal->userId),
            $this->tagProvider->getTagForContentId($signal->userGroupId),
            $this->tagProvider->getTagForUserContextHash(),
        ];
    }

    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\UserService\UnAssignUserFromUserGroupSignal;
    }
}
