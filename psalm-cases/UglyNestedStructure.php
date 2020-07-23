<?php

namespace Hamlet\Cast\Parser
{
    class A
    {
        /** @var \DateTime */
        private $c;
    }
}

namespace Hamlet\Cast\Parser\N0\N1
{
    use Hamlet\Cast\Parser\A;

    class B
    {
        private A $a;
    }
}

namespace
{
    use Hamlet\Cast\Parser\N0\N1\B;

    class C
    {
        /** @var \Hamlet\Cast\Parser\A */
        private $a;

        /** @var B */
        private $b;
    }
}
