<?
require_once('config.php');
###############################################################################


# Usage:  
# $timer     = new timer(1);
# ...
# echo "\nTook " . $timer->get() . " seconds to run\n";
class timer {
	var $start;
	var $pause_time;

	/*  start the timer  */
	function timer($start = 0) {
		if($start) { $this->start(); }
	}

	/*  start the timer  */
	function start() {
		$this->start = $this->get_time();
		$this->pause_time = 0;
	}

	/*  pause the timer  */
	function pause() {
		$this->pause_time = $this->get_time();
	}

	/*  unpause the timer  */
	function unpause() {
		$this->start += ($this->get_time() - $this->pause_time);
		$this->pause_time = 0;
	}

	/*  get the current timer value  */
	function get($decimals = 3) {
		return round(($this->get_time() - $this->start),$decimals);
	}

	/*  format the time in seconds  */
	function get_time() {
		list($usec,$sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
}

###############################################################################




###############################################################################

class NetUtil
{
    /**
     * Strips the given MAC address of all formatting making the address suitible for entry into the database.
     *
     * @param string $mac
     * @return string Returns a unformatted MAC address.
     */
    public static function cleanMacAddress($mac)
    {
        $retVal = preg_replace('/[^0-9a-fA-F]/', '', $mac);
        return strtoupper($retVal);
    }

    /**
     * Given a MAC address with any type of formatting (or none), formats the MAC address using the desired delimiter.
     *
     * @param string $mac
     * @param char $char The desired delimiter to be used in the formatting. Valid delimiters are a dash (-), colon (:) or period (.). The default delimiter is a dash (-).
     * @return string Returns a formatted MAC address.
     */
    public static function formatMacAddress($mac, $char = '-')
    {
        $retVal = '';
        // Clean MAC address of all existing formatting for a clean base string.
        $mac = self::cleanMacAddress($mac);
        $delimNum = 2;
        if($char == '.')
        {
            $delimNum = 4;
        }
        for($i = 0; $i < (strlen($mac) - $delimNum); $i += $delimNum)
        {
            $retVal .= substr($mac, $i, $delimNum) . $char;
        }
        $retVal .= substr($mac, (strlen($mac) - $delimNum), $delimNum);
        return strtoupper($retVal);
    }
    
    /**
     * Validates a given MAC address of any format.
     *
     * @param string $mac
     * @return boolean
     */
    public static function validateMacAddress($mac)
    {
        return (bool)preg_match('/([0-9a-fA-F]{2}[-:]?){5}([0-9a-fA-F]{2})|([0-9a-fA-F]{4}\.){2}([0-9a-fA-F]{4})/', $mac);
    }
    
    /**
     * Validates a given IPv4 address.
     *
     * @param string $ipv4
     * @return boolean
     */
    public static function validateIPv4Address($ipv4)
    {
        return (bool)filter_var($ipv4, FILTER_VALIDATE_IP);
    }
    
    /**
     * Validates a given IPv6 address.
     *
     * @param string $ipv6
     * @return boolean
     */
    public static function validateIPv6Address($ipv6)
    {
        return (bool)filter_var($ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }
    
    /**
     * Validates either IPv4 or IPv6 address.
     *
     * @param string $ip_addr
     * @return boolean
     */
    public static function validateIPAddress($ip_addr)
    {
        if(NetUtil::validateIPv4Address($ip_addr) || NetUtil::validateIPv6Address($ip_addr))
        {
        	return true;
        } else {
        	return false;
        }
    }
    
  /**
	 * Expand an IPv6 Address
	 *
	 * This will take an IPv6 address written in short form and expand it to include all zeros. 
	 *
	 * @param  string  $addr A valid IPv6 address
	 * @return string  The expanded notation IPv6 address
	 */
	public static function inet6_expand($ipv6_addr)
	{
    	if(!NetUtil::validateIPv6Address($ipv6_addr)) die ("inet6_expand(): $ipv6_addr does not appear to be valid! \n");
    	
    	/* Check if there are segments missing, insert if necessary */
    	if (strpos($ipv6_addr, '::') !== false) 
    	{
			$part = explode('::', $ipv6_addr);
			$part[0] = explode(':', $part[0]);
			$part[1] = explode(':', $part[1]);
			$missing = array();
			for ($i = 0; $i < (8 - (count($part[0]) + count($part[1]))); $i++)
				array_push($missing, '0000');
			$missing = array_merge($part[0], $missing);
			$part = array_merge($missing, $part[1]);
    	} else {
        	$part = explode(":", $ipv6_addr);
    	} // if .. else
    
    	/* Pad each segment until it has 4 digits */
		foreach ($part as &$p) 
		{
			while (strlen($p) < 4) $p = '0' . $p;
		} // foreach
		unset($p);
		
		/* Join segments */
		$result = implode(':', $part);
		
		/* Quick check to make sure the length is as expected */ 
		if (strlen($result) == 39) 
		{
			return $result;
		} else {
			return false;
		} // if .. else
	} // inet6_expand

	/**
	 * Compress an IPv6 Address
	 *
	 * This will take an IPv6 address and rewrite it in short form. 
	 *
	 * @param  string  $addr A valid IPv6 address
	 * @return string  The address in short form notation
	 */
	public static function inet6_compress($ipv6_addr)
	{
		if(!NetUtil::validateIPv6Address($ipv6_addr)) die ("inet6_compress(): $ipv6_addr does not appear to be valid! \n");
		/* PHP provides a shortcut for this operation */
		$result = inet_ntop(inet_pton($ipv6_addr));
		return $result;
	} // inet6_compress

}

###############################################################################

class Debug 
{
	// constructor
	// reset error list
	function Debug()
	{
		$this->resetErrorList();
	}
	
	//
	// methods (public)
	//
	
	// function to get the value of a variable (field)
	function _getValue($field)
	{
		global ${$field};
		return ${$field};
	}
	// check whether any errors have occurred in validation
	// returns Boolean
	function isError()
	{
		if (sizeof($this->_errorList) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	// return the current list of errors
	function getErrorList()
	{
		return $this->_errorList;
	}
	
	// reset the error list
	function resetErrorList()
	{
		$this->_errorList = array();
	}
	
	public static function enable()
	{
		$status = 1;
	}
	public static function disable()
	{
		$status = 0;
	}
	public static function getStatus()
	{
		return $status;
	}
	function setMsg($field,$value)
	{
		$this->_errorList[] = array("field" => $field, "value" => $value, "msg" => $msg);
	}
	public static function getMsg()
	{
		return $messages;
	}
	function printAll()
	{
		echo "
		<fieldset class=fieldset-auto-width>
		<legend>Debug</legend>
		<table border=0>
		<tr>
			<td><b>Field</b></td><td><b>Value</b></td><td><b>Message</b></td>
		</tr>";
		foreach ($this->_errorList as $row)
		{
			echo "<tr><td>" . $row['field'] . ":  </td><td>" . $row['value'] . "</td><td>" . $row['msg'] . "</td></tr>";
		}
		
		echo "
		
		</table>
		</fieldset>";
	}
}

###############################################################################

class FormValidator
{
	// constructor
	// reset error list
	function FormValidator()
	{
		$this->resetErrorList();
	}

	//
	// methods (public)
	//
	
	// function to get the value of a variable (field)
	function _getValue($field)
	{
		global ${$field};
		return ${$field};
	}

	// check whether input is empty
	function isEmpty($field, $msg)
	{
		$value = $this->_getValue($field);
		if (trim($value) == "")
		{
			$this->_errorList[] = array("field" => $field, "value" => $value, "msg" => $msg);
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function isString($field, $msg)
	{
		$value = $this->_getValue($field);
		if(!is_string($value))
		{
			$this->_errorList[] = array("field" => $field, "value" => $value, "msg" => $msg);
			return false;
		}
		else
		{
			return true;
		}
	}

	// check whether input is a number
	function isNumber($field, $value, $msg)
	{
		#$value = $this->_getValue($field);
		if(!is_numeric($value))
		{
			$this->_errorList[] = array("field" => $field, "value" => $value, "msg" => $msg);
			return false;
		}
		else
		{
			return true;
		}
	}
	// check whether input is an integer
	function isInteger($field, $msg)
	{
		$value = $this->_getValue($field);
		if(!is_integer($value))
		{
			$this->_errorList[] = array("field" => $field, "value" => $value, "msg" => $msg);
			return false;
		}
		else
		{
			return true;
		}
	}

	// check whether input is a float
	function isFloat($field, $msg)
	{
		$value = $this->_getValue($field);
		if(!is_float($value))
		{
			$this->_errorList[] = array("field" => $field, "value" => $value, "msg" => $msg);
			return false;
		}
		else
		{
			return true;
		}
	}
	
	// check whether input is within a valid numeric range
	function isWithinRange($field, $msg, $min, $max)
	{
		$value = $this->_getValue($field);
		if(!is_numeric($value) || $value < $min || $value > $max)
		{
			$this->_errorList[] = array("field" => $field, "value" => $value, "msg" => $msg);
			return false;
		}
		else
		{
			return true;
		}
	}
	
	// check whether input is alphabetic
	function isAlpha($field, $msg)
	{
		$value = $this->_getValue($field);
		$pattern = "/^[a-zA-Z]+$/";
		if(preg_match($pattern, $value))
		{
			return true;
		}
		else
		{
			$this->_errorList[] = array("field" => $field, "value" => $value, "msg" => $msg);
			return false;
		}
	}
	
	// check whether input is a valid email address
	function isEmailAddress($field, $msg)
	{
		$value = $this->_getValue($field);
		$pattern = "/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/";
		if(preg_match($pattern, $value))
		{
			return true;
		}
		else
		{
			$this->_errorList[] = array("field" => $field, "value" => $value, "msg" => $msg);
			return false;
		}
	}
	
	// check whether input is a valid DNS type
	function isDnsType($field, $value, $msg)
	{
		if($value != 'A' && $value != 'CNAME' && $value != 'MX')
		{
			$this->_errorList[] = array("field" => $field, "value" => $value, "msg" => $msg);
			return false;
		}
		else
		{
			return true;
		}
	}
	
	// check whether any errors have occurred in validation
	// returns Boolean
	function isError()
	{
		if (sizeof($this->_errorList) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	// return the current list of errors
	function getErrorList()
	{
		return $this->_errorList;
	}
	
	// reset the error list
	function resetErrorList()
	{
		$this->_errorList = array();
	}

}


###############################################################################



$dbconnect  = NULL;
$query      = NULL;

function db_connect($dbname)
{
   global $dbconnect, $dbhost, $dbusername, $dbuserpass;
   
   if (!$dbconnect) $dbconnect = mysqli_connect($dbhost, $dbusername, $dbuserpass, $dbname);
   if (!$dbconnect) {
      return 0;
   } elseif (!mysqli_select_db($dbconnect,$dbname)) {
      return 0;
   } else {
      return $dbconnect;
   } // if
   
} // db_connect


/////// ERRORHANDLER()
set_error_handler('errorHandler');

function errorHandler ($errno, $errstr, $errfile, $errline, $errcontext)
// If the error condition is E_USER_ERROR or above then abort
{
   switch ($errno)
   {
      case E_USER_WARNING:
      case E_USER_NOTICE:
      case E_WARNING:
      case E_NOTICE:
      case E_CORE_WARNING:
      case E_COMPILE_WARNING:
         break;
      case E_USER_ERROR:
      case E_ERROR:
      case E_PARSE:
      case E_CORE_ERROR:
      case E_COMPILE_ERROR:
      
         global $query;
   
         session_start();
         
         if (eregi('^(sql)$', $errstr)) {
            $MYSQL_ERRNO = mysqli_errno();
            $MYSQL_ERROR = mysqli_error();
            $errstr = "MySQL error: $MYSQL_ERRNO : $MYSQL_ERROR";
         } else {
            $query = NULL;
         } // if
         
         echo "<h2>This system is temporarily unavailable</h2>\n";
         echo "<b><font color='red'>\n";
         echo "<p>Fatal Error: $errstr (# $errno).</p>\n";
         if ($query) echo "<p>SQL query: $query</p>\n";
         echo "<p>Error in line $errline of file '$errfile'.</p>\n";
         echo "<p>Script: '{$_SERVER['PHP_SELF']}'.</p>\n";
         echo "</b></font>";
         
         // Stop the system
         session_unset();
         session_destroy();
         die();
      default:
         break;
   } // switch
} // errorHandler
/////// ERRORHANDLER()

class Default_Table
{
    var $tablename;         // table name
    var $dbname;            // database name
    var $rows_per_page;     // used in pagination
    var $pageno;            // current page number
    var $lastpage;          // highest page number
    var $fieldlist;         // list of fields in this table
    var $data_array;        // data from the database
    var $errors;            // array of error messages
    var $orderby;			// field to order returned results by

    function Default_Table ()
    {
        $this->tablename       = 'default';
        $this->dbname          = 'default';
        $this->rows_per_page   = 10;
    
        $this->fieldlist = array('column1', 'column2', 'column3');
        $this->fieldlist['column1'] = array('pkey' => 'y');
    } // constructor

		function getData ($where, $sort)
    {
        $this->data_array = array();
        $pageno          = $this->pageno;
        $rows_per_page   = $this->rows_per_page;
        $this->numrows   = 0;
        $this->lastpage  = 0;
      
        global $dbconnect, $query;
        $dbconnect = db_connect($this->dbname) or trigger_error("SQL", E_USER_ERROR);
        
        if (empty($where)) {
            $where_str = NULL;
        } else {
            $where_str = "WHERE $where";
        } // if
        
        if (!empty($sort)) {
   			$sort_str = "ORDER BY $sort ASC";
		} else {
   			$sort_str = NULL;
		} // if


        $query = "SELECT count(*) FROM $this->tablename $where_str $sort_str";
        $result = mysqli_query($dbconnect, $query) or trigger_error("SQL", E_USER_ERROR);
        $query_data = mysqli_fetch_row($result);
        $this->numrows = $query_data[0];

        if ($this->numrows <= 0) {
            $this->pageno = 0;
            return;
        } // if
        
        if ($rows_per_page > 0) {
            $this->lastpage = ceil($this->numrows/$rows_per_page);
        } else {
            $this->lastpage = 1;
        } // if

        if ($pageno == '' OR $pageno <= '1') {
            $pageno = 1;
        } elseif ($pageno > $this->lastpage) {
            $pageno = $this->lastpage;
        } // if
        
        $this->pageno = $pageno;
        
        if ($rows_per_page > 0) {
            $limit_str = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;
        } else {
            $limit_str = NULL;
        } // if

        $query = "SELECT * FROM $this->tablename $where_str $sort_str $limit_str";
        $result = mysqli_query($dbconnect, $query) or trigger_error("SQL", E_USER_ERROR);

        while ($row = mysqli_fetch_assoc($result)) {
            $this->data_array[] = $row;
        } // while

        mysqli_free_result($result);
   
        return $this->data_array;
      
    } // getData
    
    #$query = "SELECT $select_str FROM $from_str $where_str $group_str $having_str $sort_str $limit_str";

    function insertRecord ($fieldarray)
    {
        $this->errors = array();
        
        global $dbconnect, $query;
        $dbconnect = db_connect($this->dbname) or trigger_error("SQL", E_USER_ERROR);
    
        $fieldlist = $this->fieldlist;
        
        foreach ($fieldarray as $field => $fieldvalue) {
            if (!in_array($field, $fieldlist)) {
                unset ($fieldarray[$field]);
            } // if
        } // foreach

        $query = "INSERT INTO $this->tablename SET ";
        foreach ($fieldarray as $item => $value) {
            $query .= "$item='$value', ";
        } // foreach

        $query = rtrim($query, ', ');
        
        $result = @mysqli_query($dbconnect, $query);
        if (mysqli_errno() <> 0) {
            if (mysqli_errno() == 1062) {
                $this->errors[] = "A record already exists with this ID.";
            } else {
                trigger_error("SQL", E_USER_ERROR);
            } // if
        } // if

        return;
   	   
    } // insertRecord

    function updateRecord ($fieldarray)
    {
        $this->errors = array();
        global $dbconnect, $query;
        $dbconnect = db_connect($this->dbname) or trigger_error("SQL", E_USER_ERROR);
        $fieldlist = $this->fieldlist;
        
        foreach ($fieldarray as $field => $fieldvalue) {
            if (!in_array($field, $fieldlist)) {
                unset ($fieldarray[$field]);
            } // if
        } // foreach

        $where  = NULL;
        $update = NULL;
        
        foreach ($fieldarray as $item => $value) {
            if (isset($fieldlist[$item]['pkey'])) {
                $where .= "$item='$value' AND ";
            } else {
                $update .= "$item='$value', ";
            } // if
        } // foreach

        $where  = rtrim($where, ' AND ');
        $update = rtrim($update, ', ');
        $query = "UPDATE $this->tablename SET $update WHERE $where";
        $result = mysqli_query($dbconnect, $query) or trigger_error("SQL", E_USER_ERROR);
      
        return;
      
    } // updateRecord

    function deleteRecord ($fieldarray)
    {
        $this->errors = array();
        global $dbconnect, $query;
        $dbconnect = db_connect($this->dbname) or trigger_error("SQL", E_USER_ERROR);

        $fieldlist = $this->fieldlist;
        $where  = NULL;
        foreach ($fieldarray as $item => $value) {
            if (isset($fieldlist[$item]['pkey'])) {
                $where .= "$item='$value' AND ";
            } // if
        } // foreach
        
        $where  = rtrim($where, ' AND ');
        
        $query = "DELETE FROM $this->tablename WHERE $where";
        $result = mysqli_query($dbconnect, $query) or trigger_error("SQL", E_USER_ERROR);
      
        return;
      
    } // deleteRecord
} // end class

class SuperTrackData extends Default_Table
{
    // additional class variables go here
    function SuperTrackData ()
    {
        $this->tablename       = 'data';
        $this->dbname          = 'supertrack';
        $this->rows_per_page   = 0;
        $this->fieldlist       = array('id', 'firstSeen', 'lastSeen', 'CWID', 'mac_addr');
        $this->fieldlist['id'] = array('pkey' => 'y');
				
    } // end class constructor

} // end class

class SuperTrackUser extends Default_Table
{
    // additional class variables go here
    function SuperTrackUser ()
    {
        $this->tablename       = 'user';
        $this->dbname          = 'supertrack';
        $this->rows_per_page   = 0;
        $this->fieldlist       = array('CWID', 'loginName');
        $this->fieldlist['CWID'] = array('pkey' => 'y');
				
    } // end class constructor

} // end class

class ArpTrackMAC_IP extends Default_Table
{
    // additional class variables go here
    function ArpTrackMAC_IP ()
    {
        $this->tablename       = 'MAC_IP';
        $this->dbname          = 'arptrack';
        $this->rows_per_page   = 0;
        $this->fieldlist       = array('id', 'MacAddress', 'IPAddress', 'FirstSeen', 'LastSeen');
        $this->fieldlist['id'] = array('pkey' => 'y');
				
    } // end class constructor

} // end class

class ArpTrackOUI extends Default_Table
{
    // additional class variables go here
    function ArpTrackOUI ()
    {
        $this->tablename       = 'oui';
        $this->dbname          = 'arptrack';
        $this->rows_per_page   = 0;
        $this->fieldlist       = array('oui', 'manuftr');
        $this->fieldlist['oui'] = array('pkey' => 'y');
				
    } // end class constructor

} // end class

class ArpTrackIPv6 extends Default_Table
{
    // additional class variables go here
    function ArpTrackIPv6 ()
    {
        $this->tablename       = 'IPv6';
        $this->dbname          = 'arptrack6';
        $this->rows_per_page   = 0;
        $this->fieldlist       = array('id', 'MacAddress', 'IPv6Address', 'FirstSeen', 'LastSeen');
        $this->fieldlist['id'] = array('pkey' => 'y');
				
    } // end class constructor

} // end class

class netmgmt_device extends Default_Table
{
    // additional class variables go here
    function netmgmt_device ()
    {
        $this->tablename       = 'Devices';
        $this->dbname          = 'netmgmt';
        $this->rows_per_page   = 0;
        $this->fieldlist       = array('MacAddress', 'UserId', 'DepartmentId', 'Type', 'InventoryTag', 'Description', 'DateAdded', 'UserAdded', 'DateModified', 'UserModified', 'DateLastSeen');
        $this->fieldlist['MacAddress'] = array('pkey' => 'y');
				
    } // end class constructor

} // end class


function getCWID($account)
{
	global $DEBUG;
	global $dbug;
	
	$ldap_server = "ldap.example.com";
	$ldap_user       = "ldapuser";
	$ldap_pass       = "ldappass";

	$ldapconn = ldap_connect($ldap_server) or die(ldap_error($ldapconn));
	$ldapbind = ldap_bind($ldapconn,"cn=$ldap_user,o=domain",$ldap_pass) or die(ldap_error($ldapconn));
	$filter = "(|(uid=$account)(mail=$account)(mail=$account@example.com)(cwid=$account))";
	#$filter = "uid=" . $account;
	if ($DEBUG) $dbug->setMsg("LDAP_Filter", $filter);

	$ldapsearch = ldap_search($ldapconn,"o=domain",$filter) or die(ldap_error($ldapconn));
	$userinfo = ldap_get_entries($ldapconn,$ldapsearch) or die(ldap_error($ldapconn));
	$CWID = $userinfo[0]['cwid'][0];
	ldap_close($ldapconn) or die(ldap_error($ldapconn)); 
	
	if ($CWID != "")
	{
		if ($DEBUG) $dbug->setMsg("getCWID($account) returned", $CWID);
		return $CWID;
	} else {
		if ($DEBUG) $dbug->setMsg("getCWID($account)", "Returned zero results for user $user");
		return "false";
	}
}

function WouldYouLikeFriesWithThat($mac_addr,$ip_addr,$user)
{
	# Initialize variables
	global $DEBUG;
	global $dbug;
	
	# Verify and sanitize input
	if ($mac_addr != "" && (!NetUtil::validateMacAddress($mac_addr))) die("Invalid MAC address");
	if ($ip_addr != "")
	{
		#if (!NetUtil::validateIPAddress($ip_addr)) die("Invalid IPv4/IPv6 address");
		if (NetUtil::validateIPv4Address($ip_addr)) $version = 4;
		if (NetUtil::validateIPv6Address($ip_addr)) $version = 6;
	}
	
	$where1 = "MacAddress LIKE '%".$mac_addr."%'";
	
	# Search by user
	if ($user != "" && $mac_addr == "")
	{
		
		$mac_array = getMacByUser($user);
		
		if ($mac_array != "false")
		{
			$where1 = "(";
			foreach ($mac_array as &$mac)
			{
				$where1 .= "MacAddress = '" . NetUtil::formatMacAddress($mac,":") . "' OR ";
			}
			$where1 = substr($where1, 0, -4) . ")";
			if ($DEBUG) $dbug->setMsg("WHERE1", $where1);
		}
		
	}
		
	if ($version == 6)
	{
		$arpTrackRecord = new ArpTrackIPv6();
		$where = $where1." AND IPv6Address LIKE '%".NetUtil::inet6_expand($ip_addr)."%' ";
	} else {
		$arpTrackRecord = new ArpTrackMAC_IP();
		$where = $where1." AND IPAddress LIKE '%".$ip_addr."%' ";
	}
		
	$sort = "LastSeen";
	
	$data = $arpTrackRecord->getData($where, $sort);
	
	foreach ($data as $row)
	{
		$manuftr = getManuftr($row['MacAddress']);
		
		if ($version == 6)
		{
			$ip = NetUtil::inet6_compress($row['IPv6Address']);
		} else {
			$ip = $row['IPAddress'];
		}	
		
		if (getUserByMac($row['MacAddress']) == "")
		{
			$user_map = getUserFromNSS($row['MacAddress']);
		} else {
			$user_map = getUserByMac($row['MacAddress']);
		}
		
		echo "
		<tr" .(($alt = !$alt)?' class=alt':''). ">
		<td><a href=?a=search&mac=" . $row['MacAddress'] . ">" . $row['MacAddress'] . "</a></td>
		<td>" . $manuftr . "</td>
		<td><a href=?a=search&ip=" . $ip . ">" . $ip . "</a></td>
		<td>" . $row['FirstSeen'] . "</td>
		<td>" . $row['LastSeen'] . "</td>
	  <td>" . $user_map . "</td>";
		echo "</tr>";
	} 
}

/**
 * Gets MAC addresses associated with a user account
 *
 * @param string $user
 * @return array
 */
function getMacByUser($user)
{
	global $DEBUG;
	global $dbug;
	$i = 0;
	$mac_array = array();
	$CWID = getCWID($user);
	
	if ($CWID != "false") 
	{
	
		$superTrackRecord = new SuperTrackData();	
		$where = "CWID='$CWID' ";
		$data = $superTrackRecord->getData($where, $sort);
		
		foreach ($data as $row)
		{
			$mac_array[$i] = $row['mac_addr'];
			if ($DEBUG) $dbug->setMsg("MAC $i", $row['mac_addr']);
			$i++;
		}
		
		$netmgmt_device = new netmgmt_device();
		$where = "UserId='$CWID'";
		$data2 = $netmgmt_device->getData($where, $sort);
		
		foreach ($data2 as $row2)
		{
			$mac_array[$i] = $row2['MacAddress'];
			if ($DEBUG) $dbug->setMsg("MAC $i", $row2['MacAddress']);
			$i++;
		}
		
		return $mac_array;
	} else {
		if ($DEBUG) $dbug->setMsg("getMacByUser($user)", "Returned zero results for user $user");
		return "false";
	}
}

function getUserFromNSS($mac_addr)
{
	global $DEBUG;
	global $dbug;
	
	# Verify and sanitize input
	if ($mac_addr != "" && (!NetUtil::validateMacAddress($mac_addr))) die("Invalid MAC address");
	
	$netmgmt_device = new netmgmt_device();
	$clean_mac = NetUtil::cleanMacAddress($mac_addr);
	$where = "MacAddress='$clean_mac'";
	$data = $netmgmt_device->getData($where, $sort);
	foreach ($data as $row)
	{
		return $row['UserAdded'];
	}
}

function getUserByMac($mac_addr)
{
	# Verify and sanitize input
	if ($mac_addr != "" && (!NetUtil::validateMacAddress($mac_addr))) die("Invalid MAC address");
	
	$superTrackRecord = new SuperTrackData();
	$clean_mac = NetUtil::cleanMacAddress($mac_addr);
	$where = "mac_addr='$clean_mac'";
	$data = $superTrackRecord->getData($where, $sort);
	foreach ($data as $row)
	{
		return $row['CWID'];
	}
}

function getManuftr($mac_addr)
{
	# Verify and sanitize MAC address
	if (NetUtil::validateMacAddress($mac_addr))
	{
		// Remove separators and format in upper case
		$clean_mac = NetUtil::cleanMacAddress($mac_addr);
		
		// Get first 6 chars of MAC
		$oui = substr($clean_mac, 0, 6);
		
		$manuftr = new ArpTrackOUI();
		
		$data = $manuftr->getData("oui = '$oui'", $sort);
		
		foreach ($data as $row)
		{
			return $row['manuftr'];
		}
	} else {
		die("Invalid MAC address");
	}
}
?>
