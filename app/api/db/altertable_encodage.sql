ALTER TABLE dims_mod_cata_famille change label label varchar(255) collate utf8_general_ci NOT NULL; 
ALTER TABLE dims_mod_cata_famille change description description longtext collate utf8_general_ci NOT NULL;
ALTER TABLE dims_mod_cata_famille change code code char(50) collate utf8_general_ci NOT NULL DEFAULT 0;