<?php

namespace spec\EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator;

use EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ResponseCacheConfigurator;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator\LocationValueViewTagger;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;
use eZ\Publish\Core\Repository\Values\Content\Location;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Response;

class LocationValueViewTaggerSpec extends ObjectBehavior
{
    public function let(ResponseTagger $locationTagger)
    {
        $this->beConstructedWith($locationTagger);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(LocationValueViewTagger::class);
    }

    public function it_delegates_tagging_of_the_location(
        ResponseTagger $locationTagger,
        ResponseCacheConfigurator $configurator,
        Response $response,
        LocationValueView $view
    ) {
        $location = new Location();
        $view->getLocation()->willReturn($location);
        $this->tag($configurator, $response, $view);

        $locationTagger->tag($configurator, $response, $location)->shouldHaveBeenCalled();
    }
}
