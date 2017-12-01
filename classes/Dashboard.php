<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/aw-cpanel/classes/Db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/aw-cpanel/classes/Aweber.php';

error_reporting(0);

class Dashboard
{


    public $db;
    public $aw;
    public $index_page;

    /**
     * Dashboard constructor.
     */
    function __construct()
    {
        $this->db = new Db();
        $this->aw = Aweber::getInstance();
        $this->index_page = 'http://mycodebusters.com/aw-cpanel/';
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
    function get_ops_items($userid)
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
                $ops = $this->get_ops_items($userid);
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

    /**
     * @return string
     */
    function get_dashboard_panel()
    {
        $list = "";
        $users = $this->get_users_table();
        $subscribers = $this->aw->get_subscribers_page();

        $list .= "<div style='margin: auto; width: 85%;text-align: center;padding-top: 25px;'>";
        $list .= "<ul class='nav nav-tabs'>
          <li class='active'><a data-toggle='tab' href='#lists'>Subscribers</a></li>
          <li><a data-toggle='tab' href='#users'>Users</a></li>
        </ul>
        
        <div class='tab-content'>
          <div id='lists' class='tab-pane fade in active'>
            <p>$subscribers</p>
          </div>
          <div id='users' class='tab-pane fade'>
            
            <div class='row' style='margin-top: 25px;margin-bottom: 10px;'>;
            <span class='col-md-2'><button id='logout' class='btn btn-primary'>Logout</button></span>
            </div>
            
            <p><span id='users_data'>$users</span></p>
          </div>
          <span id='account' class='tab-pane fade' style='text-align: left;'>
            <h3>Account</h3>
            <p><span class='col-md-2'></span></p>
          </div>
        </div>";
        $list .= "</div>";

        return $list;
    }

    /**
     * @param $username
     * @param $password
     * @return int
     */
    function authorize($username, $password)
    {
        $query = "select * from aw_users where 
                username='$username' 
                and password='$password'  
                and suspended=0 ";
        $num = $this->db->numrows($query);
        if ($num > 0) {
            $this->update_user_activity($username);
        }
        return $num;
    }

    /**
     *
     */
    function logout()
    {
        session_unset();
        session_destroy();
    }

    /**
     * @param $username
     */
    function update_user_activity($username)
    {
        $now = time();
        $query = "update aw_users set last_activity='$now' where username='$username' ";
        $this->db->query($query);
        $_SESSION["aw_user"] = $username;
    }


}