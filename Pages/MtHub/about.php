<?php

include_once '../../functions.php';

Session::authenticateUser(Groups::GROUP_ADMINISTRATOR);

$header = [
    'title' => Session::t('About'),
    'breadcrumbs' => [
        [
            'icon' => 'fa-list-ol',
            'link' => 'Pages/MtHub/about.php',
            'pagename' => Session::t('About')
        ]
    ]
];

Helper::displayPageHeader($header);

?>

<section class="content">
    <div class="row">
        <div class="box box-warning small-padding">
            <div class="box-header">
                <h3 class="box-title"><?php echo Session::t('About Partners'); ?></h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Suppliers/adapt.png" width="200" height="200" alt="ADAPT"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>ADAPT/DCU</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                Dublin City University (DCU) is one of Ireland’s fastest-growing universities. One of
                                the research foci at DCU is the Science Foundation Ireland (SFI)-funded ADAPT Centre for
                                Digital Content Technology.
                                ADAPT is a dynamic research centre that combines the world-class expertise of
                                researchers at four universities with that of its industry partners to produce
                                ground-breaking digital content innovations.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Dr Sheila Castilho - postdoctoral researcher in machine translation evaluation.
                                    Responsible for the design and implementation of the evaluation of the machine
                                    translation systems and the iADAATPA platform. Responsible for the analysis of the
                                    evaluation results, and reports.
                                </li>
                                <li>
                                    Dr Natalia Resende - postdoctoral researcher in machine translation evaluation.
                                    Contributed for the design of machine translation evaluation. Responsible for the
                                    implementation of the evaluation, analysis of the evaluation results, and reports.
                                </li>
                                <li>
                                    Dr Federico Gaspari - postdoctoral researcher in machine translation. Contributed to
                                    the design of machine translation evaluation and supported its execution within the
                                    project.
                                </li>
                                <li>
                                    Prof Andy Way - full professor. Supervised and supported the entire execution of the
                                    project.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Suppliers/everis.png" width="200" height="200" alt="Everis"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>Everis</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                Everis is a multinational consulting firm providing business and strategy solutions,
                                application development, maintenance, and outsourcing services. Possessing the tools to
                                transform the way business is understood and managed, Everis does not merely help
                                companies become more efficient and profitable, but also helps them grow and stay one
                                step ahead.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Miguel Angel Gomez Zotano - EU Buisness Development Manager. Steering Committee
                                    member.
                                </li>
                                <li>
                                    Tania Gonzalez Cardona - Public Sector Senior Consultant, European Projects and Data
                                    Privacy Office GDPR.
                                </li>
                                <li>
                                    Carlos Francisco Alba Ponce - Software Analyst
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Suppliers/kantanmt.png" width="200" height="200" alt="KantanMT"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>KantanMT</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                KantanMT.com is the leading SaaS-based machine translation platform that enables users
                                to develop and manage customized machine translation engines in the cloud. The
                                innovative technologies offered on the KantanMT.com platform enable users to easily
                                build MT engines in over 750 language combinations; engines that will seamlessly
                                integrate into the user’s localization workflows and web applications.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Tony O'Dowd - Chef Architect. Steering Committee member. Responsible for the
                                    technical and functioanl design.
                                </li>
                                <li>
                                    Marek Mazur - Software Development Manager. Responsible for developemnt of the
                                    MT-hub.eu platform.
                                </li>
                                <li>
                                    Colin Harper - Senior Software Developer. Responsible for developemnt of the Drupal
                                    and MemoQ connector.
                                </li>
                                <li>
                                    Louise Faherty - Professional Services Team Lead. Responsible for Neural engines
                                    training.
                                </li>
                                <li>
                                    Riccardo Superbo - Senior Client Solutions Engineer. Responsible for Neural engines
                                    training.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Suppliers/pangeanic.jpg" width="200" height="200" alt="Pangeanic"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>Pangeanic</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                Pangeanic tech division (PangeaMT) mission is to develop and transfer artificial
                                intelligence and neural machine translation technology to corporations, national and
                                international institutions, translation professionals, and cognitive companies. We
                                empower users with our AI-powered Natural Language Processing ecosystem so our clients
                                can obtain actionable insights: machine translation, anonymization, summarization, audio
                                transcription, sentiment analysis, e-Discovery, Knowledge Engineering.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Manuel Herranz - CEO. Steering Committee member.
                                </li>
                                <li>
                                    Amando Estela - Software Coordinator. Responsible for the integration of AS4
                                    Domibus, and Java File Plugin.
                                </li>
                                <li>
                                    Laurent Bie - Software Analyst. Responsible for the development of Web Browser
                                    Plugin and OpenCMS Plugin.
                                </li>
                                <li>
                                    Jose Manuel De La Torre - Software Developer. Responsible of development of iADAATPA
                                    interface with Pangeanic Farm Engine.
                                </li>
                                <li>
                                    Mercedes García-Marínez - PangeaMT R&D leader. Responsible for Neural engines
                                    training.
                                </li>
                                <li>
                                    Alex Helle - PangeaMT machine translation expert. Management of training corpora.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Suppliers/plantl.jpg" width="200" height="200" alt="PlanTL"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>PlanTL</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                The PlanTL (LT-Plan) is a Spanish public initiative with the goal of promoting the
                                development of Natura Language Processing, Automatic Translation and conversational
                                systems in Spanish and co-official languages. The Secretary of State for the Digital
                                Advancement (SEAD) is the responsible for the implementation of the LT-Plan as part of
                                the Strategic Digital Agenda of Spain.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    David Pérez-Fernández - Head of the Plan-TL. Coordinator of the deployment of the
                                    MT-HUB platform in the Spanish Administration.
                                </li>
                                <li>
                                    Maite Melero - Relations with the Spanish Administration. Specifications of the
                                    Public Administration use case.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Suppliers/promsit.png" width="200" height="200" alt="Promsit"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>Prompsit</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                Prompsit is a language technology provider with a strong focus in tailored MT services.
                                It is a spin-off of the Transducens research group (University of Alicante, Spain).
                                Since 2006, Prompsit provides high-quality technical solutions based on natural language
                                applications for private companies and public institutions.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Gema Ramírez Sánchez - General Project Manager. Involved in the supervision of the
                                    project inside Prompsit and in the communication with the rest of partners.
                                </li>
                                <li>
                                    José Juan Martínez Carrascosa - Technical Project Manager. Leaded the analysis of
                                    requirements, functional and technical specifications.
                                </li>
                                <li>
                                    Sergio Ortiz Rojas - Technical Project Manager. Supervised the internal technical
                                    development.
                                </li>
                                <li>
                                    Victor Sánchez Cartagena - Localisation Engineer. Involved in the development of the
                                    MT engines and API.
                                </li>
                                <li>
                                    Encarnación Gómez Alcaraz - Localisation Engineer. Involved in the development of
                                    connectors and the quality testing of the platform.
                                </li>
                                <li>
                                    Jaume Zaragoza Bernabeu - Localisation Engineer. Tested final version of the
                                    platform, engines and connectors.
                                </li>
                                <li>
                                    Miriam Antunes Gonçalves - Financial manager and linguistic supervisor. In charge of
                                    financial activities, human resources and linguistic checks.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Suppliers/tilde.png" width="200" height="200" alt="Tilde"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>Tilde</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                Tilde develops (MT) systems that are tailor-made for each customer. Customization
                                provides significantly higher translation quality than generic MT systems, ensuring that
                                translations meet your organization’s individual requirements.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Valters Šics - Lead Software Developer.
                                </li>
                                <li>
                                    Artūrs Vasiļevskis - Head of Machine Translation Solutions.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

