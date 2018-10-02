<?php

include_once '../functions.php';

$filename = "languages.svm";

/*
 * Make language categories. Make sure the training in character alignment for this.
 */
$tr = new Trainer( $filename, trainer::$noWordAlignment );

// Define all of the offical EU Languages (well most of them) and others
  $arr = array(     "af",               // Afrikaan
                    "ar",               // arabic
                    "bg",               // bulgarian
                    "es-ES",            // spanish-spain (castilian Spanis)
                    "zh-CN",            // Simplified Chinese
                    "cs",               // Czech
                    "da",               // Danish
                    "nl",               // Dutch
                    "en",               // English
                    "et",               // Estonian
                    "fi",               // Finish
                    "fr",               // French
                    "gl-ES",            // Glacian Spanish
                    "de",               // German
                    "el",               // Greek
                    "hu",               // Hungarian
                    "gd-IE",            // Irish - Ireland
                    "it",               // Italian
                    "lv",               // Latvian
                    "lt",               // Lithuanian
                    "mt",               // Maltese
                    "ja-JP",            // Japan
                    "ko",               // Korean
                    "pl",               // Polish
                    "pt-PT",            // Portuguguese-Portugal
                    "ro",               // Romamian
                    "ru",               // Russian
                    "sk",               // Slovak
                    "sl",               // Slovian
                    "es",               // Spainish - Spain
                    "sv"                // Swedish
            );

// The following call will compile the SVM model and serialise it to disk. Comment this out if you do not 
//  want to recompute these models each time you run the unit test.
//$tr->compileCategory($arr, $filename );


/*
 * Now lets see which category the text comes from
 */
$cl = new Classifier( $filename );


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  UNIT TEST for most European Languages
//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo "#################################################################################\n";
echo "##                                                                             ##\n";
echo "##                   Unit tests for Language Detection                         ##\n";
echo "##                                                                             ##\n";
echo "#################################################################################\n\n";


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
// NOTE: This following test cases are in Alphabetical order. This makes it easier to verify results.....
//
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// afrikaan
$str = "Die oortreder moet al die lotto nommer kombinasies vanaf elke trekking sedert die lotto ingestel is memoriseer.";
detectLanguage( "af", $cl, $str );

// arabic
$str = "وحتى حزيران/يونيه 2005، كان لدى 68 بلداً شركة واحدة على الأقل معتمدة من جانب الاتحاد الأوروبي.";
detectLanguage( "ar", $cl, $str );

// bulgarian
$str = "Тъй като измененията на условията за разрешителното не се дължат на причини, свързани с безопасността, е целесъобразно да се предвиди преходен период за изчерпване на съществуващите складови наличности от премикси и комбинирани фуражи.";
detectLanguage( "bg", $cl, $str );


// castilian spanish
$str = "Les conclusions d'aquesta tesi posen en manifest la necessitat de una millor estratègia de comunicació que permeti als consumidors una percepció de millor i major qualitat d'informació en relació a aquests productes.";
detectLanguage( "es-ES", $cl, $str );


//  Chinese-Simplified
$str = "本 演示 的 目标 观众 是 任何 使用 商业 或 企业 exchange 基础 结构 的 公司 中 的 “ 重要 影响 人物 ” 或 技术顾问 。";
detectLanguage( "zh-CN", $cl, $str );

// Czech
$str = "Do přizpůsobených materiálů partner nezahrne žádné duševní vlastnictví třetí strany, aniž by byl oprávněn příslušné duševní vlastnictví třetí strany používat.";
detectLanguage( "cs", $cl, $str );

// danish
$str = "ImageDirect – Onlineselvbetjeningstjeneste til udvikling og vedligeholdelse af systemafbildninger";
detectLanguage( "da", $cl, $str );

// Dutch
$str = "De conventionele niet-klinische studies werden niet uitgevoerd, maar buiten de gegevens die in andere rubrieken van de samenvatting van de productkenmerken zijn opgenomen, zijn er geen niet-klinische feiten die relevant geacht worden voor de klinische veiligheid.";
detectLanguage( "nl", $cl, $str );

// english
$str = "Furthermore, the subcommittee recommends that the federal government, in collaboration with the provinces, establish those home care and pharmacare programs necessary for the dying.
Why can one of them not be directed toward palliative care and care of the dying in Canada?";
detectLanguage( "en", $cl, $str );

// estonian
$str = "Selleks et väidet võiks uue koostisega happeliste jookide kohta kasutada, peavad need vastama selle toidu kirjeldusele, mille kohta väide on esitatud";
detectLanguage( "et", $cl, $str );

// finish
$str = "levykuvan vakaus ja pitkän käyttöiän (15 kuukautta) tuki osana Dellin globaalit vakioalustat -ohjelmaa auttavat vähentämään ylläpidon aiheuttamia muutoksia, jotka hidastavat työntekoa,";
detectLanguage( "fi", $cl, $str );

// french
$str = "Honorables sénateurs, que le sénateur Taylor me permette de le rassurer un tant soit peu.";
detectLanguage( "fr", $cl, $str );

// Galician Spanish
$str = "Mentres que algunhas formas son capaces de realizar movementos independentes e poden nadar verticalmente centos de metros nun só día (comportamento denominado migración vertical diaria), a súa posición horizontal está determinada principalmente polas correntes.";
detectLanguage( "gl-ES", $cl, $str );

// German
$str = "Die Rahmenrichtlinie besagt, dass keine Wasserbewegung ohne vorheriges strenges Abwägen ihrer Notwendigkeit und nicht vor Ausschöpfung aller möglichen Alternativen erfolgen solle.";
detectLanguage( "de", $cl, $str );

// greek
$str = "Ως «έγκριση οχήματος» νοείται η έγκριση ενός τύπου οχήματος κατηγοριών Μ και Ν όσον αφορά το σύστημα ΠΦΑ ως αρχικού εξοπλισμού για τη χρήση στο σύστημα προώθησής του.";
detectLanguage( "el", $cl, $str );

// hungarian
$str = "felhívja a közös vállalkozást, hogy nyújtson be jelentést a mentesítésért felelős hatóság részére a már befejezett projektek társadalmi-gazdasági előnyeiről; kéri, hogy a mentesítésért felelős hatóság a Bizottság értékelésével együtt kapja meg ezt a jelentést;";
detectLanguage( "hu", $cl, $str );

// irish
$str = "Ina theannta sin, glacadh an cur chuige sin i gcás dhá thionscnamh earnála i réimse an oideachais thríú leibhéil agus na teileachumarsáide.";
detectLanguage( "gd-IE", $cl, $str );

// Italian
$str = "La remunerazione deve poter essere pagata a partire dalla data in cui la BCN che parteciperà all’Eurosistema sarà informata del fatto che queste banconote in euro sono entrate in circolazione fino al primo giorno lavorativo dell’Eurosistema successivo alla data di sostituzione del contante.";
detectLanguage( "it", $cl, $str );

// latvian
$str = "Attiecībā uz katru no saraksta 7.1. punktā izvēlētajiem atbalsta instrumentu veidiem, lūdzu, aprakstiet atbalsta piemērošanas nosacījumus (piemēram, nodokļu režīms, vai atbalsts tiek piešķirts automātiski, pamatojoties uz noteiktiem objektīviem kritērijiem, vai arī piešķīrējām iestādēm ir zināma rīcības brīvība):";
detectLanguage( "lv", $cl, $str );

// lithuanian
$str = "Pareiškėjas toliau teigė, kad visiškai tikėtina kad eksportuojantys Indijos gamintojai eksportavo į Sąjungą nedidelėmis siuntomis, už kurias mokėtos didelės momentinės kainos, todėl eksporto kainų lygis dirbtinai padidėjo.";
detectLanguage( "lt", $cl, $str );

// maltese
$str = "Min għandu l-kariżma biex jgħin kreaturi bħal ma huma l-annimali, ara kemm iktar ikollu l-kariżma biex jgħin lill-għajru";
detectLanguage( "mt", $cl, $str );

// Japanese
$str = "指定 し た 設定 オプション の 値 を 設定 し ます 。 成功 時 に 元 の 値 を 、 失敗 し た 際 に FALSE を 返し ます 。 設定 オプション は 、 スクリプト の 実行 中 、 新 し い 値 を 保持 し 、 スクリプト 終了 時 に 元 の 値 へ 戻さ れ ます 。";
detectLanguage( "ja-JP", $cl, $str );

// korean
$str = "현재 페인트 레이어의 수정자 스택에서 종료 결과 표시 버튼을 사용하면 오버레이 페인트 레이어(오브젝트의 수정자 스택에서 현재 수정자의 위에 있는 정점 페인트 수정자) 아래서 대화식으로 페인팅할 수 있습니다.";
detectLanguage( "ko", $cl, $str );

// polish
$str = "EMEA/ 202973/ 2009 Pytania i odpowiedzi dotyczące procedury arbitrażu dla preparatu Tritace, tabletki i kapsułki twarde zawierające ramipryl w dawce 1, 25 mg, 2, 5 mg, 5 mg i 10 mg";
detectLanguage( "pl", $cl, $str );

// portuguese
$str = "Use esta opção para mudar a pasta que irá conter os ficheiros de registo gerados pelo programa. Esta opção poderá ser definida por cada módulo, desde a versão 0. 64 e posteriores.";
detectLanguage( "pt-PT", $cl, $str );

// romanian
$str = "Privilegiile acordate personalului EUAM și imunitatea față de jurisdicția penală a Ucrainei nu îl exceptează de la jurisdicția statului contribuitor sau a instituțiilor UE.";
detectLanguage( "ro", $cl, $str );

// russian
$str = "Если две CMTS установлены с одними и теми же параметрами частоты восходящего потока для отдельного сегмента кабеля, тогда одна CMTS может подслушать исходный запрос диапазона от кабельного модема, соединяющего с другой CMTS.";
detectLanguage( "ru", $cl, $str );

// slovak
$str = "Pokrok sa dosahuje aj pri posilňovaní hospodárenia s verejnými financiami prostredníctvom zlepšeného systému predkladania správ a monitorovania, ako aj prostredníctvom reformy rozpočtového rámca v súlade s odporúčaniami útvarov Komisie a pracovníkov MMF.";
detectLanguage( "sk", $cl, $str );

// slovenian
$str = "Izračunana je bila z uporabo serije podatkov, ki temelji na Uredbi Evropskega parlamenta in Sveta (ES) št. 91/2003 z dne 16. decembra 2002 o statistiki železniškega prevoza [3] in jo je 6. marca 2009 predložil Eurostat za obdobje 2004–2007.";
detectLanguage( "sl", $cl, $str );

// spanish
$str = "Su médico le realizará un análisis de sangre antes de que reciba RoActemra, para determinar si tiene un recuento bajo de glóbulos blancos sanguíneos, un recuento bajo de plaquetas o elevación de las enzimas hepáticas.";
detectLanguage( "es", $cl, $str );


// swedish
$str = "För det fjärde noterar kommissionen att även om den polska tonnageskatteordningen endast kommer att vara tillämplig på ”fullständiga” fartygsförvaltare, dvs. de som samtidigt ansvarar för förvaltning av besättning och teknisk förvaltning, eftersom sådana förvaltare även ansvarar för förvaltning av besättningar, bör de särskilda kraven i avsnitt 6 i riktlinjerna för fartygsförvaltare ändå vara tillämpliga även på dessa förvaltare.";
detectLanguage( "sv", $cl, $str );


////////////////////////////////////////////////////////////////////////////////////////////////////////////



echo "\n\nDone!\n";


function detectLanguage( $language, $cl, $str )
{
    $classified = $cl->classifyText($str);
    echo "----------------------------------------------------------------------------------------------------------------------------------------\n";
    echo "Language : [$language]\n";
    echo "String   : [" . substr($str, 0, 120) . "..." . "]\n";
    echo "Result   : [" . $classified . "]\n";
    echo "Status   : [" . (strcasecmp($language, $classified) ? "ERROR : Classifier failure!!!" : "OK" ) . "]\n";
}

?>