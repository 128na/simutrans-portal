<?php
// @formatter:off
// phpcs:ignoreFile

/**
 * A helper file for Laravel, to provide autocomplete information to your IDE
 * Generated for Laravel 10.41.0.
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @see https://github.com/barryvdh/laravel-ide-helper
 */

    namespace Barryvdh\Debugbar\Facades { 
            /**
     * 
     *
     * @method static void alert(mixed $message)
     * @method static void critical(mixed $message)
     * @method static void debug(mixed $message)
     * @method static void emergency(mixed $message)
     * @method static void error(mixed $message)
     * @method static void info(mixed $message)
     * @method static void log(mixed $message)
     * @method static void notice(mixed $message)
     * @method static void warning(mixed $message)
     * @see \Barryvdh\Debugbar\LaravelDebugbar
     */ 
        class Debugbar {
                    /**
         * Enable the Debugbar and boot, if not already booted.
         *
         * @static 
         */ 
        public static function enable()
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->enable();
        }
                    /**
         * Boot the debugbar (add collectors, renderer and listener)
         *
         * @static 
         */ 
        public static function boot()
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->boot();
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function shouldCollect($name, $default = false)
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->shouldCollect($name, $default);
        }
                    /**
         * Adds a data collector
         *
         * @param \DebugBar\DataCollector\DataCollectorInterface $collector
         * @throws DebugBarException
         * @return \Barryvdh\Debugbar\LaravelDebugbar 
         * @static 
         */ 
        public static function addCollector($collector)
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->addCollector($collector);
        }
                    /**
         * Handle silenced errors
         *
         * @param $level
         * @param $message
         * @param string $file
         * @param int $line
         * @param array $context
         * @throws \ErrorException
         * @static 
         */ 
        public static function handleError($level, $message, $file = '', $line = 0, $context = [])
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->handleError($level, $message, $file, $line, $context);
        }
                    /**
         * Starts a measure
         *
         * @param string $name Internal name, used to stop the measure
         * @param string $label Public name
         * @static 
         */ 
        public static function startMeasure($name, $label = null)
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->startMeasure($name, $label);
        }
                    /**
         * Stops a measure
         *
         * @param string $name
         * @static 
         */ 
        public static function stopMeasure($name)
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->stopMeasure($name);
        }
                    /**
         * Adds an exception to be profiled in the debug bar
         *
         * @param \Exception $e
         * @deprecated in favor of addThrowable
         * @static 
         */ 
        public static function addException($e)
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->addException($e);
        }
                    /**
         * Adds an exception to be profiled in the debug bar
         *
         * @param \Throwable $e
         * @static 
         */ 
        public static function addThrowable($e)
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->addThrowable($e);
        }
                    /**
         * Returns a JavascriptRenderer for this instance
         *
         * @param string $baseUrl
         * @param string $basePathng
         * @return \Barryvdh\Debugbar\JavascriptRenderer 
         * @static 
         */ 
        public static function getJavascriptRenderer($baseUrl = null, $basePath = null)
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->getJavascriptRenderer($baseUrl, $basePath);
        }
                    /**
         * Modify the response and inject the debugbar (or data in headers)
         *
         * @param \Symfony\Component\HttpFoundation\Request $request
         * @param \Symfony\Component\HttpFoundation\Response $response
         * @return \Symfony\Component\HttpFoundation\Response 
         * @static 
         */ 
        public static function modifyResponse($request, $response)
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->modifyResponse($request, $response);
        }
                    /**
         * Check if the Debugbar is enabled
         *
         * @return boolean 
         * @static 
         */ 
        public static function isEnabled()
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->isEnabled();
        }
                    /**
         * Collects the data from the collectors
         *
         * @return array 
         * @static 
         */ 
        public static function collect()
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->collect();
        }
                    /**
         * Injects the web debug toolbar into the given Response.
         *
         * @param \Symfony\Component\HttpFoundation\Response $response A Response instance
         * Based on https://github.com/symfony/WebProfilerBundle/blob/master/EventListener/WebDebugToolbarListener.php
         * @static 
         */ 
        public static function injectDebugbar($response)
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->injectDebugbar($response);
        }
                    /**
         * Disable the Debugbar
         *
         * @static 
         */ 
        public static function disable()
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->disable();
        }
                    /**
         * Adds a measure
         *
         * @param string $label
         * @param float $start
         * @param float $end
         * @static 
         */ 
        public static function addMeasure($label, $start, $end)
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->addMeasure($label, $start, $end);
        }
                    /**
         * Utility function to measure the execution of a Closure
         *
         * @param string $label
         * @param \Closure $closure
         * @return mixed 
         * @static 
         */ 
        public static function measure($label, $closure)
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->measure($label, $closure);
        }
                    /**
         * Collect data in a CLI request
         *
         * @return array 
         * @static 
         */ 
        public static function collectConsole()
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->collectConsole();
        }
                    /**
         * Adds a message to the MessagesCollector
         * 
         * A message can be anything from an object to a string
         *
         * @param mixed $message
         * @param string $label
         * @static 
         */ 
        public static function addMessage($message, $label = 'info')
        {
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->addMessage($message, $label);
        }
                    /**
         * Checks if a data collector has been added
         *
         * @param string $name
         * @return boolean 
         * @static 
         */ 
        public static function hasCollector($name)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->hasCollector($name);
        }
                    /**
         * Returns a data collector
         *
         * @param string $name
         * @return \DebugBar\DataCollector\DataCollectorInterface 
         * @throws DebugBarException
         * @static 
         */ 
        public static function getCollector($name)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->getCollector($name);
        }
                    /**
         * Returns an array of all data collectors
         *
         * @return \DebugBar\array[DataCollectorInterface] 
         * @static 
         */ 
        public static function getCollectors()
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->getCollectors();
        }
                    /**
         * Sets the request id generator
         *
         * @param \DebugBar\RequestIdGeneratorInterface $generator
         * @return \Barryvdh\Debugbar\LaravelDebugbar 
         * @static 
         */ 
        public static function setRequestIdGenerator($generator)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->setRequestIdGenerator($generator);
        }
                    /**
         * 
         *
         * @return \DebugBar\RequestIdGeneratorInterface 
         * @static 
         */ 
        public static function getRequestIdGenerator()
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->getRequestIdGenerator();
        }
                    /**
         * Returns the id of the current request
         *
         * @return string 
         * @static 
         */ 
        public static function getCurrentRequestId()
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->getCurrentRequestId();
        }
                    /**
         * Sets the storage backend to use to store the collected data
         *
         * @param \DebugBar\StorageInterface $storage
         * @return \Barryvdh\Debugbar\LaravelDebugbar 
         * @static 
         */ 
        public static function setStorage($storage = null)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->setStorage($storage);
        }
                    /**
         * 
         *
         * @return \DebugBar\StorageInterface 
         * @static 
         */ 
        public static function getStorage()
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->getStorage();
        }
                    /**
         * Checks if the data will be persisted
         *
         * @return boolean 
         * @static 
         */ 
        public static function isDataPersisted()
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->isDataPersisted();
        }
                    /**
         * Sets the HTTP driver
         *
         * @param \DebugBar\HttpDriverInterface $driver
         * @return \Barryvdh\Debugbar\LaravelDebugbar 
         * @static 
         */ 
        public static function setHttpDriver($driver)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->setHttpDriver($driver);
        }
                    /**
         * Returns the HTTP driver
         * 
         * If no http driver where defined, a PhpHttpDriver is automatically created
         *
         * @return \DebugBar\HttpDriverInterface 
         * @static 
         */ 
        public static function getHttpDriver()
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->getHttpDriver();
        }
                    /**
         * Returns collected data
         * 
         * Will collect the data if none have been collected yet
         *
         * @return array 
         * @static 
         */ 
        public static function getData()
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->getData();
        }
                    /**
         * Returns an array of HTTP headers containing the data
         *
         * @param string $headerName
         * @param integer $maxHeaderLength
         * @return array 
         * @static 
         */ 
        public static function getDataAsHeaders($headerName = 'phpdebugbar', $maxHeaderLength = 4096, $maxTotalHeaderLength = 250000)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->getDataAsHeaders($headerName, $maxHeaderLength, $maxTotalHeaderLength);
        }
                    /**
         * Sends the data through the HTTP headers
         *
         * @param bool $useOpenHandler
         * @param string $headerName
         * @param integer $maxHeaderLength
         * @return \Barryvdh\Debugbar\LaravelDebugbar 
         * @static 
         */ 
        public static function sendDataInHeaders($useOpenHandler = null, $headerName = 'phpdebugbar', $maxHeaderLength = 4096)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->sendDataInHeaders($useOpenHandler, $headerName, $maxHeaderLength);
        }
                    /**
         * Stacks the data in the session for later rendering
         *
         * @static 
         */ 
        public static function stackData()
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->stackData();
        }
                    /**
         * Checks if there is stacked data in the session
         *
         * @return boolean 
         * @static 
         */ 
        public static function hasStackedData()
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->hasStackedData();
        }
                    /**
         * Returns the data stacked in the session
         *
         * @param boolean $delete Whether to delete the data in the session
         * @return array 
         * @static 
         */ 
        public static function getStackedData($delete = true)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->getStackedData($delete);
        }
                    /**
         * Sets the key to use in the $_SESSION array
         *
         * @param string $ns
         * @return \Barryvdh\Debugbar\LaravelDebugbar 
         * @static 
         */ 
        public static function setStackDataSessionNamespace($ns)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->setStackDataSessionNamespace($ns);
        }
                    /**
         * Returns the key used in the $_SESSION array
         *
         * @return string 
         * @static 
         */ 
        public static function getStackDataSessionNamespace()
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->getStackDataSessionNamespace();
        }
                    /**
         * Sets whether to only use the session to store stacked data even
         * if a storage is enabled
         *
         * @param boolean $enabled
         * @return \Barryvdh\Debugbar\LaravelDebugbar 
         * @static 
         */ 
        public static function setStackAlwaysUseSessionStorage($enabled = true)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->setStackAlwaysUseSessionStorage($enabled);
        }
                    /**
         * Checks if the session is always used to store stacked data
         * even if a storage is enabled
         *
         * @return boolean 
         * @static 
         */ 
        public static function isStackAlwaysUseSessionStorage()
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->isStackAlwaysUseSessionStorage();
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function offsetSet($key, $value)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->offsetSet($key, $value);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function offsetGet($key)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->offsetGet($key);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function offsetExists($key)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->offsetExists($key);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function offsetUnset($key)
        {            //Method inherited from \DebugBar\DebugBar         
                        /** @var \Barryvdh\Debugbar\LaravelDebugbar $instance */
                        return $instance->offsetUnset($key);
        }
         
    }
     
}

    namespace Berkayk\OneSignal { 
            /**
     * 
     *
     */ 
        class OneSignalFacade {
                    /**
         * Turn on, turn off async requests
         *
         * @param bool $on
         * @return \Berkayk\OneSignal\OneSignalClient 
         * @static 
         */ 
        public static function async($on = true)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->async($on);
        }
                    /**
         * Callback to execute after OneSignal returns the response
         *
         * @param Callable $requestCallback
         * @return \Berkayk\OneSignal\OneSignalClient 
         * @static 
         */ 
        public static function callback($requestCallback)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->callback($requestCallback);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function testCredentials()
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->testCredentials();
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function addParams($params = [])
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->addParams($params);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function setParam($key, $value)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->setParam($key, $value);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function sendNotificationToUser($message, $userId, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->sendNotificationToUser($message, $userId, $url, $data, $buttons, $schedule, $headings, $subtitle);
        }
                    /**
         * 
         *
         * @param $message
         * @param $userId
         * @param null $url
         * @param null $data
         * @param null $buttons
         * @param null $schedule
         * @param null $headings
         * @param null $subtitle
         * @static 
         */ 
        public static function sendNotificationToExternalUser($message, $userId, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->sendNotificationToExternalUser($message, $userId, $url, $data, $buttons, $schedule, $headings, $subtitle);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function sendNotificationUsingTags($message, $tags, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->sendNotificationUsingTags($message, $tags, $url, $data, $buttons, $schedule, $headings, $subtitle);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function sendNotificationToAll($message, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->sendNotificationToAll($message, $url, $data, $buttons, $schedule, $headings, $subtitle);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function sendNotificationToSegment($message, $segment, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->sendNotificationToSegment($message, $segment, $url, $data, $buttons, $schedule, $headings, $subtitle);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function deleteNotification($notificationId, $appId = null)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->deleteNotification($notificationId, $appId);
        }
                    /**
         * Send a notification with custom parameters defined in
         * https://documentation.onesignal.com/reference#section-example-code-create-notification
         *
         * @param array $parameters
         * @return mixed 
         * @static 
         */ 
        public static function sendNotificationCustom($parameters = [])
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->sendNotificationCustom($parameters);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function getNotification($notification_id, $app_id = null)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->getNotification($notification_id, $app_id);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function getNotifications($app_id = null, $limit = null, $offset = null)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->getNotifications($app_id, $limit, $offset);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function getApp($app_id = null)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->getApp($app_id);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function getApps()
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->getApps();
        }
                    /**
         * Creates a user/player
         *
         * @param array $parameters
         * @return mixed 
         * @throws \Exception
         * @static 
         */ 
        public static function createPlayer($parameters)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->createPlayer($parameters);
        }
                    /**
         * Edit a user/player
         *
         * @param array $parameters
         * @return mixed 
         * @static 
         */ 
        public static function editPlayer($parameters)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->editPlayer($parameters);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function requestPlayersCSV($app_id = null, $parameters = null)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->requestPlayersCSV($app_id, $parameters);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function post($endPoint)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->post($endPoint);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function put($endPoint)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->put($endPoint);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function get($endPoint)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->get($endPoint);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function delete($endPoint)
        {
                        /** @var \Berkayk\OneSignal\OneSignalClient $instance */
                        return $instance->delete($endPoint);
        }
         
    }
     
}

    namespace HTMLMin\HTMLMin\Facades { 
            /**
     * This is the htmlmin facade class.
     *
     * @author Graham Campbell <graham@alt-three.com>
     */ 
        class HTMLMin {
                    /**
         * Get the minified blade.
         *
         * @param string $value
         * @return string 
         * @static 
         */ 
        public static function blade($value)
        {
                        /** @var \HTMLMin\HTMLMin\HTMLMin $instance */
                        return $instance->blade($value);
        }
                    /**
         * Get the minified css.
         *
         * @param string $value
         * @return string 
         * @static 
         */ 
        public static function css($value)
        {
                        /** @var \HTMLMin\HTMLMin\HTMLMin $instance */
                        return $instance->css($value);
        }
                    /**
         * Get the minified js.
         *
         * @param string $value
         * @return string 
         * @static 
         */ 
        public static function js($value)
        {
                        /** @var \HTMLMin\HTMLMin\HTMLMin $instance */
                        return $instance->js($value);
        }
                    /**
         * Get the minified html.
         *
         * @param string $value
         * @return string 
         * @static 
         */ 
        public static function html($value)
        {
                        /** @var \HTMLMin\HTMLMin\HTMLMin $instance */
                        return $instance->html($value);
        }
                    /**
         * Return the blade minifier instance.
         *
         * @return \HTMLMin\HTMLMin\Minifiers\BladeMinifier 
         * @static 
         */ 
        public static function getBladeMinifier()
        {
                        /** @var \HTMLMin\HTMLMin\HTMLMin $instance */
                        return $instance->getBladeMinifier();
        }
                    /**
         * Return the css minifier instance.
         *
         * @return \HTMLMin\HTMLMin\Minifiers\CssMinifier 
         * @static 
         */ 
        public static function getCssMinifier()
        {
                        /** @var \HTMLMin\HTMLMin\HTMLMin $instance */
                        return $instance->getCssMinifier();
        }
                    /**
         * Return the js minifier instance.
         *
         * @return \HTMLMin\HTMLMin\Minifiers\JsMinifier 
         * @static 
         */ 
        public static function getJsMinifier()
        {
                        /** @var \HTMLMin\HTMLMin\HTMLMin $instance */
                        return $instance->getJsMinifier();
        }
                    /**
         * Return the html minifier instance.
         *
         * @return \HTMLMin\HTMLMin\Minifiers\HtmlMinifier 
         * @static 
         */ 
        public static function getHtmlMinifier()
        {
                        /** @var \HTMLMin\HTMLMin\HTMLMin $instance */
                        return $instance->getHtmlMinifier();
        }
         
    }
     
}

    namespace Intervention\HttpAuth\Laravel\Facades { 
            /**
     * 
     *
     */ 
        class HttpAuth {
                    /**
         * Static factory method
         *
         * @param array $config
         * @return \HttpAuth 
         * @static 
         */ 
        public static function make($config = [])
        {
                        return \Intervention\HttpAuth\HttpAuth::make($config);
        }
                    /**
         * Create vault by current parameters and secure it
         *
         * @return void 
         * @static 
         */ 
        public static function secure()
        {
                        /** @var \Intervention\HttpAuth\HttpAuth $instance */
                        $instance->secure();
        }
                    /**
         * Create HTTP basic auth instance
         *
         * @return \HttpAuth 
         * @static 
         */ 
        public static function basic()
        {
                        /** @var \Intervention\HttpAuth\HttpAuth $instance */
                        return $instance->basic();
        }
                    /**
         * Create HTTP digest auth instance
         *
         * @return \HttpAuth 
         * @static 
         */ 
        public static function digest()
        {
                        /** @var \Intervention\HttpAuth\HttpAuth $instance */
                        return $instance->digest();
        }
                    /**
         * Set type of configured vault
         *
         * @param string $value
         * @return \HttpAuth 
         * @static 
         */ 
        public static function type($value)
        {
                        /** @var \Intervention\HttpAuth\HttpAuth $instance */
                        return $instance->type($value);
        }
                    /**
         * Set realm name of configured vault
         *
         * @param string $value
         * @return \HttpAuth 
         * @static 
         */ 
        public static function realm($value)
        {
                        /** @var \Intervention\HttpAuth\HttpAuth $instance */
                        return $instance->realm($value);
        }
                    /**
         * Set username of configured vault
         *
         * @param string $value
         * @return \HttpAuth 
         * @static 
         */ 
        public static function username($value)
        {
                        /** @var \Intervention\HttpAuth\HttpAuth $instance */
                        return $instance->username($value);
        }
                    /**
         * Set password of configured vault
         *
         * @param string $value
         * @return \HttpAuth 
         * @static 
         */ 
        public static function password($value)
        {
                        /** @var \Intervention\HttpAuth\HttpAuth $instance */
                        return $instance->password($value);
        }
                    /**
         * Set credentials for configured vault
         *
         * @param string $username
         * @param string $password
         * @return \HttpAuth 
         * @static 
         */ 
        public static function credentials($username, $password)
        {
                        /** @var \Intervention\HttpAuth\HttpAuth $instance */
                        return $instance->credentials($username, $password);
        }
                    /**
         * Get type of current instance
         *
         * @return mixed 
         * @static 
         */ 
        public static function getType()
        {
                        /** @var \Intervention\HttpAuth\HttpAuth $instance */
                        return $instance->getType();
        }
                    /**
         * Get realm of current instance
         *
         * @return mixed 
         * @static 
         */ 
        public static function getRealm()
        {
                        /** @var \Intervention\HttpAuth\HttpAuth $instance */
                        return $instance->getRealm();
        }
                    /**
         * Get username of current instance
         *
         * @return mixed 
         * @static 
         */ 
        public static function getUsername()
        {
                        /** @var \Intervention\HttpAuth\HttpAuth $instance */
                        return $instance->getUsername();
        }
                    /**
         * Get password of current instance
         *
         * @return mixed 
         * @static 
         */ 
        public static function getPassword()
        {
                        /** @var \Intervention\HttpAuth\HttpAuth $instance */
                        return $instance->getPassword();
        }
         
    }
     
}

    namespace Spatie\LaravelIgnition\Facades { 
            /**
     * 
     *
     * @see \Spatie\FlareClient\Flare
     */ 
        class Flare {
                    /**
         * 
         *
         * @static 
         */ 
        public static function make($apiKey = null, $contextDetector = null)
        {
                        return \Spatie\FlareClient\Flare::make($apiKey, $contextDetector);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function setApiToken($apiToken)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->setApiToken($apiToken);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function apiTokenSet()
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->apiTokenSet();
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function setBaseUrl($baseUrl)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->setBaseUrl($baseUrl);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function setStage($stage)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->setStage($stage);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function sendReportsImmediately()
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->sendReportsImmediately();
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function determineVersionUsing($determineVersionCallable)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->determineVersionUsing($determineVersionCallable);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function reportErrorLevels($reportErrorLevels)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->reportErrorLevels($reportErrorLevels);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function filterExceptionsUsing($filterExceptionsCallable)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->filterExceptionsUsing($filterExceptionsCallable);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function filterReportsUsing($filterReportsCallable)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->filterReportsUsing($filterReportsCallable);
        }
                    /**
         * 
         *
         * @param array<class-string<ArgumentReducer>|ArgumentReducer>|\Spatie\Backtrace\Arguments\ArgumentReducers|null $argumentReducers
         * @static 
         */ 
        public static function argumentReducers($argumentReducers)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->argumentReducers($argumentReducers);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function withStackFrameArguments($withStackFrameArguments = true)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->withStackFrameArguments($withStackFrameArguments);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function version()
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->version();
        }
                    /**
         * 
         *
         * @return array<int, FlareMiddleware|class-string<FlareMiddleware>> 
         * @static 
         */ 
        public static function getMiddleware()
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->getMiddleware();
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function setContextProviderDetector($contextDetector)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->setContextProviderDetector($contextDetector);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function setContainer($container)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->setContainer($container);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function registerFlareHandlers()
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->registerFlareHandlers();
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function registerExceptionHandler()
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->registerExceptionHandler();
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function registerErrorHandler()
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->registerErrorHandler();
        }
                    /**
         * 
         *
         * @param \Spatie\FlareClient\FlareMiddleware\FlareMiddleware|array<FlareMiddleware>|\Spatie\FlareClient\class-string<FlareMiddleware>|callable $middleware
         * @return \Spatie\FlareClient\Flare 
         * @static 
         */ 
        public static function registerMiddleware($middleware)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->registerMiddleware($middleware);
        }
                    /**
         * 
         *
         * @return array<int,FlareMiddleware|class-string<FlareMiddleware>> 
         * @static 
         */ 
        public static function getMiddlewares()
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->getMiddlewares();
        }
                    /**
         * 
         *
         * @param string $name
         * @param string $messageLevel
         * @param array<int, mixed> $metaData
         * @return \Spatie\FlareClient\Flare 
         * @static 
         */ 
        public static function glow($name, $messageLevel = 'info', $metaData = [])
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->glow($name, $messageLevel, $metaData);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function handleException($throwable)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->handleException($throwable);
        }
                    /**
         * 
         *
         * @return mixed 
         * @static 
         */ 
        public static function handleError($code, $message, $file = '', $line = 0)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->handleError($code, $message, $file, $line);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function applicationPath($applicationPath)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->applicationPath($applicationPath);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function report($throwable, $callback = null, $report = null)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->report($throwable, $callback, $report);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function reportMessage($message, $logLevel, $callback = null)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->reportMessage($message, $logLevel, $callback);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function sendTestReport($throwable)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->sendTestReport($throwable);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function reset()
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->reset();
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function anonymizeIp()
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->anonymizeIp();
        }
                    /**
         * 
         *
         * @param array<int, string> $fieldNames
         * @return \Spatie\FlareClient\Flare 
         * @static 
         */ 
        public static function censorRequestBodyFields($fieldNames)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->censorRequestBodyFields($fieldNames);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function createReport($throwable)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->createReport($throwable);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function createReportFromMessage($message, $logLevel)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->createReportFromMessage($message, $logLevel);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function stage($stage)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->stage($stage);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function messageLevel($messageLevel)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->messageLevel($messageLevel);
        }
                    /**
         * 
         *
         * @param string $groupName
         * @param mixed $default
         * @return array<int, mixed> 
         * @static 
         */ 
        public static function getGroup($groupName = 'context', $default = [])
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->getGroup($groupName, $default);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function context($key, $value)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->context($key, $value);
        }
                    /**
         * 
         *
         * @param string $groupName
         * @param array<string, mixed> $properties
         * @return \Spatie\FlareClient\Flare 
         * @static 
         */ 
        public static function group($groupName, $properties)
        {
                        /** @var \Spatie\FlareClient\Flare $instance */
                        return $instance->group($groupName, $properties);
        }
         
    }
     
}

    namespace Spatie\SignalAwareCommand\Facades { 
            /**
     * 
     *
     * @see \Spatie\SignalAwareCommand\Signal
     */ 
        class Signal {
                    /**
         * 
         *
         * @static 
         */ 
        public static function handle($signal, $callable)
        {
                        /** @var \Spatie\SignalAwareCommand\Signal $instance */
                        return $instance->handle($signal, $callable);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function executeSignalHandlers($signal, $command)
        {
                        /** @var \Spatie\SignalAwareCommand\Signal $instance */
                        return $instance->executeSignalHandlers($signal, $command);
        }
                    /**
         * 
         *
         * @static 
         */ 
        public static function clearHandlers($signal = null)
        {
                        /** @var \Spatie\SignalAwareCommand\Signal $instance */
                        return $instance->clearHandlers($signal);
        }
         
    }
     
}

    namespace Illuminate\Support { 
            /**
     * 
     *
     * @template TKey of array-key
     * @template-covariant TValue
     * @implements \ArrayAccess<TKey, TValue>
     * @implements \Illuminate\Support\Enumerable<TKey, TValue>
     */ 
        class Collection {
                    /**
         * 
         *
         * @see \Barryvdh\Debugbar\ServiceProvider::register()
         * @static 
         */ 
        public static function debug()
        {
                        return \Illuminate\Support\Collection::debug();
        }
         
    }
     
}

    namespace Illuminate\Http { 
            /**
     * 
     *
     */ 
        class Request {
                    /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestValidation()
         * @param array $rules
         * @param mixed $params
         * @static 
         */ 
        public static function validate($rules, ...$params)
        {
                        return \Illuminate\Http\Request::validate($rules, ...$params);
        }
                    /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestValidation()
         * @param string $errorBag
         * @param array $rules
         * @param mixed $params
         * @static 
         */ 
        public static function validateWithBag($errorBag, $rules, ...$params)
        {
                        return \Illuminate\Http\Request::validateWithBag($errorBag, $rules, ...$params);
        }
                    /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $absolute
         * @static 
         */ 
        public static function hasValidSignature($absolute = true)
        {
                        return \Illuminate\Http\Request::hasValidSignature($absolute);
        }
                    /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @static 
         */ 
        public static function hasValidRelativeSignature()
        {
                        return \Illuminate\Http\Request::hasValidRelativeSignature();
        }
                    /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $ignoreQuery
         * @param mixed $absolute
         * @static 
         */ 
        public static function hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
        {
                        return \Illuminate\Http\Request::hasValidSignatureWhileIgnoring($ignoreQuery, $absolute);
        }
         
    }
     
}

    namespace Illuminate\Routing { 
            /**
     * 
     *
     * @mixin \Illuminate\Routing\RouteRegistrar
     */ 
        class Router {
                    /**
         * 
         *
         * @see \Spatie\Feed\FeedServiceProvider::registerRouteMacro()
         * @param mixed $baseUrl
         * @static 
         */ 
        public static function feeds($baseUrl = '')
        {
                        return \Illuminate\Routing\Router::feeds($baseUrl);
        }
                    /**
         * 
         *
         * @see \Laravel\Ui\AuthRouteMethods::auth()
         * @param mixed $options
         * @static 
         */ 
        public static function auth($options = [])
        {
                        return \Illuminate\Routing\Router::auth($options);
        }
                    /**
         * 
         *
         * @see \Laravel\Ui\AuthRouteMethods::resetPassword()
         * @static 
         */ 
        public static function resetPassword()
        {
                        return \Illuminate\Routing\Router::resetPassword();
        }
                    /**
         * 
         *
         * @see \Laravel\Ui\AuthRouteMethods::confirmPassword()
         * @static 
         */ 
        public static function confirmPassword()
        {
                        return \Illuminate\Routing\Router::confirmPassword();
        }
                    /**
         * 
         *
         * @see \Laravel\Ui\AuthRouteMethods::emailVerification()
         * @static 
         */ 
        public static function emailVerification()
        {
                        return \Illuminate\Routing\Router::emailVerification();
        }
         
    }
     
}


namespace  { 
            class Debugbar extends \Barryvdh\Debugbar\Facades\Debugbar {}
            class OneSignal extends \Berkayk\OneSignal\OneSignalFacade {}
            class HTMLMin extends \HTMLMin\HTMLMin\Facades\HTMLMin {}
            class HttpAuth extends \Intervention\HttpAuth\Laravel\Facades\HttpAuth {}
            class Flare extends \Spatie\LaravelIgnition\Facades\Flare {}
            class Signal extends \Spatie\SignalAwareCommand\Facades\Signal {}
     
}




