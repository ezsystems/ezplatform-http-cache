<?php

namespace spec\EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\MVC\Symfony\View\View;
use EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ResponseCacheConfigurator;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator\ViewParametersTagger;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class ViewParametersTaggerSpec extends ObjectBehavior
{
    function let(ResponseTagger $dispatcherTagger)
    {
        $this->beConstructedWith($dispatcherTagger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ViewParametersTagger::class);
    }

    function it_delegates_tagging_of_parameters_that_are_value_objects(
        ResponseCacheConfigurator $configurator,
        Response $response,
        ResponseTagger $dispatcherTagger,
        View $view,
        ValueObject $someValueObject,
        stdClass $someObject
    ) {
        $view->getParameters()->willReturn([
            'value_object' => $someValueObject,
            'object' => $someObject,
            'string' => 'some_string',
            'array' => ['a', 'b', 'c'],
        ]);

        $this->tag($configurator, $response, $view);

        $dispatcherTagger->tag($configurator, $response, $someValueObject)->shouldHaveBeenCalled();
        $dispatcherTagger->tag($configurator, $response, $someObject)->shouldNotHaveBeenCalled();
        $dispatcherTagger->tag($configurator, $response, 'some_string')->shouldNotHaveBeenCalled();
        $dispatcherTagger->tag($configurator, $response, ['a', 'b', 'c'])->shouldNotHaveBeenCalled();
    }
}
