На сайте Рингоман пользователь {if $data.user.is_author==1}{$data.user.nickname}{else}{if $data.user.name!='' || $data.user.last_name!=''}{$data.user.name} {$data.user.last_name}{else}{$data.user.title}{/if}{/if} добавил комментарий на странице:

{$SITE.home_url}{$data.url}

такого содержания:
-------------

{$data.post}

-------------

Автоматическое уведомление о комментариях.
