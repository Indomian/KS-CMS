{config_load file=error.conf}
<div style="border:2px solid red;background:#ffe38d;padding:10px;color:red;">{$smarty.config.$error|default:$error} <b>{$text}</b></div>
