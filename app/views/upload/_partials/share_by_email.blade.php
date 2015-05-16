<div class="box-solid-only">
    <div id="share-step-1" class="panel-body text-center">
        <div class="panel-heading row">
            <h3>
                <span class="pull-left">SHARE WITH EMAIL</span>
                <span class="pull-right">
                    <i class="glyphicon glyphicon-envelope"></i>
                </span>
            </h3>
        </div>
        <form id="fileshare" action="{{ route('files.share') }}" method="POST" enctype="multipart/form-data" accept="*/*">
        <div class="form-group">
            <div class="list-group recipients-holder">
                <div class="input-group">
                    <input name="recipient[1]" id="recipient[1]" class="form-control input-lg" type="text" placeholder="Enter email address to send files">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-lg btn-default" onclick="addRecipient(this)">
                            <span class="glyphicon glyphicon-plus-sign"></span>
                        </button>
                    </span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <input name="sender" id="sender" class="form-control input-lg" type="text" placeholder="Enter your mail">
        </div>
        <div class="form-group">
            <textarea name="message" id="message" class="form-control input-lg" rows="4" placeholder="Write a message"></textarea>
        </div>
        </form>
    </div>
    <div id="share-step-2" class="panel-body text-center" style="display: none">
        <div class="col-xs-12">
            <div class="text-center">
                <span class="fa fa-envelope fa-4x"></span>
                <h3>FILES SHARED!</h3>
                <h5>You'll receive a confirmation email in your inbox</h5><br/>
            </div>
            <div class="col-xs-12">
                <h5>
                    Maximum upload single size files: 10 GB<br/>
                    Need more? <a href="#">Upgrade to Professional</a>
                </h5>
            </div>
        </div>
    </div>

</div>

<script type="text/x-tmpl" id="tmpl-new-recipient">
	<div class="input-group">
        <input name="recipient[{%= o.index %}]" id="recipient[{%= o.index %}]" class="form-control input-lg" type="text" placeholder="Enter email address to send files">
        <span class="input-group-btn">
            <button type="button" class="btn btn-lg btn-default" onclick="addRecipient(this)">
                <span class="glyphicon glyphicon-plus-sign"></span>
            </button>
        </span>
    </div>
</script>

</script>

<script>
    var recipientIndex = 1;
    function addRecipient(button){
        recipientIndex ++;
        var inputRecipient = tmpl("tmpl-new-recipient", {index: recipientIndex});
        $('.recipients-holder').append(inputRecipient);
        $(button).parent('span').hide();
        $(button).closest('div').removeClass('input-group');
        $(button).attr('disabled','disabled');
    }
</script>
