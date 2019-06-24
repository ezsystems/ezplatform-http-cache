<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractSlotTest extends TestCase
{
    /** @var \EzSystems\PlatformHttpCacheBundle\SignalSlot\AbstractSlot */
    protected $slot;

    /** @var \EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $purgeClientMock;

    /** @var \eZ\Publish\Core\SignalSlot\Signal */
    private $signal;

    public function setUp()
    {
        $this->purgeClientMock = $this->createMock(PurgeClientInterface::class);
        $this->slot = $this->createSlot();
        $this->signal = $this->createSignal();
    }

    protected function createSlot()
    {
        $class = $this->getSlotClass();

        return new $class($this->purgeClientMock);
    }

    /**
     * @dataProvider getUnreceivedSignals
     */
    public function testDoesNotReceiveOtherSignals($signal)
    {
        $this->purgeClientMock->expects($this->never())->method('purge');
        $this->purgeClientMock->expects($this->never())->method('purgeAll');

        $this->slot->receive($signal);
    }

    /**
     * @dataProvider getReceivedSignals
     */
    public function testReceivePurgesCacheForTags($signal)
    {
        $this->purgeClientMock->expects($this->once())->method('purge')->with($this->generateTags());
        $this->purgeClientMock->expects($this->never())->method('purgeAll');
        $this->receive($signal);
    }

    /**
     * Create signal instance.
     *
     * @return mixed
     */
    abstract public function createSignal();

    /**
     * @return array
     */
    abstract public function generateTags();

    /**
     * Returns signal classes handled by tested slot.
     *
     * @return array
     */
    abstract public function getReceivedSignalClasses();

    /**
     * Returns tested slot class.
     *
     * @return string
     */
    abstract public function getSlotClass();

    protected function receive($signal)
    {
        $this->slot->receive($signal);
    }

    public function getReceivedSignals()
    {
        return [[$this->createSignal()]];
    }

    /**
     * All existing SignalSlots.
     */
    public function getUnreceivedSignals()
    {
        $arguments = [];

        $signals = $this->getAllSignals();
        foreach ($signals as $signalClass) {
            if (\in_array($signalClass, $this->getReceivedSignalClasses())) {
                continue;
            }
            $arguments[] = [new $signalClass()];
        }

        return $arguments;
    }

    /**
     * @return array
     */
    private function getAllSignals()
    {
        $signals = array(
            Signal\URLAliasService\CreateUrlAliasSignal::class,
            Signal\URLAliasService\RemoveAliasesSignal::class,
            Signal\URLAliasService\CreateGlobalUrlAliasSignal::class,
            Signal\ContentTypeService\CreateContentTypeSignal::class,
            Signal\ContentTypeService\AddFieldDefinitionSignal::class,
            Signal\ContentTypeService\CopyContentTypeSignal::class,
            Signal\ContentTypeService\DeleteContentTypeSignal::class,
            Signal\ContentTypeService\UpdateContentTypeGroupSignal::class,
            Signal\ContentTypeService\DeleteContentTypeGroupSignal::class,
            Signal\ContentTypeService\UnassignContentTypeGroupSignal::class,
            Signal\ContentTypeService\PublishContentTypeDraftSignal::class,
            Signal\ContentTypeService\AssignContentTypeGroupSignal::class,
            Signal\ContentTypeService\UpdateFieldDefinitionSignal::class,
            Signal\ContentTypeService\UpdateContentTypeDraftSignal::class,
            Signal\ContentTypeService\RemoveFieldDefinitionSignal::class,
            Signal\ContentTypeService\CreateContentTypeDraftSignal::class,
            Signal\ContentTypeService\CreateContentTypeGroupSignal::class,
            Signal\LanguageService\EnableLanguageSignal::class,
            Signal\LanguageService\UpdateLanguageNameSignal::class,
            Signal\LanguageService\CreateLanguageSignal::class,
            Signal\LanguageService\DisableLanguageSignal::class,
            Signal\LanguageService\DeleteLanguageSignal::class,
            Signal\UserService\MoveUserGroupSignal::class,
            Signal\UserService\DeleteUserGroupSignal::class,
            Signal\UserService\CreateUserGroupSignal::class,
            Signal\UserService\UpdateUserGroupSignal::class,
            Signal\UserService\UnAssignUserFromUserGroupSignal::class,
            Signal\UserService\AssignUserToUserGroupSignal::class,
            Signal\UserService\DeleteUserSignal::class,
            Signal\UserService\CreateUserSignal::class,
            Signal\UserService\UpdateUserSignal::class,
            Signal\SectionService\DeleteSectionSignal::class,
            Signal\SectionService\CreateSectionSignal::class,
            Signal\SectionService\UpdateSectionSignal::class,
            Signal\SectionService\AssignSectionSignal::class,
            Signal\RoleService\AssignRoleToUserGroupSignal::class,
            Signal\RoleService\UpdatePolicySignal::class,
            Signal\RoleService\CreateRoleSignal::class,
            Signal\RoleService\RemovePolicySignal::class,
            Signal\RoleService\UnassignRoleFromUserSignal::class,
            Signal\RoleService\AddPolicySignal::class,
            Signal\RoleService\UnassignRoleFromUserGroupSignal::class,
            Signal\RoleService\UpdateRoleSignal::class,
            Signal\RoleService\AssignRoleToUserSignal::class,
            Signal\RoleService\DeleteRoleSignal::class,
            Signal\TrashService\TrashSignal::class,
            Signal\TrashService\EmptyTrashSignal::class,
            Signal\TrashService\RecoverSignal::class,
            Signal\TrashService\DeleteTrashItemSignal::class,
            Signal\ObjectStateService\DeleteObjectStateSignal::class,
            Signal\ObjectStateService\CreateObjectStateSignal::class,
            Signal\ObjectStateService\DeleteObjectStateGroupSignal::class,
            Signal\ObjectStateService\CreateObjectStateGroupSignal::class,
            Signal\ObjectStateService\UpdateObjectStateSignal::class,
            Signal\ObjectStateService\UpdateObjectStateGroupSignal::class,
            Signal\ObjectStateService\SetContentStateSignal::class,
            Signal\ObjectStateService\SetPriorityOfObjectStateSignal::class,
            Signal\URLWildcardService\TranslateSignal::class,
            Signal\URLWildcardService\RemoveSignal::class,
            Signal\URLWildcardService\CreateSignal::class,
            Signal\ContentService\UpdateContentSignal::class,
            Signal\ContentService\CreateContentDraftSignal::class,
            Signal\ContentService\AddRelationSignal::class,
            Signal\ContentService\CreateContentSignal::class,
            Signal\ContentService\DeleteContentSignal::class,
            Signal\ContentService\AddTranslationInfoSignal::class,
            Signal\ContentService\CopyContentSignal::class,
            Signal\ContentService\UpdateContentMetadataSignal::class,
            Signal\ContentService\TranslateVersionSignal::class,
            Signal\ContentService\PublishVersionSignal::class,
            Signal\ContentService\DeleteRelationSignal::class,
            Signal\ContentService\DeleteVersionSignal::class,
            Signal\LocationService\UpdateLocationSignal::class,
            Signal\LocationService\HideLocationSignal::class,
            Signal\LocationService\SwapLocationSignal::class,
            Signal\LocationService\MoveSubtreeSignal::class,
            Signal\LocationService\UnhideLocationSignal::class,
            Signal\LocationService\CreateLocationSignal::class,
            Signal\LocationService\DeleteLocationSignal::class,
            Signal\LocationService\CopySubtreeSignal::class,
        );

        if (class_exists('eZ\Publish\Core\SignalSlot\Signal\ContentService\HideContentSignal', false)) {
            $signals[] = Signal\ContentService\HideContentSignal::class;
        }

        if (class_exists('eZ\Publish\Core\SignalSlot\Signal\ContentService\RevealContentSignal', false)) {
            $signals[] = Signal\ContentService\RevealContentSignal::class;
        }

        return $signals;
    }
}
