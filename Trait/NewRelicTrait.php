<?php

App::uses('NewRelic', 'NewRelic.Lib');

trait NewRelicTrait
{
    /**
     * The transaction name to use.
     *
     * @var string
     */
    protected $_name;

    /**
     * Set the transaction name.
     *
     * If `$name` is a Shell instance, the name will
     * automatically be derived based on best practices
     *
     * @param string|Shell $name
     */
    public function setName($name)
    {
        if ($name instanceof Shell) {
            $name = $this->_deriveNameFromShell($name);
        }

        if ($name instanceof CakeRequest) {
            $name = $this->_deriveNameFromRequest($name);
        }

        $this->_name = $name;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Change the application name.
     *
     * @param string $name
     *
     * @return void
     */
    public function applicationName($name)
    {
        NewRelic::getInstance()->applicationName($name);
    }

    /**
     * Start a NewRelic transaction.
     *
     * @param null|string $name
     *
     * @return void
     */
    public function start($name = null)
    {
        NewRelic::getInstance()->start($this->_getTransactionName($name));
    }

    /**
     * Stop a transaction.
     *
     * @return void
     */
    public function stop($ignore = false)
    {
        NewRelic::getInstance()->stop($ignore);
    }

    /**
     * Ignore current transaction.
     *
     * @return void
     */
    public function ignoreTransaction()
    {
        NewRelic::getInstance()->ignoreTransaction();
    }

    /**
     * Ignore current apdex.
     *
     * @return void
     */
    public function ignoreApdex()
    {
        NewRelic::getInstance()->ignoreApdex();
    }

    /**
     * Add custom parameter to transaction.
     *
     * @param string $key
     * @param scalar $value
     *
     * @return void
     */
    public function parameter($key, $value)
    {
        NewRelic::getInstance()->parameter($key, $value);
    }

    /**
     * Add custom metric.
     *
     * @param string $key
     * @param float  $value
     *
     * @return void
     */
    public function metric($key, $value)
    {
        NewRelic::getInstance()->metric($key, $value);
    }

    /**
     * capture params.
     *
     * @param bool $capture
     *
     * @return void
     */
    public function captureParams($capture)
    {
        NewRelic::getInstance()->captureParams($capture);
    }

    /**
     * Add custom tracer method.
     *
     * @param string $method
     */
    public function addTracer($method)
    {
        NewRelic::getInstance()->addTracer($method);
    }

    /**
     * Set user attributes.
     *
     * @param string $user
     * @param string $account
     * @param string $product
     *
     * @return void
     */
    public function user($user, $account, $product)
    {
        NewRelic::getInstance()->user($user, $account, $product);
    }

    /**
     * Send an exception to New Relic.
     *
     * @param Exception $e
     *
     * @return void
     */
    public function sendException(Exception $e)
    {
        NewRelic::getInstance()->sendException($e);
    }

    /**
     * Get transaction name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function _getTransactionName($name)
    {
        if (is_string($name)) {
            return $name;
        }

        return $this->_name;
    }

    /**
     * Derive the transaction name.
     *
     * @param Shell $name
     *
     * @return string
     */
    protected function _deriveNameFromShell(Shell $shell)
    {
        $name = [];

        if ($shell->plugin) {
            $name[] = $shell->plugin;
        }

        $name[] = $shell->name;
        $name[] = $shell->command;

        return implode('/', $name);
    }

    /**
     * Compute name based on request information.
     *
     * @param CakeRequest $request
     *
     * @return string
     */
    protected function _deriveNameFromRequest(CakeRequest $request)
    {
        $name = [];

        if ($request->prefix) {
            $name[] = $request->prefix;
        }

        if ($request->plugin) {
            $name[] = $request->plugin;
        }

        $name[] = $request->controller;
        $name[] = $request->action;

        $name = array_filter($name);
        if (empty($name)) {
            return $request->here;
        }

        $name = implode('/', $name);

        if ($request->ext) {
            $name .= '.'.$request->ext;
        }

        return $name;
    }
}
