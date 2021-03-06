<?php

/*
require_once $_SERVER['DOCUMENT_ROOT'] . '/aw-cpanel/classes/Db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/aw-cpanel/classes/Helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/aw-cpanel/lib/aweber_api/aweber_api.php';
*/

ini_set('memory_limit', '1024M'); // or you could use 1G
set_time_limit(7200);

require_once '/home/giovannirodriguez/theadriangee.com/aw-cpanel/classes/Db.php';
require_once '/home/giovannirodriguez/theadriangee.com/aw-cpanel/classes/Helper.php';
require_once '/home/giovannirodriguez/theadriangee.com/aw-cpanel/lib/aweber_api/aweber_api.php';

//error_reporting(0);

class Aweber
{

    public $awn;
    public $db;
    public $h;
    public $account;

    const MASTER_ACCOUNT_ID = 1205454;
    const APP_ID = 'bc28494b';
    const CONSUMER_KEY = 'AkAZkZiBS0lsohkYXl2ohcQD';
    const CONSUMER_SECRET = 'gCZi8CRrJrSUEq3SzMt48IenHgrYi9mXvJ2hsaBe';

    protected function __construct($update_data = true)
    {
        $this->db = new Db();
        $this->h = new Helper();
        $this->awn = new AWeberAPI(self::CONSUMER_KEY, self::CONSUMER_SECRET);
        $db_accessToken = $this->get_accessToken();
        if ($db_accessToken == '') {
            if (empty($_GET['oauth_token'])) {
                $callbackUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $this->set_callbackUrl($callbackUrl);
                list($requestToken, $requestTokenSecret) = $this->awn->getRequestToken($callbackUrl);
                $this->set_requestTokenSecret($requestTokenSecret);
                ?>
                <script type="text/javascript">
                    document.location = "<?php echo (string)$this->awn->getAuthorizeUrl();  ?>";
                </script>
                <?php
            } // end if empty($_GET['oauth_token'])
            $this->awn->user->tokenSecret = $this->get_requestTokenSecret();
            $this->awn->user->requestToken = $_GET['oauth_token'];
            $this->awn->user->verifier = $_GET['oauth_verifier'];
            list($accessToken, $accessTokenSecret) = $this->awn->getAccessToken();
            $this->set_accessToken($accessToken);
            $this->set_accessTokenSecret($accessTokenSecret);
            ?>
            <script type="text/javascript">
                document.location = "<?php echo (string)$this->get_callbackUrl();  ?>";
            </script>
            <?php
        } // end if $db_accessToken == '')
        $this->awn->adapter->debug = false;
        $account = $this->awn->getAccount($this->get_accessToken(), $this->get_accessTokenSecret());
        $this->account = $account;
        if ($update_data) {
            //$this->update_data();
            $this->update_lists_subscribers_status();
        }
    }

    /**
     * @param bool $update_data
     * @return Aweber|null
     */
    public static function getInstance($update_data = true)
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new Aweber($update_data);
        }
        return $instance;
    }

    /**
     *
     */
    function update_lists_subscribers_status()
    {
        foreach ($this->account->lists as $offset => $list) {
            $totalSubs = $list->total_subscribed_subscribers;
            $query = "update aw_lists set subs_total=$totalSubs where list_id=$list->id";
            //echo "Query: ".$query."<br>";
            $this->db->query($query);
        }
    }

    /**
     *
     */
    function update_data()
    {

        foreach ($this->account->lists as $offset => $list) {
            $list_id = $list->id;
            $list_status = $this->is_list_exists($list_id);
            if ($list_status == 0) {
                $this->add_list($list);
            }
            $this->update_list_subscriber_totals($list);
            foreach ($list->campaigns as $campaign) {
                $this->update_campaign_data($list_id, $campaign);
                $campaign_id = $campaign->id;
                $campaign_status = $this->is_campaign_exists($campaign_id);
                if ($campaign_status == 0) {
                    $this->add_campaign($list_id, $campaign);
                }
                foreach ($campaign->links as $link) {
                    $link_id = $link->id;
                    $link_status = $this->is_link_exists($link_id);
                    if ($link_status == 0) {
                        $this->add_link($list_id, $campaign_id, $link);
                    }
                } // end foreach
            } // end foreach
        } // end foreach
    }

    /*************************************** Code related to Authentication ************************************/

    function set_callbackUrl($data)
    {
        $query = "update aw_credentials set callbackUrl='$data'";
        $this->db->query($query);
    }

    /**
     * @return mixed
     */
    function get_callbackUrl()
    {
        $query = "select callbackUrl from aw_credentials";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $callbackUrl = $row['callbackUrl'];
        }
        return $callbackUrl;
    }

    /**
     * @param $data
     */
    function set_requestTokenSecret($data)
    {
        $query = "update aw_credentials set requestTokenSecret='$data'";
        $this->db->query($query);
    }

    /**
     * @return mixed
     */
    function get_requestTokenSecret()
    {
        $query = "select requestTokenSecret from aw_credentials";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $requestTokenSecret = $row['requestTokenSecret'];
        }
        return $requestTokenSecret;
    }

    /**
     * @param $data
     */
    function set_accessToken($data)
    {
        $query = "update aw_credentials set accessToken='$data'";
        $this->db->query($query);
    }

    /**
     * @return mixed
     */
    function get_accessToken()
    {
        $query = "select accessToken from aw_credentials";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $accessToken = $row['accessToken'];
        }
        return $accessToken;
    }

    /**
     * @param $data
     */
    function set_accessTokenSecret($data)
    {
        $query = "update aw_credentials set accessTokenSecret='$data'";
        $this->db->query($query);
    }

    /**
     * @return mixed
     */
    function get_accessTokenSecret()
    {
        $query = "select accessTokenSecret from aw_credentials";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $accessTokenSecret = $row['accessTokenSecret'];
        }
        return $accessTokenSecret;
    }


    /*************************************** Code related to data update ***************************************/

    function is_list_exists($list_id)
    {
        $query = "select * from aw_lists where list_id=$list_id";
        $num = $this->db->numrows($query);
        return $num;
    }


    /**
     * @param $item
     */
    function add_list($item)
    {
        $query = "insert into aw_lists 
                (list_id,
                name,
                campaigns_collection_link,
                subscribers_collection_link,
                self_link) 
                values ('" . $item->id . "',
                        '" . $item->name . "',
                        '" . $item->campaigns_collection_link . "',
                        '" . $item->subscribers_collection_link . "',
                        '" . $item->self_link . "')";
        $this->db->query($query);
    }

    /**
     * @param $item
     */
    function update_list_subscriber_totals($item)
    {
        $subscribers = $this->get_list_subscribers($item->id);
        $this->init_subscribers_process($item->id, $subscribers);
        $totalSubs = count($subscribers);
        $query = "update aw_lists set subs_total=$totalSubs where list_id=$item->id";
        $this->db->query($query);
    }

    /**
     * @param $subscriber_id
     * @return int
     */
    function is_subscriber_exists($subscriber_id)
    {
        $query = "select * from aw_subscribers where subscriber_id=$subscriber_id";
        $num = $this->db->numrows($query);
        return $num;
    }

    /**
     * @param $list_id
     * @param $subscribers
     */
    function init_subscribers_process($list_id, $subscribers)
    {
        foreach ($subscribers as $subscriber) {
            $status = $this->is_subscriber_exists($subscriber->id);
            if ($status == 0) {
                $this->add_new_subscriber($list_id, $subscriber);
            }
        }
    }

    /**
     * @param $list_id
     * @param $subscriber_id
     */
    function add_new_subscriber($list_id, $subscriber)
    {
        $query = "insert into aw_subscribers 
                  (old_list_id,
                  subscriber_id,
                  email) 
                  values ($list_id,
                          $subscriber->id, 
                          $subscriber->email)";
        $this->db->query($query);
    }


    /**
     * @param $list_id
     */
    function update_subscribers_email($list_id)
    {
        $subscribers = $this->get_list_subscribers($list_id);
        foreach ($subscribers as $subscriber) {
            echo "Subscriber ID: {$subscriber->id} <br>";
            echo "Subdcriber Email: {$subscriber->email}<br>";
            $this->update_subs_email($subscriber->id, $subscriber->email);
        } // end foreach
    }

    /**
     * @param $subscriber_id
     * @param $email
     */
    function update_subs_email($subscriber_id, $email)
    {
        $query = "update aw_subscribers set email='$email' where subscriber_id='$subscriber_id'";
        $this->db->query($query);
    }

    /**
     * @param $campaign_id
     * @return int
     */
    function is_campaign_exists($campaign_id)
    {
        $query = "select * from aw_campaigns where campaign_id=$campaign_id";
        $num = $this->db->numrows($query);
        return $num;
    }

    /**
     * @param $list_id
     * @param $item
     */
    function add_campaign($list_id, $item)
    {
        $query = "insert into aw_campaigns 
                (list_id,
                campaign_id,
                campaign_type,
                links_collection_link,
                messages_collection_link,
                self_link,
                subject) 
                values ('$list_id',
                        '" . $item->id . "',
                        '" . $item->campaign_type . "',
                        '" . $item->links_collection_link . "',
                        '" . $item->messages_collection_link . "',
                        '" . $item->self_link . "',
                        '" . $item->subject . "')";
        $this->db->query($query);
    }

    /**
     * @param $link_id
     * @return int
     */
    function is_link_exists($link_id)
    {
        $query = "select * from aw_links where link_id=$link_id";
        $num = $this->db->numrows($query);
        return $num;
    }

    /**
     * @param $list_id
     * @param $campaign_id
     * @param $item
     */
    function add_link($list_id, $campaign_id, $item)
    {
        $query = "insert into aw_links 
                (list_id,
                campaign_id,
                link_id,
                url,
                clicks_collection_link,
                self_link) 
                values ('$list_id',
                        '$campaign_id',
                        '" . $item->data['id'] . "',
                        '" . $item->data['url'] . "',
                        '" . $item->data['clicks_collection_link'] . "',
                        '" . $item->data['self_link'] . "')";
        $this->db->query($query);
    }

    /**
     * @param $list_id
     * @param $campaign
     */
    function update_campaign_data($list_id, $campaign)
    {
        $status = $this->is_campaign_stats_data_exist($campaign->id);
        if ($status == 0) {
            $this->add_campaign_stats($list_id, $campaign);
        } // end if
        else {
            $this->update_campaign_stats($campaign);
        } // end else
    }

    /**
     * @param $list_id
     * @param $campaign
     */
    function add_campaign_stats($list_id, $campaign)
    {
        $now = time();
        $query = "insert into aw_campaign_stats 
                (list_id,
                campaign_id,
                subject,
                total_opens,
                total_sent,
                total_clicks,
                updated) 
                values ('$list_id',
                        '$campaign->id',
                        '$campaign->subject',
                        '$campaign->total_opens',
                        '$campaign->total_sent',
                        '$campaign->total_clicks',
                        '$now')";
        $this->db->query($query);
    }

    /**
     * @param $campaign
     */
    function update_campaign_stats($campaign)
    {
        $now = time();
        $query = "update aw_campaign_stats  
                set subject='$campaign->subject', 
                    total_opens='$campaign->total_opens', 	
                    total_sent='$campaign->total_sent', 
                    total_clicks='$campaign->total_clicks' 
                    updated='$now' where campaign_id=$campaign->id";
        $this->db->query($query);
    }

    /**
     * @param $campaign_id
     * @return int
     */
    function is_campaign_stats_data_exist($campaign_id)
    {
        $query = "select * from aw_campaign_stats where campaign_id=$campaign_id";
        $num = $this->db->numrows($query);
        return $num;
    }

    /*************************************** Code related to toolbar ***************************************/

    /**
     * @param $type
     * @return string
     */
    function get_lists_drop_down($type)
    {
        $list_a = "";
        $list_a .= "<select id='list_dropdown_$type' style='width: 100px;'>";
        $list_a .= "<option value='0' selected>Please select</option>";
        $query = "select * from aw_lists order by name";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $list_a .= "<option value='" . $row['list_id'] . "'>" . $row['name'] . "</option>";
        }
        $list_a .= "</select>";
        return $list_a;
    }


    /**
     * @param $campaign_id
     * @return mixed
     */
    function get_campaign_name_by_id($campaign_id)
    {
        $query = "select * from aw_campaigns where campaign_id=$campaign_id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['subject'];
        }
        return $name;
    }


    /**
     * @param bool $items
     * @return string
     */
    function get_links_list($items = false)
    {
        $list_a = "";
        $list_a .= "<select multiple id='click_types_dropdown' style='width: 100%'>";
        $list_a .= "<option value='0' selected>Any Link</option>";
        $query = "select * from aw_links";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $cname = $this->get_campaign_name_by_id($row['campaign_id']);
            if ($cname != '') {
                //$name = "$cname - " . $row['url'] . "";
                $name = $row['url'];
                if ($items) {
                    $items_arr = explode(',', $items);
                    if (in_array($row['link_id'], $items_arr)) {
                        $list_a .= "<option value='" . $row['link_id'] . "' selected>$name</option>";
                    } // end if
                    else {
                        $list_a .= "<option value='" . $row['link_id'] . "'>$name</option>";
                    } // end else
                } // end if items
                else {
                    $list_a .= "<option value='" . $row['link_id'] . "'>$name</option>";
                } // end else
            } // end if $cname!=''
        } // end while
        $list_a .= "</select>";
        return $list_a;
    }

    /**
     * @return string
     */
    function get_links_num_dropdown()
    {
        $list_a = "";
        $list_a .= "<select id='links_total_dropdown' style='width: 100px;'>";
        $list_a .= "<option value='0' selected>Please select</option>";
        for ($i = 1; $i <= 18; $i++) {
            $list_a .= "<option value='$i'>$i</option>";
        }
        $list_a .= "</select>";
        return $list_a;
    }

    /**
     * @return string
     */
    function get_toolbar()
    {
        $list_a = "";
        $src_list = $this->get_lists_drop_down('src');
        $dst_list = $this->get_lists_drop_down('dst');
        $links_total = $this->get_links_num_dropdown();
        $links = $this->get_links_list();

        $list_a .= "<table class='display' style='margin-top: 35px;width: 100%'>";
        $list_a .= "<tr>";
        $list_a .= "<td class='col-md-1'>Source<span style='color: red;'>*</span></td>";
        $list_a .= "<td class='col-md-1'>$src_list</td>";
        $list_a .= "<td class='col-md-1'>Destination<span style='color: red;'>*</span></td>";
        $list_a .= "<td class='col-md-1'>$dst_list</td>";
        $list_a .= "<td class='col-md-1'>links #<span style='color: red;'>*</span></td>";
        $list_a .= "<td class='col-md-1'>$links_total</td>";
        $list_a .= "<td class='col-md-1'>Links<span style='color: red;'>*</span></td>";
        $list_a .= "<td class='col-md-5' colspan='4'>$links</td>";
        $list_a .= "</tr>";

        $list_a .= "<tr>";
        $list_a .= "<td colspan='9' style='text-align: left;padding-left: 20px;'><span style='color: red;'>*</span> Required fiedls</td>";
        $list_a .= "</tr>";

        $list_a .= "<tr>";
        $list_a .= "<td style='text-align: left;padding-left: 20px;'><button id='add_new_list_settings' class='btn btn-primary'>Add</button></td>";
        $list_a .= "</tr>";

        $list_a .= "</table>";

        return $list_a;
    }

    /**
     * @param $id
     * @return string
     */
    function get_ops_items($id)
    {
        $list = "";
        $list .= "<div class='row'>";
        $list .= "<span style='cursor: pointer;' class='col-md-1'><i id='list_edit_$id' class='fa fa-pencil-square-o' aria-hidden='true'></i></span>";
        $list .= "<span style='cursor: pointer;' class='col-md-1'><i id='config_del_$id' class='fa fa-trash-o' aria-hidden='true'></i></span>";
        $list .= "</div>";
        return $list;
    }


    /************************************ Code related to Subscribers page ***********************************/

    /**
     * @return string
     */
    function get_subscribers_page()
    {
        $list_a = "";
        $campaigns_table = $this->h->get_campaigns_stats_table();
        $config_table = $this->h->get_lists_config_data();
        $toolbar = $this->get_toolbar();

        // ****************************** Configuration table ******************************
        $list_a .= "<div class='row' style='padding-top: 25px; font-weight: bold;'>";
        $list_a .= "<span class='col-md-12'>Subscribers Config</span>";
        $list_a .= "</div>";

        $list_a .= "<div class='row' style='margin-top: 15px;'>";
        $list_a .= "<span class='col-md-12' id='settigs_data'>$config_table</span>";
        $list_a .= "</div>";

        // ****************************** Toolbar ******************************
        $list_a .= "<div class='row' style='padding-top: 15px;'>";
        $list_a .= "<span class='col-md-12' style='text-align: center'><div id='ajax_loader' style='display: none;'><img src='http://mycodebusters.com/aw-cpanel/assets/img/ajax-loader.gif'></div></span>";
        $list_a .= "</div>";

        $list_a .= "<div class='row' style=''>";
        $list_a .= "<span class='col-md-12'>$toolbar</span>";
        $list_a .= "</div>";

        $list_a .= "<div class='row' style='margin-top: 15px;'>";
        $list_a .= "<span class='col-md-12' id='add_err' style='color: red;'></span>";
        $list_a .= "</div>";

        $list_a .= "<div class='row' style='margin-top: 15px;margin-bottom:35px; '>";
        $list_a .= "<span class='col-md-12'></span>";
        $list_a .= "</div>";

        // ****************************** Campaigns table ******************************
        $list_a .= "<div class='row' style='padding-top: 25px; font-weight: bold;'>";
        $list_a .= "<span class='col-md-12'>Broadcat Campaigns Stats</span>";
        $list_a .= "</div>";

        $list_a .= "<div class='row' style='padding-top: 25px'>";
        $list_a .= "<span class='col-md-12'>";
        $list_a .= $campaigns_table;
        $list_a .= "</span>";
        $list_a .= "</div>";

        return $list_a;
    }

    /*************************************** Data processing section *******************************/

    function get_list_subscribers($list_id)
    {
        $query = "select * from aw_lists where list_id=$list_id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $url = $row['subscribers_collection_link'];
        }
        $subscribers = $this->awn->loadFromUrl($url);
        return $subscribers;
    }

    /**
     * @param $link_id
     * @return mixed
     */
    function get_link_clicks_data($link_id)
    {
        $query = "select * from aw_links where link_id=$link_id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $url = $row['clicks_collection_link'];
        }
        $clicks_data = $this->awn->loadFromUrl($url);
        return $clicks_data->data['entries'];
    }

    /**
     * @param $clicks_data
     * @return array
     */
    function get_link_click_subscribers($clicks_data)
    {
        foreach ($clicks_data as $entry) {
            $subs_link = $entry['subscriber_link'];
            $pos = strpos($subs_link, '/subscribers/');
            $id = substr($subs_link, ($pos + 13));
            $subs[] = $id;
        } // end foreach
        return $subs;
    }

    /**
     * @param $list_id
     * @return mixed
     */
    function get_list_name_by_id($list_id)
    {
        $query = "select * from aw_lists where list_id=$list_id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['name'];
        }
        return $name;
    }


    /**
     * @param $id
     * @param $links_arr
     * @param $src_list_id
     * @param $dst_list_id
     * @return bool
     */
    function process_list_subscribers($id, $links_arr, $src_list_id, $dst_list_id)
    {
        $time_start = microtime(true);
        $dest_list_name = $this->get_list_name_by_id($dst_list_id);
        $i = 0;
        $move = array();
        $totalLinks = count($links_arr);
        $subscribers = $this->get_list_subscribers($src_list_id);
        $totalSubs = count($subscribers);

        echo "<div style='font-weight: bold;'>";
        echo "Total subscribers: " . $totalSubs . "<br>";
        echo "Total Links: " . $totalLinks . "<br>";
        echo "</div>";
        echo "<br>--------------------------------------------------------------------------------------<br>";

        foreach ($subscribers as $subscriber) {
            $subsScore = 0;
            $subs_id = $subscriber->id;
            echo "Current Subscriber: " . $subs_id . "<br>";

            if ($i <= 1) {

                foreach ($links_arr as $link_id) {
                    $clicks_data = $this->get_link_clicks_data($link_id);
                    $link_subscribers = $this->get_link_click_subscribers($clicks_data);
                    echo "Link click subscribers<pre>";
                    print_r($link_subscribers);
                    echo "</pre><br>";
                    if (in_array($subs_id, $link_subscribers)) {
                        $subsScore++;
                    } // end if
                } // end foreach for links
                echo "Current Subscriber Score: " . $subsScore . "<br>";
                echo "<br>--------------------------------------------------------------------------------------<br>";
                if ($subsScore == $totalLinks) {
                    array_push($move, $subs_id);
                    $this->move_subscriber_to_other_list($subscriber, $dest_list_name);
                } // end if

            }  // end if
            else {
                $totalSubs = count($move);
                echo "Number of subscribers has been moved: $totalSubs <br>";
                echo "Subscribers list: <pre>";
                print_r($move);
                echo "</pre><br>";
                $time_end = microtime(true);
                $execution_time = ($time_end - $time_start);
                echo "<span style='font-weight: bold;'>Execution time (secs) : $execution_time </span><br>";
                $this->update_config_entry($id);
                return false;
            }
            $i++;
        } // end foreach for subscribers

        $totalSubs = count($move);
        echo "Number of subscribers has been moved: $totalSubs <br>";
        echo "Subscribers list: <pre>";
        print_r($move);
        echo "</pre><br>";
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        echo "<span style='font-weight: bold;'>Execution time (secs) : $execution_time </span><br>";
        $this->update_config_entry($id);
        return false;
    }


    /**
     * @param $id
     */
    function update_config_entry($id)
    {
        $now = time();
        $query = "update aw_lists_config set processed='$now' where id=$id";
        $this->db->query($query);
    }

    /**
     * @param $subscriber
     * @param $list_name
     */
    function move_subscriber_to_other_list($subscriber, $dest_list_id, $list_name)
    {
        $found_lists = $this->account->lists->find(array('name' => $list_name));
        $destination_list = $found_lists[0];

        echo "Destination List Object<pre>";
        print_r($destination_list);
        echo "</pre>";

        echo "Subscriber ID to be moved: " . $subscriber->id . "<br>";
        echo "New list Name: " . $list_name . "<br>";

        try {
            $subscriber->move($destination_list);
            $now = time();
            $query = "update aw_subscribers 
                  set   old_list_id=$dest_list_id  , processed='$now'
                  where subscriber_id=$subscriber->id";
            echo "Query: " . $query . "<br>";
            $this->db->query($query);
            echo "Subscriber with ID {$subscriber->id} has been moved to list $dest_list_id<br>";
            echo "<br>---------------------------------------------------------------------------<br>";

        } // end try
        catch (AWeberAPIException $exc) {
            $now = time();
            $query = "update aw_subscribers 
                  set processed='$now'
                  where subscriber_id=$subscriber->id";
            echo "Query: " . $query . "<br>";
            $this->db->query($query);
            echo "Subscriber with ID {$subscriber->id} has been moved to list $dest_list_id<br>";
            echo "<br>---------------------------------------------------------------------------<br>";
        }

    }

    /**
     * @param $subscriber
     * @return array
     */
    function get_subscriber_clicks($subscriber)
    {
        $events = array();
        $subscriber_activity = $subscriber->getActivity();
        foreach ($subscriber_activity as $event) {
            if ($event->type == 'click') {
                $pos = strpos($event->data['self_link'], '/clicks/');
                $clearlink = substr($event->data['self_link'], 0, $pos);
                if ($clearlink != '') {
                    $events[] = $clearlink;
                }
            } // end if
        } // end foreach

        $pure_events = array_unique($events);

        echo "Current Subscriber Click events: <pre>";
        print_r($events);
        echo "</pre><br>";
        echo "<br>---------------------------------------------------------------------------<br>";

        echo "Current Subscriber Click Unique events: <pre>";
        print_r($pure_events);
        echo "</pre><br>";
        echo "<br>---------------------------------------------------------------------------<br>";

        return $pure_events;
    }

    /**
     * @return array
     */
    function get_all_links_array()
    {
        $query = "select * from aw_links order by link_id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $links[] = $row['link_id'];
        }
        return $links;
    }

    /**
     *
     */
    function get_active_lists()
    {

        $now = time();
        $query = "select * from aw_lists_config order by processed limit 0,1";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if (($now - $row['processed']) > 3600) {
                $id = $row['id'];
                $src_list_id = $row['src_list'];
                $dst_list_id = $row['dest_list'];
                if ($row['clicks_type'] != 0) {
                    $links_arr = explode(',', $row['clicks_type']);
                    $anylink = false;
                } // end if
                else {
                    $links_arr = $this->get_all_links_array();
                    $anylink = true;
                }
                $this->process_list_subscribers($id, $links_arr, $src_list_id, $dst_list_id);
            } // end if
            else {
                echo "There are no lists to be processed .....";
            }
        } // end while
    }

    /**
     *
     */
    function process_list_single_subscriber()
    {
        $now = time();
        $query = "select * from aw_subscribers where processed=0 and 
                  new_list_id<>0 
                  and old_list_id<>new_list_id limit 0, 1";
        echo "Query: " . $query . "<br><br>";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $this->move_single_subscriber($row['old_list_id'], $row['new_list_id'], $row['subscriber_id'], $row['total_clicks']);
            } // end while
        } // end if $num > 0
        else {
            echo "<p style='text-align: center;'>The are no subscribers to be processed ....</p>";
        }
    }

    /**
     * @param $src
     * @param $dst
     * @param $subscriber_id
     * @param $total
     */
    function move_single_subscriber($src, $dst, $subscriber_id, $total)
    {
        $subscribers = $this->get_list_subscribers($src);
        $now = time();
        echo "Total links required to be qualified: " . $total . "<br>";
        foreach ($subscribers as $subscriber) {
            if ($subscriber->id == $subscriber_id) {
                $click_events = $this->get_subscriber_clicks($subscriber);
                $total_clicks = count($click_events);
                echo "Current Subscriber: " . $subscriber_id . "<br>";
                echo "Total Unique Clicks For Current Subscriber: " . $total_clicks . "<br>";
                if ($total_clicks == $total) {
                    $list_name = $this->get_list_name_by_id($dst);
                    $this->move_subscriber_to_other_list($subscriber, $dst, $list_name);
                } // end if
                else {
                    $query = "update aw_subscribers set processed='$now' where subscriber_id=$subscriber_id";
                    $this->db->query($query);
                    echo "Subscriber with ID $subscriber_id marked as processed<br>";
                    echo "<br>---------------------------------------------------------------------------<br>";
                }
            } // end if $subscriber->id == $subscriber_id
            else {
                continue;
            } // end else
        } // end foreach
    }


    /**
     * @param $src
     * @param $list_name
     */
    function move_subscribers_back($src, $old_list_id)
    {

        $old_lists_subscribers = $this->get_list_subscribers($old_list_id);
        $subscribers = $this->get_list_subscribers($src);
        $i = 0;
        foreach ($subscribers as $subscriber) {
            if ($subscriber->status == 'unsubscribed') {
                echo "Subscriber:<pre>";
                print_r($subscriber->data);
                echo "</pre><br>----------------------------------------------------------<br>";
                $this->resubscribe($subscriber, $old_lists_subscribers);

                /*
                try {
                    $subscriber->status = 'unsubscribed';
                    $subscriber->save();
                } // end try
                catch (AWeberAPIException $exc) {
                    print "<h3>AWeberAPIException:</h3>";
                    print " <li> Type: $exc->type              <br>";
                    print " <li> Msg : $exc->message           <br>";
                    print " <li> Docs: $exc->documentation_url <br>";
                    print "<hr>";
                }
                */

            } // end if $subscriber->status!='unsubscribed'
            $i++;
        } // end foreach
    }

    /**
     * @param $subscriber
     * @param $old_list_id
     */
    function resubscribe($subscriber, $old_lists_subscribers)
    {
        foreach ($old_lists_subscribers as $old_subscriber) {
            if ($old_subscriber->id == $subscriber->id && $old_subscriber->status == 'unsubscribed') {
                try {
                    $old_subscriber->status = 'subscribed';
                    $old_subscriber->save();
                } // end try
                catch (AWeberAPIException $exc) {
                    print "<h3>AWeberAPIException:</h3>";
                    print " <li> Type: $exc->type              <br>";
                    print " <li> Msg : $exc->message           <br>";
                    print " <li> Docs: $exc->documentation_url <br>";
                    print "<hr>";
                }
            } // end if
        } // end foreach
    }


}