insert into pre8466_address (id_customer,alias,id_country,lastname,firstname,address1,city,date_add,date_upd )SELECT id_customer, "Mon adresse",8 ,lastname,firstname,'.','.' ,date_add,date_upd FROM pre8466_customer WHERE id_customer not in (select id_customer from pre8466_address)