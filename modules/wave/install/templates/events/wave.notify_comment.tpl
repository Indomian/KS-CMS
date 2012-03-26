Приветствуем Вас, {$data.author.name|default:$data.author.title}!

{$data.date_add|date_format:"%d.%m.%Y %H:%M"} пользователем {if $data.user.is_author==1}{$data.user.nickname}{else}{if $data.user.name!='' || $data.user.last_name!=''}{$data.user.name} {$data.user.last_name}{else}{$data.user.title}{/if}{/if} был добавлен новый комментарий {if $data.is_profile}к Вашему профилю{else}к Вашему рингтону{/if}.
-------------

{$data.post}

-------------
Для просмотра комментария, пожалуйста, перейдите по ссылке {$SITE.home_url}{$data.url}.

--
С уважением,
Команда Рингоман.ру

— следуйте за нами на Твиттере http://twitter.com/#!/ringoman_ru
— вступайте в нашу группу Вконтакте http://vk.com/ringoman
— общайтесь в сообществе на Фейсбуке http://www.facebook.com/pages/RinGOman/133152933425600
— пишите нам Ваши предложения на Реформале http://ringoman.reformal.ru и на почту info@ringoman.ru — будем рады общению!
