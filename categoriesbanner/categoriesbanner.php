<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_VERSION_'))
	exit;
	
	
require_once (dirname(__FILE__) . '/classes/Banner.php');
class categoriesbanner extends Module
{

	public function __construct()
	{
		$this->name = 'categoriesbanner';
		$this->tab = 'front_office_features';
		$this->version = '0.0.1';
		$this->author = 'Vince4digitalife';
		$this->need_instance = 0;
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Ads Categories Banners');
		$this->description = $this->l('Display ads banner on categories pages');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}
	public function install()
	{
		
		$hookExist = Db::getInstance()->ExecuteS('SELECT name FROM ps_hook WHERE name="displayCategoryTop" ');
		if(empty($hookExist)){
			$sql = "INSERT INTO `ps_hook` (`id_hook`, `name`, `title`, `description`, `position`, `live_edit`) VALUES (NULL, 'displayCategoryTop', 'Category page top', 'This hook displays content above the category page', 1, 1);";
			Db::getInstance()->Execute($sql);
		}
		
		
		$sql = '
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'categories_banners` (
		  `id_categories_banners` int(11) NOT NULL AUTO_INCREMENT,
		  `path_img` varchar(255) NOT NULL,
		  `link` varchar(255) NOT NULL,
		  `active` tinyint(1) NOT NULL,
		  PRIMARY KEY (`id_categories_banners`),
		  KEY `id_banners` (`id_categories_banners`)
		) ENGINE=InnoDB;
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ps_categories_banners_shop` (
		  `id_categories_banners` int(11) NOT NULL,
		  `id_shop` int(11) NOT NULL
		) ENGINE=InnoDB;';
		
		Db::getInstance()->Execute($sql);
		
		$this->CreateAdsBannersTabs();
		
		if (!parent::install()
			|| !$this->registerHook('displayCategoryTop')
		)
			return false;
		return true;
	}
	
	public function hookDisplayCategoryTop($p){
	
		// Check we have some image for this category
		$ids  = Db::getInstance()->ExecuteS('SELECT id_categories_banners,link,id_category FROM  `'._DB_PREFIX_.'categories_banners` WHERE active=1 AND id_category LIKE "%'.$p['id_category'].'%" ');
	
	
		// Check de la req :
		$error = true;
		if( isset( $ids[0]['id_category'] ) ){
			$id_cat = explode(',' ,$ids[0]['id_category'] );
			foreach ( $id_cat as $id ) {
			//	d($id);
				if($id == $p['id_category']){
					$error = false;
				}
		
			}
		}
		// No images  load randomly	
		if( empty($ids) OR $error) {
			$ids  = Db::getInstance()->ExecuteS('SELECT id_categories_banners,link,id_category FROM  `'._DB_PREFIX_.'categories_banners` WHERE active=1 AND id_category=""');
				if( empty($ids) ) {
					return;
				}
		}
			
		$rand = array_rand($ids);
		$id = $ids[$rand];
		
		
			
		$this->smarty->assign( array(
				'path' => '/modules/categoriesbanner/images/ban_'.$id['id_categories_banners'].'.jpg',
				'link' => $id['link']
			)
		);
		
		return $this->display(__FILE__, 'views/templates/front/ads_category.tpl');
	}
	
	public function uninstall()
	{
		$idtabs = array();
		$idtabs[] = Tab::getIdFromClassName("AdminAdsBanner");
		foreach ($idtabs as $tabid):
			if ($tabid) {
				$tab = new Tab($tabid);
				$tab->delete();
			}
        endforeach;
		return parent::uninstall();
	}
	
	
	function getAllBanner(){
		return DB::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'categories_banners');
	}
	
	 private function CreateAdsBannersTabs() {
        $langs = Language::getLanguages();
        $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $smarttab = new Tab();
        $smarttab->class_name = "AdminBanner";
        $smarttab->module = "categoriesbanner";
        $smarttab->id_parent = 0;
        foreach ($langs as $l) {
            $smarttab->name[$l['id_lang']] = $this->l('Ads Banner');
        }
        $smarttab->save();
        $tab_id = $smarttab->id;
        @copy(dirname(__FILE__) . "/AdminAdsBanner.gif", _PS_ROOT_DIR_ . "/img/t/AdminAdsBanner.gif");
      	/*  $tabvalue = array(
		array(
				'class_name' => 'AdminAdsBanner',
				'id_parent' => 15,
				'module' => 'categoriesbanner',
				'name' => 'Categories Ads Banner',
			)
		);
        foreach ($tabvalue as $tab) {
            $newtab = new Tab();
            $newtab->class_name = $tab['class_name'];
            $newtab->id_parent = $tab_id;
            $newtab->module = $tab['module'];
            foreach ($langs as $l) {
                $newtab->name[$l['id_lang']] = $this->l($tab['name']);
            }
            $newtab->save();
        }*/
        return true;
    }

	
	
}