<?php

declare(strict_types=1);

namespace spec\Sylius\ShopApiPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\ShopApiPlugin\Http\RequestBasedLocaleContext;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class RequestLocaleSetterSpec extends ObjectBehavior
{
    function let(LocaleProviderInterface $localeProvider): void
    {
        $this->beConstructedWith($localeProvider);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RequestBasedLocaleContext::class);
    }

    function it_sets_default_locale_on_request_if_locale_is_not_set(
        LocaleProviderInterface $localeProvider,
        GetResponseEvent $event,
        Request $request,
        HeaderBag $headerBag
    ): void {
        $event->getRequest()->willReturn($request);

        $request->get('locale')->willReturn(null);

        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');
        $request->headers = $headerBag;

        $headerBag->has('Accept-Language')->willReturn(false);
        $headerBag->get('Accept-Language')->shouldNotBeCalled();

        $request->setLocale('pl_PL')->shouldNotBeCalled();
        $request->setDefaultLocale('en_US')->shouldBeCalled();

        $this->onKernelRequest($event);
    }

    function it_sets_accept_headers_locale_on_request_if_locale_is_not_set(
        LocaleProviderInterface $localeProvider,
        GetResponseEvent $event,
        Request $request,
        HeaderBag $headerBag
    ): void {
        $event->getRequest()->willReturn($request);

        $request->get('locale')->willReturn(null);
        $request->headers = $headerBag;

        $headerBag->has('Accept-Language')->willReturn(true);
        $headerBag->get('Accept-Language')->willReturn('pl_PL');

        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');
        $localeProvider->getAvailableLocalesCodes()->willReturn(['en_US', 'pl_PL']);

        $request->setLocale('pl_PL')->shouldBeCalled();
        $request->setDefaultLocale('en_US')->shouldBeCalled();

        $this->onKernelRequest($event);
    }

    function it_sets_default_locale_on_request_if_provided_locale_is_not_available(
        LocaleProviderInterface $localeProvider,
        GetResponseEvent $event,
        Request $request
    ): void {
        $event->getRequest()->willReturn($request);

        $request->get('locale')->willReturn('pl_PL');

        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');
        $localeProvider->getAvailableLocalesCodes()->willReturn(['en_US']);

        $request->setLocale('pl_PL')->shouldNotBeCalled();
        $request->setDefaultLocale('en_US')->shouldBeCalled();

        $this->onKernelRequest($event);
    }

    function it_sets_locale_and_default_locale_on_request(
        LocaleProviderInterface $localeProvider,
        GetResponseEvent $event,
        Request $request
    ): void {
        $event->getRequest()->willReturn($request);

        $request->get('locale')->willReturn('pl_PL');

        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');
        $localeProvider->getAvailableLocalesCodes()->willReturn(['en_US', 'pl_PL']);

        $request->setLocale('pl_PL')->shouldBeCalled();
        $request->setDefaultLocale('en_US')->shouldBeCalled();

        $this->onKernelRequest($event);
    }
}
