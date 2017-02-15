<?php
namespace Libs;

require_once __DIR__ . '/vendor/autoload.php';

class Hotaru extends Initialize
{    
        protected $version = '1.7.3';  // Hotaru CMS version       
        
        /**
	 * CONSTRUCTOR - Initialize
	 */
	public function __construct($start = '')
	{   
                // Managed directives
		ini_set('default_charset', $charset='UTF-8');
		ini_set('display_errors', 1);
		// Abort on startup error
		// Intercept errors/exceptions; PHP5.3-compatible
		error_reporting(E_ALL|E_STRICT);
                
                if(!ini_get('date.timezone')) {
                    date_default_timezone_set('GMT');
                }
            
                // define shorthand paths
		if (!defined('BASE')) {
                    $base = dirname(__FILE__). '/';
                    define("BASE", $base);
                    define("CACHE", $base.'cache/');
                    define("ADMIN", $base.'admin/');
                    define("INSTALL", $base.'install/');
                    define("LIBS", $base.'libs/');
                    define("EXTENSIONS", $base.'libs/extensions/');
                    define("FRAMEWORKS", $base.'libs/frameworks/');
                    define("FUNCTIONS", $base.'functions/');
                    define("CONTENT", $base.'content/');
                    define("THEMES", $base.'content/themes/');
                    define("PLUGINS", $base.'content/plugins/');
                    define("ADMIN_THEMES", $base.'content/admin_themes/');
                    define("SITEURL", BASEURL);
		}                        
                
		if (!$start) {
                        // initialize
                        parent::__construct();
                
                        $this->currentUser  = UserBase::instance();       // the current user
                        $this->displayUser  = DisplayUser::instance();
                        $this->plugin       = Plugin::instance();         // instantiate Plugin object
			$this->post         = Post::instance();           // instantiate Post object
			$this->comment      = Comment::instance();          
                        $this->includes     = IncludeCssJs::instance();   // instantiate Includes object
			$this->pageHandling = PageHandling::instance();   // instantiate PageHandling object
			$this->debug        = Debug::instance();          // instantiate Debug object
			
			$this->csrf('set');                         // set a csrfToken
                        
			$this->db->setHotaru($this);                // pass $h object to EzSQL for error reporting
                        //$this->mdb->setHotaru($this);               // pass $h object to meekroDb for error reporting
                        //print 'time: ' . timer_stop(4,'hotaru');
                        //roughly here at 0.0040 Nov 2, 2014 tests
                        // time: 0.0047 Nov11, 2014 after moving a few more functions to init
                }
        }
        
    
        /* *************************************************************
        *
        *  HOTARU FUNCTIONS
        *
        * *********************************************************** */

	/**
	 * START - the top of "Hotaru", i.e. the page-building process
	 */
	public function start($type = '')
	{
		// include "main" language pack
		$lang = Language::instance();
		$this->lang = $lang->includeLanguagePack($this->lang, 'main');
		
                // fills $h->pageName
		$this->getPageName();
		
                // special diversion for api calls to api plugin to avoid session,cookie vars etc
                if ($this->pageName == 'api') {
                    $type = 'api';
                }
                
		switch ($type) {
			case 'admin':
				$this->adminPage = true;
				$this->lang = $lang->includeLanguagePack($this->lang, 'admin');				
				Authorization::checkSession($this);                   // check cookie reads user details
				$this->checkSiteAccess();                   // site closed if no access permitted
				$admin = AdminAuth::instance();               // new Admin object
                                $page = $admin->adminInit($this);       // initialize Admin & get desired page
				$this->adminPages($page);               // Direct to desired Admin page
				break;
                        case 'api':
                                $this->adminPage = false;
                                //if (SITE_OPEN == 'false') { return true; }
                                if (!defined(REST_API) || REST_API == false) { return false; }

                                $this->pluginHook('hotaru_api', 'api');
                                
                                // dont check cookies, dont set session
                                // check access by http access
                                
                                // go to api class to extract data for this call
                                $this->apiCall();
                                
                                break;
                        case 'install':
                                $this->adminPage = false;
                                Authorization::checkSession($this);     // log in user if session exists
                                return;
                                break;
			default:
				$this->adminPage = false;
                                // TODO dont check cookie if we are using the login page or even the register page or forget password page maybe				
                                Authorization::checkSession($this);     // log in user if session exists
				$this->checkSiteAccess();               // site closed if no access permitted
				if (!$type) { return false; }           // stop here if start type not defined
				$this->template('index');               // displays the index page
                                break;
		}
		
		$lang->writeLanguageCache($this);
		
		exit;
	}
        
/* *************************************************************
 *
 *  ACCESS MODIFIERS
 *
 * *********************************************************** */
 
 
	/**
	 * Access modifier to set protected properties
	 */
	public function __set($var, $val)
	{
		$this->$var = $val;
	}
    
    
	/**
	 * Access modifier to get protected properties
	 * The & is necessary (http://bugs.php.net/bug.php?id=39449)
	 */
	public function &__get($var)
	{
		return $this->$var;
	}
        
        // to get protected properties
        public function __isset($name)
        {
                return isset($this->data[$name]);
        }

    
        /* *************************************************************
        *
        *  DEFAULT PLUGIN HOOK ACTIONS
        *
        * *********************************************************** */
 
     
	/**
	 * Include language file if available
	 */
	public function install_plugin()
	{
		$this->includeLanguage($this->plugin->folder);
	}
     
     
	/**
	 * Include All CSS and JavaScript files for this plugin
	 */
	public function header_include()
	{
		if ($this->adminPage) { return false; }
		
		// include a files that match the name of the plugin folder:
		$this->includeJs($this->plugin->folder); // folder name, filename
		$this->includeCss($this->plugin->folder);
	}
    
    
	/**
	 * Include All CSS and JavaScript files for this plugin in Admin
	 */
	public function admin_header_include()
	{
		if (!$this->adminPage) { return false; }
		
		// include a files that match the name of the plugin folder:
		$this->includeJs($this->plugin->folder); // folder name, filename
		$this->includeCss($this->plugin->folder);
	}
    
    
	/**
	 * Include code as a template before the closing </body> tag
	 */
	public function pre_close_body()
	{
		$this->template($this->plugin->folder . '_footer', $this->plugin->folder);
	}
    
	
	/**
	 * Display Admin settings page
	 *
	 * @return true
	 */
	public function admin_plugin_settings()
	{
		// This requires there to be a file in the plugin folder called pluginname_settings.php
		// The file must contain a class titled PluginNameSettings
		// The class must have a method called "settings".
		if (($this->cage->get->testAlnumLines('plugin') != $this->plugin->folder)
			&& ($this->cage->post->testAlnumLines('plugin') != $this->plugin->folder)) { 
			return false; 
		}
		
		if (file_exists(PLUGINS . $this->plugin->folder . '/' . $this->plugin->folder . '_settings.php')) {
		    include_once(PLUGINS . $this->plugin->folder . '/' . $this->plugin->folder . '_settings.php');
		
		    $settings_class = make_name($this->plugin->folder, '_') . 'Settings'; // e.g. CategoriesSettings
		    $settings_class = str_replace(' ', '', $settings_class); // strip spaces
		    $settings_object = new $settings_class();
		    $settings_object->settings($this);   // call the settings function		
		} else {
		    $this->showMessage($this->lang["admin_theme_plugins_filenotfound"] . "<br/><br/>", 'red');		    
                    $this->showMessage($this->lang["admin_theme_plugins_checkforfile"] . PLUGINS . $this->plugin->folder . '/' . $this->plugin->folder . '_settings.php', 'red');
		}
		return true;
	}
    
    
/* *************************************************************
 *
 *  PAGE HANDLING FUNCTIONS
 *
 * *********************************************************** */

	/**
	 * Set the homepage (and set page name)
	 *
	 * @param string $home
	 * @param string $pagename
	 */
	public function setHome($home = '', $pagename = '')
	{
		$this->pageHandling->setHome($this, $home, $pagename);
	}

	/**
	 * Test if the current url is the *true* homepage, i.e. equal to SITEURL
	 *
	 * @return bool
	 */
	public function isHome()
	{
		return $this->pageHandling->isHome($this);
	}
	
	
	/**
	 * Determine the title tags for the header
	 *
	 * @param bool $raw -return the title only
	 * @return string - the title
	 */
	public function getTitle($delimiter = ' &laquo; ', $raw = false)
	{
		return $this->pageHandling->getTitle($this, $delimiter, $raw);
	}
    
    
        /**
	 * Deprecated function in favor of $h->template
         * 
	 */
	public function displayTemplate($page = '', $plugin = '', $include_once = true)
	{
		$this->template($page, $plugin, $include_once);
	}
        
        
	/**
	 * Includes a template to display
	 *
	 * @param string $page page name
	 * @param string $plugin optional plugin name
	 * @param bool $include_once true or false
	 */
	public function template($page = '', $plugin = '', $include_once = true)
	{
		$this->pageHandling->template($this, $page, $plugin, $include_once);
	}
        
        
        /**
         * 
         * @return boolean
         */
        protected function apiCall()
        {
                $urlMethod = $this->cage->get->noTags('method'); 
                                
                $apiArray = explode('.', $urlMethod);
                // check that we have 3 parts
                $class = isset($apiArray[0]) ? $apiArray[0] : '';
                $method = isset($apiArray[1]) ? $apiArray[1] : '';
                $action = isset($apiArray[2]) ? $apiArray[2] : '';
                
                if ($class !== 'hotaru') { return false; }
                if (!$method || !$action ) { return false; }

                $result = $this->pluginHook('api_call', $method, $action);
                
                // log
                $content = 'IP: ' . $this->cage->server->testIp('REMOTE_ADDR') . ' URI: ' . $this->cage->server->sanitizeTags('REQUEST_URI') . '. Result: ' . var_export($result);
                $this->openLog('api_log');
                $this->writeLog('api_log', $content);
                $this->closeLog('api_log');
                
                // extract the right array and items for this call
                // try catch this to make sure no problems exist
                $arrayName = ucfirst($method) . '_api_call';  
                
                try {
                    if ($result) {
                        $result = array('error' => '', 'data' => $result);
                    } else {
                        $result = array('error' => 'data error');
                    }
                } catch (Exception $e) {
                    // send to debug log
                    $result = array('error' => 'data error');
                }                
                
                // return json
                if ($result) {
                    sendResponse(200, json_encode($result), 'application/json');
                } else {
                    sendResponse(501, sprintf('Mode <b>%s</b> is not implemented for <b>%s</b>', $action, $this->folder));
                }
        }
        
    
	/**
	 * Checks if current page (in url or form) matches the page parameter
	 *
	 * @param string $page page name
	 */
	public function isPage($page = '')
	{
		return $this->pageHandling->isPage($this, $page);
	}
    
    
	/**
	 * Check to see if the Admin settings page we are looking at  
	 * matches the plugin passed to this function.
	 *
	 * @param string $folder - plugin folder
	 * @return bool
	 *
	 *  Notes: This is used in "admin_header_include" so we only include the css, 
	 *         javascript etc. for the plugin we're trying to change settings for.
	 *  Usage: $h->isSettingsPage('submit') returns true if 
	 *         page=plugin_settings and plugin=submit in the url.
	 */
	public function isSettingsPage($folder = '')
	{
		return $this->pageHandling->isSettingsPage($this, $folder);
	}

    
	/**
	 * Gets the current page name
	 */
	public function getPageName()
	{
		$this->pageName = $this->pageHandling->getPageName($this);
		return $this->pageName;
	}
    
    
	/**
	 * Converts a friendly url into a standard one
	 *
	 * @param string $friendly_url
	 * return string $standard_url
	 */
	public function friendlyToStandardUrl($friendly_url) 
	{
		return $this->pageHandling->friendlyToStandardUrl($this, $friendly_url);
	}
    
    
	/**
	 * Generate either default or friendly urls
	 *
	 * @param array $parameters an array of pairs, e.g. 'page' => 'about' 
	 * @param string $head either 'index' or 'admin'
	 * @return string
	 */
	public function url($parameters = array(), $head = 'index')
	{
		return $this->pageHandling->url($this, $parameters, $head);
	}
    
        /**
         * 
         * @param type $page
         * @return type
         */
        public function urlPage($page = '')
        {
            $url = $this->url(array('page'=>$page));
            return $url;
        }
    
	/**
	 * Pagination with query and row count (better for large sets of data)
	 *
	 * @param string $query - SQL query
	 * @param int $total_items - total row count
	 * @param int $items_per_page
	 * @param string $cache_table - must provide a table, e.g. "posts" for caching to be used
	 * @return object|false - object
	 */
	public function pagination($query, $total_items, $items_per_page = 10, $cache_table = '')
	{
		$paginator = Paginator::instance();
		return $paginator->pagination($this, $query, $total_items, $items_per_page, $cache_table);
	}
    

	/**
	 * Pagination with full dataset (easier for small sets of data)
	 *
	 * @param array $data - array of results for paginating
	 * @param int $items_per_page
	 * @return object|false - object
	 */
	public function paginationFull($data, $items_per_page = 10)
	{
		$paginator = Paginator::instance();
		return $paginator->paginationFull($this, $data, $items_per_page);
	}
    
 
	/**
	 * Return page numbers bar
	 *
	 * @param object $paginator - current object of type Paginator
	 * @return string - HTML for page number bar
	 */
	public function pageBar($paginator = NULL)
	{
                if (!$paginator) {
                    $paginator = Paginator::instance();
                }
		return $paginator->pageBar($this);
	}
    

/* *************************************************************
 *
 *  BREADCRUMB FUNCTIONS
 *
 * *********************************************************** */
 
 
	/**
	 * Build breadcrumbs
	 */
	public function breadcrumbs($sep = "/")  //&raquo;
	{
		$breadcrumbs = Breadcrumbs::instance();
		return $breadcrumbs->buildBreadcrumbs($this, $sep);
	}
    
    
	/**
	 * prepares the RSS link found in breadcrumbs
	 *
	 * @param string $status - post status, e.g. new, top, etc.
	 * @param array $vars - array of key -> value pairs
	 * @return string
	 */    
	public function rssBreadcrumbsLink($status = '', $vars = array())
	{
		$breadcrumbs = Breadcrumbs::instance();
		return $breadcrumbs->rssBreadcrumbsLink($this, $status, $vars);
	}
    
 
 /* *************************************************************
 *
 *  USERAUTH FUNCTIONS / USERBASE FUNCTIONS
 *
 * *********************************************************** */
 
	/* UserBase & UserAuth functions should be called directly if you want to 
	   retain the user object being used. E.g.
		
		$user = new UserAuth();
		$user->getUserBasic($h);
		$user->updateUserBasic($h);
	*/
	
	
	/**
	 * check cookie and log in
	 *
	 * @return bool
	 */
	public function checkCookie()
	{
		Authorization::checkSession($this);
	}
        
        
        /**
	 * set cookie
	 *
	 * @return bool
	 */
	public function setCookie($remember = false)
	{
		Authorization::setCookie($this, $remember);
	}
        
        
        /**
         * TODO What are we actually checking here
         * This is the old loginCheck, the new passwordSignIn
         * 
         * @param type $username
         * @param type $password
         * @return type
         */
        public function loginCheck($username = '', $password = '', $rememberMe = false, $shouldLockout = false)
        {
                return Authorization::passwordSignIn($this, $username, $password, $rememberMe, $shouldLockout);   
        }
        
        
        public function passwordCheck($password = '')
        {
                return Authorization::passwordCheck($this, $password);
        }
        
        
        public function isUserLockedOut($username = '')
        {
                return Authorization::isUserLockedOut($this, $username);   
        }
	
        public function destroyCookieAndSession()
        {
                return Authorization::destroyCookieAndSession($this);   
        }
        
        
        public function newUserAuth()
        {
                return UserBase::instance();
        }
        
        
        /**
         * set the $user object to be the currentUser using a mapping plan
         * 
         * @param type $user
         */
        public function setCurrentUser($user)
        {
                $userbase = UserBase::instance();
                $userbase->setCurrentUser($this, $user);
        }
        
        
	/**
	 * Get basic user details
	 *
	 * @param int $userid 
	 * @param string $username
	 * @param bool $no_cache - set true to disable caching of SQl results
	 * @return array|false
	 *
	 * Note: Needs either userid or username, not both
	 */
	public function getUserBasic($userId = 0, $username = '', $no_cache = false)
	{
		$userbase = UserBase::instance();
		return $userbase->getUserBasic($this, $userId, $username, $no_cache);
	}
	
	
	/**
	 * Get full user details (i.e. permissions and settings, too)
	 *
	 * @param int $userid 
	 * @param string $username
	 * @param bool $no_cache - set true to disable caching of SQl results
	 * @return array|false
	 *
	 * Note: Needs either userid or username, not both
	 */
	public function getUser($userid = 0, $username = '', $no_cache = false)
	{
		$userbase = UserBase::instance();
		return $userbase->getUser($this, $userid, $username, $no_cache);
	}
	
        
        public function getUserLogins($userId)
        {
                return \Hotaru\Models2\UserLogin::getLogins($this, $userId);
        }
        
        
        public function deleteUserLogin($userId, $key)
        {
                return \Hotaru\Models2\UserLogin::removeLogin($this, $userId, $key);
        }
        
        
        public function deleteUserLogins($userId)
        {
                return \Hotaru\Models2\UserLogin::removeLogins($this, $userId);
        }
	
	/**
	 * Default permissions
	 *
	 * @param string $role or 'all'
	 * @param string $field 'site' for site defaults and 'base' for base defaults
	 * @param book $options_only returns just the options if true
	 * @return array $perms
	 */
	public function getDefaultPermissions($role = '', $defaults = 'site', $options_only = false) 
	{
		$userbase = UserBase::instance();
		return $userbase->getDefaultPermissions($this, $role, $defaults, $options_only);
	}
	
	
	/**
	 * Update Default permissions
	 *
	 * @param array $new_perms from a plugin's install function
	 * @param string $defaults - either "site", "base" or "both" 
	 * @param bool $remove - false if adding perms, true if deleting them
	 */
	public function updateDefaultPermissions($new_perms = array(), $defaults = 'both', $remove = false) 
	{
		$userbase = UserBase::instance();
		return $userbase->updateDefaultPermissions($this, $new_perms, $defaults, $remove);
	}
	
	
	/**
	 * Get the default user settings
	 *
	 * @param string $type either 'site' or 'base' (base for the originals)
	 * @return array
	 */
	public function getDefaultSettings($type = 'site')
	{
		$userbase = UserBase::instance();
		return $userbase->getDefaultSettings($this, $type);
	}
	
	
	/**
	 * Update the default user settings
	 *
	 * @param array $settings 
	 * @param string $type either 'site' or 'base' (base for the originals)
	 * @return array
	 */
	public function updateDefaultSettings($settings, $type = 'site')
	{
		$userbase = UserBase::instance();
		return $userbase->updateDefaultSettings($this, $settings, $type);
	}
	
	
	/**
	 * Get a user's profile or settings data
	 *
	 * @return array|false
	 */
	public function getProfileSettingsData($type = 'user_profile', $userid = 0, $check_exists_only = false)
	{
		$userbase = UserBase::instance();
		return $userbase->getProfileSettingsData($this, $type, $userid, $check_exists_only);
	}
	
	
	/**
	 * Physically delete a user
	 * Note: You should delete all their posts, comments, etc. first
	 *
	 * @param int $user_id (optional)
	 */
	public function deleteUser($user_id = 0) 
	{
		$userbase = UserBase::instance();
		return $userbase->deleteUser($this, $user_id);
	}


	/**
	 * Update user_lastvisit field
	 *
	 * @param int $user_id (optional)
	 */
	public function updateUserLastVisit($user_id = 0) 
	{
		return Authorization::updateUserLastVisit($this, $user_id);
	}
	

	/**
	 * Get User Roles - returns an array of role names
	 *
	 * @param string $type 'all', 'default', or 'custom'
	 * @return array|false
	 */
	public function getRoles($type = 'all') 
	{
		return $this->currentUser->getRoles($this, $type);
	}


	/**
	 * Get Unique User Roles - DEPRECATED Hotaru 1.4.1 - Plugins should be updated to use above getRoles() instead
	 *
	 * @return array|false
	 */
	public function getUniqueRoles() 
	{
		return $this->getRoles();
	}

	
 /* *************************************************************
 *
 *  USERINFO FUNCTIONS
 *
 * *********************************************************** */
	
	
	/**
	 * Get the username for a given user id
	 *
	 * @param int $id user id
	 * @return string|false
	 */
	public function getUserNameFromId($id = 0)
	{
		$userInfo = UserInfo::instance();
		return $userInfo->getUserNameFromId($this, $id);
	}
	
	
	/**
	 * Get the user id for a given username
	 *
	 * @param string $username
	 * @return int|false
	 */
	public function getUserIdFromName($username = '')
	{
		$userInfo = UserInfo::instance();
		return $userInfo->getUserIdFromName($this, $username);
	}
	
	
	/**
	 * Get the email from user id
	 *
	 * @param int $userid
	 * @return string|false
	 */
	public function getEmailFromId($userid = 0)
	{
		$userInfo = UserInfo::instance();
		return $userInfo->getEmailFromId($this, $userid);
	}
	
	
	/**
	 * Get the user id from email
	 *
	 * @param string $email
	 * @return string|false
	 */
	public function getUserIdFromEmail($email = '')
	{
		$userInfo = UserInfo::instance();
		return $userInfo->getUserIdFromEmail($this, $email);
	}
	
	
	 /**
	 * Checks if the user has an 'admin' role
	 *
	 * @return bool
	 */
	public function isAdmin($username = '')
	{                
		$userInfo = UserInfo::instance();
		return $userInfo->isAdmin($this, $username);
	}
	
	
	/**
	 * Check if a user exists
	 *
	 * @param int $userid 
	 * @param string $username
	 * @return int
	 *
	 * Notes: Returns 'no' if a user doesn't exist, else field under which found
	 */
	public function userExists($id = 0, $username = '', $email = '')
	{
		$userInfo = UserInfo::instance();
		return $userInfo->userExists($this->db, $id, $username, $email);
	}
	
	
	/**
	 * Check if an username exists in the database (used in forgotten password)
	 *
	 * @param string $username user username
	 * @param string $role user role (optional)
	 * @param int $exclude - exclude a user
	 * @return string|false
	 */
	public function nameExists($username = '', $role = '', $exclude = 0)
	{
		$userInfo = UserInfo::instance();
		return $userInfo->nameExists($this, $username, $role, $exclude);
	}
	
	
	/**
	 * Check if an email exists in the database (used in forgotten password)
	 *
	 * @param string $email user email
	 * @param string $role user role (optional)
	 * @param int $exclude - exclude a user
	 * @return string|false
	 */
	public function emailExists($email = '', $role = '', $exclude = 0)
	{
		$userInfo = UserInfo::instance();
		return $userInfo->emailExists($this, $email, $role, $exclude);
	}
	
	
	/**
	 * Get all users with permission to (access admin)
	 *
	 * @param string $permission
	 * @param string $value - value for the permission, usually yes, no, own or mod
	 * @return array
	 */
	public function getMods($permission = 'can_access_admin', $value = 'yes')
	{
		$userInfo = UserInfo::instance();
		return $userInfo->getMods($this, $permission, $value);
	}
	
	
	/**
	 * Get the ids and names of all users or those with a specified role, sorted alphabetically
	 *
	 * @param string $role - optional user role to filter to
	 * @return array
	 */
	public function userIdNameList($role = '')
	{
		$userInfo = UserInfo::instance();
		return $userInfo->userIdNameList($this, $role);
	}
	
	
	/**
	 * Get full details of all users or batches of users, sorted alphabetically
	 *
	 * @param array $id_array - optional array of user ids
	 * @param int $start - LIMIT $start $range (optional)
	 * @param int $range - LIMIT $start $range (optional)
	 * @return array
	 */
	public function userListFull($id_array = array(), $start = 0, $range = 0)
	{
		$userInfo = UserInfo::instance();
		return $userInfo->userListFull($this, $id_array, $start, $range);
	}
	
	
	/**
	 * Get settings for all users
	 *
	 * @param int $userid - optional user id 
	 * @return array
	 */
	public function userSettingsList($userid = 0)
	{
		$userInfo = UserInfo::instance();
		return $userInfo->userSettingsList($this, $userid);
	}

        
        public function getUsers($limit, $type = '', $fromId = 0)
        {
                $users = Users::instance();
		return $users->getUsers($this, $limit, $type, $fromId);
        }
    
 /* *************************************************************
 *
 *  PLUGIN FUNCTIONS
 *
 * *********************************************************** */
 
 
        /**
	 * Read and return plugin info from top of a plugin file.
	 *
	 * @param string $theme - theme folder
	 * @return array|false
	 */
	public function readPluginMeta($folder = '')
	{
		$pluginFunctions = PluginFunctions::instance();
		return $pluginFunctions->readPluginMeta($this, $folder);
	}
        
        
	/**
	 * Look for and run actions at a given plugin hook
	 *
	 * @param string $hook name of the plugin hook
	 * @param bool $perform false to check existence, true to actually run
	 * @param string $folder name of plugin folder
	 * @param array $parameters mixed values passed from plugin hook
	 * @return array | bool
	 */
	public function pluginHook($hook = '', $folder = '', $parameters = array(), $exclude = array())
	{
		$pluginFunctions = PluginFunctions::instance();
		return $pluginFunctions->pluginHook($this, $hook, $folder, $parameters, $exclude);
	}
	
	
	/**
	 * Get a single plugin's details for Hotaru
	 *
	 * @param string $folder - plugin folder name, else $h->plugin->folder is used
	 * @return array - $key array object, e.g. $key->plugin_id
	 */
	public function readPlugin($folder = '', $admin = false)
	{
		$pluginFunctions = PluginFunctions::instance();
		return $pluginFunctions->readPlugin($this, $folder, $admin);
	}
	
	
	/**
	 * Get a single property from a specified plugin
	 *
	 * @param string $property - plugin property, e.g. "plugin_version"
	 * @param string $folder - plugin folder name, else $h->plugin->folder is used
	 * @param string $field - an alternative field to use instead of $folder
	 */
	public function getPluginProperty($property = '', $folder = '', $field = '')
	{
		$pluginFunctions = PluginFunctions::instance();
		return $pluginFunctions->getPluginProperty($this, $property, $folder, $field);
	}
	
	
	/**
	 * Get number of active plugins
	 *
	 * @return int|false
	 */
	public function numActivePlugins()
	{
                return isset($this->plugins['activeFolders']) ? count($this->plugins['activeFolders']) : 0;
	}
	
	
	/**
	 * Get version number of plugin if active
	 *
	 * @param string $folder plugin folder name
	 * @return string|false
	 */
	public function getPluginVersion($folder = '')
	{
		return $this->getPluginProperty('plugin_version', $folder);
	}
	
	
	/**
	 * Get a plugin's actual name from its folder name
	 *
	 * @param string $folder plugin folder name
	 * @return string
	 */
	public function getPluginName($folder = '')
	{
		return $this->getPluginProperty('plugin_name', $folder);
	}
	
	
	/**
	 * Get a plugin's folder from its class name
	 *
	 * @param string $class plugin class name
	 * @return string|false
	 */
	public function getPluginFolderFromClass($class = '')
	{
		$pluginFunctions = PluginFunctions::instance();
		$this->plugin->folder = $pluginFunctions->getPluginFolderFromClass($this, $class);
	}
	
	
	/**
	 * Get a plugin's class from its folder name
	 *
	 * @param string $folder plugin folder name
	 * @return string|false
	 */
	public function getPluginClass($folder = '')
	{
		return $this->getPluginProperty('plugin_class', $folder);
	}
	
	
	/**
	 * Determines if a plugin "type" is enabled, if not, plugin "folder"
	 *
	 * @param string $type plugin type or folder name
	 * @return bool
	 */
	public function isActive($type = '')
	{
                $type = strtolower($type);
                
                //print "here for isActive with folder" . $this->plugin->folder . " type " . $type . '<br/>';
                if (!$type) {
                    return isset($this->plugins['activeFolders'][$this->plugin->folder]);
                }
            
                if (isset($this->plugins['activeTypes'][$type])) {
                    return true;
                }
                
                // finally, try the $type param in the $folder name. It is possible a call was made for a plugin by mistake
                return isset($this->plugins['activeFolders'][$type]);
                
                //print "could not find active settings for folder: " . $h->plugin->folder . " or type: " . $type;                		
	}
	
	
	/**
	 * Determines if a specific plugin is installed
	 *
	 * @param string $folder folder name
	 * @return bool
	 */
	public function isInstalled($folder = '')
	{
		//$pluginFunctions = PluginFunctions::instance();
		$result = $this->getPluginProperty('plugin_id', $folder);
		return $result;
	}
	
	
	/**
	 * Determines if a plugin has a settings page or not
	 *
	 * @param object $h
	 * @param string $folder plugin folder name (optional)
	 * @return bool
	 */
	public function hasSettings($folder = '')
	{
		$pluginFunctions = PluginFunctions::instance();
		return $pluginFunctions->hasSettings($this, $folder);
	}


 /* *************************************************************
 *
 *  PLUGIN SETTINGS FUNCTIONS
 *
 * *********************************************************** */
 
 
	/**
	 * Get the value for a given plugin and setting
	 *
	 * @param string $folder name of plugin folder
	 * @param string $setting name of the setting to retrieve
	 * @return string|false
	 *
	 * Notes: If there are multiple settings with the same name,
	 * this will only get the first.
	 */
	public function getSetting($setting = '', $folder = '')
	{
		$pluginSettings = PluginSettings::instance();
		return $pluginSettings->getSetting($this, $setting, $folder);
	}
	
	
	/**
	 * Get an array of settings for a given plugin
	 *
	 * @param string $folder name of plugin folder
	 * @return array|false
	 *
	 * Note: Unlike "getSetting", this will get ALL settings with the same name.
	 */
	public function getSettingsArray($folder = '')
	{
		$pluginSettings = PluginSettings::instance();
		return $pluginSettings->getSettingsArray($this, $folder);
	}
	
	
	/**
	 * Get and unserialize serialized settings
	 *
	 * @param string $folder plugin folder name
	 * @param string $settings_name optional settings name if different from folder
	 * @return array - of submit settings
	 */
	public function getSerializedSettings($folder = '', $settings_name = '')
	{
		$pluginSettings = PluginSettings::instance();
		return $pluginSettings->getSerializedSettings($this, $folder, $settings_name);
	}
	
	
	/**
	 * Get and store all plugin settings in $h->pluginSettings
	 * $forceUpdate ensures that we will update memcache when calling internally
         * 
	 * @return array - all settings
	 */
	public function getAllPluginSettings($forceUpdate = true)
	{
                // from Initialize
		$this->readAllPluginSettings($forceUpdate);
	}
	
	
	/**
	 * Determine if a plugin setting already exists
	 *
	 * @param string $folder name of plugin folder
	 * @param string $setting name of the setting to retrieve
	 * @return string|false
	 */
	public function isSetting($setting = '', $folder = '')
	{
		$pluginSettings = PluginSettings::instance();
		return $pluginSettings->isSetting($this, $setting, $folder);
	}
	
	
	/**
	 * Update a plugin setting
	 *
	 * @param string $folder name of plugin folder
	 * @param string $setting name of the setting
	 * @param string $setting stting value
	 */
	public function updateSetting($setting = '', $value = '', $folder = '')
	{
		$pluginSettings = PluginSettings::instance();
                return $pluginSettings->updateSetting($this, $setting, $value, $folder);
	}


 /* *************************************************************
 *
 *  THEME SETTINGS FUNCTIONS
 *
 * *********************************************************** */

	/**
	 * Read and return plugin info from top of a plugin file.
	 *
	 * @param string $theme - theme folder
	 * @return array|false
	 */
	public function readThemeMeta($theme = 'default')
	{
		$themeSettings = ThemeSettings::instance();
		return $themeSettings->readThemeMeta($this, $theme);
	}
	
	
	/**
	 * Get and unserialize serialized settings
	 *
	 * @param string $theme theme folder name
	 * @param string $return 'value' or 'default'
	 * @return array - of theme settings
	 */
	public function getThemeSettings($theme = '', $return = 'value')
	{
		$themeSettings = ThemeSettings::instance();
		return $themeSettings->getThemeSettings($this, $theme, $return);
	}
	
	
	/**
	 * Update theme settings
	 *
	 * @param array $settings array of settings
	 * @param string $theme theme folder name
	 * @param string $column 'value', 'default' or 'both'
	
	 */
	public function updateThemeSettings($settings = array(), $theme = '', $column = 'value')
	{
		$themeSettings = ThemeSettings::instance();
		return $themeSettings->updateThemeSettings($this, $settings, $theme, $column);
	}

        
        /* SystemJobs
         * 
         */
        
        public function checkSystemJobs()
        {
            $systemJobs = SystemJobs::instance();
            return $systemJobs->checkRunCron($this);
        }
        
        public function getCronArray()
        {
            $systemJobs = SystemJobs::instance();
            return $systemJobs->getCronArray($this);
        }
        
        public function cronGetSchedules()
        {
            $systemJobs = SystemJobs::instance();
            return $systemJobs->cronGetSchedules($this);
        }
        
        public function cronUpdateJob($data)
        {
            $systemJobs = SystemJobs::instance();
            return $systemJobs->cronUpdateJob($this, $data);
        }
        
        public function cronDeleteJob($data)
        {
            $systemJobs = SystemJobs::instance();
            return $systemJobs->cronDeleteJob($this, $data);
        }
        
        public function cronFlushHook($data)
        {
            $systemJobs = SystemJobs::instance();
            return $systemJobs->cronFlushHook($this, $data);
        }
        
        public function systemJobsRestoreDefaults()
        {
            $systemJobs = SystemJobs::instance();
            return $systemJobs->restoreDefaults($this);
        }
        
        
/* *************************************************************
 * 
 * SPAM LOG
 * 
* *********************************************************** */
        
        public function spamLogAdd($pluginFolder, $type, $email = '')
        {
                $spamLog = SpamLog::instance();
                return $spamLog->add($this, $pluginFolder, $type, $email);
        }
        
        public function spamLogGetAll()
        {
                $spamLog = SpamLog::instance();
                return $spamLog->getAll($this);
        }
        
        public function spamLogGet($pluginFolder = '')
        {
                $spamLog = SpamLog::instance();
                return $spamLog->get($this, $pluginFolder);
        }
        
        public function spamLogCount($pluginFolder = '')
        {
                $spamLog = SpamLog::instance();
                return $spamLog->count($this, $pluginFolder);
        }
        
        
        
/* *************************************************************
 *
 *  MISCDATA
 *
 * *********************************************************** */
        
        public function miscdata($key = '', $cache = 'true')
        {
                $systemInfo = SystemInfo::instance();
		return $systemInfo->miscdata($this, $key, $cache);
        }
        
        
        public function loginForum($username = '', $password = '')
        {
                $systemInfo = SystemInfo::instance();
		return $systemInfo->loginForum($this, $username, $password);
        }

 /* *************************************************************
 *
 *  INCLUDE CSS & JAVASCRIPT FUNCTIONS
 *
 * *********************************************************** */
 

	/**
	 * Do Includes (called from template header.php)
	 */
	 public function doIncludes($type = 'all')
	 {                
              // Note: careful using async or defer on the js otherwise inline jquery wihch may be in plugins has trouble running
             switch ($type) {
                    case 'all':
                        //$this->getFramework('bootstrap-lite');

                        // for old themes that dont split between loading js and css
                        //if ($this->vars['framework']['bootstrap-js'])
                            //$this->includeJs(LIBS . 'frameworks/bootstrap3/js/', 'bootstrap.min');  
                        echo "<script type='text/javascript' src='" . $this->bootstrapJsUri . "'></script>";
                            
                        $version_js = $this->includes->combineIncludes($this, 'js');
                        $version_css = $this->includes->combineIncludes($this, 'css');
                        $this->includes->includeCombined($this, $version_js, $version_css, $this->adminPage);                               	                        
                        
                        // only load jquery if we havent already loaded it
                        if (!isset($this->vars['framework']['jquery'])) {
                            echo '<script type="text/javascript" src="' . $this->jqueryUri . '"></script>';             
                            $this->vars['framework']['jquery'] = true;                            
                        }
                        
                        echo '<script type="text/javascript" src="' . $this->summernoteJsUri . '"></script>'; 
                        echo '<script type="text/javascript" src="' . $this->knockoutJsUri . '"></script>';             
                        echo '<script type="text/javascript" src="' . $this->knockoutMappingJsUri . '"></script>';

                        break;
                    case 'js': 
                        
                        // for better caching we should send this js file separately to hotarus combined js
                        if (!isset($this->vars['framework']['bootstrap-js']) || $this->vars['framework']['bootstrap-js']) {
                            echo "<script type='text/javascript' src='" . $this->bootstrapJsUri . "' type='text/css' /></script>";
                        }
                          
                        $version_js = $this->includes->combineIncludes($this, 'js');
                        $this->includes->includeCombined($this, $version_js, 0, $this->adminPage);  
                        
                        echo '<script type="text/javascript" src="' . $this->summernoteJsUri . '"></script>'; 
                        echo '<script type="text/javascript" src="' . $this->knockoutJsUri . '"></script>';             
                        echo '<script type="text/javascript" src="' . $this->knockoutMappingJsUri . '"></script>';

                        break;
                    case 'css': 
                        $version_css = $this->includes->combineIncludes($this, 'css');
                        $this->includes->includeCombined($this, 0, $version_css, $this->adminPage);
                        
                        // bringing this up-top with css because some inline js on plugins needs to have jquery loaded first to work
                        // only load jquery if we havent already loaded it
                        if (!isset($this->vars['framework']['jquery'])) {
                            echo '<script type="text/javascript" src="' . $this->jqueryUri . '"></script>';             
                            $this->vars['framework']['jquery'] = true;                            
                        }
                        
                        break;
                    default :
                        break;
             }
	 }
	 
         
	/**
	 * Build an array of css files to combine
	 *
	 * @param $folder - the folder name of the plugin
	 * @param $filename - optional css file without an extension
	 */
	 public function includeCss($folder = '', $filename = '')
	 {
		return $this->includes->includeCss($this, $folder, $filename);
	 }
	
	
	/**
	 * Build an array of JavaScript files to combine
	 *
	 * @param $folder - the folder name of the plugin
	 * @param $filename - optional js file without an extension
	 */
	 public function includeJs($folder = '', $filename = '')
	 {
		return $this->includes->includeJs($this, $folder, $filename);
	 }
	 
	 
	/**
	 * Include individual CSS files, not merged into the CSS archive
	 *
	 * @param $files- array of files to include (no extensions)
	 * @param $folder - optional plugin folder
	 */
	 public function includeOnceCss($files = array(), $folder = '')
	 {
		return $this->includes->includeOnceCss($this, $files, $folder);
	 }
	 
	 
	/**
	 * Include individual JavaScript files, not merged into the JavaScript archive
	 *
	 * @param $files- array of files to include (no extensions)
	 * @param $folder - optional plugin folder
	 */
	 public function includeOnceJs($files = array(), $folder = '')
	 {
		return $this->includes->includeOnceJs($this, $files, $folder);
	 }
         
         
         public function getThemeCss()
	 {
                if ($this->adminPage) {
                       echo '<link rel="stylesheet" href="' . SITEURL . 'content/admin_themes/' . ADMIN_THEME . 'css/style.css" type="text/css" />';         
                } else {
                       echo '<link rel="stylesheet" href="' . SITEURL . 'content/themes/' . THEME . 'css/style.css" type="text/css" />';                    
                }        

                echo '<link href="' . $this->fontAwesomeUri . '" rel="stylesheet">';
         }                

         
         public function getFramework($file = 'bootstrap3', $jsInclude = true)
	 {    
                //js files first unless prohibited
                $this->vars['framework']['bootstrap-js'] = $jsInclude ? true : false;  
                                          
                // then css files
                switch ($file) {
                    case 'bootstrap3':                        
                        echo "<link rel='stylesheet' href='" . $this->bootstrapCssUri . "' type='text/css' />\n";
                        $this->vars['framework']['bootstrap'] = true;
                        break;
                    case 'bootstrap':                        
                        echo "<link rel='stylesheet' href='" . BASEURL . "libs/frameworks/bootstrap/css/bootstrap.min.css' type='text/css' />\n";
                        $this->vars['framework']['bootstrap'] = true;
                         break;
                    case 'bootstrap-lite':          
                        if (!isset($this->vars['framework']['bootstrap']) || !$this->vars['framework']['bootstrap']) {
                            echo "<link rel='stylesheet' href='" . BASEURL . "libs/frameworks/bootstrap/css/bootstrap-lite.min.css' type='text/css' />\n";
                            $this->vars['framework']['bootstrap'] = true;
                        }
                        break;
                    case 'bootstrap-responsive':                        
                        //$this->includeCss(LIBS . 'frameworks/bootstrap', 'bootstrap-responsive.min'); 
                        echo "<link rel='stylesheet' href='" . BASEURL . "libs/frameworks/bootstrap/css/bootstrap-responsive.min.css' type='text/css' />\n";
                        break;
                    case 'none':
                        $this->vars['framework']['bootstrap'] = true;  // trick it into thinking we already have this done
                        break;
                    default:
                        //echo 'framework css incorrect params : ' . $file;
                        break;
                }   
                
                echo '<link rel="stylesheet" href="' . $this->summernoteCssUri . '" type="text/css" />';                
                echo '<link rel="stylesheet" href="' . $this->summernoteCssBs3Uri . '" type="text/css" />';
                echo '<link rel="stylesheet" href="' . $this->animateCssUri . '" type="text/css" />';
         }
     
     
 /* *************************************************************
 *
 *  MESSAGE FUNCTIONS (success/error messages)
 *
 * *********************************************************** */
 
         /**
          * Add a new message to the messages array for display later
          * Default role is empty for members
          * 
          * @param type $msg
          * @param type $msg_type
          * @param type $msg_role
          */
         public function addMessage($msg = '', $msg_type = '', $msg_role = '')
         {
                $messages = Messages::instance();
                $messages->addMessage($this, $msg, $msg_type, $msg_role);
         }
         
 
	/**
	 * Display a SINGLE success or failure message
	 *
	 * @param string $msg
	 * @param string $msg_type ('green' or 'red')
	 */
	public function showMessage($msg = '', $msg_type = 'green')
	{
		$messages = Messages::instance();
		$messages->showMessage($this, $msg, $msg_type);
	}
	
	
	/**
	 * Displays ALL success or failure messages
	 */
	public function showMessages()
	{
		$messages = Messages::instance();
		$messages->showMessages($this);
	}
    
    
 /* *************************************************************
 *
 *  ANNOUNCEMENT FUNCTIONS
 *
 * *********************************************************** */
 
 
	/**
	 * Displays an announcement at the top of the screen
	 *
	 * @param string $announcement - optional for non-admin pages
	 * @return array
	 */
	public function checkAnnouncements($announcement = '') 
	{
		$announce = Announcements::instance();
		if ($this->adminPage) {
			return $announce->checkAdminAnnouncements($this);
		} else {
			return $announce->checkAnnouncements($this, $announcement);
		}
	}
    
    
 /* *************************************************************
 *
 *  DEBUG FUNCTIONS
 *
 * *********************************************************** */
 
 
	/**
	 * Shows number of database queries and the time it takes for a page to load
	 */
	public function showQueriesAndTime()
	{
                $this->debug->showQueriesAndTime($this);
	}
	
	/**
	 * Open file for logging
	 *
	 * @param string $type "speed", "error", etc.
	 * @param string $mode e.g. 'a' or 'w'. 
	 * @link http://php.net/manual/en/function.fopen.php
	 */
	public function openLog($type = 'debug', $mode = 'a+')
	{
		$this->debug->openLog($type, $mode);
	}
	
	
	/**
	 * Log performance and errors
	 *
	 * @param string $type "speed", "error", etc.
	 */
	public function writeLog($type = 'error', $string = '')
	{
		$this->debug->writeLog($type, $string);
	}
	
	
	/**
	 * Close log file
	 *
	 * @param string $type "speed", "error", etc.
	 */
	public function closeLog($type = 'error')
	{
		$this->debug->closeLog($type);
	}
	
	
	/**
	 * Generate a system report
	 *
	 * @param string $type "log" or "object"
	 */
	public function generateReport($type = 'log', $level = '')
	{
		if (!is_object($this->debug)) { 
			$this->debug = Debug::instance();
		}
		return $this->debug->generateReport($this, $type, $level);
	}

    
 /* *************************************************************
 *
 *  RSS FEED FUNCTIONS
 *
 * *********************************************************** */
 
 
	/**
	 * Includes the SimplePie RSS file and sets the cache
	 *
	 * @param string $feed
	 * @param bool $cache
	 * @param int $cache_duration
	 *
	 * @return object|false $sp
	 */
	public function newSimplePie($feed='', $cache=RSS_CACHE, $cache_duration=RSS_CACHE_DURATION)
	{
		$feeds = Feeds::instance();
		return $feeds->newSimplePie($feed, $cache, $cache_duration);
	}
	
	
	 /**
	 * Display Hotaru forums feed on Admin front page
	 *
	 * @param int $max_items
	 * @param int $items_with_content
	 * @param int $max_chars
	 */
	public function adminNews($max_items = 10, $items_with_content = 3, $max_chars = 300)
	{
		$feeds = Feeds::instance();
		$feeds->adminNews($this->lang, $max_items, $items_with_content, $max_chars);
	}


	 /**
	 * Create an RSS Feed
	 *
	 * @param string $title - feed title
	 * @param string $link - url feed title should point to
	 * @param string $description - feed description
	 * @param array $items - $items[0] = array('title'=>TITLE, 'link'=>URL, 'date'=>TIMESTAMP, 'description'=>DESCRIPTION)
	 */
	public function rss($title = '', $link = '', $description = '', $items = array())
	{
		$feeds = Feeds::instance();
		$feeds->rss($this, $title, $link, $description, $items);
	}
	
	
 /* *************************************************************
 *
 *  ADMIN FUNCTIONS
 *
 * *********************************************************** */
 
 
	 /**
	 * Admin Pages
	 */
	public function adminPages($page = 'admin_login')
	{
		$admin = AdminPages::instance();
		$admin->pages($this, $page);
	}
	
	
	 /**
	 * Admin login/logout
	 *
	 * @param string $action
	 */
	public function adminLoginLogout($action = 'logout')
	{
		$admin = AdminAuth::instance();
		return ($action == 'login') ? $admin->adminLogin($this) : $admin->adminLogout($this);
	}
	
	
	 /**
	 * Admin login form
	 */
	public function adminLoginForm()
	{
		$admin = AdminAuth::instance();
		$admin->adminLoginForm($this);
	}
        
        
        public function adminNav()
        {
                $admin = AdminPages::instance();
                $admin->adminNav($this);
        }
        
        
        public function debugNav()
        {
                $admin = Debug::instance();
                $admin->debugNav($this);
        }
    
    
 /* *************************************************************
 *
 *  MAINTENANCE FUNCTIONS
 *
 * *********************************************************** */
 
 
	/**
	 * Check if site is open or closed. Exit if closed
	 *
	 * @param object $h
	 */
	public function checkSiteAccess()
	{
		if (SITE_OPEN == 'true') { return true; }   // site is open, go back and continue
		
		// site closed, but user has admin access so go back and continue as normal
		if ($this->currentUser->getPermission('can_access_admin') == 'yes') { return true; }

		if ($this->pageName == 'admin_login') { return true; }
		
		$maintenance = Maintenance::instance();
		return $maintenance->siteClosed($this, $this->lang); // displays "Site Closed for Maintenance"
	}
	
	
	/**
	 * Open or close the site for maintenance
	 *
	 * @param string $switch - 'open' or 'close'
	 */
	public function openCloseSite($switch = 'open')
	{
		$maintenance = Maintenance::instance();
		$maintenance->openCloseSite($this, $switch);
	}
	
	
	/**
	 * Optimize all database tables
	 */
	public function optimizeTables()
	{
		$maintenance = Maintenance::instance();
		$maintenance->optimizeTables($this);
	}
        
        
        /**
	 * Optimize all database tables
	 */
	public function exportDatabase()
	{
		$maintenance = Maintenance::instance();
		$maintenance->exportDatabase($this);
	}
	
	
	/**
	 * Empty plugin database table
	 *
	 * @param string $table_name - table to empty
	 * @param string $msg - show "emptied" message or not
	 */
	public function emptyTable($table_name = '', $msg = true)
	{
		$maintenance = Maintenance::instance();
		$maintenance->emptyTable($this, $table_name, $msg);
	}
	
	
	/**
	 * Delete plugin database table
	 *
	 * @param string $table_name - table to drop
	 * @param string $msg - show "dropped" message or not
	 */
	public function dropTable($table_name = '', $msg = true)
	{
		$maintenance = Maintenance::instance();
		$maintenance->dropTable($this, $table_name, $msg);
	}
	
	
	/**
	 * Remove plugin settings
	 *
	 * @param string $folder - plugin folder name
	 * @param bool $msg - show "Removed" message or not
	 */
	public function removeSettings($folder = '', $msg = true)
	{
		$maintenance = Maintenance::instance();
		$maintenance->removeSettings($this, $folder, $msg);
	}
	
	
	/**
	 * Deletes rows from pluginsettings that match a given setting or plugin
	 *
	 * @param string $setting name of the setting to remove
	 * @param string $folder name of plugin folder
	 */
	public function deleteSettings($setting = '', $folder = '')
	{
		$maintenance = Maintenance::instance();
		$maintenance->deleteSettings($this, $setting, $folder);
	}
	
	
	/**
	 * Delete all files in the specified directory except placeholder.txt
	 *
	 * @param string $dir - path to the cache folder
	 * @return bool
	 */    
	public function deleteFiles($dir = '')
	{
		$maintenance = Maintenance::instance();
		return $maintenance->deleteFiles($dir);
	}
	
	
	/**
	 * Calls the delete_files function, then displays a message.
	 *
	 * @param string $folder - path to the cache folder
	 * @param string $msg - show "cleared" message or not
	 */
	public function clearCache($folder = '', $msg = true)
	{
		$maintenance = Maintenance::instance();
		return $maintenance->clearCache($this, $folder, $msg);
	}
	
	
	/**
	 * Get all files in the specified directory except placeholder.txt
	 *
	 * @param string $dir - path to the folder
	 * @param array $exclude - array of file/folder names to exclude
	 * @return array
	 */    
	public function getFiles($dir = '', $exclude = array())
	{
		$maintenance = Maintenance::instance();
		return $maintenance->getFiles($dir, $exclude);
	}
	
	
	/** 
	 * System Report is under Debug Functions
	 */
    
    
 /* *************************************************************
 *
 *  CACHING FUNCTIONS (Note: "clearCache" is in Maintenance above)
 *
 * *********************************************************** */
	
	
	/**
	 * Hotaru CMS Smart Caching
	 *
	 * This function does one query on the database to get the last updated time for a 
	 * specified table. If that time is more recent than the $timeout length (e.g. 10 minutes),
	 * the database will be used. If there hasn't been an update, any cached results from the 
	 * last 10 minutes will be used.
	 *
	 * @param string $switch either "on", "off" or "html"
	 * @param string $table DB table name
	 * @param int $timeout time before DB cache expires
	 * @param string $html_sql output as HTML, or an SQL query
	 * @param string $label optional label to append to filename
	 * @return bool
	 */
	public function smartCache($switch = 'off', $table = '', $timeout = 0, $html_sql = '', $label = '')
	{
		$caching = Caching::instance();
		return $caching->smartCache($this, $switch, $table, $timeout, $html_sql, $label);
	}
	
	
	/**
	 * Cache HTML without checking for database updates
	 *
	 * This function caches blocks of HTML code
	 *
	 * @param int $timeout timeout in minutes before cache file is deleted
	 * @param string $html block of HTML to cache
	 * @param string $label name to identify the cached file
	 * @return bool
	 */
	public function cacheHTML($timeout = 0, $html = '', $label = '')
	{
		$caching = Caching::instance();
		return $caching->cacheHTML($this, $timeout, $html, $label);
	}
    
    
 /* *************************************************************
 *
 *  BLOCKED FUNCTIONS (i.e. Admin's Blocked list)
 *
 * *********************************************************** */
 
	 /**
	 * Check if a value is blocked from registration and post submission)
	 *
	 * @param string $type - i.e. ip, url, email, user
	 * @param string $value
	 * @param bool $like - used for LIKE sql if true
	 * @return bool
	 */
	public function isBlocked($type = '', $value = '', $operator = '=')
	{
		$blocked = Blocked::instance();
		return $blocked->isBlocked($this->db, $type, $value, $operator);
	}
	
	
	 /**
	 * Add or update blocked items 
	 *
	 * @param string $type - e.g. url, email, ip
	 * @param string $value - item to block
	 * @param bool $msg - show a success/failure message on Maintenance page
	 * @return bool
	 */
	public function addToBlockedList($type = '', $value = 0, $msg = false)
	{
		$blocked = Blocked::instance();
		return $blocked->addToBlockedList($this, $type, $value, $msg);
	}


 /* *************************************************************
 *
 *  LANGUAGE FUNCTIONS
 *
 * *********************************************************** */

        /**
         * echoes the lang file variable if found and a clean error message if not
         * 
         * @param type $title
         */
        function lang($title = '')
        {
            if (isset($this->lang[$title])) { // || ($this->currentUser->isAdmin && $this->isDebug)) {
                return $this->lang[$title];
            } else {
                return $title;
            }
        }
        

	/**
	 * Include a language file in a plugin
	 *
	 * @param string $folder name of plugin folder
	 * @param string $filename optional filename without file extension
	 *
	 * Note: the language file should be in a plugin folder named 'languages'.
	 * '_language.php' is appended automatically to the folder of file name.
	 */
	public function includeLanguage($folder = '', $filename = '')
	{
		$language = Language::instance();
		$language->includeLanguage($this, $folder, $filename);
	}
    
    
	/**
	 * Include a language file for a theme
	 *
	 * @param string $filename optional filename without '_language.php' file extension
	 *
	 * Note: the language file should be in a plugin folder named 'languages'.
	 * '_language.php' is appended automatically to the folder of file name.
	 */    
	public function includeThemeLanguage($filename = 'main')
	{
		$language = Language::instance();
		$language->includeThemeLanguage($this, $filename);
	}
    
    
/* *************************************************************
 *
 *  CSRF FUNCTIONS
 *
 * *********************************************************** */


	/**
	 * Shortcut for CSRF functions
	 *
	 * @param string $type - either "set" or "check" CSRF key
	 * @param string $script - optional name of page using the key
	 * @param int $life - minutes before the token expires
	 * @return string $key (if using $type "fetch")
	 */
	public function csrf($type = 'check', $script = '', $life = 60)
	{
                // check whether we are specifically being told not to create a newToken first
                // this is required for many js scripts ajaxing back Hotaru and accidentaly setting a new token in session state, preventing form from posting correctly on csrf check
//                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {    
//                    return true;
//                }

                // above ajax test didnt work so use this hard set test
                $newToken = $this->cage->post->testAlnum('newToken'); 
                if ($newToken == 'false') {
                    return true;
                }
                
                $csrf = \csrf::instance();
                return $csrf->csrfInit($this, $type, $script, $life);  
	}
    
    
/* *************************************************************
 *
 *  POST FUNCTIONS
 *
 * *********************************************************** */


	/**
	 * Get all the parameters for the current post
	 *
	 * @param int $post_id - Optional row from the posts table in the database
	 * @param array $post_row - a post already fetched from the db, just needs reading
	 * @return bool
	 */    
	public function readPost($post_id = 0, $post_row = NULL)
	{
                $p = Post::instance();
		return $p->readPost($this, $post_id, $post_row);
	}
	
	
	/**
	 * Gets a single post from the database
	 *
	 * @param int $post_id - post id of the post to get
	 * @return array|false
	 */    
	public function getPost($post_id = 0)
	{
		return $this->post->getPost($this, $post_id);
	}
	
	
	/**
	 * Add a post to the database
	 *
	 * @return int $last_insert_id
	 */
	public function addPost()
	{
		return $this->post->addPost($this);
	}
	
	
	/**
	 * Update a post in the database
	 *
	 * @return true
	 */    
	public function updatePost()
	{
		$this->post->updatePost($this);
	}
        
        /**
         * Update a post with image data
         * 
         * @param type $postId
         * @param type $img
         */
        public function postImageUpdate($postId, $img)
        {
                $this->post->imageUpdate($this, $postId, $img);
        }
	
	
	/**
	 * Physically delete a post from the database 
	 *
	 * There's a plugin hook in here to delete their parts, e.g. votes, coments, tags, etc.
	 */    
	public function deletePost()
	{
		$this->post->deletePost($this);
	}
	
	
	/**
	 * Physically delete all posts by a specified user
	 *
	 * @param array $user_id
	 * @return bool
	 */
	public function deletePosts($user_id = 0) 
	{
		return $this->post->deletePosts($this, $user_id);
	}
	
	
	/**
	 * Delete posts with "processing" status that are older than 30 minutes
	 * This is called automatically when a new post is submitted
	 */
	public function deleteProcessingPosts()
	{
		$this->post->deleteProcessingPosts($this);
	}
	
	
	/**
	 * Update a post's status
	 *
	 * @param string $status
	 * @param int $post_id (optional)
	 * @return true
	 */    
	public function changePostStatus($status = "processing", $post_id = 0)
	{
		return $this->post->changePostStatus($this, $status, $post_id);
	}
	
        
        /**
         * Count posts in the last X hours/minutes for this tag or category filter
         * 
         * @param type $hours
         * @param type $minutes
         * @param type $tag
         * @param type $category
         * @param type $post_type
         */
        public function countPostsFilter($hours = 0, $minutes = 0, $filter = '', $filterText = '', $link = '', $post_type = 'news')
	{
                return $this->post->countPostsFilter($this, $hours, $minutes, $filter, $filterText, $link, $post_type);
        }
	
	/**
	 * Count how many approved posts a user has had
	 *
	 * @param int $userid (optional)
	 * @param int $post_type (optional)
	 * @return int 
	 */
	public function postsApproved($userid = 0, $post_type = 'news')
	{
		return $this->post->postsApproved($this, $userid, $post_type);
	}
	
	
	/**
	 * Count posts in the last X hours/minutes for this user
	 *
	 * @param int $hours
	 * @param int $minutes
	 * @param int $user_id (optional)
	 * @param int $post_type (optional)
	 * @return int 
	 */
	public function countPosts($hours = 0, $minutes = 0, $user_id = 0, $post_type = 'news')
	{
		return $this->post->countPosts($this, $hours, $minutes, $user_id, $post_type);
	}
	
	
	/**
	 * Checks for existence of a url
	 *
	 * @return array|false - array containing existing post
	 */    
	public function urlExists($url = '')
	{
		return $this->post->urlExists($this, $url);
	}
	
	
	/**
	 * Checks for existence of a title
	 *
	 * @param str $title
	 * @return int - id of post with matching title
	 */
	public function titleExists($title = '')
	{
		return $this->post->titleExists($this, $title);
	}
	
	
	/**
	 * Checks for existence of a post with given post_url
	 *
	 * @param str $post_url (slug)
	 * @return int - id of post with matching url
	 */
	public function isPostUrl($post_url = '')
	{
		return $this->post->isPostUrl($this, $post_url);
	}
        
        
        /**
	 * Gets post url of the current comment in $h->comment
	 *
	 * @return string - post url
	 */
	public function getPostUrlForCurrentComment()
	{
                if (!isset($this->comment)) { return false; }
            
                // Note: we are passing both the id and the url here to make sure friendly and non-friendly urls work well
                $postUrl = $this->url(array('postUrl' => $this->comment->postUrl, 'post'=>$this->comment->postId));
		return $postUrl;
	}
	
	
	/**
	 * Get Unique Post Statuses
	 *
	 * @return array|false
	 */
	public function getUniqueStatuses() 
	{
		return $this->post->getUniqueStatuses($this);
	}
	
	
	/**
	 * Prepares and calls functions to send a trackback
	 * Uses $h->post->id
	 */
	public function sendTrackback()
	{
		$trackback = Trackback::instance();
		return $trackback->sendTrackback($this);
	}
        
        public function postGetFlags($postId)
        {
                return Post::instance()->getFlags($this, $postId);
        }
        
        /**
	 * Prepares and calls functions to send a trackback
	 * Uses $h->post->id
	 */
	public function postStats($stat_type)
	{
		$post = Post::instance();
		return $post->stats($this, $stat_type);
	}
    
        
/* *************************************************************
 *
 *  SEARCH FUNCTIONS
 *
 * *********************************************************** */
        
        
        /**
         * Prepare search filter
         */
        public function prepareSearchFilter($h, $search, $return = 'posts')
        {
                $searchFuncs = new Search();
                return  $searchFuncs->prepareSearchFilter($this, $search, $return);
        }
        
   
/* *************************************************************
 *
 *  AVATAR FUNCTIONS
 *
 * *********************************************************** */
 

	/**
	 * setAvatar
	 *
	 * @param $user_id
	 * @param $size avatar size in pixels
	 * @param $rating avatar rating (g, pg, r or x in Gravatar)
	 * @return bool
	 */
	public function setAvatar($user_id = 0, $size = 32, $rating = 'g', $img_class = '', $email = '', $username = '')
	{
		return $this->avatar = new Avatar($this, $user_id, $size, $rating, $img_class, $email, $username);
	}
	
	
	/**
	 * get the plain avatar with no surrounding HTML div
	 *
	 * @return return the avatar
	 */
	public function getAvatar()
	{
		return $this->avatar->getAvatar($this);
	}
	
	
	/**
	 * option to display the avatar linked to ther user's profile
	 *
	 * @return return the avatar
	 */
	public function linkAvatar()
	{
		return $this->avatar->linkAvatar($this);
	}
	
	
	/**
	 * option to display the profile-linked avatar wrapped in a div
	 *
	 * @return return the avatar
	 */
	public function wrapAvatar()
	{
		return $this->avatar->wrapAvatar($this);
	}
    
    
/* *************************************************************
 *
 *  CATEGORY FUNCTIONS
 *
 * *********************************************************** */

        public function getCatFullData($cat_id = 0, $cat_safe_name = '')
	{
		$category = Category::instance($this);
		return $category->getCatFullData($this, $cat_id, $cat_safe_name);
	}
        
	/**
	 * Returns the category id for a given category safe name.
	 *
	 * @param string $cat_name
	 * @return int
	 */
	public function getCatId($cat_safe_name = '')
	{
		$category = Category::instance($this);
		return $category->getCatId($this, $cat_safe_name);
	}
	
	
	/**
	 * Returns the category name for a given category id or safe name.
	 *
	 * @param int $cat_id
	 * @param string $cat_safe_name
	 * @return string
	 */
	public function getCatName($cat_id = 0, $cat_safe_name = '')
	{
		$category = Category::instance($this);
		return $category->getCatName($this, $cat_id, $cat_safe_name);
	}
	
	
	/**
	 * Returns the category safe name for a given category id 
	 *
	 * @param int $cat_id
	 * @return string
	 */
	public function getCatSafeName($cat_id = 0)
	{
		$category = Category::instance($this);
		return $category->getCatSafeName($this, $cat_id);
	}
	
	
	/**
	 * Returns parent id
	 *
	 * @param int $cat_id
	 * @return int
	 */
	public function getCatParent($cat_id = 0)
	{
		$category = Category::instance($this);
		return $category->getCatParent($this, $cat_id);
	}
	
	
	/**
	 * Returns child ids
	 *
	 * @param int $cat_parent_id
	 * @return int
	 */
	public function getCatChildren($cat_parent_id = 0)
	{
		$category = Category::instance($this);
		return $category->getCatChildren($this, $cat_parent_id);
	}
	
	 /**
	 * Returns Category list ids
	 *
	 * @param array $args
	 * @return int
	 */
	public function getCategories($args = array())
	{
		$category = Category::instance($this);
		return $category->getCategories($this, $args);
	}
	
	
	/**
	 * Returns meta description and keywords for the category (if available)
	 *
	 * @param int $cat_id
	 * @return array|false
	 */
	public function getCatMeta($cat_id = 0)
	{
		$category = Category::instance($this);
		return $category->getCatMeta($this, $cat_id);
	}


	/**
	 * Add a new category
	 *
	 * @param int $parent
	 * @param string $new_cat_name
	 * @return bool
	 */
	public function addCategory($parent = 0, $new_cat_name = '')
	{
		$category = Category::instance($this);
		return $category->addCategory($this, $parent, $new_cat_name);
	}


	/**
	 * rebuild the category tree
	 *
	 * @param int $parent_id
	 * @param int $left
	 * @return int
	 */
	public function rebuildTree($parent_id = 0, $left = 0)
	{
		$category = Category::instance($this);
		return $category->rebuildTree($this, $parent_id, $left);
	}


	/**
	 * Delete a category
	 *
	 * @param int $delete_category
	 * @return bool
	 */
	function deleteCategory($delete_category = 0)
	{
		$category = Category::instance($this);
		return $category->deleteCategory($this, $delete_category);
	}

        
//        function setCatMemCache()
//        {
//            $category = Category::instance($this);
//            $category->setCatMemCache($this);
//        }
        

/* *************************************************************
 *
 *  COMMENT FUNCTIONS
 *
 * *********************************************************** */

	/**
	 * Count comments
	 *
	 * @param bool $digits_only - return just the count (if false, returns "3 comments", etc.)
	 * @param string $no_comments_text - e.g. "Leave a comment" or "No comments"
	 * @return string - text to show, e.g. "3 comments"
	 */
	function countComments($digits_only = true, $no_comments_text = '')
	{
		$comment = Comment::instance();
		return $comment->countComments($this, $digits_only, $no_comments_text);
	}
	
	
	/**
	 * Count all user comments
	 *
	 * @param int $user_id
	 * @return int
	 */
	function countUserComments($user_id = 0)
	{
		$comment = Comment::instance();
		return $comment->countUserComments($this, $user_id);
	}
	
	
	/**
	 * Physically delete all comments by a specified user (and responses)
	 *
	 * @param array $user_id
	 * @return bool
	 */
	public function deleteComments($user_id) 
	{
		$comment = Comment::instance();
		return $comment->deleteComments($this, $user_id);
	}
	
	
	/**
	 * Get comment from database
	 *
	 * @param int $comment_id
	 * @return array|false
	 */
	public function getComment($comment_id = 0)
	{
		$comment = Comment::instance();
		return $comment->getComment($this, $comment_id);
	}
	
	
	/**
	 * Read comment
	 *
	 * @param array $comment_row pulled from database
	 */
	public function readComment($comment_row = array())
	{
		$comment = Comment::instance();
		return $comment->readComment($this, $comment_row);
	}
        
        
        public function updateCommentCountBulk() 
        {
                \Hotaru\Models2\Post::updateCommentCountBulk($this);
        }
    
    
/* *************************************************************
 *
 *  WIDGET FUNCTIONS
 *
 * *********************************************************** */

	/**
	 * Add widget
	 *
	 * @param string $plugin
	 * @param string $function
	 * @param string $value
	 */
	public function addWidget($plugin = '', $function = '', $args = '')
	{
		$widget = Widget::instance();
		$widget->addWidget($this, $plugin, $function, $args);
	}
	

	/**
	 * Get widgets from widgets_settings array
	 *
	 * USAGE: foreach ($widgets as $widget=>$details) 
	 * { echo "Name: " . $widget; echo $details['order']; echo $details['args']; } 
	 * 
	 * @param $widget_name - optional for a single widget
	 * @return array - of widgets
	 */
	public function getArrayWidgets($widget_name = '')
	{
		$widget = Widget::instance();
		return $widget->getArrayWidgets($this, $widget_name);
	}
	
	
	/**
	 * Delete a widget from the widget db table
	 *
	 * @param string $function
	 * @param string $plugin - plugin folder (optional: used for double checking the plugin is uninstalled)
	 */
	public function deleteWidget($function = '', $plugin = '')
	{
		$widget = Widget::instance();
		$widget->deleteWidget($this, $function, $plugin);
	}
	
	
	/**
	 * Get plugin name from widget function name
	 *
	 * @return string
	 */
	public function getPluginFromFunction($function)
	{
		$widget = Widget::instance();
		return $widget->getPluginFromFunction($this, $function);
	}
    
    
/* *************************************************************
 *
 *  EMAIL FUNCTIONS
 *
 * *********************************************************** */
 
	/**
	 * Send emails
	 *
	 * @param string $to - defaults to SITE_EMAIL
	 * @param string $subject - defaults to "No Subject";
	 * @param string $body - returns false if empty
	 * @param string $headers default is "From: " . SITE_EMAIL . "\r\nReply-To: " . SITE_EMAIL . "\r\nX-Priority: 3\r\n";
	 * @param string $type - default is "email", but you can write to a "log" file, print to "screen" or "return" an array of the content
	 * @return array|false - only if $type = "return"
	 */
	public function email($to = '', $subject = '', $body = '', $headers = '', $type = 'email', $isHtml = true)
	{
		if (!is_object($this->email)) { 
			$this->email = EmailFunctions::instance();
		}
		
		$this->email->to = $to;
		$this->email->subject = $subject;
		$this->email->body = $body;
		$this->email->headers = $headers;
		$this->email->type = $type;
                $this->email->isHtml = $isHtml;
		
		return $this->email->doEmail($this);
	}
	
	
/* *************************************************************
 *
 *  FRIEND FUNCTIONS
 *
 * *********************************************************** */

	/**
	 * count followers
	 *
	 * @param int $user_id - get people following this user
	 * @return int
	 */
	public function countFollowers($user_id = 0)
	{
		$friends = Friends::instance();
		return $friends->countFriends($this, $user_id, 'follower');
	}
	
	
	/**
	 * count following
	 *
	 * @param int $user_id - get people following this user
	 * @return int
	 */
	public function countFollowing($user_id = 0)
	{
		$friends = Friends::instance();
		return $friends->countFriends($this, $user_id, 'following');
	}
	
	
	/**
	 * get followers
	 *
	 * @param int $user_id - get this user's followers
	 * @param string $return - return 'array' of users of prepared 'query'
	 * @return array|string
	 */
	public function getFollowers($user_id = 0, $return = 'array')
	{
		$friends = Friends::instance();
		return $friends->getFriends($this, $user_id, 'follower', $return);
	}
	
	
	/**
	 * get people this user is following
	 *
	 * @param int $user_id
	 * @param string $return - return 'array' of users of prepared 'query'
	 * @return array|string
	 */
	public function getFollowing($user_id = 0, $return = 'array')
	{
		$friends = Friends::instance();
		return $friends->getFriends($this, $user_id, 'following', $return);
	}
	
	
	/**
	 * Is current user being followed by user X?
	 *
	 * @param int $user_id - user X
	 * @return bool
	 */
	public function isFollower($user_id = 0)
	{
		$friends = Friends::instance();
		return $friends->checkFriends($this, $user_id, 'follower');
	}
	
	
	/**
	 * Is current user following user X?
	 *
	 * @param int $user_id - user X
	 * @return bool
	 */
	public function isFollowing($user_id = 0)
	{
		$friends = Friends::instance();
		return $friends->checkFriends($this, $user_id, 'following');
	}
	
	
	/**
	 * Follow / become a fan of user X
	 *
	 * @param int $follow - user to follow
	 * @return bool
	 */
	public function follow($user_id = 0)
	{
		$friends = Friends::instance();
		return $friends->updateFriends($this, $user_id, 'follow');
	}
	
	
	/**
	 * Unfollow / stop being a fan of user X
	 *
	 * @param int $unfollow - user to stop following
	 * @return bool
	 */
	public function unfollow($user_id = 0)
	{
		$friends = Friends::instance();
		return $friends->updateFriends($this, $user_id, 'unfollow');
	}
	
	
 /* *************************************************************
 *
 *  SITE ACTIVITY FUNCTIONS
 *
 * *********************************************************** */
 
        public function activity($method = '', $params = '')
        {
            if (class_exists('UserActivity'))  { 
                //print "class exists<br/>"; 
            } else {
                //print "class does not exist<br/>"; 
            }
            $activity = UserActivity::instance();
            
            //print "method = " . $method . '<br/>';             
            if (method_exists($activity,$method)) { 
                //print "method exists<br/>";
            } else { 
                //print "method does not exist<br/>";
            }
                
            print ($activity->$method);
            
            $r = new ReflectionMethod($activity, $method);
            $params = $r->getParameters();
            foreach ($params as $param) {
                //$param is an instance of ReflectionParameter
                echo $param->getName();
                echo $param->isOptional();
            }
            
            foreach ($params as $param) {
                extract($params);                
            }
            
            $result = $activity->$method($this, $params);
            return $result;
        }
        
        
 
	/**
	 * Get the latest site activity
	 *
	 * @param int $limit
	 * @param int $userid
	 * @param string $type blank or "count" or "query"
	 * @return array|false
	 */
	public function getLatestActivity($limit = 0, $userid = 0, $type = '', $fromId = 0)
	{
		$activity = UserActivity::instance();
		return $activity->getLatestActivity($this, $limit, $userid, $type, $fromId);
	}
	
	
	/**
	 * Check if an action already exists
	 *
	 * @param array $args e.g. array('userid'=>4, 'key'=>'post', 'value'=>'6408')
	 * @return bool
	 */
	public function activityExists($args = array())
	{
		$activity = UserActivity::instance();
		return $activity->activityExists($this, $args);
	}
	
	
	/**
	 * Insert new activity
	 *
	 * @param array $args e.g. array('userid'=>4, 'key'=>'post', 'value'=>'6408')
	 */
	public function insertActivity($args = array())
	{
		$activity = UserActivity::instance();
		return $activity->insertActivity($this, $args);
	}
	
	
	/**
	 * Update activity
	 *
	 * @param array $args e.g. array('userid'=>4, 'key'=>'post', 'value'=>'6408')
	 */
	public function updateActivity($args = array())
	{
		$activity = UserActivity::instance();
		return $activity->updateActivity($this, $args);
	}
	
	
	/**
	 * Remove activity
	 *
	 * @param array $args e.g. array('userid'=>4, 'key'=>'post', 'value'=>'6408')
	 */
	public function removeActivity($args = array())
	{
		$activity = UserActivity::instance();
		return $activity->removeActivity($this, $args);
	}
	
	
 /* *************************************************************
 *
 *  MESSAGING FUNCTIONS
 *
 * *********************************************************** */
 

	/**
	 * Get Messages
	 *
	 * @param string $box "inbox" or "outbox"
	 * @param string $type blank or "count" or "query"
	 * @return int | array | false
	 */
	public function getMessages($box = 'inbox', $type = '')
	{
		$pm = PrivateMessaging::instance();
		return $pm->getMessages($this, $box, $type);
	}
	
	
	/**
	 * Get Message
	 *
	 * @param int $message_id
	 * @return array
	 */
	public function getMessage($message_id = 0)
	{
		$pm = PrivateMessaging::instance();
		return $pm->getMessage($this, $message_id);
	}
        
        
        /**
         * Get unread Message Count for user
         * 
         * @param type $userId
         * @return type
         */
	public function getCountMessagesUnread($userId = 0)
	{
                if ($userId == 0) {
                    $userId = $this->currentUser->id;
                }
                
		$pm = PrivateMessaging::instance();
		return $pm->getCountMessagesUnread($this, $userId);
	}
        
	 
	/**
	 * Mark message as read
	 *
	 * @param int $message_id
	 */
	public function markRead($message_id = 0)
	{
		$pm = PrivateMessaging::instance();
		$pm->markRead($this, $message_id);
	}
	
	
	/**
	 * Delete Message
	 *
	 * @param int $message_id
	 * @param string $box "inbox" or "outbox"
	 * @return bool
	 */
	public function deleteMessage($message_id = 0, $box = 'inbox')
	{
		$pm = PrivateMessaging::instance();
		$pm->deleteMessage($this, $message_id, $box);
	}
	
	
	/**
	 * Send Message
	 *
	 * @param string $to
	 * @param string $from
	 * @param string $subject
	 * @param string $body
	 * @return int | array (int on success, array on failure)
	 */
	public function sendMessage($to = '', $from = '', $subject = '', $body = '')
	{
		$pm = PrivateMessaging::instance();
		return $pm->sendMessage($this, $to, $from, $subject, $body);
	}


 /* *************************************************************
 *
 *  VOTE FUNCTIONS
 *
 * *********************************************************** */
 

	/**
	 * Get Individual Vote Rating 
	 *
	 * @param int $post_id
	 * @param int $user_id
	 * @param string $ip
	 * @param bool $anon
	 * @return int - vote rating e.g. 10, -10
	 */
	public function getVoteRating($post_id = 0, $user_id = 0, $ip = '', $anon = false)
	{
                $voteFuncs = VoteFunctions::instance();
		return $voteFuncs->getVoteRating($this, $post_id, $user_id, $ip, $anon);
	}


	/**
	 * Get Post Vote Info
	 *
	 * @param int $post_id
	 * @return array|false
	 */
	public function getPostVoteInfo($post_id = 0)
	{
		return VoteFunctions::getPostVoteInfo($this, $post_id);
	}


	/**
	 * Add Vote to PostVotes table
	 *
	 * @param int $post_id
	 * @param int $user_id
	 * @param string $ip
	 * @param int $rating
	 * @param string $type - usually the plugin name
	 */
	public function addVote($post_id = 0, $user_id = 0, $ip = '', $rating = 0, $type = 'vote')
	{
		VoteFunctions::addVote($this, $post_id, $user_id, $ip, $rating, $type);
	}


	/**
	 * Update Post Vote Info
	 *
	 * @param int $post_id
	 * @param int $post_votes_up - either -1, 0 or 1
	 * @param int $post_votes_down - either -1, 0 or 1
	 * @param string $post_status
	 * @param bool $pub_date - set to TRUE to update to current time
	 */
	public function updatePostVoteInfo($post_id = 0, $post_votes_up = 0, $post_votes_down = 0, $post_status = '', $pub_date = FALSE)
	{
		VoteFunctions::updatePostVoteInfo($this, $post_id, $post_votes_up, $post_votes_down, $post_status, $pub_date);
	}


	/**
	 * Delete Vote from PostVotes table
	 *
	 * @param int $post_id
	 * @param int $user_id
	 * @param int $rating
	 * @param string $ip
	 * @param bool $anon
	 */
	public function deleteVote($post_id = 0, $user_id = 0, $rating = 0, $ip = '', $anon = FALSE)
	{
		VoteFunctions::deleteVote($this, $post_id, $user_id, $rating, $ip, $anon);
	}


	/**
	 * Count votes by a user
	 *
	 * @param string $type - 'all', 'pos', 'neg', 'flags' (flags not included in 'all')
	 * @param int $user_id
	 * return int|false
	 */
	public function countUserVotes($type = 'all', $user_id = 0)
	{
		return VoteFunctions::countUserVotes($this, $type, $user_id);
	}
}
