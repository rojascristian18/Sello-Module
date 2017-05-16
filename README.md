# Módulo agregar sello a productos
Este módulo para <b>Prestashop 1.6<b> permite agregar una imagen o sello a todos los productos que 
esten relacionados con la categoría registrada en el módulo.


# Utilidades
- Poner un sello a los productos de la categoría ofertas por ejemplo.
- Programar un sello en un rango de tiempo.
- Customizar el estilo CSS del sello desde el módulo.

Este módulo se utiliza el hook <b>displayProductListFunctionalButtons</b>.

# Configurar
Sí queremos que el sello se muestre en todas las páginas se debe modificar el 
archivo <b>product-list.tpl</b> de nuestro tema.

Buscar el bloque
```
  {if $page_name != 'index'}
    <div class="functional-buttons clearfix">
      {hook h='displayProductListFunctionalButtons' product=$product}
      {if isset($comparator_max_item) && $comparator_max_item}
        <div class="compare">
          <a class="add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}">{l s='Add to Compare'}</a>
        </div>
      {/if}
    </div>
  {/if}
```
Luego reemplazar por
```
  {if $page_name != 'index'}
    <div class="functional-buttons clearfix">
      {hook h='displayProductListFunctionalButtons' product=$product}
      {if isset($comparator_max_item) && $comparator_max_item}
        <div class="compare">
          <a class="add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}">{l s='Add to Compare'}</a>
        </div>
      {/if}
    </div>
  {else}
    {hook h='displayProductListFunctionalButtons' product=$product}
  {/if}
 ```
 
 Con estos cambios el sello se mostrará en el index tambien.
 
 <b>OJO, sí tienes algun otro módulo enlazado con el hook displayProductListFunctionalButtons, 
 tambien se mostrará en todos los productos y páginas.</b>
 
 Salud!
