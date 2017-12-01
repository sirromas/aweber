<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/aw-cpanel/classes/Db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/aw-cpanel/classes/Helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/aw-cpanel/lib/aweber_api/aweber_api.php';

class Processor
{

    public $awn;
    public $db;
    public $h;
    public $account;

    const MASTER_ACCOUNT_ID = 1205454;
    const APP_ID = 'bc28494b';
    const CONSUMER_KEY = 'AkAZkZiBS0lsohkYXl2ohcQD';
    const CONSUMER_SECRET = 'gCZi8CRrJrSUEq3SzMt48IenHgrYi9mXvJ2hsaBe';

    protected  function __construct()
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
    }


    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new Processor();
        }
        return $instance;
    }




}