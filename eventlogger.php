<?php

/**
 * This is a simple API wrapper for EventLog (http://eventlogapp.com)
 *
 * Basic Usage:
 * 
 *     $logger = new EventLogger('foo@bar.com', 'password', 'your_app_api_key');
 *     $logger->log('Something went horribly wrong!');
 * 
 * @package    EventLogger
 * @author     Dan Horrigan
 * @copyright  2011 Dan Horrigan
 * @license    MIT License
 */
class EventLogger {
	
	const ERROR = 1;
	const WARNING = 2;
	const NOTICE = 3;
	const SUCCESS = 4;
	const GENERAL = 5;
	
	/**
	 * Holds the Curl handle.
	 *
	 * @var  handle
	 */
	protected $curl = null;

	/**
	 * The log queue
	 *
	 * @var  array
	 */
	protected $logs = array();
	
	/**
	 * The EventLog username
	 *
	 * @var  string
	 */
	protected $username = '';
	
	/**
	 * The password for EventLog
	 *
	 * @var  string
	 */
	protected $password = '';
	
	/**
	 * The API Key
	 *
	 * @var  string
	 */
	protected $api_key = '';
	
	/**
	 * The API Url
	 *
	 * @var  string
	 */
	protected $url = 'http://eventlogapp.com/api/log_message';
	
	/**
	 * Set everything up.
	 *
	 * @param  string  $username  the EventLog username (email)
	 * @param  string  $password  the EventLog password
	 * @param  string  $api_key   the EventLog API key
	 */
	public function __construct($username = '', $password = '', $api_key = '') {
		$this->username = $username;
		$this->password = $password;
		$this->api_key = $api_key;
		
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_URL, $this->url);
		
		register_shutdown_function(array($this, 'send_it'));
	}
	
	/**
	 * Shut the mother down!
	 *
	 * @return  void
	 */
	public function __destruct() {
		curl_close($this->curl);
	}
	
	/**
	 * Sets the username for EventLog
	 *
	 * @param   string       $username  the username
	 * @return  EventLogger  $this
	 */
	public function set_username($username) {
		$this->username = $username;
		return $this;
	}
	
	/**
	 * Sets the password for EventLog
	 *
	 * @param   string       $password  the password
	 * @return  EventLogger  $this
	 */
	public function set_password($password) {
		$this->password = $password;
		return $this;
	}
	
	/**
	 * Sets the API Key for EventLog
	 *
	 * @param   string       $api_key  the api key
	 * @return  EventLogger  $this
	 */
	public function set_api_key($api_key) {
		$this->api_key = $api_key;
		return $this;
	}
	
	/**
	 * Adds the given log to the queue.  Defaults to an ERROR message.
	 *
	 * @param   string       $message  the message
	 * @param   string       $type     the type
	 * @return  EventLogger  $this
	 */
	public function log($message, $type = EventLogger::ERROR) {
		$this->logs[] = array(
			'message' => $message,
			'type'    => $type,
		);
		return $this;
	}

	/**
	 * Sends all the logs to EventLog
	 *
	 * @return  EventLogger  $this
	 */
	public function send_it() {
		if (empty($this->logs)) {
			return;
		}
		
		foreach ($this->logs as $log) {
			$this->write($log['message'], $log['type']);
		}
		return $this;
	}

	/**
	 * Writes the log out to EventLog.  Will throw an exception on error.
	 *
	 * @param   string  $message  the message
	 * @param   string  $type     the event type
	 * @return  bool    true on success
	 * @throws  Exception
	 */
	public function write($message, $type = EventLogger::ERROR) {
		$fields = array(
			'username'   => $this->username,
			'password'   => $this->password,
			'event_type' => $type,
			'message'    => $message,
		);
		curl_setopt($this->curl, CURLOPT_URL, $this->url);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($fields));
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, array("X_API_KEY: {$this->api_key}"));

		$result = json_decode(curl_exec($this->curl));

		if ( ! $result->status) {
			throw new Exception('EventLog Error: '.$result->message);
		}
		return true;
	}

}

/* End of file eventlogger.php */