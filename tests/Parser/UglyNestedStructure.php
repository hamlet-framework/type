<?php

namespace Hamlet\Type\Parser
{
    class A
    {
        /** @var \DateTime */
        private $c;
    }
}

namespace Hamlet\Type\Parser\N0\N1
{
    use Hamlet\Type\Parser\A;

    class B
    {
        /** @var A */
        private $a;
    }
}

namespace
{
    use Hamlet\Type\Parser\N0\N1\B;

    class C
    {
        /** @var \Hamlet\Type\Parser\A */
        private $a;

        /** @var B */
        private $b;
    }
}
