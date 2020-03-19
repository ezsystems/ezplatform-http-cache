<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace spec\EzSystems\PlatformHttpCacheBundle\Handler;

use EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix;
use FOS\HttpCache\TagHeaderFormatter\CommaSeparatedTagHeaderFormatter;
use FOS\HttpCacheBundle\CacheManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class TagHandlerSpec extends ObjectBehavior
{
    public function let(
        CacheManager $cacheManager,
        Response $response,
        ResponseHeaderBag $responseHeaderBag,
        RepositoryTagPrefix $tagPrefix,
        LoggerInterface $logger
    ) {
        $response->headers = $responseHeaderBag;
        $cacheManager->supports(CacheManager::INVALIDATE)->willReturn(true);

        $headerFormatter = new CommaSeparatedTagHeaderFormatter('xkey', ' ');
        $this->beConstructedWith($tagPrefix, $logger, [
            'header_formatter' => $headerFormatter,
            'tag_max_length' => 1000,
            'tag_max_length_ttl' => 300,
        ]);

        $tagPrefix->getRepositoryPrefix()->willReturn('');
    }

    public function it_only_tags_ez_all_when_no_tags(Response $response, ResponseHeaderBag $responseHeaderBag)
    {
        $responseHeaderBag->has('xkey')->willReturn(false);
        $responseHeaderBag->set('xkey', Argument::exact('ez-all'))->shouldBeCalled();

        $this->tagSymfonyResponse($response, false);
    }

    public function it_only_tags_ez_all_when_no_tags_also_on_replace(Response $response, ResponseHeaderBag $responseHeaderBag)
    {
        $responseHeaderBag->has('xkey')->shouldNotBeCalled();
        $responseHeaderBag->set('xkey', Argument::exact('ez-all'))->shouldBeCalled();

        $this->tagSymfonyResponse($response, true);
    }

    public function it_tags_with_existing_header_string(Response $response, ResponseHeaderBag $responseHeaderBag)
    {
        $responseHeaderBag->has('xkey')->willReturn(true);
        $responseHeaderBag->all('xkey')->willReturn(['tag1,tag2 tag3']);
        $responseHeaderBag->set('xkey', Argument::exact('tag1 tag2 tag3 ez-all'))->shouldBeCalled();

        $this->tagSymfonyResponse($response);
    }

    public function it_tags_with_existing_header_array(Response $response, ResponseHeaderBag $responseHeaderBag)
    {
        $responseHeaderBag->has('xkey')->willReturn(true);
        $responseHeaderBag->all('xkey')->willReturn(['tag1', 'tag2', 'tag3']);
        $responseHeaderBag->set('xkey', Argument::exact('tag1 tag2 tag3 ez-all'))->shouldBeCalled();

        $this->tagSymfonyResponse($response);
    }

    public function it_tags_with_existing_header_mixed(Response $response, ResponseHeaderBag $responseHeaderBag)
    {
        $responseHeaderBag->has('xkey')->willReturn(true);
        $responseHeaderBag->all('xkey')->willReturn(['tag1', 'tag2,tag3']);
        $responseHeaderBag->set('xkey', Argument::exact('tag1 tag2 tag3 ez-all'))->shouldBeCalled();

        $this->tagSymfonyResponse($response);
    }

    public function it_tags_all_tags_we_add(Response $response, ResponseHeaderBag $responseHeaderBag)
    {
        $responseHeaderBag->set('xkey', Argument::exact('ez-all l4 c4 p2'))->shouldBeCalled();

        $this->addTags(['l4', 'c4']);
        $this->addTags(['p2']);
        $this->tagSymfonyResponse($response, true);
    }

    public function it_tags_all_tags_we_add_and_prefix_with_repo_id(Response $response, ResponseHeaderBag $responseHeaderBag, RepositoryTagPrefix $tagPrefix)
    {
        $tagPrefix->getRepositoryPrefix()->willReturn('0');
        $responseHeaderBag->set('xkey', Argument::exact('ez-all 0ez-all 0l4 0c4 0p2'))->shouldBeCalled();

        $this->addTags(['l4', 'c4']);
        $this->addTags(['p2']);
        $this->tagSymfonyResponse($response, true);
    }

    public function it_tags_all_tags_we_add_and_prefix_with_repo_id_also_with_existing_header(Response $response, ResponseHeaderBag $responseHeaderBag, RepositoryTagPrefix $tagPrefix)
    {
        $tagPrefix->getRepositoryPrefix()->willReturn('2');
        $responseHeaderBag->has('xkey')->willReturn(true);
        $responseHeaderBag->all('xkey')->willReturn(['tag1']);
        $responseHeaderBag->set('xkey', Argument::exact('ez-all 2tag1 2ez-all 2l4 2c4 2p2'))->shouldBeCalled();

        $this->addTags(['l4', 'c4']);
        $this->addTags(['p2']);
        $this->tagSymfonyResponse($response, false);
    }

    public function it_ignores_too_long_tag_header(Response $response, ResponseHeaderBag $responseHeaderBag, LoggerInterface $logger)
    {
        $underLimitTags = 'ez-all';
        $length = 6;
        while(true) {
            $tag = ' c' . $length;
            $tagLength = strlen($tag);
            if ($length + $tagLength  > 1000) {
                break; // too long if we add more
            }
            $underLimitTags .= $tag;
            $length += $tagLength;
        }
        $responseHeaderBag->getCacheControlDirective('s-maxage')->shouldBeCalled()->willReturn(200);
        $response->setSharedMaxAge(300)->shouldNotBeCalled();
        $logger->warning(Argument::containingString('HTTP Cache tags header max length reached and truncated to'))->shouldBeCalled();
        $responseHeaderBag->set('xkey', Argument::exact($underLimitTags))->shouldBeCalled();

        $this->addTags(explode(' ', $underLimitTags));
        $this->addTags(['l1111111', 'c1111111']); // these tags are ignored
        $this->tagSymfonyResponse($response, true);
    }

    public function it_ignores_too_long_tag_header_and_reduces_ttl(Response $response, ResponseHeaderBag $responseHeaderBag)
    {
        $underLimitTags = 'ez-all';
        $length = 6;
        while(true) {
            $tag = ' c' . $length;
            $tagLength = strlen($tag);
            if ($length + $tagLength  > 1000) {
                break; // too long if we add more
            }
            $underLimitTags .= $tag;
            $length += $tagLength;
        }
        $responseHeaderBag->getCacheControlDirective('s-maxage')->shouldBeCalled()->willReturn(500);
        $response->setSharedMaxAge(300)->shouldBeCalled();
        $responseHeaderBag->set('xkey', Argument::exact($underLimitTags))->shouldBeCalled();

        $this->addTags(explode(' ', $underLimitTags));
        $this->addTags(['l1111111', 'c1111111']); // these tags are ignored
        $this->tagSymfonyResponse($response, true);
    }
}
