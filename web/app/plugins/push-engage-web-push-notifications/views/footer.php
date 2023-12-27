<div class="modal fade" id="pe-troubleshoot-modal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Are you facing any issue ?</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info show" style="margin-left: unset;">
                    <i class="fa fa-info" aria-hidden="true"></i> &nbsp;&nbsp; If you are using any caching, then please clear the cache and try again.
                </div>
                <p style="font-size: 16px; color: #333333; line-height: unset; margin-top: 16px;">Please report error with screenshot to <a>care@pushengage.com</a>. Help us to remotely debug your plugin issue by clicking below button.</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-default" id="pe-send-debug-data-btn" onClick="peSendDataToServer()">Send dubugging data to server</button>
            </div>
        </div>
    </div>
</div>

<div>
    <p class="text-center">
    Copyright © 2015 Onwards <a href="https://app.pushengage.com" target="_blank">PushEngage</a> All rights reserved. |  <span id="pe-troubleshoot" data-toggle="modal" data-target="#pe-troubleshoot-modal" > <a href="#">Troubleshooting</a> </span>  | <span>Version <?php echo Pushengage::$pushengage_version  ?><span></p>
</div>
<hr>


