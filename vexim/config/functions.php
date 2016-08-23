<?php

    /**
     * validate user password
     *
     * validate if password and confirmation password match.
     * They can not be empty.
     *
     * @param   string   $password   cleartext password
     * @param   string   $confirmationPassword  cleartext password (for validation)
     * @return  boolean  true if they match
     */
    function validate_password($password, $confirmationPassword)
    {
        return is_string($password) && ($password === $confirmationPassword) && ($password !== "");
    }

    /**
     * check user password strength
     *
     * validate if password is strong enough
     *
     * @param   string   $candidate   cleartext password
     * @return  boolean  true if password is strong enough
     */
    function password_strengthcheck($candidate)
    {
        global $passwordstrengthcheck;
        
        if ( $passwordstrengthcheck == 0
          || preg_match_all('$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$', $candidate)
          || preg_match_all('$\S*(?=\S{12,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$', $candidate)
          || preg_match_all('$\S*(?=\S{16,})(?=\S*[a-z])(?=\S*[A-Z])\S*$', $candidate)
          || (strlen($candidate)>20)
        ) {
            if (strtolower($candidate) <> strtolower($_POST['localpart'])
                && strtolower($candidate) <> strtolower($_POST['username'])
                )
            {
                return TRUE;
            }
        }

        return FALSE;
/*
    Explaining $\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$
    $ = beginning of string
    \S* = any set of characters
    (?=\S{8,}) = of at least length 8
    (?=\S*[a-z]) = containing at least one lowercase letter
    (?=\S*[A-Z]) = and at least one uppercase letter
    (?=\S*[\d]) = and at least one number
    (?=\S*[\W]) = and at least a special character (non-word characters)
    $ = end of the string

 */
    }
    
    /**
     * Check if a user already exists.
     *
     * Queries database $dbh, and redirects to the $page is the user already
     * exists.
     *
     * @param  mixed   $dbh         database to query
     * @param  string  $localpart  
     * @param  string  $domain_id
     * @param  string  $page       page to return to
     */
    function check_user_exists($dbh,$localpart,$domain_id,$page)
    {
        $query = "SELECT COUNT(*) AS c 
                  FROM   users 
                  WHERE  localpart=:localpart
                  AND    domain_id=:domain_id";
        $sth = $dbh->prepare($query);
        $sth->execute(array(':localpart'=>$localpart, ':domain_id'=>$domain_id));
        $row = $sth->fetch();
        if ($row['c'] != 0) 
        {
            header ("Location: $page?userexists=$localpart");
            die;
        }
    }

    /**
     * Check if mail address is compliant with RFC 3696.
     * localpart must not exceed 64 char, and the complete mail address
     * must not exceed 254 characters.
     *
     * @param  string  $localpart
     * @param  string  $domain
     * @param  string  $page       page to return to in case of failure
     */
    function check_mail_address($localpart,$domain,$page)
    {
      if ((strlen($localpart)+strlen($domain)>253) || strlen($localpart)>64)
        {
            header ("Location: $page?addresstoolong=$localpart");
            die;
        }
    }

    /**
     * Render the alphabet. Directly onto the page.
     *
     * @param  unknown  $flag  unknown
     */
    function alpha_menu($flag) 
    {
        global $letter;	// needs to be available to the parent
        if ($letter == 'all') 
        {
            $letter = '';
        }
        if ($flag) 
        {
            print "\n<p class='alpha'><a href='" . $_SERVER['PHP_SELF'] . 
                  "?LETTER=ALL' class='alpha'>ALL</a>&nbsp;&nbsp; ";
            // loops through the alphabet. 
            // For international alphabets, replace the string in the proper order
            foreach (preg_split('//', _("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), -1, 
                                PREG_SPLIT_NO_EMPTY) as $i) 
            {
      	        print "<a href='" . $_SERVER['PHP_SELF'] . 
                      "?LETTER=$i' class='alpha'>$i</a>&nbsp; ";
            }
            print "</p>\n";
        }
    }

    /**
     * crypt the plaintext password.
     *
     * @golbal  string  $cryptscheme
     * @param   string  $clear  the cleartext password
     * @param   string  $salt   optional salt
     * @return  string          the properly crypted password
     */
    function crypt_password($clear, $salt = '')
    {
        global $cryptscheme;

        if($cryptscheme === 'sha') {
            $hash = sha1($clear);
            $cryptedpass = '{SHA}' . base64_encode(pack('H*', $hash));
        } elseif ($cryptscheme === 'clear') {
            $cryptedpass=$clear;
        } else {
            if(empty($salt)) {
                switch($cryptscheme){
                    case 'des':
                        $salt = '';
                    break;
                    case 'md5':
                        $salt='$1$';
                    break;
                    case 'sha512':
                        $salt='$6$';
                    break;
                    case 'bcrypt':
                        $salt='$2a$10$';
                    break;
                    default:
                        if(preg_match('/\$[:digit:][:alnum:]?\$/', $cryptscheme)) {
                            $salt=$cryptscheme;
                        } else {
                            die(_('The value of $cryptscheme is invalid!'));
                        }
                }
                $salt.=get_random_bytes(CRYPT_SALT_LENGTH).'$';
            }
            $cryptedpass = crypt($clear, $salt);
        }
        return $cryptedpass;
    }

    /**
     * Generate pseudo random bytes
     *
     * @param   int     $count  number of bytes to generate
     * @return  string          A random string
     */
    function get_random_bytes($count)
    {
        $output = base64_encode(openssl_random_pseudo_bytes($count));
        $output = strtr(substr($output, 0, $count), '+', '.'); //base64 is longer, so must truncate the result
        return $output;
    }

    /**
     * Properly encode a mail header text for using with mail().
     *
     * @param   string  $text   the text to encode
     * @return  string          The encoded header
     */
    function vexim_encode_header($text)
    {
        if (function_exists('mb_encode_mimeheader')) {
            mb_internal_encoding('UTF-8');
            $text = mb_encode_mimeheader($text, 'UTF-8', 'Q');
        } else {
            $text = str_replace(" ", "_", quoted_printable_encode(trim($text)));
            $text = str_replace("?", "=3F", $text);
            $text = str_replace("=\r\n", "?=\r\n =?UTF-8?Q?", $text);
            $text = "=?UTF-8?Q?" . $text . "?=" ;
        }
    }

    /**
     * End current session and delete $_SESSION variable.
     *
     */
    function invalidate_session()
    {
      $_SESSION = array();
      session_destroy();
    }

    /**
     * Makes any text safe to be displayed on a web page
     * @param $text string
     * @return string
     */
    function html_escape($text)
    {
        return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
    }