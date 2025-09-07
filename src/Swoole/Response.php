<?php

namespace Utopia\Swoole;

use Exception;
use Swoole\Http\Response as SwooleResponse;
use Utopia\Response as UtopiaResponse;

class Response extends UtopiaResponse
{
    /**
     * Swoole Response Object
     *
     * @var SwooleResponse
     */
    protected SwooleResponse $swoole;

    /**
     * HTTP response status codes
     */
    public const STATUS_CODE_CONTINUE = 100;

    public const STATUS_CODE_SWITCHING_PROTOCOLS = 101;

    public const STATUS_CODE_OK = 200;

    public const STATUS_CODE_CREATED = 201;

    public const STATUS_CODE_ACCEPTED = 202;

    public const STATUS_CODE_NON_AUTHORITATIVE_INFORMATION = 203;

    public const STATUS_CODE_NOCONTENT = 204;

    public const STATUS_CODE_RESETCONTENT = 205;

    public const STATUS_CODE_PARTIALCONTENT = 206;

    public const STATUS_CODE_MULTIPLE_CHOICES = 300;

    public const STATUS_CODE_MOVED_PERMANENTLY = 301;

    public const STATUS_CODE_FOUND = 302;

    public const STATUS_CODE_SEE_OTHER = 303;

    public const STATUS_CODE_NOT_MODIFIED = 304;

    public const STATUS_CODE_USE_PROXY = 305;

    public const STATUS_CODE_UNUSED = 306;

    public const STATUS_CODE_TEMPORARY_REDIRECT = 307;

    public const STATUS_CODE_PERMANENT_REDIRECT = 308;

    public const STATUS_CODE_BAD_REQUEST = 400;

    public const STATUS_CODE_UNAUTHORIZED = 401;

    public const STATUS_CODE_PAYMENT_REQUIRED = 402;

    public const STATUS_CODE_FORBIDDEN = 403;

    public const STATUS_CODE_NOT_FOUND = 404;

    public const STATUS_CODE_METHOD_NOT_ALLOWED = 405;

    public const STATUS_CODE_NOT_ACCEPTABLE = 406;

    public const STATUS_CODE_PROXY_AUTHENTICATION_REQUIRED = 407;

    public const STATUS_CODE_REQUEST_TIMEOUT = 408;

    public const STATUS_CODE_CONFLICT = 409;

    public const STATUS_CODE_GONE = 410;

    public const STATUS_CODE_LENGTH_REQUIRED = 411;

    public const STATUS_CODE_PRECONDITION_FAILED = 412;

    public const STATUS_CODE_REQUEST_ENTITY_TOO_LARGE = 413;

    public const STATUS_CODE_REQUEST_URI_TOO_LONG = 414;

    public const STATUS_CODE_UNSUPPORTED_MEDIA_TYPE = 415;

    public const STATUS_CODE_REQUESTED_RANGE_NOT_SATISFIABLE = 416;

    public const STATUS_CODE_EXPECTATION_FAILED = 417;

    public const STATUS_CODE_TOO_EARLY = 425;

    public const STATUS_CODE_TOO_MANY_REQUESTS = 429;

    public const STATUS_CODE_UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    public const STATUS_CODE_INTERNAL_SERVER_ERROR = 500;

    public const STATUS_CODE_NOT_IMPLEMENTED = 501;

    public const STATUS_CODE_BAD_GATEWAY = 502;

    public const STATUS_CODE_SERVICE_UNAVAILABLE = 503;

    public const STATUS_CODE_GATEWAY_TIMEOUT = 504;

    public const STATUS_CODE_HTTP_VERSION_NOT_SUPPORTED = 505;

    /**
     * @var array<int, string> $statusCodes
     */
    protected $statusCodes = [
        self::STATUS_CODE_CONTINUE => 'Continue',
        self::STATUS_CODE_SWITCHING_PROTOCOLS => 'Switching Protocols',
        self::STATUS_CODE_OK => 'OK',
        self::STATUS_CODE_CREATED => 'Created',
        self::STATUS_CODE_ACCEPTED => 'Accepted',
        self::STATUS_CODE_NON_AUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
        self::STATUS_CODE_NOCONTENT => 'No Content',
        self::STATUS_CODE_RESETCONTENT => 'Reset Content',
        self::STATUS_CODE_PARTIALCONTENT => 'Partial Content',
        self::STATUS_CODE_MULTIPLE_CHOICES => 'Multiple Choices',
        self::STATUS_CODE_MOVED_PERMANENTLY => 'Moved Permanently',
        self::STATUS_CODE_FOUND => 'Found',
        self::STATUS_CODE_SEE_OTHER => 'See Other',
        self::STATUS_CODE_NOT_MODIFIED => 'Not Modified',
        self::STATUS_CODE_USE_PROXY => 'Use Proxy',
        self::STATUS_CODE_UNUSED => 'Unused',
        self::STATUS_CODE_TEMPORARY_REDIRECT => 'Temporary Redirect',
        self::STATUS_CODE_PERMANENT_REDIRECT => 'Permanent Redirect',
        self::STATUS_CODE_BAD_REQUEST => 'Bad Request',
        self::STATUS_CODE_UNAUTHORIZED => 'Unauthorized',
        self::STATUS_CODE_PAYMENT_REQUIRED => 'Payment Required',
        self::STATUS_CODE_FORBIDDEN => 'Forbidden',
        self::STATUS_CODE_NOT_FOUND => 'Not Found',
        self::STATUS_CODE_METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::STATUS_CODE_NOT_ACCEPTABLE => 'Not Acceptable',
        self::STATUS_CODE_PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
        self::STATUS_CODE_REQUEST_TIMEOUT => 'Request Timeout',
        self::STATUS_CODE_CONFLICT => 'Conflict',
        self::STATUS_CODE_GONE => 'Gone',
        self::STATUS_CODE_LENGTH_REQUIRED => 'Length Required',
        self::STATUS_CODE_PRECONDITION_FAILED => 'Precondition Failed',
        self::STATUS_CODE_REQUEST_ENTITY_TOO_LARGE => 'Request Entity Too Large',
        self::STATUS_CODE_REQUEST_URI_TOO_LONG => 'Request-URI Too Long',
        self::STATUS_CODE_UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
        self::STATUS_CODE_REQUESTED_RANGE_NOT_SATISFIABLE => 'Requested Range Not Satisfiable',
        self::STATUS_CODE_EXPECTATION_FAILED => 'Expectation Failed',
        self::STATUS_CODE_TOO_EARLY => 'Too Early',
        self::STATUS_CODE_TOO_MANY_REQUESTS => 'Too Many Requests',
        self::STATUS_CODE_UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable For Legal Reasons',
        self::STATUS_CODE_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::STATUS_CODE_NOT_IMPLEMENTED => 'Not Implemented',
        self::STATUS_CODE_BAD_GATEWAY => 'Bad Gateway',
        self::STATUS_CODE_SERVICE_UNAVAILABLE => 'Service Unavailable',
        self::STATUS_CODE_GATEWAY_TIMEOUT => 'Gateway Timeout',
        self::STATUS_CODE_HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported',
    ];

    /**
     * Response constructor.
     */
    public function __construct(SwooleResponse $response)
    {
        $this->swoole = $response;
        parent::__construct(\microtime(true));
    }

    /**
     * Write
     *
     * @param  string  $content
     * @return void
     */
    protected function write(string $content): void
    {
        $this->swoole->write($content);
    }

    /**
     * End
     *
     * @param  string|null  $content
     * @return void
     */
    protected function end(?string $content = null): void
    {
        $this->swoole->end($content);
    }

    /**
     * Get status code reason
     *
     * Get HTTP response status code reason from available options. If status code is unknown an exception will be thrown.
     *
     * @param  int  $code
     * @return string
     *
     * @throws Exception
     */
    protected function getStatusCodeReason(int $code): string
    {
        if (!\array_key_exists($code, $this->statusCodes)) {
            throw new Exception('Unknown HTTP status code');
        }

        return $this->statusCodes[$code];
    }

    /**
     * Send Status Code
     *
     * @param  int  $statusCode
     * @return void
     */
    protected function sendStatus(int $statusCode): void
    {
        $this->swoole->status((string) $statusCode, $this->getStatusCodeReason($statusCode));
    }

    /**
     * Send Header
     *
     * @param  string  $key
     * @param  string|array<string>  $values
     * @return void
     */
    protected function sendHeader(string $key, mixed $values): void
    {
        $this->swoole->header($key, $values);
    }

    /**
     * Send Cookie
     *
     * Send a cookie
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array<string, mixed>  $options
     * @return void
     */
    protected function sendCookie(string $name, string $value, array $options): void
    {
        $this->swoole->cookie(
            name: $name,
            value: $value,
            expires: $options['expire'] ?? 0,
            path: $options['path'] ?? '',
            domain: $options['domain'] ?? '',
            secure: $options['secure'] ?? false,
            httponly: $options['httponly'] ?? false,
            samesite: $options['samesite'] ?? false,
        );
    }
}
