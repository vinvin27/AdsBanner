<?php

require_once (dirname(__FILE__) . '/../../classes/Banner.php');

class AdminBannerController extends AdminController {


  public function __construct() {
      
      	$this->table = 'categories_banners';
        $this->className = 'Banner';
        $this->module = 'categoriesbanner';
        $this->lang = false;
        $this->image_dir = '../modules/categoriesbanner/images';
        $this->context = Context::getContext();
        $this->_defaultOrderBy = 'created';
        $this->_defaultorderWay = 'DESC';
        $this->bootstrap = true;
        $this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'icon' => 'icon-trash',
				'confirm' => $this->l('Delete selected items?')
			)
		);
        if (Shop::isFeatureActive())
            Shop::addTableAssociation($this->table, array('type' => 'shop'));
        parent::__construct();
        $this->fields_list = array(
            /*'id_categories_banners' => array(
                'title' => $this->l('Id'),
                'width' => 50,
                'type' => 'text',
                'orderby' => false,
                'filter' => false,
                'search' => false
            ),*/
             'path_img' => array(
                'title' => $this->l('Image'),
                'width' => 100,
                'type' => 'text',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback' => 'DisplayImage'
            ),
            'link' => array(
                'title' => $this->l('Link'),
                'width' => 50,
                'type' => 'text',
                'orderby' => false,
                'filter' => false,
                'search' => false
            ),
            'id_category' => array(
                'title' => $this->l('Category'),
                'width' => 50,
                'type' => 'text',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback' => 'DisplayCategorieById'
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'width' => '70',
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter' => false,
                'search' => false
            )
        );
        $this->_defaultOrderBy = 'a.id_categories_banners';
        $this->_defaultOrderWay = 'DESC';
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->_group = 'GROUP BY a.id_categories_banners';
        }
        parent::__construct();
    }
    public function renderList() {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

	public function initToolbar() {
        parent::initToolbar();
    }
    
     public function DisplayImage($path_img){
    
    	return '<img src="'.$path_img.'" width="80px" />';
    	
    
	}
	
	
	
    public function DisplayCategorieById($cats){
		if(empty($cats)){ return '-'; };
    	$ids =  explode(',',$cats);
    	$return = '';
    	foreach( $ids as $id ){
    	
			$id_lang = (int) Context::getContext()->language->id;
			$cat = new Category($id,$id_lang);
			$return .= $cat->name . ', ';    	
    	}
    	return $return;
    	
    	
    
	}
    
    public function renderForm() {
    
    	$id_lang = (int) Context::getContext()->language->id;
    	$categories = Category::getSimpleCategories($id_lang);
    	
    	
    	//array_unshift($categories,array( 'id_category' => 0 , 'name' => '--' ));
    	
    	
    	$selected_category =  array(
							'id' => '01',
							'use_checkbox' => true
    					);
    	if( Tools::getIsset('id_categories_banners') ) {
    		
    		$banner = new Banner(Tools::getValue('id_categories_banners'));
    		//d($banner);
    		$cats = explode(',',$banner->id_category);
    		
    		$selected_category['selected_categories'] = ($cats);
    		//($selected_category);
    	}
    	
    	$category_field =  array(
                    'type' => 'categories',
                    'label' => $this->l('Associated to Categorie : '),
                    'name' => 'id_category',
                    'tree' => $selected_category,
                    'options' => array(
                    	'query' => $categories,
                    	'id' => 'id_category',
                    	'name' => 'name'
                    )
        );
    	
    	
    	
    	$img_desc = $this->l('Upload an Ads from your computer.N.B : Only jpg image is allowed');
        
        if (Tools::getvalue('id_categories_banners') != '' && Tools::getvalue('id_categories_banners') != NULL) {
            $img_desc .= '<br/><img style="height:auto;width:300px;clear:both;border:1px solid black;" alt="" src="/modules/categoriesbanner/images/ban_' . Tools::getvalue('id_categories_banners') . '.jpg" /><br />';
        }
        
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Add a banner'),
            ),
            'input' => array(
            	array(
                    'type' => 'radio',
                    'label' => $this->l('Is random ads ?'),
                    'name' => 'is_random',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
                $category_field
                ,
                array(
                    'type' => 'text',
                    'label' => $this->l('Link : '),
                    'name' => 'link',
                    'size' => 60,
                    'required' => true,
                    'desc' => $this->l('Enter a link for your ads')
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Banner image : '),
                    'name' => 'path_img',
                    'display_image' => true,
                    'desc' => $img_desc
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association:'),
                'name' => 'checkBoxShopAsso',
            );
        }
        if (!($BlogCategory = $this->loadObject(true)))
            return;
        $this->fields_form['submit'] = array(
            'title' => $this->l('Save   '),
            'class' => 'button'
        );
        return parent::renderForm();
    }
    
    
    
 	public function postProcess() {
 	
 		//DELETE
        if (Tools::isSubmit('deletecategories_banners') && Tools::getValue('id_categories_banners') != '') {
            $id_lang = (int) Context::getContext()->language->id;
                $banner = new Banner((int) Tools::getValue('id_categories_banners'));
                if (!$banner->delete()) {
                    $this->errors[] = Tools::displayError('An error occurred while deleting the object.')
                            . ' <b>' . $this->table . ' (' . Db::getInstance()->getMsgError() . ')</b>';
                } else {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminBanner'));
                }
        } elseif (Tools::isSubmit('submitAddcategories_banners')) {
        
        
            parent::validateRules();
            if (count($this->errors))
                return false;
                
            // ADD WAY
            if (!$id_smart_blog_category = (int) Tools::getValue('id_categories_banners')) {
                $banner = new Banner();
                
				$banner->link = Tools::getValue('link');
				$banner->active = Tools::getValue('active');
				
                $banner->id_category = implode(',',Tools::getValue('id_category'));
                //d($banner);
                $banner->is_random = Tools::getValue('is_random');
                
                if (!$banner->save())
                    $this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current object');
                else {
                    $this->processImageCategory($_FILES, $banner->id);
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminBanner'));
                }
                
            // UPDATE WAY
            } elseif ($id_smart_blog_category = Tools::getValue('id_categories_banners')) {
                $banner = new Banner($id_smart_blog_category);
                
				$banner->link = Tools::getValue('link');
				$banner->active = Tools::getValue('active');
				
				$banner->id_category = implode(',',Tools::getValue('id_category'));
				$banner->is_random = Tools::getValue('is_random');
				
				
                if (!$banner->save())
                    $this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current object');
                else {
                    $this->processImageCategory($_FILES, $banner->id);
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminBanner'));
                }
                
            }
            
            
       }elseif (Tools::isSubmit('statuscategories_banners') && Tools::getValue($this->identifier)) {
            if ($this->tabAccess['edit'] === '1') {
                if (Validate::isLoadedObject($object = $this->loadObject())) {
                    if ($object->toggleStatus()) {
                        $identifier = ((int) $object->id_parent ? '&id_categories_banners=' . (int) $object->id_parent : '');
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminBanner'));
                    } else
                        $this->errors[] = Tools::displayError('An error occurred while updating the status.');
                } else
                    $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.')
                            . ' <b>' . $this->table . '</b> ' . Tools::displayError('(cannot load object)');
            } else
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        }
        elseif( Tools::getIsset('submitBulkdeletecategories_banners') && (Tools::getValue('categories_bannersBox')) ){
        
        	$ids_banner_deleted = Tools::getValue('categories_bannersBox');
        	
        	// remove each banner
        	foreach( $ids_banner_deleted as $id ){
        	
        		$b = new Banner($id);
        		$b->delete();
        		unset($b);
        		
        	}
        
        }
        
        
        
       
	}


	public function processImageCategory($FILES, $id) {
        if (isset($FILES['path_img']) && isset($FILES['path_img']['tmp_name']) && !empty($FILES['path_img']['tmp_name'])) {
            if ($error = ImageManager::validateUpload($FILES['path_img'], 4000000))
                return Tools::displayError($this->l('Invalid image'));
            else {
                $ext = substr($FILES['path_img']['name'], strrpos($FILES['path_img']['name'], '.') + 1);
                $file_name = 'ban_'.$id . '.' . $ext;
                $path = _PS_MODULE_DIR_ . 'categoriesbanner/images/' . $file_name;
                
                // if file exist (update case)
                if (file_exists($path)){
                	unlink($path);
                }
                
                $banner = new Banner($id);
				$banner->path_img = '/modules/categoriesbanner/images/' . $file_name;
				$banner->save();
				if (!move_uploaded_file($FILES['path_img']['tmp_name'], $path))
                    return Tools::displayError($this->l('An error occurred while attempting to upload the file.'));
                
            }
        }
    }
}