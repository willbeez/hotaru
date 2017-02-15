<?php

class myMemcache
{
	const DEFAULT_PORT = 11211;

	private $memcache;

	/**
	 * Creates a Memcache instance.
	 *
	 * Takes an $options array w/ the following parameters:
	 *
	 * <ul>
	 * <li><b>host:</b> host for the memcache server </li>
	 * <li><b>port:</b> port for the memcache server </li>
	 * </ul>
	 * @param array $options
	 */
	public function __construct($options)
	{
                if (class_exists('memcached')) {
                    $this->memcached = new Memcache('hotaru_' . SITENAME);
                } elseif(class_exists('memcache')) {
                    $this->memcache = new Memcache();
                } else {
                    return false; 
                }
                
		$options['port'] = isset($options['port']) ? $options['port'] : self::DEFAULT_PORT;

		if (!$this->memcache->connect($options['host'],$options['port'])) {
			throw new Exception("Could not connect to $options[host]:$options[port]");
                }
	}

	public function flush()
	{
		$result = $this->memcache->flush();
                
                $time = time()+1; //one second future 
                while(time() < $time) { 
                  //sleep 
                } 
                
                return $result;
	}

	public function read($key)
	{
		return $this->memcache->get($key);
	}

	public function write($key, $value, $expire)
	{
		$this->memcache->set($key,$value,null,$expire);
	}
}
