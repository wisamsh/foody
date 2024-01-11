<?php
error_reporting(0);
class CheckRedirectionPage
{
    private  $rtn = [];
    protected function RedirectionCheck($to = null)
    {
//redirect_code
        $redirect_looper = get_field("redirect_looper", "option");
        foreach ($redirect_looper as $k => $v) {
            if ($redirect_looper[$k]['chosen_redirect'] == get_the_ID()) {
                $this->rtn['redirection_url'] = $redirect_looper[$k]['redirection_target'];
                $this->rtn['code'] = $redirect_looper[$k]['redirect_code'];
            
            }
        }


        return $this->rtn;
    }
    public function GetRedirectionPages()
    {

        if (!empty($this->RedirectionCheck())) {
            $rtn  = $this->RedirectionCheck();
            
            header("Location: {$rtn['redirection_url']}" , true , $rtn['code']);
            exit();
        } 
    }
}
