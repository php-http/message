<?php

namespace spec\Http\Message\Encoding {

    use Psr\Http\Message\StreamInterface;
    use PhpSpec\Exception\Example\SkippingException;

    trait ZlibStreamBehavior
    {
        function it_throws_an_exception_when_zlib_is_not_enabled()
        {
            ZlibExtensionLoadedMock::$enabled = true;

            $this->shouldThrow('RuntimeException')->duringInstantiation();

            ZlibExtensionLoadedMock::$enabled = false;
        }
    }

    class ZlibExtensionLoadedMock
    {
        /**
         * If enabled, the mocked method is used.
         *
         * @var bool
         */
        public static $enabled = false;

        /**
         * extension_loaded function mock.
         */
        public static function extensionLoaded()
        {
            if (static::$enabled) {
                return false;
            } else {
                return call_user_func_array('\extension_loaded', func_get_args());
            }
        }
    }
}

namespace Http\Message\Encoding {

    use spec\Http\Message\Encoding\ZlibExtensionLoadedMock;

    function extension_loaded($extension)
    {
        return ZlibExtensionLoadedMock::extensionLoaded($extension);
    }
}
