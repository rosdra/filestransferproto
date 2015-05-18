<form id="fileshare" action="{{ route('files.share') }}" method="POST" enctype="multipart/form-data" accept="*/*">
    <div class="whitebox">
    @if ($errors->any())
            {{ implode('', $errors->all('<div>:message</div>')) }}
    @endif

        <div class="share-mail-first-step">
            <div class="inside">
                <h4>SHARE WITH EMAIL <span class="glyphicon glyphicon-envelope"></span></h4>

                <div class="form-group">
                    <div class="list-group recipients-holder" style="margin-bottom: 0;">
                        <div class="input-group">
                            <input name="recipient[1]" id="recipient[1]" class="form-control" type="text" placeholder="Enter email address to send files">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-lg btn-default" onclick="addRecipient(this)" style="padding: 7px 5px; font-size: 14px; border-radius: 0;">
                                    <span class="moreemail">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" id="from-email" name="from-email" placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <textarea class="form-control" id="message" name="message" placeholder="Write a message"></textarea>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" disabled> share in private mode
                    </label>
                </div>

            </div>

            {{--<button class="round orange" id="sharewithmail">SHARE</button>--}}
        </div>

        <div class="share-mail-second-step">
            <div class="inside">
                {{ HTML::image('filestransfer/img/icon_done_mail.png') }}
                <h2>FILES SHARED!</h2>
                You’ll receive a confirmation email in your inbox

                <p>Maximum upload single size files: 10 GB<br>
                Need more? <a href="#">Upgrade to Professional</a></p>
            </div>

            {{--<button class="round grey">Share again</button>--}}
        </div>

    </div>
</form>

<script type="text/x-tmpl" id="tmpl-new-recipient">
	<div class="input-group">
        <input name="recipient[{%= o.index %}]" id="recipient[{%= o.index %}]" class="form-control" type="text" placeholder="Enter email address to send files" style="margin-top:3px;">
        <span class="input-group-btn">
            <button type="button" class="btn btn-lg btn-default" onclick="addRecipient(this)" style="padding: 7px 5px; font-size: 14px; margin-top:3px; border-radius: 0;">
                <span class="moreemail">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
            </button>
        </span>
    </div>
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

