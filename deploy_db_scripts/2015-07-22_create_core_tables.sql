﻿SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  `english_name` varchar(100) DEFAULT NULL,
  `native_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=186 DEFAULT CHARSET=utf8;
INSERT INTO `languages` VALUES (1,'aa','Afar','Qafar (Afaraf)'),(2,'ab','Abkhazian','аҧсуа бызшәа'),(3,'ae','Avestan',''),(4,'af','Afrikaans','Afrikaans'),(5,'ak','Akan',''),(6,'am','Amharic','አማርኛ'),(7,'an','Aragonese','Aragonés'),(8,'ar','Arabic','عربي'),(9,'as','Assamese','অসমীয়া'),(10,'av','Avaric','Авар'),(11,'ay','Aymara','Aymar aru'),(12,'az','Azerbaijani','azərbaycan'),(13,'ba','Bashkir','Башҡортса'),(14,'be','Belarusian','Беларуская - Biełaruskaja'),(15,'bg','Bulgarian','Български'),(16,'bh','Bihari languages','भोजपुरी'),(17,'bi','Bislama','Bislama'),(18,'bm','Bambara','Bamanankan'),(19,'bn','Bengali','বাংলা'),(20,'bo','Tibetan','བོད་ཡིག'),(21,'br','Breton','Brezhoneg'),(22,'bs','Bosnian','Bosanski'),(23,'ca','Catalan; Valencian','Català'),(24,'ce','Chechen','Нохчийн'),(25,'ch','Chamorro','Chamoru'),(26,'co','Corsican','Corsu'),(27,'cr','Cree',''),(28,'cs','Czech','Čeština'),(29,'cu','Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic','Словѣ́ньскъ'),(30,'cv','Chuvash','Чӑвашла'),(31,'cy','Welsh','Cymraeg'),(32,'da','Danish','Dansk'),(33,'de','German','Deutsch'),(34,'dv','Divehi; Dhivehi; Maldivian','ދިވެހިބަސ'),(35,'dz','Dzongkha','རྫོང་ཁ'),(36,'ee','Ewe','Eʋegbe'),(37,'el','Greek, Modern (1453-)','Ελληνικά'),(38,'en','English',''),(39,'eo','Esperanto','Esperanto'),(40,'es','Spanish; Castilian','Español'),(41,'et','Estonian','Eesti'),(42,'eu','Basque','Euskera'),(43,'fa','Persian','پارسی'),(44,'ff','Fulah','Fulfulde'),(45,'fi','Finnish','Suomi'),(46,'fj','Fijian','Na Vosa Vakaviti'),(47,'fo','Faroese','føroyskt'),(48,'fr','French','Français'),(49,'fy','Western Frisian','Frysk'),(50,'ga','Irish','Gaeilge'),(51,'gd','Gaelic; Scottish Gaelic','Gàidhlig'),(52,'gl','Galician','Galego'),(53,'gn','Guarani','Avañe\'ẽ'),(54,'gu','Gujarati','ગુજરાતી'),(55,'gv','Manx','Gaelg'),(56,'ha','Hausa','هَوُسَ'),(57,'he','Hebrew','עברית'),(58,'hi','Hindi','हिन्दी'),(59,'ho','Hiri Motu',''),(60,'hr','Croatian','Hrvatski'),(61,'ht','Haitian; Haitian Creole','Kreyòl ayisyen'),(62,'hu','Hungarian','Magyar'),(63,'hy','Armenian','Հայերեն'),(64,'hz','Herero',''),(65,'ia','Interlingua (International Auxiliary Language Association)','Interlingua'),(66,'id','Indonesian','Bahasa Indonesia'),(67,'ie','Interlingue; Occidental','Interlingue'),(68,'ig','Igbo','Igbo'),(69,'ii','Sichuan Yi; Nuosu','ꆇꉙ'),(70,'ik','Inupiaq','Iñupiak'),(71,'io','Ido','Ido'),(72,'is','Icelandic','Icelandic'),(73,'it','Italian','Italian'),(74,'iu','Inuktitut','ᐃᓄᒃᑎᑐᑦ/inuktitut'),(75,'ja','Japanese','日本語'),(76,'jv','Javanese','Basa Jawa'),(77,'ka','Georgian','ქართული'),(78,'kg','Kongo',''),(79,'ki','Kikuyu; Gikuyu',''),(80,'kj','Kuanyama; Kwanyama',''),(81,'kk','Kazakh','Қазақ'),(82,'kl','Kalaallisut; Greenlandic','Kalaallisut'),(83,'km','Central Khmer','ខ្មែរ'),(84,'kn','Kannada','ಕನ್ನಡ'),(85,'ko','Korean','한국어'),(86,'kr','Kanuri',''),(87,'ks','Kashmiri','कश्मीरी - (كشميري)'),(88,'ku','Kurdish','وۆردپرێس بەکوردی'),(89,'kv','Komi','Коми'),(90,'kw','Cornish','kernewek'),(91,'ky','Kirghiz; Kyrgyz','Кыргыз'),(92,'la','Latin','Latina'),(93,'lb','Luxembourgish; Letzeburgesch',''),(94,'lg','Ganda','Luganda'),(95,'li','Limburgan; Limburger; Limburgish','Limburgs'),(96,'ln','Lingala','Lingála'),(97,'lo','Lao','ລາວ'),(98,'lt','Lithuanian','Lietuvių'),(99,'lu','Luba-Katanga',''),(100,'lv','Latvian','Latviešu'),(101,'mg','Malagasy','Malagasy'),(102,'mh','Marshallese',''),(103,'mi','Maori','Māori'),(104,'mk','Macedonian','Македонски'),(105,'ml','Malayalam','മലയാളം'),(106,'mn','Mongolian','Монгол хэл'),(107,'mr','Marathi','मराठी'),(108,'ms','Malay','Bahasa Melayu'),(109,'mt','Maltese','Malt'),(110,'my','Burmese',''),(111,'na','Nauru','Dorerin Naoero'),(112,'nb','Bokmal, Norwegian; Norwegian Bokmal','Bokmål'),(113,'nd','Ndebele, North; North Ndebele',''),(114,'ne','Nepali','नेपाली'),(115,'ng','Ndonga',''),(116,'nl','Dutch; Flemish','Nederlands'),(117,'nn','Norwegian Nynorsk; Nynorsk, Norwegian','Nynorsk'),(118,'no','Norwegian','Norsk (bokmål)‬'),(119,'nr','Ndebele, South; South Ndebele',''),(120,'nv','Navajo; Navaho','Diné bizaad'),(121,'ny','Chichewa; Chewa; Nyanja','Chi-Chewa'),(122,'oc','Occitan','Occitan'),(123,'oj','Ojibwa',''),(124,'om','Oromo','Oromoo'),(125,'or','Oriya','ଓଡ଼ିଆ'),(126,'os','Ossetian; Ossetic','Ирон'),(127,'pa','Panjabi; Punjabi','ਪੰਜਾਬੀ'),(128,'pi','Pali','पािऴ'),(129,'pl','Polish','Polski'),(130,'ps','Pushto; Pashto','پښتو'),(131,'pt','Portuguese','Português'),(132,'qu','Quechua','Runa Simi'),(133,'rm','Romansh','Rumantsch'),(134,'rn','Rundi',''),(135,'ro','Romanian; Moldavian; Moldovan','Română'),(136,'ru','Russian','Русский'),(137,'rw','Kinyarwanda',''),(138,'sa','Sanskrit','संस्कृत'),(139,'sc','Sardinian','Sardu'),(140,'sd','Sindhi','سنڌي'),(141,'se','Northern Sami','Sámegiella'),(142,'sg','Sango','Sängö'),(143,'si','Sinhala; Sinhalese','සිංහල'),(144,'sk','Slovak','Slovenčina'),(145,'sl','Slovenian','Slovenščina'),(146,'sm','Samoan','Gagana Samoa'),(147,'sn','Shona','chiShona'),(148,'so','Somali','Soomaali'),(149,'sq','Albanian','Shqip'),(150,'sr','Serbian','Српски'),(151,'ss','Swati','SiSwati'),(152,'st','Sotho, Southern','Sesotho'),(153,'su','Sundanese','Basa Sunda'),(154,'sv','Swedish','Svenska'),(155,'sw','Swahili','Kiswahili'),(156,'ta','Tamil','தமிழ்'),(157,'te','Telugu','తెలుగు'),(158,'tg','Tajik','Tajik'),(159,'th','Thai','ภาษาไทย'),(160,'ti','Tigrinya','ትግርኛ'),(161,'tk','Turkmen','Türkmençe'),(162,'tl','Tagalog','Tagalog'),(163,'tn','Tswana','Setswana'),(164,'to','Tonga (Tonga Islands)','lea faka-Tonga'),(165,'tr','Turkish','Türkçe'),(166,'ts','Tsonga','Xitsonga'),(167,'tt','Tatar','Tatarça'),(168,'tw','Twi',''),(169,'ty','Tahitian','Reo Mā`ohi'),(170,'ug','Uighur; Uyghur','ئۇيغۇرچە'),(171,'uk','Ukrainian','Українська'),(172,'ur','Urdu','اردو'),(173,'uz','Uzbek','O‘zbekcha'),(174,'ve','Venda','Tshivenda'),(175,'vi','Vietnamese','Tiếng Việt'),(176,'vo','Volapuk','Volapük'),(177,'wa','Walloon','Walon'),(178,'wo','Wolof','Wolof'),(179,'xh','Xhosa','isiXhosa'),(180,'yi','Yiddish','ייִדיש'),(181,'yo','Yoruba','Yorùbá'),(182,'za','Zhuang; Chuang','Vahcuengh'),(183,'zh','Chinese','中文 (zh_CN); 香港 (zh_HK); 台灣 (zh_TW)'),(184,'zu','Zulu','isiZulu'),(185,'other','Other','Другой');

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_a2` varchar(10) DEFAULT NULL,
  `code_a3` varchar(10) DEFAULT NULL,
  `code_num` int(11) DEFAULT NULL,
  `russian_name` varchar(100) DEFAULT NULL,
  `english_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=255 DEFAULT CHARSET=utf8;

INSERT INTO `countries` VALUES (1,'AU','AUS',36,'Австралия','Australia'),(2,'AT','AUT',40,'Австрия','Austria'),(3,'AZ','AZE',31,'Азербайджан','Azerbaijan'),(4,'AX','ALA',248,'Аландские острова','Aland Islands'),(5,'AL','ALB',8,'Албания','Albania'),(6,'DZ','DZA',12,'Алжир','Algeria'),(7,'VI','VIR',850,'Американские Виргинские острова','Virgin Islands, U.S.'),(8,'AS','ASM',16,'Американское Самоа','American Samoa'),(9,'AI','AIA',660,'Ангилья','Anguilla'),(10,'AO','AGO',24,'Ангола','Angola'),(11,'AD','AND',20,'Андорра','Andorra'),(12,'AQ','ATA',10,'Антарктида','Antarctica'),(13,'AG','ATG',28,'Антигуа и Барбуда','Antigua and Barbuda'),(14,'AR','ARG',32,'Аргентина','Argentina'),(15,'AM','ARM',51,'Армения','Armenia'),(16,'AW','ABW',533,'Аруба','Aruba'),(17,'AF','AFG',4,'Афганистан','Afghanistan'),(18,'BS','BHS',44,'Багамы','Bahamas'),(19,'BD','BGD',50,'Бангладеш','Bangladesh'),(20,'BB','BRB',52,'Барбадос','Barbados'),(21,'BH','BHR',48,'Бахрейн','Bahrain'),(22,'BZ','BLZ',84,'Белиз','Belize'),(23,'BY','BLR',112,'Белоруссия','Belarus'),(24,'BE','BEL',56,'Бельгия','Belgium'),(25,'BJ','BEN',204,'Бенин','Benin'),(26,'BM','BMU',60,'Бермуды','Bermuda'),(27,'BG','BGR',100,'Болгария','Bulgaria'),(28,'BO','BOL',68,'Боливия','Bolivia, Plurinational State of'),(29,'BQ','BES',535,'Бонэйр, Синт-Эстатиус и Саба','Bonaire, Sint Eustatius and Saba'),(30,'BA','BIH',70,'Босния и Герцеговина','Bosnia and Herzegovina'),(31,'BW','BWA',72,'Ботсвана','Botswana'),(32,'BR','BRA',76,'Бразилия','Brazil'),(33,'IO','IOT',86,'Британская территория в Индийском океане','British Indian Ocean Territory'),(34,'VG','VGB',92,'Британские Виргинские острова','Virgin Islands, British'),(35,'BN','BRN',96,'Бруней','Brunei Darussalam'),(36,'BF','BFA',854,'Буркина-Фасо','Burkina Faso'),(37,'BI','BDI',108,'Бурунди','Burundi'),(38,'BT','BTN',64,'Бутан','Bhutan'),(39,'VU','VUT',548,'Вануату','Vanuatu'),(40,'VA','VAT',336,'Ватикан','Holy See (Vatican City State)'),(41,'GB','GBR',826,'Великобритания','United Kingdom'),(42,'HU','HUN',348,'Венгрия','Hungary'),(43,'VE','VEN',862,'Венесуэла','Venezuela, Bolivarian Republic of'),(44,'UM','UMI',581,'Внешние малые острова (США)','United States Minor Outlying Islands'),(45,'TL','TLS',626,'Восточный Тимор','Timor-Leste'),(46,'VN','VNM',704,'Вьетнам','Viet Nam'),(47,'GA','GAB',266,'Габон','Gabon'),(48,'HT','HTI',332,'Гаити','Haiti'),(49,'GY','GUY',328,'Гайана','Guyana'),(50,'GM','GMB',270,'Гамбия','Gambia'),(51,'GH','GHA',288,'Гана','Ghana'),(52,'GP','GLP',312,'Гваделупа','Guadeloupe'),(53,'GT','GTM',320,'Гватемала','Guatemala'),(54,'GF','GUF',254,'Гвиана','French Guiana'),(55,'GN','GIN',324,'Гвинея','Guinea'),(56,'GW','GNB',624,'Гвинея-Бисау','Guinea-Bissau'),(57,'DE','DEU',276,'Германия','Germany'),(58,'GG','GGY',831,'Гернси','Guernsey'),(59,'GI','GIB',292,'Гибралтар','Gibraltar'),(60,'HN','HND',340,'Гондурас','Honduras'),(61,'HK','HKG',344,'Гонконг','Hong Kong'),(62,'GD','GRD',308,'Гренада','Grenada'),(63,'GL','GRL',304,'Гренландия','Greenland'),(64,'GR','GRC',300,'Греция','Greece'),(65,'GE','GEO',268,'Грузия','Georgia'),(66,'GU','GUM',316,'Гуам','Guam'),(67,'DK','DNK',208,'Дания','Denmark'),(68,'JE','JEY',832,'Джерси','Jersey'),(69,'DJ','DJI',262,'Джибути','Djibouti'),(70,'DM','DMA',212,'Доминика','Dominica'),(71,'DO','DOM',214,'Доминиканская Республика','Dominican Republic'),(72,'CD','COD',180,'ДР Конго','Congo, the Democratic Republic of the'),(73,'EU','',0,'Европейский союз','EU'),(74,'EG','EGY',818,'Египет','Egypt'),(75,'ZM','ZMB',894,'Замбия','Zambia'),(76,'EH','ESH',732,'Западная Сахара','Western Sahara'),(77,'ZW','ZWE',716,'Зимбабве','Zimbabwe'),(78,'IL','ISR',376,'Израиль','Israel'),(79,'IN','IND',356,'Индия','India'),(80,'ID','IDN',360,'Индонезия','Indonesia'),(81,'JO','JOR',400,'Иордания','Jordan'),(82,'IQ','IRQ',368,'Ирак','Iraq'),(83,'IR','IRN',364,'Иран','Iran, Islamic Republic of'),(84,'IE','IRL',372,'Ирландия','Ireland'),(85,'IS','ISL',352,'Исландия','Iceland'),(86,'ES','ESP',724,'Испания','Spain'),(87,'IT','ITA',380,'Италия','Italy'),(88,'YE','YEM',887,'Йемен','Yemen'),(89,'CV','CPV',132,'Кабо-Верде','Cape Verde'),(90,'KZ','KAZ',398,'Казахстан','Kazakhstan'),(91,'KY','CYM',136,'Каймановы острова','Cayman Islands'),(92,'KH','KHM',116,'Камбоджа','Cambodia'),(93,'CM','CMR',120,'Камерун','Cameroon'),(94,'CA','CAN',124,'Канада','Canada'),(95,'QA','QAT',634,'Катар','Qatar'),(96,'KE','KEN',404,'Кения','Kenya'),(97,'CY','CYP',196,'Кипр','Cyprus'),(98,'KG','KGZ',417,'Киргизия','Kyrgyzstan'),(99,'KI','KIR',296,'Кирибати','Kiribati'),(100,'TW','TWN',158,'Китайская Республика','Taiwan, Province of China'),(101,'KP','PRK',408,'КНДР','Korea, Democratic People\'s Republic of'),(102,'CN','CHN',156,'КНР','China'),(103,'CC','CCK',166,'Кокосовые острова','Cocos (Keeling) Islands'),(104,'CO','COL',170,'Колумбия','Colombia'),(105,'KM','COM',174,'Коморы','Comoros'),(106,'CR','CRI',188,'Коста-Рика','Costa Rica'),(107,'CI','CIV',384,'Кот-д’Ивуар','Cote d\'Ivoire'),(108,'CU','CUB',192,'Куба','Cuba'),(109,'KW','KWT',414,'Кувейт','Kuwait'),(110,'CW','CUW',531,'Кюрасао','Curacao'),(111,'LA','LAO',418,'Лаос','Lao People\'s Democratic Republic'),(112,'LV','LVA',428,'Латвия','Latvia'),(113,'LS','LSO',426,'Лесото','Lesotho'),(114,'LR','LBR',430,'Либерия','Liberia'),(115,'LB','LBN',422,'Ливан','Lebanon'),(116,'LY','LBY',434,'Ливия','Libya'),(117,'LT','LTU',440,'Литва','Lithuania'),(118,'LI','LIE',438,'Лихтенштейн','Liechtenstein'),(119,'LU','LUX',442,'Люксембург','Luxembourg'),(120,'MU','MUS',480,'Маврикий','Mauritius'),(121,'MR','MRT',478,'Мавритания','Mauritania'),(122,'MG','MDG',450,'Мадагаскар','Madagascar'),(123,'YT','MYT',175,'Майотта','Mayotte'),(124,'MO','MAC',446,'Макао','Macao'),(125,'MK','MKD',807,'Македония','Macedonia, The Former Yugoslav Republic of'),(126,'MW','MWI',454,'Малави','Malawi'),(127,'MY','MYS',458,'Малайзия','Malaysia'),(128,'ML','MLI',466,'Мали','Mali'),(129,'MV','MDV',462,'Мальдивы','Maldives'),(130,'MT','MLT',470,'Мальта','Malta'),(131,'MA','MAR',504,'Марокко','Morocco'),(132,'MQ','MTQ',474,'Мартиника','Martinique'),(133,'MH','MHL',584,'Маршалловы Острова','Marshall Islands'),(134,'MX','MEX',484,'Мексика','Mexico'),(135,'FM','FSM',583,'Микронезия','Micronesia, Federated States of'),(136,'MZ','MOZ',508,'Мозамбик','Mozambique'),(137,'MD','MDA',498,'Молдавия','Moldova, Republic of'),(138,'MC','MCO',492,'Монако','Monaco'),(139,'MN','MNG',496,'Монголия','Mongolia'),(140,'MS','MSR',500,'Монтсеррат','Montserrat'),(141,'MM','MMR',104,'Мьянма','Myanmar'),(142,'NA','NAM',516,'Намибия','Namibia'),(143,'NR','NRU',520,'Науру','Nauru'),(144,'NP','NPL',524,'Непал','Nepal'),(145,'NE','NER',562,'Нигер','Niger'),(146,'NG','NGA',566,'Нигерия','Nigeria'),(147,'NL','NLD',528,'Нидерланды','Netherlands'),(148,'NI','NIC',558,'Никарагуа','Nicaragua'),(149,'NU','NIU',570,'Ниуэ','Niue'),(150,'NZ','NZL',554,'Новая Зеландия','New Zealand'),(151,'NC','NCL',540,'Новая Каледония','New Caledonia'),(152,'NO','NOR',578,'Норвегия','Norway'),(153,'AE','ARE',784,'ОАЭ','United Arab Emirates'),(154,'OM','OMN',512,'Оман','Oman'),(155,'BV','BVT',74,'Остров Буве','Bouvet Island'),(156,'IM','IMN',833,'Остров Мэн','Isle of Man'),(157,'CK','COK',184,'Острова Кука','Cook Islands'),(158,'NF','NFK',574,'Остров Норфолк','Norfolk Island'),(159,'CX','CXR',162,'Остров Рождества','Christmas Island'),(160,'PN','PCN',612,'Острова Питкэрн','Pitcairn'),(161,'SH','SHN',654,'Острова Святой Елены, Вознесения и Тристан-да-Кунья','Saint Helena, Ascension and Tristan da Cunha'),(162,'PK','PAK',586,'Пакистан','Pakistan'),(163,'PW','PLW',585,'Палау','Palau'),(164,'PS','PSE',275,'Государство Палестина','Palestine, State of'),(165,'PA','PAN',591,'Панама','Panama'),(166,'PG','PNG',598,'Папуа &mdash; Новая Гвинея','Papua New Guinea'),(167,'PY','PRY',600,'Парагвай','Paraguay'),(168,'PE','PER',604,'Перу','Peru'),(169,'PL','POL',616,'Польша','Poland'),(170,'PT','PRT',620,'Португалия','Portugal'),(171,'PR','PRI',630,'Пуэрто-Рико','Puerto Rico'),(172,'CG','COG',178,'Республика Конго','Congo'),(173,'KR','KOR',410,'Республика Корея','Korea, Republic of'),(174,'RE','REU',638,'Реюньон','Reunion'),(175,'RU','RUS',643,'Россия','Russian Federation'),(176,'RW','RWA',646,'Руанда','Rwanda'),(177,'RO','ROU',642,'Румыния','Romania'),(178,'SV','SLV',222,'Сальвадор','El Salvador'),(179,'WS','WSM',882,'Самоа','Samoa'),(180,'SM','SMR',674,'Сан-Марино','San Marino'),(181,'ST','STP',678,'Сан-Томе и Принсипи','Sao Tome and Principe'),(182,'SA','SAU',682,'Саудовская Аравия','Saudi Arabia'),(183,'SZ','SWZ',748,'Свазиленд','Swaziland'),(184,'MP','MNP',580,'Северные Марианские острова','Northern Mariana Islands'),(185,'SC','SYC',690,'Сейшельские Острова','Seychelles'),(186,'BL','BLM',652,'Сен-Бартелеми','Saint Barthelemy'),(187,'MF','MAF',663,'Сен-Мартен','Saint Martin (French part)'),(188,'PM','SPM',666,'Сен-Пьер и Микелон','Saint Pierre and Miquelon'),(189,'SN','SEN',686,'Сенегал','Senegal'),(190,'VC','VCT',670,'Сент-Винсент и Гренадины','Saint Vincent and the Grenadines'),(191,'KN','KNA',659,'Сент-Китс и Невис','Saint Kitts and Nevis'),(192,'LC','LCA',662,'Сент-Люсия','Saint Lucia'),(193,'RS','SRB',688,'Сербия','Serbia'),(194,'SG','SGP',702,'Сингапур','Singapore'),(195,'SX','SXM',534,'Синт-Мартен','Sint Maarten (Dutch part)'),(196,'SY','SYR',760,'Сирия','Syrian Arab Republic'),(197,'SK','SVK',703,'Словакия','Slovakia'),(198,'SI','SVN',705,'Словения','Slovenia'),(199,'SB','SLB',90,'Соломоновы Острова','Solomon Islands'),(200,'SO','SOM',706,'Сомали','Somalia'),(201,'SD','SDN',729,'Судан','Sudan'),(202,'SU','SUN',810,'СССР','USSR'),(203,'SR','SUR',740,'Суринам','Suriname'),(204,'US','USA',840,'США','United States'),(205,'SL','SLE',694,'Сьерра-Леоне','Sierra Leone'),(206,'TJ','TJK',762,'Таджикистан','Tajikistan'),(207,'TH','THA',764,'Таиланд','Thailand'),(208,'TZ','TZA',834,'Танзания','Tanzania, United Republic of'),(209,'TC','TCA',796,'Тёркс и Кайкос','Turks and Caicos Islands'),(210,'TG','TGO',768,'Того','Togo'),(211,'TK','TKL',772,'Токелау','Tokelau'),(212,'TO','TON',776,'Тонга','Tonga'),(213,'TT','TTO',780,'Тринидад и Тобаго','Trinidad and Tobago'),(214,'TV','TUV',798,'Тувалу','Tuvalu'),(215,'TN','TUN',788,'Тунис','Tunisia'),(216,'TM','TKM',795,'Туркмения','Turkmenistan'),(217,'TR','TUR',792,'Турция','Turkey'),(218,'UG','UGA',800,'Уганда','Uganda'),(219,'UZ','UZB',860,'Узбекистан','Uzbekistan'),(220,'UA','UKR',804,'Украина','Ukraine'),(221,'WF','WLF',876,'Уоллис и Футуна','Wallis and Futuna'),(222,'UY','URY',858,'Уругвай','Uruguay'),(223,'FO','FRO',234,'Фарерские острова','Faroe Islands'),(224,'FJ','FJI',242,'Фиджи','Fiji'),(225,'PH','PHL',608,'Филиппины','Philippines'),(226,'FI','FIN',246,'Финляндия','Finland'),(227,'FK','FLK',238,'Фолклендские острова','Falkland Islands (Malvinas)'),(228,'FR','FRA',250,'Франция','France'),(229,'PF','PYF',258,'Французская Полинезия','French Polynesia'),(230,'TF','ATF',260,'Французские Южные и Антарктические Территории','French Southern Territories'),(231,'HM','HMD',334,'Херд и Макдональд','Heard Island and McDonald Islands'),(232,'HR','HRV',191,'Хорватия','Croatia'),(233,'CF','CAF',140,'ЦАР','Central African Republic'),(234,'TD','TCD',148,'Чад','Chad'),(235,'ME','MNE',499,'Черногория','Montenegro'),(236,'CZ','CZE',203,'Чехия','Czech Republic'),(237,'CL','CHL',152,'Чили','Chile'),(238,'CH','CHE',756,'Швейцария','Switzerland'),(239,'SE','SWE',752,'Швеция','Sweden'),(240,'SJ','SJM',744,'Шпицберген и Ян-Майен','Svalbard and Jan Mayen'),(241,'LK','LKA',144,'Шри-Ланка','Sri Lanka'),(242,'EC','ECU',218,'Эквадор','Ecuador'),(243,'GQ','GNQ',226,'Экваториальная Гвинея','Equatorial Guinea'),(244,'ER','ERI',232,'Эритрея','Eritrea'),(245,'EE','EST',233,'Эстония','Estonia'),(246,'ET','ETH',231,'Эфиопия','Ethiopia'),(247,'ZA','ZAF',710,'ЮАР','South Africa'),(248,'GS','SGS',239,'Южная Георгия и Южные Сандвичевы острова','South Georgia and the South Sandwich Islands'),(249,'SS','SSD',728,'Южный Судан','South Sudan'),(250,'JM','JAM',388,'Ямайка','Jamaica'),(251,'JP','JPN',392,'Япония','Japan'),(252,'europe',NULL,NULL,'Европа','Europe'),(253,'asia',NULL,NULL,'Азия','Asia'),(254,'other',NULL,NULL,'Другое','Other');


DROP TABLE IF EXISTS `locks`;
CREATE TABLE  `locks` (
  `name` varchar(100) NOT NULL,
  `created` datetime DEFAULT NULL,
  `timeout` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `application_name` varchar(255) DEFAULT NULL,
  `param_name` varchar(100) NOT NULL,
  `param_displayed_name` varchar(255) DEFAULT NULL,
  `param_displayed_unit` varchar(255) DEFAULT NULL,
  `group_name` varchar(100) NOT NULL,
  `group_displayed_name` varchar(255) DEFAULT NULL,
  `param_type` varchar(100) DEFAULT NULL,
  `param_value` text,
  `is_mandatory` tinyint(1) DEFAULT '0',
  `constraints` text,
  `seq` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_name`,`param_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `id` varchar(30),
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_role` (`id`, `name`) VALUES
('admin', 'Administrator'),
('registered', 'Registered User');


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `login` varchar(100) NOT NULL DEFAULT '',
  `pass` varchar(100) NOT NULL DEFAULT '',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `registered` datetime DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `pre_last_login_time` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `con_user_01` (`country_id`),
  KEY `con_user_02` (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `user` VALUES (
  1, 'Admin', '', 'admin@site.com', 'admin@site.com', '21232f297a57a5a743894a0e4a801fc3', 1, '2015-06-04 10:35:26', 38, 41, NULL, NULL, 0
);


DROP TABLE IF EXISTS `user_role_coupling`;
CREATE TABLE `user_role_coupling` (
  `user_id` int(11) NOT NULL,
  `role_id` varchar(30) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `con_user_role_coupling_02` (`role_id`),
  CONSTRAINT `con_user_role_coupling_01` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `con_user_role_coupling_02` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_role_coupling` VALUES (1,'admin');



DROP TABLE IF EXISTS `document`;
CREATE TABLE `document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `seq` int(11) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(64) NOT NULL DEFAULT '',
  `menu` int(1) NOT NULL DEFAULT '0',
  `category` int(1) NOT NULL DEFAULT '0',
  `open_new_window` tinyint(1) DEFAULT NULL,
  `open_link` varchar(255) DEFAULT NULL,
  `protected` tinyint(1) DEFAULT '0',  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `document_content`;
CREATE TABLE  `document_content` (
  `document_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `content` longtext,
  `meta_title` varchar(255) NOT NULL DEFAULT '',
  `meta_desc` text,
  `meta_key` text,
  PRIMARY KEY (`document_id`,`language_id`),
  KEY `document_content_fk1` (`language_id`),
  CONSTRAINT `document_content_fk1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`),
  CONSTRAINT `document_content_fk2` FOREIGN KEY (`document_id`) REFERENCES `document` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


SET FOREIGN_KEY_CHECKS=1;