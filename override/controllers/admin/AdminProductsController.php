<?php
/****************************************************************
* Ajout des champs couleur, conditionnement et millésime dans la
* liste des produits sur le back-office
*
* @date      04/10/2016
* @package   Prestashop customisé pour Vinicom
* @author    Pascal Véron <pveron@cyberbrain.net
* @copyright 2016 VEZIM SARL 
****************************************************************/

/****************************************************************
NOTE : Le champ "reference" devenant ambigu suite à l'ajout des 
requetes sur les déclinaisons il faut remplacer dans le core 
/classes/AdminProductsController.php (ligne 217 environ) 

        $this->fields_list['reference'] = array(
            'title' => $this->l('Reference'),
            'align' => 'left',
        );

par

        $this->fields_list['reference'] = array(
            'title' => $this->l('Reference'),
            'filter_key' => 'a!reference',
            'align' => 'left',
        );

MERCI à http://weblogs.sqlteam.com/jeffs/archive/2007/10/11/mixing-inner-outer-joins-sql.aspx
qui nous a permis de réaliser la requête ci-dessous   
****************************************************************/
class AdminProductsController extends AdminProductsControllerCore
{
    public function __construct()
    {

        parent::__construct();
		unset($this->fields_list['name_category']);
		unset($this->fields_list['image']);
		unset($this->fields_list['price_final']);
		$this->fields_list['name']['width']=300;

        $this->fields_list['weight'] = array(
            'title' => $this->l('Poids'),
            'type' => 'value',
            'align' => 'text-right',
            'filter_key' => 'a!weight'
        );

        $this->fields_list['millesime'] = array(
            'title' => $this->l('Millésime'),
            'filter_key' => 'fvl!value',
        );
        $this->fields_list['Conditionnement'] = array(
            'title' => $this->l('U.C.'),
            'filter_key' => 'fvlCond!value',
        );
        $this->fields_list['Contenant'] = array(
            'title' => $this->l('Cont'),
            'filter_key' => 'fvlCont!value',
        );

        $this->fields_list['Couleur'] = array(
            'title' => $this->l('Coul'),
            'filter_key' => 'fvlCoul!value',
        );

        $this->fields_list['UPC'] = array(
            'title' => $this->l('Ordre'),
            'align' => 'text-center',
            'type' => 'text-left',
            'orderby' => true
        );
 
        // Ajout du champ "millésime" (id_feature #8)
        $this->_join .= ' LEFT OUTER JOIN '._DB_PREFIX_.'feature_product fp ON fp.id_product = a.id_product AND fp.id_feature=8';
        $this->_join .= ' LEFT OUTER JOIN '._DB_PREFIX_.'feature_value_lang fvl ON fvl.id_feature_value = fp.id_feature_value';

        // Ajout du champ "conditionnement" (id_feature #9)
        $this->_join .= ' LEFT OUTER JOIN '._DB_PREFIX_.'feature_product fpCond ON fpCond.id_product = a.id_product AND fpCond.id_feature=9';
        $this->_join .= ' LEFT OUTER JOIN '._DB_PREFIX_.'feature_value_lang fvlCond ON fvlCond.id_feature_value = fpCond.id_feature_value';

        // Ajout du champ "Couleur" (id_feature #11)
        $this->_join .= ' LEFT OUTER JOIN '._DB_PREFIX_.'feature_product fpCoul ON fpCoul.id_product = a.id_product AND fpCoul.id_feature=11';
        $this->_join .= ' LEFT OUTER JOIN '._DB_PREFIX_.'feature_value_lang fvlCoul ON fvlCoul.id_feature_value = fpCoul.id_feature_value';

        // Ajout du champ "contenant" (id_feature #12)
        $this->_join .= ' LEFT OUTER JOIN '._DB_PREFIX_.'feature_product fpCont ON fpCont.id_product = a.id_product AND fpCont.id_feature=12';
        $this->_join .= ' LEFT OUTER JOIN '._DB_PREFIX_.'feature_value_lang fvlCont ON fvlCont.id_feature_value = fpCont.id_feature_value';
    }
}