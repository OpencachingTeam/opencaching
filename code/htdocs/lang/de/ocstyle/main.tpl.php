<?php
/***************************************************************************
											./lang/de/ocstyle/main.tpl.php
															-------------------
		begin                : Mon June 14 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 german main template

	 template replacement(s):

	   title          HTML page title
	   lang           language
	   style          style
	   htmlheaders    additional HTML headers
	   loginbox       login status (login form or username)
	   functionsbox   available function on this site
	   template       template to display
	   runtime        computing time

 ****************************************************************************/

	//Menü laden
	global $mnu_bgcolor, $mnu_selmenuitem, $develwarning, $tpl_subtitle, $opt, $rootpath, $oc_nodeid;

	require_once($rootpath . 'lib2/smarty/ocplugins/function.season.php');
	require_once($rootpath . '/lib/menu2.class.php');

	$menu2 = new Menu2();
	$sUserCountry = getUserCountry();
	$menuitem = $menu2->setSelectedItem($tplname);
	$menucolor = $menu2->getMenuColor();

	if (isset($menucolor))
	{
		$mnu_bgcolor = $menucolor;
	}
	else
	{
		$mnu_bgcolor = '#D5D9FF';
	}

	if ($tplname != 'start')
		$tpl_subtitle .= htmlspecialchars($menu2->getMenuTitle() . ' - ', ENT_COMPAT, 'UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo $tpl_subtitle; ?>{title}</title>
		<meta name="KEYWORDS" content="geocaching, opencaching, geocashing, longitude, latitude, utm, coordinates, treasure hunting, treasure, GPS, global positioning system, garmin, magellan, mapping, geo, hiking, outdoors, sport, hunt, stash, cache, geocaching, geocache, cache, treasure, hunting, satellite, navigation, tracking, bugs, travel bugs" />
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Language" content="{lang}" />
		<meta http-equiv="gallerimg" content="no" />
		<meta http-equiv="cache-control" content="no-cache" />
		<link rel="SHORTCUT ICON" href="favicon.ico">
		<link rel="stylesheet" type="text/css" media="screen,projection" href="resource2/{style}/css/style_screen.css">
      <script type="text/javascript">
				<!--
<?php
					$seasons = array('winter' => 'resource2/ocstyle/css/seasons/style_winter.css',
					                 'spring' => 'resource2/ocstyle/css/seasons/style_spring.css',
					                 'summer' => 'resource2/ocstyle/css/seasons/style_summer.css',
					                 'autumn' => 'resource2/ocstyle/css/seasons/style_autumn.css');
					$smarty_dummy = 0;
?>
					var nWindowWidth = 9999;
					if (window.innerWidth)
						nWindowWidth = window.innerWidth;
					else if (screen.availWidth)
						nWindowWidth = screen.availWidth;
<?php if((!isset($opt['logic']['lowresfriendly']))or($opt['logic']['lowresfriendly']==false)) echo "/*"; ?>
					if (screen.availHeight < 800) {
						document.writeln('<link rel="stylesheet" type="text/css" media="screen,projection" href="resource2/{style}/css/style_netbook.css">');
					}
<?php if((!isset($opt['logic']['lowresfriendly']))or($opt['logic']['lowresfriendly']==false)) echo "*/"; ?>
<?php if(isset($opt['logic']['theme']) and $opt['logic']['theme']=='') echo "/*"; ?>
						if (nWindowWidth > 970)
						document.writeln('<link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo smarty_function_season($seasons, $smarty_dummy); ?>">');
<?php if(isset($opt['logic']['theme']) and $opt['logic']['theme']=='') echo "*/"; ?>

					function usercountry_change()
					{
						var sCurrentOption = "<?php echo $sUserCountry; ?>";
						var oUserCountryCombo = document.getElementById('usercountry');

						if (sCurrentOption!=oUserCountryCombo.value)
						{
							window.location = 'index.php?usercountry=' + oUserCountryCombo.value;
						}
					}
				//-->
      </script>
  	<link rel="stylesheet" type="text/css" media="print" href="resource2/{style}/css/style_print.css">
		<script type="text/javascript" src="resource2/{style}/js/enlargeit.js"></script>
		{htmlheaders}
	</head>

	<body>
		<div id="overall">
				<div id="langstripe">

				<!-- Navigation Level 1 -->
				<div class="nav1-container">
					<div class="nav1" style="text-align: right; margin-right: 20px;">
						{loginbox}
					</div>
				</div>

					<div class="navflag">
						<ul>
							<li><strong>{t}Language:{/t}</strong></li>
<?php
							foreach ($opt['template']['locales'] AS $k => $lang)
							{
								if ($lang['show'] == true)
									echo '<li><a style="text-decoration: none;" href="index.php?locale=' . $k . '"><img src="' . $lang['flag'] . '" alt="' . $lang['name'] . '" width="16px" height="11px" /></a></li>';
							}
?>
            	<li>&nbsp;&nbsp;&nbsp;&nbsp;<strong>{t}Country:{/t}</strong></li>
							<li>
								<select id="usercountry" onclick="javascript:usercountry_change()">
<?php
									global $tpl_usercountries;
									$nLastGroup = 0;
									for ($i = 0; $i < count($tpl_usercountries); $i++)
									{
										if ($nLastGroup != $tpl_usercountries[$i]['group'])
										{
											echo '<option disabled="disabled">';
											if ($tpl_usercountries[$i]['group'] == 1)
												echo '- ' . t('This OC node') . ' -';
											elseif ($tpl_usercountries[$i]['group'] == 2)
												echo '- ' . t('Other OC nodes') . ' -';
											elseif ($tpl_usercountries[$i]['group'] == 3)
												echo '- ' . t('Others') . ' -';
											else
												echo '-';
											echo '</option>';
										}
										$nLastGroup = $tpl_usercountries[$i]['group'];

										echo '<option value="' . htmlspecialchars($tpl_usercountries[$i]['country'], ENT_COMPAT, 'UTF-8') . '"' . (($sUserCountry==$tpl_usercountries[$i]['country']) ? ' selected="selected"' : '') . '>' . htmlspecialchars($tpl_usercountries[$i]['name'], ENT_COMPAT, 'UTF-8') . '</option>';
									}
?>
								</select>
							</li>
            </ul>
					</div>
				</div>
		  <div class="page-container-1" style="position: relative;">
				<div id="bg1">&nbsp;</div>
				<div id="bg2">&nbsp;</div>

  			<!-- HEADER -->
				<!-- OC-Logo -->
				<div class="header" style="height:81px;">
					<div style="width: 970px; padding-top: 1px;">
						<img src="resource2/{style}/images/oc_logo.png" alt="" style="margin-top: 5px; margin-left: 3px;" />
						<img src="resource2/{style}/images/head/se/rotator.php?" alt="" style="border: 0px none; float: right;" />
					</div>
				</div>

				<div class="site-name">
					<a href="index.php">
						<p class="title">{t}Opencaching.de{/t}</p>
						<p class="subtitle1">Geocaching med Opencaching</p>
						<p class="subtitle2"></p>
					</a>
				</div>

				<div id="suchbox"><form action="searchplugin.php" method="post" style="display:inline;">
					<b>{t}Waypoint-Search{/t}:</b><br />
					<input type="hidden" name="source" value="waypoint-suche" />
					<input type="text" name="userinput" size="10" /><input type="submit" style="width:70px" value="{t}Go{/t}" />
				</form></div>

				<!-- Site slogan -->
				<div class="site-slogan-container">
					{sponsortopright}
				</div>	
		
				<?php echo isset($develwarning) ? $develwarning : '' ?>

				<!-- Navigation Level 2 -->
				<div class="nav2">
					<ul>
<?php 
					echo $menu2->getTopMenuHtml();
?>
<?php
if ($oc_nodeid==7)
echo '<!--'
?>
<li><a href="http://blog.geocaching.de/" target="_blank">Teamblog</a></li>
<li><a href="http://www.geoclub.de/viewforum.php?f=52" target="_blank">Forum</a></li>
<li><a href="http://webchat.freenode.net/?nick=Gast&channels=opencaching.de&prompt=1" target="_blank">Chat/IRC</a></li>
<li><a href="http://geokrety.org/index.php?lang=de_DE.UTF-8" target="_blank">Geokrety</a></li>
<?php
if ($oc_nodeid==7)
echo '-->'
?>
					</ul>
				</div>
				<!-- Buffer after header -->
				<div class="buffer" style="height: 30px;"></div>

				<!-- NAVIGATION -->
				<!-- Navigation Level 3 -->
				<div class="nav3">
<?php
					$mainmenu = $menu2->getSubMenuItems(MNU_START);
?>
						<ul>
							<li class="title">{t}Main menu{/t}</li>
<?php
							echo $menu2->getSubMenuHtml($mainmenu);
?>
						</ul>
<?php
					$my_menu = $menu2->getSubMenuItems(MNU_MYPROFILE);
					if ($my_menu)
					{
?>
						<ul>
							<li class="title">{t}My menu{/t}</li>
<?php
							echo $menu2->getSubMenuHtml($my_menu);
?>
						</ul>
<?php
					}
?>
					<!-- Länderknoten -->
					<p class="sidebar-maintitle">{t}Country sites{/t}</p>
					<div style="text-align: center;" class="nodeflags">
						<a href="http://www.opencaching.cz" target="_blank"><img src="resource2/ocstyle/images/nodes/oc-cz.png" width="100" height="22" /></a><br />
						<a href="http://www.opencaching.de" target="_blank"><img src="resource2/ocstyle/images/nodes/oc-de.png" width="100" height="22" /></a><br />
						<a href="http://www.opencachingspain.es" target="_blank"><img src="resource2/ocstyle/images/nodes/oc-es.png" width="100" height="22" /></a><br />
						<a href="http://www.opencaching.it" target="_blank"><img src="resource2/ocstyle/images/nodes/oc-it.png" width="100" height="22" /></a><br />
						<a href="http://www.opencaching.jp" target="_blank"><img src="resource2/ocstyle/images/nodes/oc-jp.png" width="100" height="22" /></a><br />
						<a href="http://www.opencaching.no" target="_blank"><img src="resource2/ocstyle/images/nodes/oc-no.png" width="100" height="22" /></a><br />
						<a href="http://www.opencaching.pl" target="_blank"><img src="resource2/ocstyle/images/nodes/oc-pl.png" width="100" height="22" /></a><br />
						<a href="http://www.opencaching.se" target="_blank"><img src="resource2/ocstyle/images/nodes/oc-se.png" width="100" height="22" /></a><br />
						<a href="http://www.opencaching.org.uk" target="_blank"><img src="resource2/ocstyle/images/nodes/oc-org-uk.png" width="100" height="22" /></a><br />
						<a href="http://www.opencaching.us" target="_blank"><img src="resource2/ocstyle/images/nodes/oc-us.png" width="100" height="22" /></a>
					</div>

					<!-- Paypalbutton -->
<?php
					if (isset($opt['page']['showdonations']) && $opt['page']['showdonations'])
					{
?>
						<p class="sidebar-maintitle">{t}Donations{/t}</p>
						<div style="margin-top:20px;width:100%;text-align:center;">
							<a href="http://www.opencaching.de/articles.php?page=donations">
								<img src="https://www.paypal.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" alt="{t}Donations{/t}" style="border:0px;" />
							</a>
						</div>
<?php
					}
?>

					<div class="sidebar-txtbox-noshade">
						<p class="content-txtbox-noshade-size5">
							<small>
								{t}Page performance{/t}: {scripttime} {t}sec{/t}<br />
								{t}Page creation{/t}: <?php $bTemplateBuild->Stop(); echo sprintf('%1.3f', $bTemplateBuild->Diff()); ?> {t}sec{/t}
							</small>
						</p>
					</div>

				</div>

  			<!-- CONTENT -->
				<div class="content2">
					<div id="breadcrumb">
<?php
						echo $menu2->getBreadCrumbHtml();
?>
					</div>
			
				<div id="ocmain">
					{template}
				</div>
			</div>
			<!-- End Text Container -->

			<!-- FOOTER -->
			<div class="footer">
				<p><a href="articles.php?page=dsb">{t}Privacy statement{/t}</a> | <a href="articles.php?page=impressum">{t}Terms of use and legal information{/t}</a> | <a href="articles.php?page=contact">{t}Contact{/t}</a> | <a href="sitemap.php">{t}Sitemap{/t}</a></p>
				<p><strong>{sponsorbottom}</strong></p>
			</div>
		</div>
	</body>
</html>
