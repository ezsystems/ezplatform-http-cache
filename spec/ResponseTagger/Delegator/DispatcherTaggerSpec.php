<?php

namespace spec\EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator;

use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator\DispatcherTagger;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use PhpSpec\ObjectBehavior;

class DispatcherTaggerSpec extends ObjectBehavior
{
    public function let(ResponseTagger $taggerOne, ResponseTagger $taggerTwo)
    {
        $this->beConstructedWith([$taggerOne, $taggerTwo]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(DispatcherTagger::class);
    }

    public function it_calls_tag_on_every_tagger(
        ResponseTagger $taggerOne,
        ResponseTagger $taggerTwo,
        ValueObject $value
    ) {
        $this->tag($value);

        $taggerOne->tag($value)->shouldHaveBeenCalled();
        $taggerTwo->tag($value)->shouldHaveBeenCalled();
    }
}
