if(!window.indexPage)
{
	window.indexPage={
		"toFavorites":function(url,title)
		{
			if (window.sidebar) window.sidebar.addPanel(title, url,"");
			else if( window.opera && window.print )
			{
				var mbm = document.createElement('a');
				mbm.setAttribute('rel','sidebar');
				mbm.setAttribute('href',url);
				mbm.setAttribute('title',title);
				mbm.click();
			}
			else if( document.all ) window.external.AddFavorite( url, title);
		},
		'makeHomepage':function(url)
		{
			if (document.all)
			{
			   url.style.behavior='url(#default#homepage)';
			   url.setHomePage(url.href);
			}
			else
			if(!document.layers)
			{
			      netscape.security.PrivilegeManager.enablePrivilege("UniversalPreferencesWrite");
			      navigator.preference("browser.startup.homepage", url.href); 
			}
		}
	};
}