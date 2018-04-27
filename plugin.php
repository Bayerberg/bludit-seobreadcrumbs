<?php

class pluginSEOBreadcrumbs extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'label'=>'You are here:',
      'enableHome'=>false,
		);
	}

	public function form()
	{
		global $Language;

    $html = '<style>
      #jsformplugin div {margin:0!important;display:block!important;}
      #pluginSEOBreadcrumbs {background:#fff;padding:2rem; margin:0rem; box-sizing: border-box; overflow:hidden; max-width:960px; display:block; width: auto; font-size:14px;}
      #pluginSEOBreadcrumbs p {margin:0; padding:0; line-height135%;}
      .plugin-pod-header {background:#cfd7df; color:#444; padding:2rem 1rem; margin:1rem 1rem 0rem 1rem;box-sizing: border-box;display:block; width: auto}
      .plugin-pod {border:1px solid #eff3f4; background:#fafbfd; color:#323c46; padding:2rem; margin:0 1rem 1rem 1rem;box-sizing: border-box;display:block; width: auto}
      #pluginSEOBreadcrumbs h1, #pluginSEOBreadcrumbs h2, #pluginSEOBreadcrumbs h3, #pluginSEOBreadcrumbs h4, #pluginSEOBreadcrumbs h5, #pluginSEOBreadcrumbs h6 {line-height:125%;  padding:0;}
      .plugin-pod-header h1 {color:#222;font-size:24px;margin:0; font-weight:bold}
      .plugin-pod-header h2 {color:#222;font-size:22px;margin:0; font-weight:bold}
      .plugin-pod-header h3 {color:#222;font-size:20px;margin:0; font-weight:normal}
      #pluginSEOBreadcrumbs label {font-weight:bold}
      </style>';

    $html .= '<div id="pluginSEOBreadcrumbs">';

    $html .= '<div class="plugin-pod-header">';
      $html .= '<h1>SEO Breadcrumbs</h1>';
      $html .= '<h3>SEO ready breadcrumbs. For all your UX/ SEO needs.</h3>';
    $html .= '</div>';

		$html .= '<div class="plugin-pod">';
    $html .= '<p>Microdata format: <a href="http://schema.org/BreadcrumbList" target="_blank">schema.org/BreadcrumbList</a></p>';
    $html .= '<p>Breadcrumb trail will be displayed on pages by default. </p><hr/>';
		$html .= '<p><label>'.$Language->get('Label').' </label> <input id="jslabel" name="label" type="text" value="'.$this->getValue('label').'"></p>';
    $html .= '<p>Hint: you can leave the label blank to save space.</p><hr/>';
    $html .= '<label>Enable breadcrumb trail on home page </label> ';
    $html .= '<select name="enableHome">';
    $html .= '<option value="true" '.($this->getValue('enableHome')===true?'selected':'').'>'.$Language->get('enabled').'</option>';
    $html .= '<option value="false" '.($this->getValue('enableHome')===false?'selected':'').'>'.$Language->get('disabled').'</option>';
    $html .= '</select>';
    $html .= '<p>Hint: test if your theme supports breadcrumbs on home page first. If not move <i>	&lt;?php Theme::plugins(&#8216;pageBegin&#8216;); ?&gt; </i> outside and above the page loop.</p><hr/>';
		$html .= '</div>';
    $html .= '</div>';
		return $html;
	}

	public function pageBegin()
	{
    global $L;
    global $Url;
    global $Site;
    global $Page;
    global $dbTags;
    global $Users;
    if ($Url->whereAmI()=='page') {
    $seoposition=1;
    $html  = '<style>.seo-breadcrumbs {margin:0; padding:0.4rem 0;} .seo-breadcrumbs li {display:inline;padding:0.2rem 0.4rem 0.2rem 0; font-size:14px; list-style:none; } .seo-breadcrumbs li a {} .seo-breadcrumbs li+li:before {padding: 2px; color: black; content: "/\00a0";}</style>';
    $html .= '<ol itemscope itemtype="http://schema.org/BreadcrumbList" class="seo-breadcrumbs">';
		$html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
    if ($this->getValue('label')) {
      $html .= $this->getValue('label');
    }
    $html .= ' <a href="'.$Site->url().'" itemprop="item" ><span itemprop="name">'.$Site->title().'</span></a><meta itemprop="position" content="'.$seoposition.'" /></li>';
    $seoposition++;
    if ($Page->isChild()) {
		    $html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="'.$Page->parentMethod('permalink').'" itemprop="item" ><span itemprop="name">'.$Page->parentMethod('title').'</span></a><meta itemprop="position" content="'.$seoposition.'" /></li>';
    }
    $html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name"><a href="'.HTML_PATH_ADMIN_ROOT.'edit-content/';
      if ($Page->isChild()) {
        $seoposition++;
        $html .=  $Page->parentMethod('slug').'/';
      }
    $html .=  $Page->slug().'" itemprop="item">'.$Page->title().'</a></span><meta itemprop="position" content="'.$seoposition.'" /></li>';
 		$html .= '</ol>';
		return $html;
    }
    if ($this->getDbField('enableHome') && ($Url->whereAmI()=='home')) {
      $seoposition=1;
      $html  = '<style>.seo-breadcrumbs {margin:0; padding:0.4rem 0;} .seo-breadcrumbs li {display:inline;padding:0.2rem 0.4rem 0.2rem 0; font-size:14px; list-style:none; } .seo-breadcrumbs li a {} .seo-breadcrumbs li+li:before {padding: 2px; color: black; content: "/\00a0";}</style>';
      $html .= '<ol itemscope itemtype="http://schema.org/BreadcrumbList" class="seo-breadcrumbs">';
      $html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
      if ($this->getValue('label')) {
        $html .= $this->getValue('label');
      }
      $html .= ' <a href="'.$Site->url().'" itemprop="item" ><span itemprop="name">'.$Site->title().'</span></a><meta itemprop="position" content="'.$seoposition.'" /></li>';
      $html .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
      $html .= 'Page '.Paginator::currentPage().' of '.Paginator::amountOfPages();
      $html .= '</li>';
      $html .= '</ol>';
      return $html;
    }
	}
}
