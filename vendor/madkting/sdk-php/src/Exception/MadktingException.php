<?php

namespace Madkting\Exception;

/**
 * Represents an Madkting  exception that is thrown when a command fails.
 */
class MadktingException extends \RuntimeException
{
    private $response;
    private $request;
    private $result;
    private $connectionError;

    /**
     * @param string           $message Exception message
     * @param array            $context Exception context
     * @param \Exception       $previous  Previous exception (if any)
     */
    public function __construct(
        $message,
        array $context = array(), \Exception $previous = null)
    {
        $this->response = isset($context['response']) ? $context['response'] : null;
        $this->request = isset($context['request']) ? $context['request'] : null;
        $this->connectionError = !empty($context['connection_error']);
        $this->result = isset($context['result']) ? $context['result'] : null;
        parent::__construct($message, 0, $previous);
    }

    public function __toString()
    {
        if (!$this->getPrevious()) {
            return parent::__toString();
        }

        // PHP strangely shows the innermost exception first before the outer
        // exception message. It also has a default character limit for
        // exception message strings such that the "next" exception (this one)
        // might not even get shown, causing developers to attempt to catch
        // the inner exception instead of the actual exception because they
        // can't see the outer exception's __toString output.
        return sprintf(
            "exception '%s' with message '%s'\n\n%s",
            get_class($this),
            $this->getMessage(),
            parent::__toString()
        );
    }


    /**
     * Get the sent HTTP request if any.
     *
     * @return RequestInterface|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the received HTTP response if any.
     *
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the result of the exception if available
     *
     * @return ResultInterface|null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Returns true if this is a connection error.
     *
     * @return bool
     */
    public function isConnectionError()
    {
        return $this->connectionError;
    }
}