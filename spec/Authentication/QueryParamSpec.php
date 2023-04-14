<?php

namespace spec\Http\Message\Authentication;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class QueryParamSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith([
            'username' => 'username',
            'password' => 'password',
        ]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Authentication\QueryParam');
    }

    public function it_is_an_authentication()
    {
        $this->shouldImplement('Http\Message\Authentication');
    }

    public function it_authenticates_a_request(
        RequestInterface $request,
        UriInterface $uri,
        RequestInterface $newRequest,
        UriInterface $newUri
    ) {
        $request->getUri()->willReturn($uri);
        $uri->getQuery()->willReturn('param1=value1&param2[]=value2');
        $uri->withQuery('param1=value1&param2%5B0%5D=value2&username=username&password=password')->will(
            function ($args) use ($newUri) {
                $newUri->getQuery()->willReturn($args[0]);

                return $newUri;
            }
        );

        $request->withUri($newUri)->will(function ($args) use ($newRequest) {
            $newRequest->getUri()->willReturn($args[0]);

            return $newRequest;
        });

        $authenticatedRequest = $this->authenticate($request);
        $authenticatedRequest->shouldBe($newRequest);

        $authenticatedUri = $authenticatedRequest->getUri();
        $authenticatedUri->shouldBe($newUri);
        $authenticatedUri->getQuery()->shouldReturn('param1=value1&param2%5B0%5D=value2&username=username&password=password');
    }
}
