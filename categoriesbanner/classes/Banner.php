<?php

class Banner extends ObjectModel {

	public $id_banners;
	public $path_img;
	public $link;
	public $active;
	public $is_random;
	public $id_category;
	
	
	public static $definition = array(
	
		'table' => 'categories_banners',
		'primary' => 'id_categories_banners',
		'multilang' => false,
		'fields' => array(
			'id_categories_banners' => array(
				'type' => ObjectModel::TYPE_INT
			),
			'path_img' => array(
				'type' => ObjectModel::TYPE_STRING
			),
			'link' => array(
				'type' => ObjectModel::TYPE_STRING
			),
			'is_random' => array(
				'type' => ObjectModel::TYPE_BOOL
			),
			'id_category' => array(
				'type' => ObjectModel::TYPE_STRING
			),
			'active' => array(
				'type' => ObjectModel::TYPE_BOOL
			)
		)
	);
	

	/*public function remove(){
	
		parent::remove();
	}*/

}