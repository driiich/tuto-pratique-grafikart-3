<?php
namespace Framework\Middleware;

use Framework\Exception\CsrfInvalidException;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CsrfMidlleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $formKey;

    /**
     * @var string
     */
    private $sessionKey;

    /**
     * @var \ArrayAccess
     */
    private $session;

    /**
     * @var int
     */
    private $limit;

    public function __construct(
        &$session,
        int $limit = 50,
        string $formKey = '_csrf',
        string $sessionKey = 'csrf'
    ) {
        $this->ValidSession($session);
        $this->session = &$session;
        $this->formKey = $formKey;
        $this->sessionKey = $sessionKey;
        $this->limit = $limit;
    }

    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $params = $request->getParsedBody() ?: [];
            if (!array_key_exists($this->formKey, $params)) {
                $this->reject();
            } else {
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->formKey], $csrfList)) {
                    return $delegate->process($request);
                } else {
                    $this->reject();
                }
            }
        } else {
            return $delegate->process($request);
        }
    }

    private function reject(): void
    {
        throw new CsrfInvalidException();
    }

    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;
        $this->limitTokens();

        return $token;
    }

    private function useToken($token): void
    {
        $tokens = array_filter($this->session[$this->sessionKey], function ($t) use ($token) {
            return $token !== $t;
        });
        $this->session[$this->sessionKey] = $token;
    }

    private function limitTokens(): void
    {
        $tokens = $this->session[$this->sessionKey] ?? [];
        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        $this->session[$this->sessionKey] = $tokens;
    }

    private function ValidSession($session)
    {
        if (!is_array($session) && !$session instanceof \ArrayAccess) {
            throw new \TypeError('La session passée au Middleware n\'est pas traitable comme un tableau');
        }
    }

    /**
     * @return string
     */
    public function getFormKey(): string
    {
        return $this->formKey;
    }
}
