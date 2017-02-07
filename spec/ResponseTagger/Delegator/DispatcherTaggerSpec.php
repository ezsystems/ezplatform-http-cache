<?php

namespace spec\EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator;

use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ResponseCacheConfigurator;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator\DispatcherTagger;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Response;

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
        ResponseCacheConfigurator $configurator,
        Response $response,
        ValueObject $value
    ) {
        $this->tag($configurator, $response, $value);

        $taggerOne->tag($configurator, $response, $value)->shouldHaveBeenCalled();
        $taggerTwo->tag($configurator, $response, $value)->shouldHaveBeenCalled();
    }
}
