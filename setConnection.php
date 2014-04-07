<?php

class SetConnection {

    private  $link;
    public function __construct($hostname,$username, $password) {
        //print_r($hostname);
        //echo "!!!!!!!!!!!!!!!!!!!!!";
         $this->link = mysql_connect("$hostname", "$username", "$password") or die(mysql_error());
    }
  
    public function closeConnection(){
        mysql_close($this->link);
    }

    public function getGUID() {
        $guid;
        if (function_exists('com_create_guid')) {
            $guid = com_create_guid();
        } else {
            mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $guid = substr($charid, 0, 8) . $hyphen
                    . substr($charid, 8, 4) . $hyphen
                    . substr($charid, 12, 4) . $hyphen
                    . substr($charid, 16, 4) . $hyphen
                    . substr($charid, 20, 12)
            ; // "}"
        }
        $rmarr = array("{", "}");
        $guid = str_replace($rmarr, "", $guid);
        return $guid;
    }

    public function getWebSiteId($name, $domain) {
       $query = "select `w`.`id` from `kr_website` `w` LEFT JOIN `kr_website_cstm` `wc`"
                . "ON(`w`.`id` = `wc`.`id_c`) where `w`.`name` LIKE '" . mysql_real_escape_string($name) . "' AND `wc`.`url_c` LIKE '$domain'";

        $result = mysql_query($query) or die(mysql_error());
        $webId = "";
        while ($row = mysql_fetch_array($result)) {
            $webId = $row['id'];
        }
        if ($webId == "") {
            $guid = $this->getGUID();
            $query = "insert into kr_website(`id`,`name`) Values ('$guid','" . mysql_real_escape_string($name) . "')";
            //print_r($query);
            mysql_query($query)or die(mysql_error());
            $query = "insert into kr_website_cstm(`id_c`,`url_c`) Values ('$guid','$domain')";
            //print_r($query);
            mysql_query($query)or die(mysql_error());
            $webId = $guid;
        }
        return $webId;
    }

}
?>
