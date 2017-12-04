<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/aw-cpanel/classes/Db.php';

class Helper
{
    public $db;

    function __construct()
    {
        $this->db = new Db();
    }


    /**
     * @param $campaign_id
     * @return string
     */
    function get_campaign_stats($campaign_id)
    {
        $list_a = "";
        $query = "select * from aw_campaign_stats where campaign_id=$campaign_id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $item = new stdClass();
            foreach ($row as $key => $value) {
                $item->$key = $value;
            } // end foreach
        } // end while

        $list_a .= "<div class='row' style='text-align: left;'>";
        $list_a .= "<span class='col-md-4'>Sent</span>";
        $list_a .= "<span class='col-md-1'>{$item->total_sent}</span>";
        $list_a .= "</div>";

        $list_a .= "<div class='row' style='text-align: left;'>";
        $list_a .= "<span class='col-md-4'>Opened</span>";
        $list_a .= "<span class='col-md-1'>{$item->total_opens}</span>";
        $list_a .= "</div>";

        $list_a .= "<div class='row' style='text-align: left;'>";
        $list_a .= "<span class='col-md-4'>Clicked</span>";
        $list_a .= "<span class='col-md-1'>{$item->total_clicks}</span>";
        $list_a .= "</div>";

        return $list_a;
    }

    /**
     * @param $campaigns
     * @return string
     */
    function get_list_campaigns_section($campaigns)
    {
        $list_a = "";
        foreach ($campaigns as $campaign_id) {
            $campaign_stats = $this->get_campaign_stats($campaign_id);
            $campaign_name = $this->get_campaign_name_by_id($campaign_id);
            $list_a .= "<div class='row' style='text-align: left;'>";
            $list_a .= "<span class='col-md-8'>$campaign_name</span>";
            $list_a .= "<span class='col-md-4'>$campaign_stats</span>";
            $list_a .= "</div>";

            $list_a .= "<div class='row' style='text-align: left;'>";
            $list_a .= "<span class='col-md-12'><hr/></span>";
            $list_a .= "</div>";
        } // end foreach
        return $list_a;
    }

    /**
     * @param $list_id
     * @return array
     */
    function get_list_campaigns($list_id)
    {
        $query = "select * from aw_campaigns where list_id=$list_id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $campaigns[] = $row['campaign_id'];
        }
        return $campaigns;
    }


    /**
     * @return string
     */
    function get_campaigns_stats_table()
    {

        $list_a = "";

        $list_a .= "<table id='list_table' class='display' cellspacing='0' width='100%'>";

        $list_a .= "<thead>";
        $list_a .= "<tr>";
        $list_a .= "<th>ListID</th>";
        $list_a .= "<th>ListName</th>";
        $list_a .= "<th>Total Subscribers</th>";
        $list_a .= "<th>Campaigns Data</th>";
        $list_a .= "</tr>";
        $list_a .= "</thead>";

        $list_a .= "<tbody>";

        $query = "select * from aw_lists order by name ";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['list_id'];
            $name = $row['name'];
            $total = $row['subs_total'];
            $campaigns = $this->get_list_campaigns($id); // array
            $campaigns_data = $this->get_list_campaigns_section($campaigns);
            $list_a .= "<tr>";
            $list_a .= "<td style='text-align: left;'>$id</td>";
            $list_a .= "<td style='text-align: left;'>$name</td>";
            $list_a .= "<td style='text-align: left;'>$total</td>";
            $list_a .= "<td>$campaigns_data</td>";
            $list_a .= "</tr>";
        }

        $list_a .= "</tbody>";
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
     * @param $link_id
     * @return stdClass
     */
    function get_link_data($link_id)
    {
        $query = "select * from aw_links where link_id=$link_id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $link = new stdClass();
            foreach ($row as $key => $value) {
                $link->$key = $value;
            } // end foreach
        } // end while
        return $link;
    }

    /**
     * @param $links_list
     * @return string
     */
    function get_links_block($links_list)
    {
        $list_a = "";
        $links_arr = explode(',', $links_list);
        foreach ($links_arr as $link_id) {
            if ($link_id > 0) {
                $link = $this->get_link_data($link_id); // object
                //$cname = $this->get_campaign_name_by_id($link->campaign_id);
                $anchor = "<span class='col-md-12'><a href='{$link->url}' target='_blank'>{$link->url}</a></span>";
                $list_a .= "<div class='row'>";
                $list_a .= "<span class='col-md-12'>$anchor</span>";
                $list_a .= "</div>";
                $list_a .= "<div class='row'>";
                $list_a .= "<span class='col-md-12'><hr/></span>";
                $list_a .= "</div>";
            } // end if $link_id>0
            else {
                $list_a .= "<div class='row'>";
                $list_a .= "<span class='col-md-12'>Any Link</span>";
                $list_a .= "</div>";
            }
        } // end foreach
        return $list_a;
    }

    /**
     * @return string
     */
    function get_lists_config_data()
    {
        $list_a = "";

        $list_a .= "<table id='settings_table' class='display' cellspacing='0' width='100%'>";

        $list_a .= "<thead>";
        $list_a .= "<tr>";
        $list_a .= "<th>Source</th>";
        $list_a .= "<th>Destination</th>";
        $list_a .= "<th>Links Number</th>";
        $list_a .= "<th>Links List</th>";
        $list_a .= "<th>Last Updated</th>";
        $list_a .= "<th>Ops</th>";
        $list_a .= "</tr>";
        $list_a .= "</thead>";

        $list_a .= "<tbody>";

        $query = "select * from aw_lists_config order by updated desc ";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $src = $this->get_list_name_by_id($row['src_list']);
                $dst = $this->get_list_name_by_id($row['dest_list']);
                $total = $row['clicks_num'];
                $updated = date('m-d-Y', $row['updated']);
                $ops = $this->get_ops_items($id);
                $links = $this->get_links_block($row['clicks_type']);
                $list_a .= "<tr>";
                $list_a .= "<td style='text-align: left;'>$src</td>";
                $list_a .= "<td style='text-align: left;'>$dst</td>";
                $list_a .= "<td style='text-align: left;'>$total</td>";
                $list_a .= "<td style='text-align: left;'>$links</td>";
                $list_a .= "<td style='text-align: left;'>$updated</td>";
                $list_a .= "<td style='padding-left:15px;'>$ops</td>";
                $list_a .= "</tr>";
            } // end while
        } // end if $num > 0

        $list_a .= "</tbody>";

        $list_a .= "</table>";

        return $list_a;
    }

    /**
     * @param $item
     */
    function add_list_config_item($item)
    {
        $now = time();
        $type = implode(',', $item->type);
        $query = "insert into aw_lists_config 
                (src_list, 
                 dest_list, clicks_num,	
                 clicks_type, 
                 updated) 
                 values ('$item->src',
                         '$item->dst', '$item->total',
                        '$type',
                        '$now');";
        $this->db->query($query);
        $this->update_subscribers_list_data($item->src, $item->dst, $item->total);
    }

    /**
     * @param $src
     * @param $dst
     */
    function update_subscribers_list_data($src, $dst, $total)
    {
        $query = "update aw_subscribers 
                  set new_list_id=$dst, 
                  total_clicks=$total, 
                  processed=0
                  where old_list_id=$src";
        $this->db->query($query);
    }

    /**
     *
     */
    function logout()
    {
        $_SESSION["aw_user"] = '';
    }

    /**
     * @param $id
     */
    function delete_config_item($id)
    {
        $query = "select * from aw_lists_config where id=$id ";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $src = $row['src_list'];
        }
        $this->erase_subscribers_list_data($src);

        $query = "delete from aw_lists_config where id=$id";
        $this->db->query($query);
    }

    /**
     * @param $src
     */
    function erase_subscribers_list_data($src)
    {
        $query = "update aw_subscribers set new_list_id=0 where old_list_id=$src";
        $this->db->query($query);
    }


    /**
     * @param $type
     * @param null $item
     * @return string
     */
    function get_lists_drop_down($type, $item = null)
    {
        $list_a = "";
        $list_a .= "<select id='list_dropdown_$type' style='width:100%;'>";
        $list_a .= "<option value='0' selected>Please select</option>";
        $query = "select * from aw_lists order by name";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if ($item == null) {
                $list_a .= "<option value='" . $row['list_id'] . "'>" . $row['name'] . "</option>";
            } // end if
            else {
                if ($row['list_id'] == $item) {
                    $list_a .= "<option value='" . $row['list_id'] . "' selected>" . $row['name'] . "</option>";
                } // end if
                else {
                    $list_a .= "<option value='" . $row['list_id'] . "'>" . $row['name'] . "</option>";
                } // end else
            } // end else
        } // end while
        $list_a .= "</select>";
        return $list_a;
    }

    /**
     * @param $total
     * @return string
     */
    function get_links_num_dropdown($total)
    {
        $list_a = "";
        $list_a .= "<select id='links_total_edit_dropdown' style='width: 100px;'>";
        $list_a .= "<option value='0' selected>Please select</option>";
        for ($i = 1; $i <= 18; $i++) {
            if ($total == $i) {
                $list_a .= "<option value='$i' selected>$i</option>";
            } // end if
            else {
                $list_a .= "<option value='$i'>$i</option>";
            } // end else
        }
        $list_a .= "</select>";
        return $list_a;
    }

    /**
     * @param bool $items
     * @return string
     */
    function get_links_list($items = false)
    {
        $list_a = "";
        $list_a .= "<select multiple id='click_types_edit_dropdown' style='width:100%'>";
        $list_a .= "<option value='0' selected>Any Link</option>";
        $query = "select * from aw_links";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $cname = $this->get_campaign_name_by_id($row['campaign_id']);
            if ($cname != '') {
                $name = "$cname - " . $row['url'] . "";
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
     * @param $id
     * @return string
     */
    function get_list_edit_dialog($id)
    {
        $list_a = "";

        $query = "select * from aw_lists_config where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $sr_dwn = $this->get_lists_drop_down('src_edit', $row['src_list']);
            $dt_dwn = $this->get_lists_drop_down('dst_edit', $row['dest_list']);
            $to_dwn = $this->get_links_num_dropdown($row['clicks_num']);
            $ln_dwn = $this->get_links_list($row['clicks_type']);
        }

        $list_a .= "<div id='myModal' class='modal fade' role='dialog'>
          <div class='modal-dialog'>
           <input type='hidden' id='config_id' value='$id'>
            <div class='modal-content'>
              <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
                <h4 class='modal-title'>Edit Subscribers Data</h4>
              </div>
              <div class='modal-body' style=''>
                
                <div class='row' style='margin-bottom:10px;padding-left: 15px;'>
                <span class='col-md-3'>Source List*</span>
                <span class='col-md-8'>$sr_dwn</span>
                </div>
                
                <div class='row' style='margin-bottom:10px;padding-left:15px;'>
                <span class='col-md-3'>Destination List*</span>
                <span class='col-md-8'>$dt_dwn</span>
                </div>
                
                <div class='row' style='margin-bottom:10px;padding-left:15px;'>
                <span class='col-md-3'>Links total*</span>
                <span class='col-md-8'>$to_dwn</span>
                </div>
                
                <div class='row' style='margin-bottom:10px;padding-left:15px;'>
                <span class='col-md-3'>Links List*</span>
                <span class='col-md-8'>$ln_dwn</span>
                </div>
                
                <div class='row'>
                <span class='col-md-6' id='subs_err' style='color: red;width: 885px;margin-left: 15px;'></span>
                </div>
                
              </div>
              <div class='modal-footer'>
                <span class='col-md-3'><button type='button' class='btn btn-primary' id='update_list_subs'>Update</button></span>
                <span class='col-md-3'><button type='button' class='btn btn-primary' id='cancel_list_edit_dialog'>Cancel</button></span>
              </div>
            </div>
          </div>
        </div>";

        return $list_a;
    }

    /**
     * @param $id
     * @return string
     */
    function get_user_edit_dialog($id)
    {
        $list_a = "";
        $query = "select * from aw_users where id=$id";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $user = new stdClass();
            foreach ($row as $key => $value) {
                $user->$key = $value;
            } // end foreach
        } // end while

        $list_a .= "<div id='myModal' class='modal fade' role='dialog'>
          <div class='modal-dialog'>
           <input type='hidden' id='userid' value='$id'>
            <div class='modal-content'>
              <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
                <h4 class='modal-title'>Edit User Data</h4>
              </div>
              <div class='modal-body' style=''>
                
                <div class='row' style='margin-bottom:10px;padding-left: 15px;'>
                <span class='col-md-3'>Firstname*</span>
                <span class='col-md-8'><input type='text' id='fname' value='{$user->fname}'></span>
                </div>
                
                <div class='row' style='margin-bottom:10px;padding-left:15px;'>
                <span class='col-md-3'>Lastname*</span>
                <span class='col-md-8'><input type='text' id='lname' value='{$user->lname}'></span>
                </div>
                
                <div class='row' style='margin-bottom:10px;padding-left:15px;'>
                <span class='col-md-3'>Email*</span>
                <span class='col-md-8'><input type='text' id='username' value='{$user->username}'></span>
                </div>
                
                <div class='row' style='margin-bottom:10px;padding-left:15px;'>
                <span class='col-md-3'>Passsword*</span>
                <span class='col-md-8'><input type='text' id='password' value='{$user->password}'></span>
                </div>
                
                <div class='row'>
                <span class='col-md-6' id='user_err' style='color: red;width: 885px;margin-left: 15px;'></span>
                </div>
                
              </div>
              <div class='modal-footer' style='text-align: center;'>
                <span class='col-md-6' style='text-align: right;'><button type='button' class='btn btn-primary' id='update_user_data'>Update</button></span>
                <span class='col-md-6' style='text-align: left;'><button type='button' class='btn btn-primary' id='cancel_list_edit_dialog'>Cancel</button></span>
              </div>
            </div>
          </div>
        </div>";

        return $list_a;
    }

    /**
     * @param $item
     */
    function update_subs_data($item)
    {
        $now = time();
        $links = implode(',', $item->lst);
        $query = "update aw_lists_config set
                           src_list='$item->src', 
                           dest_list='$item->dst', clicks_num='$item->to', 
                           clicks_type='$links', 
                           updated='$now' WHERE id=$item->id";
        $this->db->query($query);
        $this->update_subscribers_list_data($item->src, $item->dst, $item->to);
    }

    /**
     * @param $item
     */
    function update_user_data($item)
    {
        $now = time();
        $query = "update aw_users set 
                          fname='$item->fname', 
                          lname='$item->lname', 
                          username='$item->username', 
                          password='$item->password' 
                          where id=$item->userid";
        $this->db->query($query);
    }

    /**
     * @param $userid
     * @return mixed
     */
    function is_admin($userid)
    {
        $query = "select * from aw_users where id=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $status = $row['admin'];
        }
        return $status;
    }

    /**
     * @param $userid
     * @return string
     */
    function get_user_ops_items($userid)
    {
        $list = "";
        $status = $this->is_admin($userid);
        $list .= "<div class='row'>";
        $list .= "<span style='cursor: pointer;padding-left: 35px;' class='col-md-1'><i id='user_edit_$userid' class='fa fa-pencil-square-o' aria-hidden='true'></i></span>";
        if ($status == 0) {
            $list .= "<span style='cursor: pointer;' class='col-md-1'><i id='user_del_$userid' class='fa fa-trash-o' aria-hidden='true'></i></span>";
        }
        $list .= "</div>";

        return $list;
    }

    /**
     * @return string
     */
    function get_users_table()
    {
        $list = "";

        $list .= "<table id='users_table' class='display' cellspacing='0' width='100%'>";

        $list .= "<thead>";
        $list .= "<tr>";
        $list .= "<th>FirstName</th>";
        $list .= "<th>LastName</th>";
        $list .= "<th>Email</th>";
        $list .= "<th>Password</th>";
        $list .= "<th>Admin User</th>";
        $list .= "<th>Suspended</th>";
        $list .= "<th>Last Activity</th>";
        $list .= "<th>Ops</th>";
        $list .= "</tr>";
        $list .= "</thead>";

        $list .= "<tbody>";

        $query = "select * from aw_users order by fname";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $userid = $row['id'];
                $ops = $this->get_user_ops_items($userid);
                $last_activity = date('m-d-Y h:i:s', $row['last_activity']);
                $fname = $row['fname'];
                $lname = $row['lname'];
                $email = $row['username'];
                $password = $row['password'];
                $admin = ($row['admin'] == 1) ? 'Yes' : 'No';
                $suspended = ($row['suspended'] == 1) ? 'Yes' : 'No';

                $list .= "<tr>";
                $list .= "<td>$fname</td>";
                $list .= "<td>$lname</td>";
                $list .= "<td>$email</td>";
                $list .= "<td>$password</td>";
                $list .= "<td>$admin</td>";
                $list .= "<td>$suspended</td>";
                $list .= "<td>$last_activity</td>";
                $list .= "<td>$ops</td>";
                $list .= "</tr>";
            }
        } // end if $num>0

        $list .= "</tbody>";

        $list .= "</table>";

        return $list;
    }

}