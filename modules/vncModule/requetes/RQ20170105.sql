-- Liste des prix à Modifier
SELECT
  p.`id_product`,
  p.reference,
  pl.name,
  p.price as PrixBase,
  pa.price,
  p.price +pa.price as PrixModif
FROM
  `pre8466_product_attribute` pa,
  pre8466_product p, pre8466_product_lang pl
WHERE
  p.id_product = pa.id_product and  pa.price <>0 and p.id_product = pl.id_product
ORDER BY
  `p`.`id_product` ASC;
-- MAJ du prix dans la table product
update pre8466_product p, pre8466_product_attribute pa2
set p.price =   pa2.price +p.price
where p.id_product = pa2.id_product and p.reference <> '';
-- RAZ des impacts sur les prixs
update pre8466_product_attribute pa2
set pa2.price =   0
