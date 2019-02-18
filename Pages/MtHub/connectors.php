<?php

include_once '../../functions.php';

$header = [
    'title' => Session::t('Connectors'),
    'breadcrumbs' => [
        [
            'icon' => 'fa-list-ol',
            'link' => 'Pages/MtHub/connectors.php',
            'pagename' => Session::t('Connectors')
        ]
    ]
];

Helper::displayPageHeader($header);

?>

<section class="content">
    <div class="row">
        <div class="box box-warning small-padding">
            <div class="box-header">
                <h3 class="box-title"><?php echo Session::t('About Connectors'); ?></h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <p>
                            All MT-Hub connectors use the Consumer API to connect the MT-Hub platform engines to various translation tools such as CAT tools and browser extensions.
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Connectors/domibus.jpg" width="200" height="200" alt="Domibus"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>Domibus</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                An MT-Hub connector for translating content within the Domibus software.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Pangeanic - Amando Estela - Software Coordinator.
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <p>GitHub Repository: <a href='https://github.com/iADAATPA/domibus'>/iADAATPA/domibus</a></p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Connectors/drupal.png" width="200" height="200" alt="Drupal"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>Drupal</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                THe Drupal 7 module (connector) allows users to translate all their content on their
                                website using the MT-Hub platform. Users can also export translations for post-editing
                                and then import the post-edited version back into their site.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    KantanMT - Colin Harper - Senior Software Developer.
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <p>GitHub Repository: <a href='https://github.com/iADAATPA/drupal'>/iADAATPA/drupal</a></p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Connectors/fileplugin.jpg" width="200" height="200" alt="File Plugin"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>File Plugin</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                An MT-Hub connector for translating files.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Pangeanic - Amando Estela - Software Coordinator.
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <p>GitHub Repository: <a href='https://github.com/iADAATPA/fileplugin'>/iADAATPA/fileplugin</a></p>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Connectors/matecat.jpg" width="200" height="200" alt="MateCAT"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>MateCAT</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                An MT-Hub connector for translating content within the MateCAT tool.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Prompsit - Encarnación Gómez Alcaraz - Localisation Engineer.
                                </li>
                                <li>
                                    Prompsit - Jaume Zaragoza Bernabeu - Localisation Engineer.
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <p>GitHub Repository: <a href='https://github.com/iADAATPA/matecat'>/iADAATPA/matecat</a></p>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Connectors/memoq.png" width="200" height="200" alt="MemoQ"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>MemoQ</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                An MT-Hub connector for translating content within the MemoQ CAT tool.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    KantanMT - Colin Harper - Senior Software Developer.
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <p>GitHub Repository: <a href='https://github.com/iADAATPA/memoq'>/iADAATPA/memoq</a></p>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Connectors/omegat.png" width="200" height="200" alt="OmegaT"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>OmegaT</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                An MT-Hub connector for translating content within the OmegaT CAT tool.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Prompsit - Encarnación Gómez Alcaraz - Localisation Engineer.
                                </li>
                                <li>
                                    Prompsit - Jaume Zaragoza Bernabeu - Localisation Engineer.
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <p>GitHub Repository: <a href='https://github.com/iADAATPA/omegat'>/iADAATPA/omegat</a></p>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Connectors/opencms.png" width="200" height="200" alt="OpenCMS"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>OpenCMS</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                An MT-Hub connector for translating content within the OpenCMS environment.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Pangeanic - Laurent Bie - Software Analyst.
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <p>GitHub Repository: <a href='https://github.com/iADAATPA/OpenCMS'>/iADAATPA/OpenCMS</a></p>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Connectors/pentaho.jpg" width="200" height="200" alt="Pentaho Kettle"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>Pentaho Kettle</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                An MT-Hub connector for translating content within the Pentaho Kettle environment.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Arturs Vasiļevskis - General Project Manager. Involved in the supervision of the
                                    project finance, development and coordination.
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <p>GitHub Repository: <a href='https://github.com/iADAATPA/pentahokettle'>/iADAATPA/pentahokettle</a></p>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Connectors/tradosstudio.png" width="200" height="200" alt="Trados Studio"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>SDL - Trados Studio</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                An MT-Hub connector for translating content within the Trados Studio CAT tool.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Tilde - Rihards Krišlauks - Lead developer.
                                </li>
                                <li>
                                    Tilde - Valters Šics - Development team leader.
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <p>GitHub Repository: <a href='https://github.com/iADAATPA/tradosstudio'>/iADAATPA/tradosstudio</a></p>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-3 center-content">
                        <img src="Images/Connectors/chromewebbrowser.jpg" width="200" height="200" alt="Chrome Web Browser"/>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <h4><b>Chrome Web Browser</b></h4>
                        </div>
                        <div class="col-md-12">
                            <p>
                                An MT-Hub connector which can be installed as an extension on the Chrome browser for translating web content.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Contributors:</p>
                        </div>
                        <div class="col-md-12">
                            <ul>
                                <li>
                                    Pangeanic - Laurent Bie - Software Analyst.
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <p>GitHub Repository: <a href='https://github.com/iADAATPA/webbrowser'>/iADAATPA/webbrowser</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

