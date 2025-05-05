<form action="https://secure.paygate.co.za/payweb3/process.trans" method="post" id="paygate">

    <input type="hidden" name="PAY_REQUEST_ID" value="{{ $result['PAY_REQUEST_ID'] }}">

    <input type="hidden" name="REFERENCE" value="{{ $result['REFERENCE'] ?? null }}">

    <input type="hidden" name="CHECKSUM" value="{{ $result['CHECKSUM'] }}">

    <input type="submit" name="paygate_process" value="Click here if you are not redirected automatically" />

</form>



<script type="text/javascript">document.getElementById('paygate').submit();</script>