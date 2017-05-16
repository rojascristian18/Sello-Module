<?php
/**
* 2017 Cristian Rojas
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
*  @author    Cristian Rojas <cristian@mediawolves.cl>
*  @copyright 2017 mediawolves.cl
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_'))
  exit;
 
class Sello extends Module
{
    public function __construct()
    {
        $this->name = 'sello';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Cristian Rojas <cristian@mediawolves.cl>';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Sellos para categorías');
        $this->description = $this->l('Agrega un sello a los productos que contenga la categoría seleccionada.');

        $this->confirmUninstall = $this->l('¿Seguro que quires desintalar?');

    }

    /**
     * Procesar el formulario del administrador y actualizar la información del módulo
     * @return view
     */
    public function getContent()
    {
        $output = null;
     
        if (Tools::isSubmit('submit'.$this->name))
        {
            
            if ($_FILES['MODULO_SELLO_IMG']['name'] != '') 
            {
    
                $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                $imagen = '';
                $salt = sha1(microtime());
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES['MODULO_SELLO_IMG']['name'], '.'), 1));
                $imagesize = @getimagesize($_FILES['MODULO_SELLO_IMG']['tmp_name']);
     
                    if ($error = ImageManager::validateUpload($_FILES['MODULO_SELLO_IMG']))
                        return $this->_html .= $this->displayError($this->l($error) );
                    elseif (!$temp_name || !move_uploaded_file($_FILES['MODULO_SELLO_IMG']['tmp_name'], $temp_name))
                        return false;
                    elseif (!ImageManager::resize($temp_name, dirname(__FILE__).'/images/'.$salt.'_'.$_FILES['MODULO_SELLO_IMG']['name'], null, null, $type))
                       return $this->_html .= $this->displayError($this->l('An error occurred during the image upload process.'));
                    if (isset($temp_name))
                        @unlink($temp_name);

                $selloImg = $salt.'_'.$_FILES['MODULO_SELLO_IMG']['name'];
            }

        	if (isset($selloImg)) 
        	{
        		Configuration::updateValue('MODULO_SELLO_IMG', $selloImg);
        	}
            
            Configuration::updateValue('MODULO_SELLO_CAT', strval(Tools::getValue('MODULO_SELLO_CAT')));
            Configuration::updateValue('MODULO_SELLO_CSS', Tools::getValue('MODULO_SELLO_CSS'));
            Configuration::updateValue('MODULO_SELLO_INICIO', Tools::getValue('MODULO_SELLO_INICIO'));
            Configuration::updateValue('MODULO_SELLO_FINAL', Tools::getValue('MODULO_SELLO_FINAL'));

            $output .= $this->displayConfirmation($this->l('Configuración guardada'));
        }

        return $output.$this->displayForm();
    }

    /**
     * Instalar las configuraciones iniciales del módulo
     * @return bool
     */
    public function initConfiguracion()
    {
    	# Iniciamos la configuración del módulo y el registro de hooks
    	if ( !Configuration::updateValue('MODULO_SELLO_IMG', '') 
    		|| !Configuration::updateValue('MODULO_SELLO_CAT', '') 
    		|| !Configuration::updateValue('MODULO_SELLO_CSS', $this->selloCSS())
    		|| !Configuration::updateValue('MODULO_SELLO_INICIO', '0000-00-00 00:00:00')
    		|| !Configuration::updateValue('MODULO_SELLO_FINAL', '0000-00-00 00:00:00')
    		|| !$this->registerHook('displayProductListFunctionalButtons')
    		) 
    	{
    		return false;
    	}

    	return true;
    }

    /**
     * Desinstalar las configuraciones del módulo
     * @return bool
     */
    public function delConfiguracion()
    {
    	if (!Configuration::deleteByName('MODULO_SELLO_IMG', '') 
    		|| !Configuration::deleteByName('MODULO_SELLO_CAT', '') 
    		|| !Configuration::deleteByName('MODULO_SELLO_CSS')
    		|| !Configuration::deleteByName('MODULO_SELLO_INICIO')
    		|| !Configuration::deleteByName('MODULO_SELLO_FINAL') )
    		return false;
    	return true;
    }

    public function install()
    {
      if (Shop::isFeatureActive())
        Shop::setContext(Shop::CONTEXT_ALL);
     
      if (!parent::install() || ! $this->initConfiguracion() )
        return false;
     
      return true;
    }

    public function uninstall()
    {   
        if (!parent::uninstall() || !$this->delConfiguracion())
            return false;
        return true;
    }

    /**
     * Crear el formulario
     * @return formulario creado
     */
    public function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
         
        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Configurar sello')
            ),
            'input' => array(
                array(
                    'type' => 'file',
                    'label' => $this->l('Sello'),
                    'name' => 'MODULO_SELLO_IMG',
                    'display_image' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('ID Categoría'),
                    'name' => 'MODULO_SELLO_CAT'
                ),
                array(
                    'type' => 'datetime',
                    'label' => $this->l('Fecha inicio sello'),
                    'name' => 'MODULO_SELLO_INICIO'
                ),
                array(
                    'type' => 'datetime',
                    'label' => $this->l('Fecha final sello'),
                    'name' => 'MODULO_SELLO_FINAL'
                ),
                array(
                	'type' => 'textarea',
                	'label' => 'Estilo CSS del sello',
                	'name' => 'MODULO_SELLO_CSS'
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

         
        $helper = new HelperForm();
         
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        
        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
         
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
        $helper->tpl_vars = array(
        	'url_image' => $this->_path . 'images/' . Configuration::get('MODULO_SELLO_IMG')
        	);

        // Load current value
        $helper->fields_value['MODULO_SELLO_IMG'] = Configuration::get('MODULO_SELLO_IMG');
        $helper->fields_value['MODULO_SELLO_CAT'] = Configuration::get('MODULO_SELLO_CAT');
        $helper->fields_value['MODULO_SELLO_CSS'] = Configuration::get('MODULO_SELLO_CSS');
        $helper->fields_value['MODULO_SELLO_INICIO'] = Configuration::get('MODULO_SELLO_INICIO');
        $helper->fields_value['MODULO_SELLO_FINAL'] = Configuration::get('MODULO_SELLO_FINAL');
        
       	return $helper->generateForm($fields_form);
    }


    /**
     * Método encargado de procesar la información del producto y del módulo para
     * posteriormente mostrarlo en el front
     * @param  array 	$product 	Contiene la información del producto que se desea evaluar 
     * @return view
     */
    public function hookDisplayProductListFunctionalButtons($product =  array()){

    	if ( !empty($product) 
    		&& !empty(Configuration::get('MODULO_SELLO_IMG')) 
    		&& !empty(Configuration::get('MODULO_SELLO_CAT')) 
    		&& !empty(Configuration::get('MODULO_SELLO_CSS'))
    		&& !empty(Configuration::get('MODULO_SELLO_INICIO'))
    		&& !empty(Configuration::get('MODULO_SELLO_FINAL'))
    		) 
    	{
			# Todas las categorias del producto
			$category = Product::getProductCategoriesFull($product['product']['id_product']);
    		
    		# Verificamos que exista el ID registrado en el módulo en el listado de categorias del producto y que la fecha esté dentro del rango
			if (array_key_exists(Configuration::get('MODULO_SELLO_CAT'), $category) && Configuration::get('MODULO_SELLO_INICIO') <= date('Y-m-d H:i:s') && Configuration::get('MODULO_SELLO_FINAL') >= date('Y-m-d H:i:s') ) {
				# Llevamos al front la imagen
				$this->context->smarty->assign(
					array(
					'imagen_sello' => $this->_path . 'images/' . Configuration::get('MODULO_SELLO_IMG'),	 
					'categoria_sello' => Configuration::get('MODULO_SELLO_CAT'),
					'estilo_sello' => Configuration::get('MODULO_SELLO_CSS'),
					)
				);

				return $this->display(__FILE__ , 'sello.tpl');
			}
    	}
    }
    

    /**
     * Estilo default para el sello
     * @return  	string 		 
     */			
    public function selloCSS() 
    {
    	$style = '.sello-module {
			position: absolute;
			top: 0%;
			width: 20%;
			height: auto;
			right: 0;
		}

		.sello-module .img-sello-module {
			width: 80%;
			display: block;
			margin: 0 auto;
		}';

		return $style;
    }
}

