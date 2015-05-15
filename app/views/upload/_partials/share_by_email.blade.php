<div class="box-solid-only">
    <div class="panel-body text-center">
        <div class="panel-heading row">
            <h3>
                <span class="pull-left">SHARE WITH EMAIL</span>
                <span class="pull-right">
                    <i class="glyphicon glyphicon-envelope"></i>
                </span>
            </h3>
        </div>

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
