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

        $this->_join .= 'LEFT OUTER JOIN ('._DB_PREFIX_.'product_attribute vzmpa ';

        // Ajout du champ "couleur" (déclinaison #3)
        $this->_join .= ' INNER JOIN '._DB_PREFIX_.'product_attribute_combination vzmpac ON vzmpa.id_product_attribute = vzmpac.id_product_attribute';
        $this->_join .= ' INNER JOIN '._DB_PREFIX_.'attribute_lang vzmal ON vzmpac.id_attribute = vzmal.id_attribute';
        $this->_join .= ' INNER JOIN '._DB_PREFIX_.'attribute vzmag ON vzmal.id_attribute = vzmag.id_attribute AND vzmag.id_attribute_group=3';

        $this->fields_list['couleur'] = array(
            'title' => $this->l('Couleur'),
            'filter_key' => 'vzmal!name',
        );

        // Ajout du champ "conditionnement" (déclinaison #1)
        $this->_join .= ' INNER JOIN '._DB_PREFIX_.'product_attribute_combination vzpac ON vzpac.id_product_attribute = vzmpa.id_product_attribute';
        $this->_join .= ' INNER JOIN '._DB_PREFIX_.'attribute_lang vzal ON vzpac.id_attribute = vzal.id_attribute';
        $this->_join .= ' INNER JOIN '._DB_PREFIX_.'attribute vzag ON vzal.id_attribute = vzag.id_attribute AND vzag.id_attribute_group=1';

        $this->fields_list['conditionnement'] = array(
            'title' => $this->l('Conditionnement'),
            'filter_key' => 'vzal!name',
        );

        $this->_join .= ') ON a.id_product=vzmpa.id_product ';    
        
        // Ajout du champ "millésime" (id_feature #8)
        $this->_join .= ' LEFT OUTER JOIN '._DB_PREFIX_.'feature_product fp ON fp.id_product = a.id_product AND fp.id_feature=8';
        $this->_join .= ' LEFT OUTER JOIN '._DB_PREFIX_.'feature_value_lang fvl ON fvl.id_feature_value = fp.id_feature_value';

        $this->fields_list['millesime'] = array(
            'title' => $this->l('Millésime'),
            'filter_key' => 'fvl!value',
        );

    }
}