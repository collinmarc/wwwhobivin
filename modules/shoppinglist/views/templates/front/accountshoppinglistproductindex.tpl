 
{**
* AccountShoppingListProductIndex Template
* 
* @author Olivier Michaud
* @copyright  Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<!-- Module/ShoppingList/view/accountshoppinglistproductindex.tpl -->
<!-- Detail d'une shoppingList -->
{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='Mon Compte'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='Ma commande pré-établie' mod='shoppinglist'}</span>{/capture}

<!--<h1 class="page-heading">{l s='Ma commande pré-établie' mod='shoppinglist'}</h1>-->


{if $shoppingListProducts}

<!--
<div id="spinner" style="z-index:999; position:absolute; top:0; bottom:0; left:0; right:0; margin:auto; display:none; width:400px;  height:130px; background-color:#A71E4C; color:#FFF; text-align: center; padding-top:30px" ><h2><i class="icon-spinner icon-spin icon-large"></i> Ajout au panier</h2></div>
-->

    <script type="text/javascript" src="//cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css">
	
<Form id="ShoppingList" method="post" action="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'addAllToCart', 'id_shopping_list' => $shoppingListObj->id_shopping_list])}"> 

    <!--<table id="shopping-list" class="std table table-bordered footab footable-loaded footable tablet breakpoint">-->
    <table id="shopping-list" class="table tableDnD">
        <thead>
            <tr>
                <th>{l s="Référence"}</th>
                <th>{l s= "Désignation"}</th>
                <th>{l s= "Couleur"}</th>
                <th>{l s= "Contenant"}</th>
                <th>{l s= "Millésime"}</th>
                <th>{l s= "U.C."}</th>
                <th>{l s= "cmd"}</th>
                <th align="center">{l s='Quantité'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$shoppingListProducts item=itemList}
                <tr>
                    <!--<td>{$itemList.id_product}</td>-->
                    <!--<td>{$itemList.id_product_attribute}</td>-->
                    <td>{$itemList.reference}</td>
                    <td >{$itemList.designation}</td>
                    <td>{$itemList.couleur}</td>
                    <td>{$itemList.contenant}</td>
                    <td>{$itemList.millesime}</td>
                    <td>{$itemList.conditionnement}</td>
                    <td>{$itemList.quantity}</td>
                    <td align="center">
                        
                        
                        <input type="text" name="qty_{$itemList.id_product}" id="qty_{$itemList.id_product}" value="" style="width:50px;text-align:center">

                        
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <p id="no-product">{l s='Pas de produit référencé' }</p>
{/if}

<ul class="action">
    {if $shoppingListProducts}
        <li>
			<!-- Appel de la méthode ShoppingListAccountShoppingListProductModuleFrontController.accountshoppinglistproduct -->
<!--            <a class="add-all btn btn-default button button-medium" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'addAllToCart', 'id_shopping_list' => $shoppingListObj->id_shopping_list])}" onclick="$(this).closest('form').submit()">
-->
            <a class="add-all btn btn-default button button-medium" onclick="$(this).closest('form').submit()">
                <span>
                    <img class="icon" src="{$base_dir}modules/shoppinglist/img/add-product.png" alt="{l s='Préparer la commande' mod='shoppinglist'}">{l s='Préparer la commande'}<i class="icon-shopping-cart right"></i>
                </span>
            </a>
        </li>
    {/if}
    <li>
       <a class="back-shopping-list btn btn-default button button-medium exclusive" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
            <span>
                {l s='Retour à mon compte'}<i class="icon-chevron-left right"></i>
            </span>
        </a>
     </li>
</ul>
	</form>


<!--
attention avec les {} utilisés par Smarty et Javascript
https://datatables.net/forums/discussion/11939/resolved-datatables-smarty
-->
<script type="text/javascript">
    ajaxCart.refresh(); 

    $('.quickAddToBasket').click(function() {
        var id_product = $(this).attr("id");
        var id_product_attribute = $(this).attr("data-attribute");
        var qty = $('#qty_'+id_product).val();
        //alert ('quickAdd : id_product : '+id_product+' qty : '+qty);
        //document.location.href="index.php?action=addOneToCart&add=1&qty="+qty+"&id_shopping_list={$itemList.id_shopping_list}&id_product="+id_product+"&id_product_attribute="+id_product_attribute+"&fc=module&module=shoppinglist&controller=accountshoppinglistproduct";

        //$(this).addClass("");
        $('#spinner').show();

        $.ajax({
            url: 'index.php',
            type: 'GET',
            data: "action=addOneToCart&add=1&qty="+qty+"&id_shopping_list={$itemList.id_shopping_list}&id_product="+id_product+"&id_product_attribute="+id_product_attribute+"&fc=module&module=shoppinglist&controller=accountshoppinglistproduct",   
            success: function(json) {
                ajaxCart.refresh(); 
            }
        }).done(function(){
            $('#spinner').hide();
        });



    } );

    /*
<a class="quickAddToBasket btn btn-default button button-small" href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['action' => 'addOneToCart', 'add' => '1', 'id_shopping_list' => $itemList.id_shopping_list, 'id_product' => $itemList.id_product, 'id_product_attribute' => $itemList.id_product_attribute])}">

<a class="quickAddToBasket btn btn-default button button-small" href="http://vinicom.wine/index.php?action=addOneToCart&add=1&id_shopping_list=28&id_product=508&id_product_attribute=682&fc=module&module=shoppinglist&controller=accountshoppinglistproduct">
    */

    var table = $('#shopping-list').DataTable( {
	"autowidth":true,
	"paging":false, 
	"scrollY": 550, 
	"searching":true,
    "aoColumns": [
    { "bSortable":true },
    { "bSortable":true },
    { "bSortable":true },
    { "bSortable":true },
    { "bSortable":true },
    { "bSortable":true },
    { "bSortable":false },
    { "bSortable":false }
    ],

    "oLanguage": {
            "sProcessing":     "<div class=loading>Traitement en cours...</div>",
            "sSearch":         "Rechercher&nbsp;:",
            "sLengthMenu":     "Afficher _MENU_ &eacute;l&eacute;ments",
            "sInfo":           "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            "sInfoEmpty":      "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
            "sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            "sInfoPostFix":    "",
            "sLoadingRecords": "Chargement en cours...",
            "sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
            "sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
            "oPaginate": {
              "sFirst":      "Premier",
              "sPrevious":   "Pr&eacute;c&eacute;dent",
              "sNext":       "Suivant",
              "sLast":       "Dernier"
            },
         "oAria": {
                "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
            }
    }

    });  
</script>


