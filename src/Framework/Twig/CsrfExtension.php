<?php
namespace Framework\Twig;

use Framework\Middleware\CsrfMidlleware;

class CsrfExtension extends \Twig_Extension
{
    /**
     * @var CsrfMidlleware
     */
    private $csrfMidlleware;

    /**
     * CsrfExtension constructor.
     * @param CsrfMidlleware $csrfMidlleware
     */
    public function __construct(CsrfMidlleware $csrfMidlleware)
    {
        $this->csrfMidlleware = $csrfMidlleware;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('csrf_input', [$this, 'csrfInput'], ['is_safe' => 'html'])
        ];
    }

    public function csrfInput()
    {
        return '<input type="hidden" ' .
        'name="' . $this->csrfMidlleware->getFormKey().'"'.
        'value="' . $this->csrfMidlleware->generateToken() . '"/>';
    }
}
