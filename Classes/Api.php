<?php

use Slim\Http\Response;
use Slim\Http\Request;

/**
 * Class Api
 * @package Classes
 */
class Api
{
    protected $container;
    private $accounts = null;
    private $domainId = null;
    private $source = null;
    private $target = null;
    private $engineName = null;
    private $engineCustomId = null;
    private $engineId = null;
    private $supplierToken = null;
    private $supplierAccountId = null;
    private $segments = null;
    private $file = null;
    private $fileType = null;
    private $guId = null;
    private $domainName = null;
    private $conentType = null;
    private $requestLog = null;
    private $userName = null;
    private $password = null;
    private $callBackUrl = null;
    private $cache = null;
    private $activiaTm = null;
    private $activiaTmUserName = null;
    private $activiaTmPassword = null;
    private $token = null;

    /**
     * @var ApiResponses
     */
    protected $apiResponses;

    const SEGMENTS_LENGHT_LIMIT = 30;
    const SEGMENT_SIZE_LIMIT = 1000;

    /**
     * Api constructor.
     */
    public function __construct()
    {
        $this->setRequestLog(new RequestLog());
        $this->setApiResponses(new ApiResponses());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return array|bool|Response|static
     * @throws Exception
     */
    public function aRetrieveTranslation(Request $request, Response $response, $args)
    {
        // Pass the response to the apiResponses class
        $this->apiResponses()->setResponse($response);
        // Check if token is set
        $guid = isset($args['guid']) ? trim($args['guid']) : null;
        $token = isset($args['token']) ? trim($args['token']) : null;

        $response = $this->validateAccount($token);
        if (is_object($response)) {
            return $response;
        }

        return $this->retrieveTranslation($guid);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return array|bool|Response|static
     * @throws Exception
     */
    public function aRetrieveTranslationWithQes(Request $request, Response $response, $args)
    {
        // Pass the response to the apiResponses class
        $this->apiResponses()->setResponse($response);
        // Check if token is set
        $guid = isset($args['guid']) ? trim($args['guid']) : null;
        $token = isset($args['token']) ? trim($args['token']) : null;

        $response = $this->validateAccount($token);
        if (is_object($response)) {
            return $response;
        }

        return $this->retrieveTranslation($guid, UrlConfig::METHOD_ATRANSLATE_WITH_QES_ID);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return array|bool|Response|static
     * @throws Exception
     */
    private function retrieveTranslation($guId, $methodId = null)
    {
        if (empty($guId)) {
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
            $this->apiResponses()->setCode(ApiResponses::MISSING_GUID_CODE);
            $this->apiResponses()->setMessage('Missing <guid>');
        } else {
            $asynchronousRequests = new AsynchronousRequests(null, $guId);
            // If there is more than one translation we deal with the translatebestof method
            if ($asynchronousRequests->getMultipleTranslation()) {
                // Based on the consumer token or supplierId select supplier
                $relations = new Relations();
                $relations->setConsumerAccountId($this->getAccount()->getId());
                $allSuppliers = $relations->getConsumerSuppliers();

                $requests = $asynchronousRequests->get();

                if (is_array($requests)) {
                    $tranlsatedRequests = 0;
                    $translationResponse = null;
                    $segments = null;
                    $segmentsForRank = null;

                    // Prepare segments for ranking
                    foreach ($requests as $request) {
                        if (!empty($request['status'])) {
                            $translatedSegments = json_decode($request['translation'], true);
                            $segments = empty($segments) ? json_decode($request['text'], true) : $segments;

                            // If the request segments have been translated
                            if (!empty($translatedSegments)) {
                                foreach ($translatedSegments as $key => $translation) {
                                    reset($segments);
                                    $segmentsKey = key($segments);
                                    $segment = empty($segments[$segmentsKey]) ? '' : $segments[$segmentsKey];
                                    unset($segments[$segmentsKey]);
                                    $supplierId = empty($request['supplieraccountid']) ? '' : $request['supplieraccountid'];
                                    $supplierName = empty($allSuppliers[$supplierId]['name']) ? '' : $allSuppliers[$supplierId]['name'];

                                    $translationResponse[$segmentsKey][$tranlsatedRequests] = [
                                        'segment' => $segment,
                                        'translation' => $translation,
                                        'supplierId' => $supplierId,
                                        'supplierName' => $supplierName
                                    ];

                                    $segmentsForRank[$segmentsKey][$tranlsatedRequests] = $translation;
                                }
                            }

                            $tranlsatedRequests++;
                        } else {
                            // Defualt response
                            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_201_CODE);
                            $this->apiResponses()->setCode(ApiResponses::AWAITING_TRANSLATION);
                            $this->apiResponses()->setMessage('Awaiting translation');

                            break;
                        }
                    }

                    if ($tranlsatedRequests == count($requests)) {
                        $segmentsData = null;

                        // Rank the segments and return the best one
                        if (!empty($segmentsForRank) && is_array($segmentsForRank)) {
                            // Load model for scoring
                            $source = explode( '-', $asynchronousRequests->getSource());
                            $source = empty($source[0]) ? '' : $source[0];
                            $classifier = new Classifier('Classifier/qes-' . $source . '.svm');
                            foreach ($segmentsForRank as $key => $segments) {
                                $theBestSegmentKey = $classifier->rankTexts($segments);
                                $segmentsData[$key] = $translationResponse[$key][$theBestSegmentKey];
                            }
                        }

                        $data = [
                            "segments" => $segmentsData
                        ];

                        // Lets update the request time
                        $asynchronousRequests->setRequestTime(date('Y-m-d H:i:s'));
                        $asynchronousRequests->updateMultipleTranslationRequestTime();

                        $this->apiResponses()->setData($data);
                    }
                } else {
                    $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
                    $this->apiResponses()->setCode(ApiResponses::INVALID_GUID_CODE);
                    $this->apiResponses()->setMessage('Invalid <guid>');
                }
            } else {
                if ($asynchronousRequests->getStatus() == ApiResponses::HTTP_200_CODE) {
                    $segments = json_decode($asynchronousRequests->getText(), true);
                    $methodId = empty($methodId) ? $asynchronousRequests->getMethodId() : $methodId;
                    $translatedSegments = json_decode($asynchronousRequests->getTranslation(), true);

                    // If the request segments have been translated
                    if (!empty($translatedSegments)) {
                        $translationResponse = null;

                        if (is_array($segments)) {
                            foreach ($segments as $key => $segment) {
                                $translation = array_shift($translatedSegments);

                                $translationResponse[$key] = [
                                    'segment' => $segment,
                                    'translation' => $translation,
                                ];

                                // If the asynchronous request was for translation with QES, calculate and return the QES
                                if ($methodId == UrlConfig::METHOD_ATRANSLATE_WITH_QES_ID) {
                                    // Load model for scoring
                                    $source = explode( '-', $asynchronousRequests->getSource());
                                    $source = empty($source[0]) ? '' : $source[0];
                                    $classifier = new Classifier('Classifier/qes-' . $source . '.svm');

                                    $translationResponse[$key]['qes'] = $classifier->generateQESScore($translation);
                                }
                            }
                        }

                        $data = [
                            "segments" => $translationResponse
                        ];

                        // Lets update the request time
                        $asynchronousRequests->setRequestTime(date('Y-m-d H:i:s'));
                        $asynchronousRequests->update();

                        $this->apiResponses()->setData($data);
                    }
                } else {
                    if (empty($asynchronousRequests->getStatus()) && empty($asynchronousRequests->getText())) {
                        $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
                        $this->apiResponses()->setCode(ApiResponses::INVALID_GUID_CODE);
                        $this->apiResponses()->setMessage('Invalid <guid>');
                    } else {
                        if (!empty($asynchronousRequests->getStatus())) {
                            $this->apiResponses()->setStatusCode($asynchronousRequests->getStatus());
                            $this->apiResponses()->setCode(ApiResponses::UNDEFINED_ERROR);
                            $this->apiResponses()->setMessage($asynchronousRequests->getError());
                        } else {
                            // Defualt response
                            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_201_CODE);
                            $this->apiResponses()->setCode(ApiResponses::AWAITING_TRANSLATION);
                            $this->apiResponses()->setMessage('Awaiting translation');
                        }
                    }
                }
            }
        }

        return $this->apiResponses()->get();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return ApiDev|array|bool|Response|static
     */
    public function aTranslate(Request $request, Response $response, $args)
    {
        // Pass the response to the apiResponses class
        $this->apiResponses()->setResponse($response);
        $methodId = UrlConfig::METHOD_ATRANSLATE_ID;
        $supplierGuId = null;
        $guId = UUID::v4();
        // Get post data
        $postData = $request->getParams();

        $response = $this->validateRequest($postData);

        if (is_object($response)) {
            $this->getRequestLog()->setResponse($this->apiResponses()->getToJsonArray());
            $this->getRequestLog()->setHttpCode($this->apiResponses()->getStatusCode());

            return $response;
        }

        $response = $this->setSupplierAndEngine();

        if (is_object($response)) {
            $this->getRequestLog()->setResponse($this->apiResponses()->getToJsonArray());
            $this->getRequestLog()->setHttpCode($this->apiResponses()->getStatusCode());

            return $response;
        }

        // Store the request in the db and return a unique id
        $asynchronousRequests = new AsynchronousRequests();
        $asynchronousRequests->setGuId($guId);
        $asynchronousRequests->setConsumerAccountid($this->getAccount()->getId());
        $asynchronousRequests->setSupplierAccountId($this->getSupplierAccountId());
        $asynchronousRequests->setEngineName($this->getEngineName());
        $asynchronousRequests->setEngineCustomId($this->getEngineCustomId());
        $asynchronousRequests->setText(json_encode($this->getSegments()));
        $asynchronousRequests->setSource($this->getSource());
        $asynchronousRequests->setTarget($this->getTarget());
        $asynchronousRequests->setDomain($this->getDomainId());
        $asynchronousRequests->setSupplierGuId($supplierGuId);
        $asynchronousRequests->setMethodId($methodId);
        $id = $asynchronousRequests->insert();
        $asynchronousRequests->setId($id);
        // Set the guid
        $this->setGuId($guId);

        //Translate the segments with all the options
        $supplierGuId = $this->makeRequest(UrlConfig::METHOD_ATRANSLATE_ID);

        if (is_object($supplierGuId)) {
            $methodId = UrlConfig::METHOD_TRANSLATE_ID;
            $supplierGuId = null;
            // Reset response
            $this->apiResponses()->setStatusCode(null);
            $this->apiResponses()->setCode(null);
            $this->apiResponses()->setMessage(null);
        } elseif (is_numeric($supplierGuId) && $supplierGuId < 0) {
            $asynchronousRequests->delete();
            // this case is for eTransaltion error handling only.
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_500_CODE);
            $this->apiResponses()->setCode($supplierGuId);
            $this->apiResponses()->setMessage('Unexpected error occured');

            return $this->apiResponses()->get();
        }

        // Update asycnhrounus request table
        $asynchronousRequests->setMethodId($methodId);
        $asynchronousRequests->setSupplierGuId($supplierGuId);
        $asynchronousRequests->update();

        // If the request segments have been translated
        if (!empty($guId) && !empty($id)) {
            $data = [
                "guid" => $guId
            ];

            $this->apiResponses()->setData($data);
        } else {
            // token is invalid - return error
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_500_CODE);
            $this->apiResponses()->setCode(ApiResponses::UNDEFINED_ERROR);
            $this->apiResponses()->setMessage('Unexpected error occured');
        }

        return $this->apiResponses()->get();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Api|ApiDev|array|bool|Response|static
     * @throws Exception
     */
    public function translateWithQes(Request $request, Response $response, $args)
    {
        // Pass the response to the apiResponses class
        $this->apiResponses()->setResponse($response);
        // Get post data
        $postData = $request->getParams();
        $response = $this->validateRequest($postData);

        if (is_object($response)) {
            return $response;
        }

        $response = $this->setSupplierAndEngine();
        if (is_object($response)) {
            return $response;
        }

        //Translate the segments with all the options
        $translatedSegments = $this->makeRequest(UrlConfig::METHOD_TRANSLATE_ID);

        if (is_object($translatedSegments)) {
            return $translatedSegments;
        }

        // If the request segments have been translated
        if (!empty($translatedSegments)) {
            $translationResponse = null;

            if (is_array($this->getSegments())) {
                // Load model for scoring
                $classifier = new Classifier('Classifier/qes-' . $this->getSource() . '.svm');

                foreach ($this->getSegments() as $key => $segment) {
                    $translation = array_shift($translatedSegments);

                    $translationResponse[$key] = [
                        'segment' => $segment,
                        'translation' => $translation,
                        'qes' => $classifier->generateQESScore($translation)
                    ];
                }
            }

            $data = [
                "segments" => $translationResponse,
                "debug" => [
                    "supplierId" => $this->getSupplierAccountId(),
                    "engineName" => $this->getEngineName()
                ]
            ];

            $this->apiResponses()->setData($data);

            $this->storeStatistics(UrlConfig::METHOD_TRANSLATE_ID);
        }

        return $this->apiResponses()->get();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return ApiDev|array|bool|Response|static
     */
    public function detectLanguage(Request $request, Response $response, $args)
    {
        // Pass the response to the apiResponses class
        $this->apiResponses()->setResponse($response);
        // Get post data
        $postData = $request->getParams();
        $this->getRequestLog()->setRequest($postData);
        $this->getRequestLog()->setMethodId(UrlConfig::METHOD_DETECT_LANGUAGE_ID);

        $response = $this->validateRequest(
            $postData,
            true,
            false,
            false,
            false
        );

        if (is_object($response)) {
            return $response;
        }

        // Detect the language
        $classifier = new Classifier('Classifier/languages.svm');
        $segments = empty($this->getSegments()) ? null : $this->getSegments();
        $segments = is_array($segments) ? implode(' ', $segments) : $segments;
        $languageCode = $classifier->classifyText($segments);

        if ($languageCode) {
            // Get languages
            $languages = new Languages();
            $languageList = $languages->getAll();

            $languageName = empty($languageList[$languageCode]) ? Session::t('Unknown') : $languageList[$languageCode]['name'];

            $data = [
                'language' => [
                    'code' => $languageCode,
                    'name' => $languageName
                ]
            ];

            $this->apiResponses()->setData($data);
        } else {
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
            $this->apiResponses()->setMessage('Unknown language');
        }

        $this->getRequestLog()->setSrc($languageCode);
        $this->getRequestLog()->setResponse($this->apiResponses()->getToJsonArray());
        $this->getRequestLog()->setHttpCode($this->apiResponses()->getStatusCode());
        $this->getRequestLog()->insert();

        return $this->apiResponses()->get();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return ApiDev|array|bool|Response|static
     */
    public function detectDomain(Request $request, Response $response, $args)
    {
        // Pass the response to the apiResponses class
        $this->apiResponses()->setResponse($response);
        // Get post data
        $postData = $request->getParams();
        $this->getRequestLog()->setRequest($postData);
        $this->getRequestLog()->setMethodId(UrlConfig::METHOD_DETECT_DOMAIN_ID);

        $response = $this->validateRequest(
            $postData,
            true,
            false,
            false,
            false
        );

        if (is_object($response)) {
            return $response;
        }

        // Detect domain
        $domainModels = new DomainModels();
        $domainModels->setAccountId($this->getAccount()->getId());
        $svmModel = $domainModels->getAccountModel();
        $classifier = new Classifier(null, $svmModel);
        $segments = empty($this->getSegments()) ? null : $this->getSegments();
        $segments = is_array($segments) ? implode(' ', $segments) : $segments;
        $domain = $classifier->classifyText($segments);
        $domains = new Domains($domain);

        if ($domains->getName()) {
            $data[] = [
                'domain' => $domains->getName()
            ];

            $this->apiResponses()->setData($data);
        } else {
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
            $this->apiResponses()->setMessage('No domain available');
        }

        $this->getRequestLog()->setResponse($this->apiResponses()->getToJsonArray());
        $this->getRequestLog()->setHttpCode($this->apiResponses()->getStatusCode());
        $this->getRequestLog()->insert();

        return $this->apiResponses()->get();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return array|bool|Response|static
     */
    public function describeLanguages(Request $request, Response $response, $args)
    {
        // Pass the response to the apiResponses class
        $this->apiResponses()->setResponse($response);
        // Check if token is set
        $token = isset($args['token']) ? trim($args['token']) : null;

        $response = $this->validateAccount($token);
        if (is_object($response)) {
            return $response;
        }

        $data = ['languages' => []];

        $languages = new Languages();
        $languageList = $languages->getAll();

        if ($languageList && is_array($languageList)) {
            foreach ($languageList as $language) {
                $data['languages'][] = [
                    'code' => $language['code'],
                    'name' => $language['name'],
                ];
            }
        }

        // Set data response
        $this->apiResponses()->setData($data);

        return $this->apiResponses()->get();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return array|bool|Response|static
     */
    public function describeSuppliers(Request $request, Response $response, $args)
    {
        // Pass the response to the apiResponses class
        $this->apiResponses()->setResponse($response);
        // Check if token is set
        $token = isset($args['token']) ? trim($args['token']) : null;

        $response = $this->validateAccount($token);
        if (is_object($response)) {
            return $response;
        }

        $data = ['suppliers' => []];

        $relations = new Relations();
        $accountId = empty($this->getAccount()->getId()) ? null : $this->getAccount()->getId();
        $relations->setConsumerAccountId($accountId);
        $consumerSuppliers = $relations->getConsumerSuppliers();

        if ($consumerSuppliers && is_array($consumerSuppliers)) {
            // Get account names
            $accounts = new Accounts();
            $accountsList = $accounts->getAll();
            // Get domain names
            $domains = new Domains();
            $domainsList = $domains->getAll();

            foreach ($consumerSuppliers as $supplier) {
                $supplierAccountId = empty($supplier['supplieraccountid']) ? null : $supplier['supplieraccountid'];
                // get engines
                $enginesList = [];
                $engines = new Engines();
                $engines->setAccountId($supplierAccountId);
                $supplierEngines = $engines->getAllAccountEngines();

                if ($supplierEngines && is_array($supplierEngines)) {
                    foreach ($supplierEngines as $engine) {
                        $enginesList[] = [
                            'name' => $engine['name'],
                            'source' => $engine['src'],
                            'target' => $engine['trg'],
                            'domain' => empty($domainsList[$engine['domainid']]['name']) ? '' : $domainsList[$engine['domainid']]['name'],
                            'ter' => $engine['ter'],
                            'bleu' => $engine['bleu'],
                            'fmeasure' => $engine['fmeasure']
                        ];
                    }
                }

                $data['suppliers'][] = [
                    'id' => $supplierAccountId,
                    'name' => empty($accountsList[$supplierAccountId]['name']) ? 'n/a' : $accountsList[$supplierAccountId]['name'],
                    'engines' => $enginesList
                ];
            }
        }

        // Set data response
        $this->apiResponses()->setData($data);

        return $this->apiResponses()->get();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Api|ApiDev|array|bool|Response|static
     */
    public function retrieveFileTranslation(Request $request, Response $response, $args)
    {
        // Pass the response to the apiResponses class
        $this->apiResponses()->setResponse($response);
        // Get post data
        $postData = $request->getParams();

        $response = $this->validateRequest(
            $postData,
            false,
            false,
            false,
            false,
            false,
            true
        );

        if (is_object($response)) {
            return $response;
        }

        // Check the guid
        $asynchronousRequests = new AsynchronousRequests(null, $this->getGuId());

        if (empty($asynchronousRequests->getGuId())) {
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
            $this->apiResponses()->setCode(ApiResponses::MISSING_GUID_CODE);
            $this->apiResponses()->setMessage('Missing <guid>');
        } else {
            if ($asynchronousRequests->getConsumerAccountid() != $this->getAccount()->getId()) {
                $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
                $this->apiResponses()->setCode(ApiResponses::INVALID_GUID_CODE);
                $this->apiResponses()->setMessage('Invalid <guid>');
            } else {
                // If the file translation was made by method with a callback (aTransalteFile) check the database for result
                if ($asynchronousRequests->getMethodId() == UrlConfig::METHOD_ATRANSLATE_FILE_ID) {
                    $translatedSegments = json_decode($asynchronousRequests->getTranslation(), true);

                    // If the request segments have been translated
                    if (!empty($translatedSegments)) {
                        $data = [
                            "guid" => $asynchronousRequests->getGuId(),
                            "fileType" => $asynchronousRequests->getFileType(),
                            "file" => is_array($translatedSegments) ? $translatedSegments [0] : $translatedSegments
                        ];

                        $this->apiResponses()->setData($data);

                        // Lets update the request time
                        $asynchronousRequests->setRequestTime(date('Y-m-d H:i:s'));
                        $asynchronousRequests->update();

                        $this->apiResponses()->setData($data);
                    } else {
                        // Defualt response
                        $this->apiResponses()->setStatusCode(ApiResponses::HTTP_201_CODE);
                        $this->apiResponses()->setCode(ApiResponses::AWAITING_TRANSLATION);
                        $this->apiResponses()->setMessage('Awaiting translation');
                    }
                } else {
                    // Get access details
                    $relations = new Relations();
                    $relations->setSupplierAccountId($asynchronousRequests->getSupplierAccountId());
                    $relations->setConsumerAccountId($asynchronousRequests->getConsumerAccountid());
                    $details = $relations->getSupplierConsumerRelation();
                    $relations->set($details);

                    $this->setSupplierToken(Encryption::decrypt($relations->getApiToken(), $relations->getToken()));
                    $this->setUserName($relations->getUserName());
                    $this->setPassword(Encryption::decrypt($relations->getPassword(), $relations->getToken()));
                    $this->setSupplierAccountId($asynchronousRequests->getSupplierAccountId());
                    $this->setEngineName($asynchronousRequests->getEngineName());
                    $this->setEngineCustomId($asynchronousRequests->getEngineCustomId());
                    $this->setSegments($asynchronousRequests->getText());
                    $this->setSource($asynchronousRequests->getSource());
                    $this->setTarget($asynchronousRequests->getTarget());
                    $this->setDomainName($asynchronousRequests->getDomain());
                    $this->setFile($this->getFile());
                    $this->setFileType($asynchronousRequests->getFileType());
                    $this->setGuId($asynchronousRequests->getSupplierGuId());

                    $response = $this->makeRequest(UrlConfig::METHOD_RETRIEVE_FILE_TRANSLATION_ID);

                    if (is_object($response)) {
                        return $response;
                    }

                    // Store the request in the db and return a unique id
                    $asynchronousRequests->setRequestTime(date('Y-m-d H:i:s'));
                    $asynchronousRequests->setStatus(ApiResponses::HTTP_200_CODE);
                    $asynchronousRequests->update();

                    // If the request segments have been translated
                    if ($response) {
                        $data = [
                            "guid" => $asynchronousRequests->getGuId(),
                            "fileType" => $asynchronousRequests->getFileType(),
                            "file" => is_array($response) ? $response[0] : $response
                        ];

                        $this->apiResponses()->setData($data);
                    } else {
                        // token is invalid - return error
                        $this->apiResponses()->setStatusCode(ApiResponses::HTTP_500_CODE);
                        $this->apiResponses()->setCode(ApiResponses::UNDEFINED_ERROR);
                        $this->apiResponses()->setMessage('Unexpected error occured');
                    }
                }
            }
        }

        return $this->apiResponses()->get();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Api|ApiDev|array|bool|Response|static
     */
    public function translate(Request $request, Response $response, $args)
    {
        // Pass the response to the apiResponses class
        $this->apiResponses()->setResponse($response);
        // Get post data
        $postData = $request->getParams();
        // Store debug details in the database
        $this->getRequestLog()->setRequest($postData);
        $this->getRequestLog()->setMethodId(UrlConfig::METHOD_TRANSLATE_ID);

        $response = $this->validateRequest($postData);

        if (is_object($response)) {
            $this->getRequestLog()->setResponse($this->apiResponses()->getToJsonArray());
            $this->getRequestLog()->setHttpCode($this->apiResponses()->getStatusCode());

            return $response;
        }

        $response = $this->setSupplierAndEngine();

        if (is_object($response)) {
            $this->getRequestLog()->setResponse($this->apiResponses()->getToJsonArray());
            $this->getRequestLog()->setHttpCode($this->apiResponses()->getStatusCode());

            return $response;
        }

        // Check if local cache enabled and the segments are cached
        if ($this->getCache()) {
            $cache = new Cache();
            $cache->setEngineId($this->getEngineId());
            $cache->setSupplierAccountId($this->getSupplierAccountId());
            $cache->setSegments($this->getSegments());
            $cache->setDomainId($this->getDomainId());
            $cache->setSrc($this->getSource());
            $cache->setTrg($this->getTarget());
            $translatedSegments = $cache->getCachedTranslatedSegments();
        }

        // Check for ActiviaTM. We use ActiviaTM only for a single segments.
        if ($this->getActiviaTm()) {
            $activiaTm = new ActviaTM($this->getActiviaTmUserName(), $this->getActiviaTmPassword());
            $activiaTm->setDomain($this->getDomainName());
            $activiaTm->setSrc($this->getSource());
            $activiaTm->setTrg($this->getTarget());

            if (is_array($this->getSegments()) && count($this->getSegments()) == 1) {
                $segmetns = $this->getSegments();
                $activiaTm->setSegment(reset($segmetns));
                $translatedSegments = $activiaTm->getTM();
            }
        }

        if (empty($translatedSegments)) {
            //Translate the segments with all the options
            $translatedSegments = $this->makeRequest(UrlConfig::METHOD_TRANSLATE_ID);

            // store the segments in a cache
            if ($this->getCache()) {
                $cache->setTranslatedSegments($translatedSegments);
                $cache->insert();
            }
        }

        if (is_object($translatedSegments)) {
            $this->getRequestLog()->setResponse($this->apiResponses()->getToJsonArray());
            $this->getRequestLog()->setHttpCode($this->apiResponses()->getStatusCode());
            $this->getRequestLog()->insert();

            return $translatedSegments;
        }

        // If the request segments have been translated
        if (!empty($translatedSegments)) {
            $translationResponse = null;

            if (is_array($this->getSegments())) {
                foreach ($this->getSegments() as $key => $segment) {
                    $translation = array_shift($translatedSegments);
                    $translationResponse[$key] = [
                        'segment' => $segment,
                        'translation' => $translation,
                    ];

                    if ($this->getActiviaTm()) {
                        $activiaTm->setSegment($segment);
                        $activiaTm->setTranslation($translation);
                        $activiaTm->addTM();
                    }
                }
            }

            $data = [
                "segments" => $translationResponse,
                "debug" => [
                    "supplierId" => $this->getSupplierAccountId(),
                    "engineName" => $this->getEngineName()
                ]
            ];

            $this->apiResponses()->setData($data);

            // Store statistics
            $this->storeStatistics(UrlConfig::METHOD_TRANSLATE_ID);
        }

        $this->getRequestLog()->setResponse($this->apiResponses()->getToJsonArray());
        $this->getRequestLog()->setHttpCode($this->apiResponses()->getStatusCode());
        $this->getRequestLog()->insert();

        return $this->apiResponses()->get();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Api|ApiDev|array|bool|Response|static
     */
    public function translateFile(Request $request, Response $response, $args)
    {
        // Pass the response to the apiResponses class
        $this->apiResponses()->setResponse($response);
        $guId = UUID::v4();
        $this->setGuId($guId);
        $methodId = UrlConfig::METHOD_TRANSLATE_FILE_ID;
        // Get post data
        $postData = $request->getParams();

        $response = $this->validateRequest(
            $postData,
            false,
            true,
            true,
            true,
            true
        );

        if (is_object($response)) {
            return $response;
        }

        $response = $this->setSupplierAndEngine();

        if (is_object($response)) {
            return $response;
        }

        if (strlen($this->getFile()) > 0) {
            $response = $this->makeRequest($methodId);
            if (is_object($response)) {
                // Reset error
                $this->apiResponses()->setStatusCode(ApiResponses::HTTP_200_CODE);
                $this->apiResponses()->setCode(null);
                $this->apiResponses()->setMessage(null);

                $methodId = UrlConfig::METHOD_ATRANSLATE_FILE_ID;
                $supplierGuId = $this->makeRequest($methodId);

                if (is_object($supplierGuId)) {
                    return $supplierGuId;
                } elseif (is_numeric($supplierGuId) && $supplierGuId < 0) {
                    // this case is for eTransaltion error handling only.
                    $this->apiResponses()->setStatusCode(ApiResponses::HTTP_500_CODE);
                    $this->apiResponses()->setCode($supplierGuId);
                    $this->apiResponses()->setMessage('Unexpected error occured');

                    return $this->apiResponses()->get();
                }
            }

            // Store the request in the db and return a unique id
            $asynchronousRequests = new AsynchronousRequests();
            $asynchronousRequests->setGuId($guId);
            $asynchronousRequests->setConsumerAccountid($this->getAccount()->getId());
            $asynchronousRequests->setSupplierAccountId($this->getSupplierAccountId());
            $asynchronousRequests->setEngineName($this->getEngineName());
            $asynchronousRequests->setEngineCustomId($this->getEngineCustomId());
            $asynchronousRequests->setSupplierGuId(is_array($response) ? $response[0] : $response);
            $asynchronousRequests->setFileType($this->getFileType());
            $asynchronousRequests->setRequestTime(date("Y-m-d H:i:s"));
            $asynchronousRequests->setSource($this->getSource());
            $asynchronousRequests->setTarget($this->getTarget());
            $asynchronousRequests->setDomain($this->getDomainId());
            $asynchronousRequests->setMethodId($methodId);
            $asynchronousRequests->setSupplierGuId(empty($supplierGuId) ? null : $supplierGuId);
            $id = $asynchronousRequests->insert();
            $asynchronousRequests->setId($id);

            // If the request segments have been translated
            if (!empty($guId) && !empty($id)) {
                $data = [
                    "guid" => $guId
                ];

                $this->apiResponses()->setData($data);
            } else {
                // token is invalid - return error
                $this->apiResponses()->setStatusCode(ApiResponses::HTTP_500_CODE);
                $this->apiResponses()->setCode(ApiResponses::UNDEFINED_ERROR);
                $this->apiResponses()->setMessage('Unexpected error occured');
            }
        } else {
            // error reading content
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_500_CODE);
            $this->apiResponses()->setCode(ApiResponses::ERROR_CONTENT);
            $this->apiResponses()->setMessage('Couldn\'t get the content');
        }

        return $this->apiResponses()->get();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return ApiDev|array|bool|Response|static
     * @throws Exception
     */
    public function translateBestOf(Request $request, Response $response, $args)
    {
        // Pass the response to the apiResponses class
        $this->apiResponses()->setResponse($response);
        // Get post data
        $postData = $request->getParams();
        $response = $this->validateRequest($postData);

        if (is_object($response)) {
            return $response;
        }

        // Based on the consumer token or supplierId select supplier
        $relations = new Relations();
        $relations->setConsumerAccountId($this->getAccount()->getId());
        $allSuppliers = $relations->getConsumerSuppliers();

        $supplierAccountId = null;
        $supplierToken = null;
        // Suppliers id
        $supplierIds = [];

        if (is_array($allSuppliers)) {
            foreach ($allSuppliers as $supplier) {
                $supplierIds[] = $supplier['supplieraccountid'];
            }
        };

        // Find an engine
        $engines = new Engines();
        $engines->setSource($this->getSource());
        $engines->setTarget($this->getTarget());
        $engineList = $engines->getEnginesForApi($supplierIds, $this->getDomainId());

        if (!$engineList) {
            // token is invalid - return error
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
            $this->apiResponses()->setCode(ApiResponses::ENGINE_NOT_AVAILBALE);
            $this->apiResponses()->setMessage('No available engine for selected language pair');

            return $this->apiResponses()->get();
        }

        $translatedSegmentsBySupplier = null;
        $guId = UUID::v4();

        if (is_array($engineList)) {
            foreach ($engineList as $engineDetails) {
                $methodId = UrlConfig::METHOD_ATRANSLATE_ID;
                $supplierGuId = null;

                $this->setEngineCustomId($engineDetails['customid']);
                $this->setEngineName($engineDetails['name']);
                $this->setSupplierAccountId($allSuppliers[$engineDetails['accountid']]['supplieraccountid']);
                $this->setSupplierToken($allSuppliers[$engineDetails['accountid']]['supplierapitoken']);

                // Store the request in the db and return a unique id
                $asynchronousRequests = new AsynchronousRequests();
                $asynchronousRequests->setGuId($guId);
                $asynchronousRequests->setConsumerAccountid($this->getAccount()->getId());
                $asynchronousRequests->setSupplierAccountId($allSuppliers[$engineDetails['accountid']]['supplieraccountid']);
                $asynchronousRequests->setEngineName($engineDetails['name']);
                $asynchronousRequests->setEngineCustomId($engineDetails['customid']);
                $asynchronousRequests->setText(json_encode($this->getSegments()));
                $asynchronousRequests->setSource($this->getSource());
                $asynchronousRequests->setTarget($this->getTarget());
                $asynchronousRequests->setDomain($this->getDomainId());
                $asynchronousRequests->setSupplierGuId($supplierGuId);
                $asynchronousRequests->setMethodId($methodId);
                $id = $asynchronousRequests->insert();
                $asynchronousRequests->setId($id);
                // Set the guid
                $this->setGuId($guId);

                //Translate the segments with all the options
                $supplierGuId = $this->makeRequest(UrlConfig::METHOD_ATRANSLATE_ID);

                if (is_object($supplierGuId)) {
                    $methodId = UrlConfig::METHOD_TRANSLATE_ID;
                    $supplierGuId = null;
                } elseif (is_numeric($supplierGuId) && $supplierGuId < 0) {
                    $asynchronousRequests->delete();
                }

                // Reset response
                $this->apiResponses()->setStatusCode(null);
                $this->apiResponses()->setCode(null);
                $this->apiResponses()->setMessage(null);

                // Update asycnhrounus request table
                $asynchronousRequests->setMethodId($methodId);
                $asynchronousRequests->setSupplierGuId($supplierGuId);
                $asynchronousRequests->update();
            }
        }

        // If the request segments have been translated
        if (!empty($guId) && !empty($id)) {
            $data = [
                "guid" => $guId
            ];

            $this->apiResponses()->setData($data);
        } else {
            // token is invalid - return error
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_500_CODE);
            $this->apiResponses()->setCode(ApiResponses::UNDEFINED_ERROR);
            $this->apiResponses()->setMessage('Unexpected error occured');
        }

        return $this->apiResponses()->get();
    }

    /**
     * Validate POST request data
     *
     * @param $postData
     * @param bool $validateSegments
     * @param bool $validateSource
     * @param bool $validateTarget
     * @param bool $validateDomain
     * @param bool $validateFile
     * @param bool $validateGuid
     * @return ApiDev|array|bool
     */
    public function validateRequest(
        $postData,
        $validateSegments = true,
        $validateSource = true,
        $validateTarget = true,
        $validateDomain = true,
        $validateFile = false,
        $validateGuid = false
    ) {
        // Check if token is set
        $token = isset($postData['token']) && !is_array($postData['token']) ? trim($postData['token']) : null;
        $source = isset($postData['source']) && !is_array($postData['source']) ? trim($postData['source']) : null;
        $target = isset($postData['target']) && !is_array($postData['target']) ? trim($postData['target']) : null;
        $domain = isset($postData['domain']) && !is_array($postData['domain']) ? trim($postData['domain']) : null;
        $file = isset($postData['file']) && !is_array($postData['file']) ? trim($postData['file']) : null;
        $fileType = isset($postData['fileType']) && !is_array($postData['fileType']) ? trim($postData['fileType']) : null;
        $segments = isset($postData['segments']) ? $postData['segments'] : null;
        $supplierId = isset($postData['supplierId']) ? trim($postData['supplierId']) : null;
        $this->setSupplierAccountId($supplierId);
        $contentType = isset($postData['contentType']) ? trim($postData['contentType']) : null;
        $this->setConentType($contentType);
        $guId = isset($postData['guid']) ? trim($postData['guid']) : null;

        // Validate API token
        $response = $this->validateAccount($token);
        if (is_object($response)) {
            return $response;
        }

        // Validate segments
        if ($validateSegments) {
            if (!is_array($segments)) {
                $segments = [$segments];
            }

            $response = $this->validateSegments($segments);
            if (is_object($response)) {
                return $response;
            }

            $this->setSegments($segments);
        }

        // Validate source
        if ($validateSource) {
            $response = $this->validateSource($source);
            if (is_object($response)) {
                return $response;
            } else {
                if (!$response) {
                    // Autodetect language
                    $classifier = new Classifier('Classifier/languages.svm');
                    $source = $classifier->classifyText(is_array($segments) ? implode(' ', $segments) : $segments);
                }

                $this->setSource($source);
            }
        }

        // Validate target
        if ($validateTarget) {
            $response = $this->validateTarget($target);
            if (is_object($response)) {
                return $response;
            }

            $this->setTarget($target);
        }

        // Validate domain
        if ($validateDomain) {
            $response = $this->validateDomain($domain, $source);
            if (is_object($response)) {
                return $response;
            }

            $this->setDomainId(empty($response['id']) ? null : $response['id']);
            $this->setDomainName(empty($response['name']) ? null : $response['name']);
        }

        // Validate file
        if ($validateFile) {
            $response = $this->validateFile($file, $fileType);
            if (is_object($response)) {
                return $response;
            }

            $this->setFile($file);
            $this->setFileType($fileType);
        }

        // Validate guid
        if ($validateGuid) {
            $response = $this->validateGuid($guId);
            if (is_object($response)) {
                return $response;
            }

            $this->setGuId($guId);
        }

        return true;
    }

    /**
     * @return array|bool|static
     */
    private function setSupplierAndEngine()
    {
        // Based on the consumer token or supplierId select supplier
        $relations = new Relations();
        $relations->setConsumerAccountId($this->getAccount()->getId());

        if (!$this->getSupplierAccountId()) {
            $allSuppliers = $relations->getConsumerSuppliers();

            $supplierAccountId = null;
            $supplierToken = null;
            // Suppliers id
            $supplierIds = [];

            if (is_array($allSuppliers)) {
                foreach ($allSuppliers as $supplier) {
                    $supplierIds[] = $supplier['supplieraccountid'];
                }
            };
        } elseif (!is_numeric($this->getSupplierAccountId())) {
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
            $this->apiResponses()->setCode(ApiResponses::INVALID_SUPPLIER_ID);
            $this->apiResponses()->setMessage('Invalid <supplierId>');

            return $this->apiResponses()->get();
        } else {
            $allSuppliers = $relations->getConsumerSuppliers($this->getSupplierAccountId());
            $supplierIds[] = $this->getSupplierAccountId();
        }

        // Find an engine
        $engines = new Engines();
        $engines->setSource($this->getSource());
        $engines->setTarget($this->getTarget());
        $engine = $engines->getEnginesForApi($supplierIds, $this->getDomainId());

        $engineName = empty($engine[0]['name']) ? null : $engine[0]['name'];
        $this->setEngineName($engineName);
        $engineId = empty($engine[0]['id']) ? null : $engine[0]['id'];
        $this->setEngineId($engineId);
        $engineCustomId = empty($engine[0]['customid']) ? null : $engine[0]['customid'];
        $this->setEngineCustomId($engineCustomId);
        // get Supplier token
        $supplierToken = null;

        if (!$engine) {
            // token is invalid - return error
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
            $this->apiResponses()->setCode(ApiResponses::ENGINE_NOT_AVAILBALE);
            $this->apiResponses()->setMessage('No available engine for selected language pair');

            return $this->apiResponses()->get();
        }

        if (!empty($allSuppliers)) {
            foreach ($allSuppliers as $supplier) {
                if ($engine[0]['accountid'] == $supplier['supplieraccountid']) {
                    $supplierToken = empty($supplier['apitoken']) || empty($supplier['token']) ? null : Encryption::decrypt($supplier['apitoken'],
                        $supplier['token']);
                    $this->setSupplierToken($supplierToken);
                    $userName = empty($supplier['username']) ? null : $supplier['username'];
                    $this->setUserName($userName);
                    $password = empty($supplier['password']) || empty($supplier['token']) ? null : Encryption::decrypt($supplier['password'],
                        $supplier['token']);
                    $this->setPassword($password);
                    $supplierAccountId = $supplier['supplieraccountid'];
                    $this->setSupplierAccountId($supplierAccountId);
                    $this->setCache($supplier['cache']);
                    $this->setActiviaTm($supplier['activiatm']);
                    $this->setActiviaTmUserName($supplier['activiatmusername']);
                    $this->setActiviaTmPassword(Encryption::decrypt($supplier['activiatmpassword'], $supplier['activiatmtoken']));

                    break;
                }
            }
        }

        return true;
    }

    /**
     * Store API statistics
     *
     * @param int $methodId
     */
    private function storeStatistics($methodId = UrlConfig::METHOD_TRANSLATE_ID)
    {
        // Calcualte request Word count
        $wordCount = WordCount::countWords($this->getSource(), $this->getSegments());

        // Store some statistics
        $statisticsTemporary = new StatisticsTemporary();
        $statisticsTemporary->setConsumerAccountId($this->getAccount()->getId());
        $statisticsTemporary->setSupplierAccountId($this->getSupplierAccountId());
        $statisticsTemporary->setEngineId($this->getEngineId());
        $statisticsTemporary->setMethodId($methodId);
        $statisticsTemporary->setRequestCount(1);
        $statisticsTemporary->setWordCount($wordCount);
        $statisticsTemporary->setTime(Helper::getMySqlCurrentTime());
        $statisticsTemporary->insert();

        return;
    }

    /**
     * Validate iADAATPA account token
     *
     * @param $token string
     * @return bool
     */
    private function validateAccount($token)
    {
        $accounts = new Accounts(null, $token);
        $this->setAccount($accounts);

        // The sessionId was not set so we translate using the other parameters
        // Validate the token
        if (empty($token)) {
            // token is missing - return error
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
            $this->apiResponses()->setCode(ApiResponses::MISSING_TOKEN_CODE);
            $this->apiResponses()->setMessage('Missing <token>');
        } else {
            if ($accounts->getId()) {
                if (!$accounts->getActive()) {
                    // Account inactive
                    $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
                    $this->apiResponses()->setCode(ApiResponses::ACCOUNT_INACTIVE);
                    $this->apiResponses()->setMessage('Account inactive');
                } else {
                    if ($accounts->getExpired() && $accounts->getExpired() <= date("Y-m-d H:i:s")) {
                        // Account expired
                        $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
                        $this->apiResponses()->setCode(ApiResponses::ACCOUNT_INACTIVE);
                        $this->apiResponses()->setMessage('Account expired');
                    } else {
                        return true;
                    }
                }
            } else {
                // token is invalid - return error
                $this->apiResponses()->setStatusCode(ApiResponses::HTTP_401_CODE);
                $this->apiResponses()->setCode(ApiResponses::INVALID_TOKEN_CODE);
                $this->apiResponses()->setMessage('Invalid <token>');
            }
        }

        return $this->apiResponses()->get();
    }

    /**
     * Validate given language code (ISO 639-1 standard)
     *
     * @param $code
     * @return array|bool|static
     */
    private function validateSource($code)
    {
        if (empty($code)) {
            // Missing lang code
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
            $this->apiResponses()->setCode(ApiResponses::INVALID_LANG_CODE_CODE);
            $this->apiResponses()->setMessage('Missing <source>');
        } else {
            $languages = new Languages(null, $code);

            if (!$languages->getId()) {
                // Missing lang code
                $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
                $this->apiResponses()->setCode(ApiResponses::INVALID_LANG_CODE_CODE);
                $this->apiResponses()->setMessage('Invalid source <source>');
            } else {
                return true;
            }
        }

        return $this->apiResponses()->get();
    }

    /**
     * Validate given language code (ISO 639-1 standard)
     *
     * @param $code
     * @return array|bool|static
     */
    private function validateTarget($code)
    {
        if (empty($code)) {
            // Missing lang code
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
            $this->apiResponses()->setCode(ApiResponses::INVALID_LANG_CODE_CODE);
            $this->apiResponses()->setMessage('Missing target <langcode>');
        } else {
            $languages = new Languages(null, $code);

            if (!$languages->getId()) {
                // Missing lang code
                $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
                $this->apiResponses()->setCode(ApiResponses::INVALID_LANG_CODE_CODE);
                $this->apiResponses()->setMessage('Invalid target <langcode>');
            } else {
                return true;
            }
        }

        return $this->apiResponses()->get();
    }

    /**
     * Validate file conent and type
     *
     * @param $file
     * @param $fileType
     * @return array|bool|static
     */
    private function validateFile($file, $fileType)
    {
        if (empty($file)) {
            // Missing file
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
            $this->apiResponses()->setCode(ApiResponses::MISSING_FILE);
            $this->apiResponses()->setMessage('Missing or not base64 encoded <file>');
        } elseif (empty($fileType)) {
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
            $this->apiResponses()->setCode(ApiResponses::MISSING_FILE_TYPE);
            $this->apiResponses()->setMessage('Missing <fileType>');
        } else {
            if (strlen($fileType) > 10) {
                $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
                $this->apiResponses()->setCode(ApiResponses::INVALID_FORMAT_LENGTH);
                $this->apiResponses()->setMessage('Invalid or too long <fileType>');
            } else {
                if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $file)) {
                    $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
                    $this->apiResponses()->setCode(ApiResponses::FILE_NOT_BASE64_ENCODED);
                    $this->apiResponses()->setMessage('<file> must be base64 encoded');
                } else {
                    return true;
                }
            }
        }

        return $this->apiResponses()->get();
    }

    /**
     * Validate given domain
     *
     * @param $domain
     * @return bool
     */
    private function validateDomain($domain, $source)
    {
        if (empty($domain)) {
            return false;
        } else {
            $domains = new Domains();
            $response = $domains->getDomainByNameAndSource($domain, $source);

            if (!$response) {
                $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
                $this->apiResponses()->setCode(ApiResponses::INVALID_DOMAIN_CODE);
                $this->apiResponses()->setMessage('Invalid <domain>');

                return $this->apiResponses()->get();
            }

            return empty($response) ? false : $response;
        }
    }

    /**
     * Validate GUID
     *
     * @param string $guid
     * @return bool
     */
    private function validateGuid($guid)
    {
        // We used uuid v4 so lets check it agains its format
        $UUIDv4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

        if (in_array(strlen($guid), [32, 36]) && preg_match($UUIDv4, $guid)) {
            return true;
        }

        return false;
    }

    /**
     * Validate segments
     *
     * @param $segments
     * @return array|bool|static
     */
    private function validateSegments($segments)
    {
        if (empty($segments)) {
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
            $this->apiResponses()->setCode(ApiResponses::MISSING_SEGMENT_CODE);
            $this->apiResponses()->setMessage('Missing <segments>');

            return $this->apiResponses()->get();
        } else {
            if (is_array($segments) && count($segments) >= self::SEGMENTS_LENGHT_LIMIT) {
                $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
                $this->apiResponses()->setCode(ApiResponses::SEGMENTS_LIMIT_CODE);
                $this->apiResponses()->setMessage('Too many <segments>. The segments limit is ' . self::SEGMENTS_LENGHT_LIMIT . '.');

                return $this->apiResponses()->get();
            } else {
                if (is_array($segments)) {
                    foreach ($segments as $key => $text) {
                        if (strlen($text) >= self::SEGMENT_SIZE_LIMIT) {
                            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
                            $this->apiResponses()->setCode(ApiResponses::SEGMENTS_SIZE_CODE);
                            $this->apiResponses()->setMessage('<segments> too long. The segment maximum size is ' . self::SEGMENTS_LENGHT_LIMIT . ' characters.');

                            return $this->apiResponses()->get();
                        }
                    }
                } else {
                    if (strlen($segments) >= self::SEGMENT_SIZE_LIMIT) {
                        $this->apiResponses()->setStatusCode(ApiResponses::HTTP_400_CODE);
                        $this->apiResponses()->setCode(ApiResponses::SEGMENTS_SIZE_CODE);
                        $this->apiResponses()->setMessage('<segments> too long. The segment maximum size is ' . self::SEGMENTS_LENGHT_LIMIT . ' characters.');

                        return $this->apiResponses()->get();
                    }
                }
            }
        }

        return true;
    }

    /**
     * Make a curl request to a Supplier for a given method id.
     *
     * @param $methodId
     * @return array|static
     */
    public function makeRequest($methodId)
    {
        // Get api configuration for given method id
        $urlConfig = new UrlConfig();
        $urlConfig->setAccountId($this->getSupplierAccountId());
        $configurations = $urlConfig->getAllByMethodId();
        $configuration = !empty($configurations[$methodId]) ? $configurations[$methodId] : null;
        $urlEndPoint = !empty($configuration['urlendpoint']) ? $configuration['urlendpoint'] : null;
        $request = !empty($configuration['request']) ? json_decode($configuration['request'], true) : null;
        $callBack = !empty($configuration['callback']) ? json_decode($configuration['callback'], true) : null;
        $header = !empty($configuration['header']) ? json_decode($configuration['header'], true) : null;
        $authorization = !empty($configuration['authorization']) ? json_decode($configuration['authorization'],
            true) : null;
        $responseFormat = !empty($configuration['response']) ? $configuration['response'] : null;

        // Set callback
        if (!empty($callBack)) {
            $callBackUrl = $this->populateData($callBack);
            $this->setCallBackUrl($callBackUrl);
        }

        // Set the request data array
        $data = null;
        if (is_array($request)) {
            $data = $this->populateData($request);
        }

        // Set the request header array
        $customHeader = null;
        if (is_array($header)) {
            $header = $this->populateData($header);

            if (is_array($header)) {
                foreach ($header as $key => $value) {
                    $customHeader[] = $key . ":" . $value;
                }
            }
        }

        $customHeader[] = "Content-Type: application/json";

        if ($urlEndPoint) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $urlEndPoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeader);

            if ($authorization == UrlConfig::AUTH_DIGEST) {
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
                curl_setopt($ch, CURLOPT_USERPWD, $this->getUserName() . ":" . $this->getPassword());
            } elseif ($authorization == UrlConfig::AUTH_BASIC) {
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, $this->getUserName() . ":" . $this->getPassword());
            }

            $response = curl_exec($ch);

            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpStatus == ApiResponses::HTTP_200_CODE) {
                if ($response) {
                    $response = json_decode($response, true);
                    $responseFormatArray = explode('/', $responseFormat);

                    if ($responseFormatArray && is_array($responseFormatArray)) {
                        foreach ($responseFormatArray as $key => $part) {
                            if ($part != '[]' && isset($response[$part])) {
                                unset($responseFormatArray[$key]);
                                $response = $response[$part];
                            } elseif ($part == '[]') {
                                unset($responseFormatArray[$key]);
                                break;
                            } else {
                                break;
                            }
                        }
                    }

                    $responseFormatLastElement = count($responseFormatArray) > 0 ? implode('',
                        $responseFormatArray) : null;
                    $result = [];
                    // At this point we are expecting only one or null elements in the responeFormatArray. If we have more,
                    // that means the response doesnt match the format and therfore we treat is as an error
                    if (count($responseFormatArray) > 1) {
                        // Error on the Supplier settings side
                        $this->apiResponses()->setStatusCode(ApiResponses::HTTP_500_CODE);
                        $this->apiResponses()->setCode(ApiResponses::SUPPLIER_ERROR);
                        $this->apiResponses()->setMessage($response);
                    } else {
                        if ($responseFormatLastElement && is_array($response)) {
                            foreach ($response as $seg) {
                                if (strpos($responseFormatLastElement, '[]') !== false) {
                                    $seg = reset($seg);
                                    $result[] = $seg[str_replace('[]', '', $responseFormatLastElement)];
                                } else {
                                    $result[] = $seg[$responseFormatLastElement];
                                }
                            }
                        } else {
                            $result = $response;
                        }

                        if (empty($result)) {
                            // Nothing returned
                            $this->apiResponses()->setStatusCode($httpStatus);
                            $this->apiResponses()->setCode(ApiResponses::SUPPLIER_ERROR);
                            $this->apiResponses()->setMessage(empty($response) ? "N/A" : $response);
                        } else {
                            return $result;
                        }
                    }
                }
            } else {
                // Error message recieved from a Supplier (everything that is not 200)
                $this->apiResponses()->setStatusCode($httpStatus);
                $this->apiResponses()->setCode(ApiResponses::SUPPLIER_ERROR);
                // Right now we dont map supplier error messages and we return what we get from the Supplier
                $this->apiResponses()->setMessage(empty($response) ? "N/A" : $response);
            }
        } else {
            // token is invalid - return error
            $this->apiResponses()->setStatusCode(ApiResponses::HTTP_500_CODE);
            $this->apiResponses()->setCode(ApiResponses::INVALID_URLENDPOINT);
            $this->apiResponses()->setMessage('Supplier end point not set');
        }

        return $this->apiResponses()->get();
    }

    /**
     * @param $array
     * @return null
     */
    private function populateData($array)
    {
        $token = $this->getSupplierToken();
        $engineName = $this->getEngineName();
        $engineCustomId = $this->getEngineCustomId();
        // Solve issue where some suppliers don't accept associate arrays. We get only values from the array
        $segments = is_array($this->getSegments()) ? array_values($this->getSegments()) : $this->getSegments();
        $source = $this->getSource();
        $target = $this->getTarget();
        $domain = $this->getDomainName();
        $userName = $this->getUserName();
        $password = $this->getPassword();
        $file = $this->getFile();
        $fileType = $this->getFileType();
        $guId = $this->getGuId();
        $callBack = $this->getCallBackUrl();

        if ($array && is_array($array)) {
            foreach ($array as $key => $value) {
                // Special case for segments. eTranslation doesnt accept array of segmnets so we will convert it to string
                if (is_array($value)) {
                    $data[$key] = $this->populateData($value);
                } else {
                    if (strpos($value, '[]') !== false) {
                        $value = str_replace('[]', '', $value);
                        $value = empty($$value) ? $value : $$value;
                        $value = is_array($value) ? $value : [$value];
                        $data[$key] = $value;
                    } else {
                        $value = empty($$value) ? $value : $$value;
                        $value = is_array($value) ? implode(";;", $value) : $value;
                        $data[$key] = $value;
                    }


                }
            }
        }

        return empty($data) ? null : $data;
    }

    /**
     * @return null
     */
    public function getAccount()
    {
        return $this->accounts;
    }

    /**
     * @param null $account
     */
    public function setAccount($account)
    {
        if (is_a($account, 'Accounts')) {
            $this->getRequestLog()->setConsumerAccountId($account->getId());
            $this->accounts = $account;
        }
    }

    /**
     * @return null
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @param null $domains
     */
    public function setDomainId($id)
    {
        if (is_numeric($id)) {
            $this->domainId = $id;
        }
    }

    /**
     * @return null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param null $source
     */
    public function setSource($source)
    {
        if (strlen($source) < 12) {
            $this->getRequestLog()->setSrc($source);
            $this->source = $source;
        }
    }

    /**
     * @return null
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param null $target
     */
    public function setTarget($target)
    {
        if (strlen($target) < 12) {
            $this->getRequestLog()->setTrg($target);
            $this->target = $target;
        }
    }

    /**
     * @return null
     */
    public function getEngineName()
    {
        return $this->engineName;
    }

    /**
     * @param null $engineName
     */
    public function setEngineName($engineName)
    {
        $this->engineName = $engineName;
    }

    /**
     * @return null
     */
    public function getEngineCustomId()
    {
        return $this->engineCustomId;
    }

    /**
     * @param null $engineCustomId
     */
    public function setEngineCustomId($engineCustomId)
    {
        $this->engineCustomId = $engineCustomId;
    }

    /**
     * @return null
     */
    public function getSupplierToken()
    {
        return $this->supplierToken;
    }

    /**
     * @param null $supplierToken
     */
    public function setSupplierToken($supplierToken)
    {
        $this->supplierToken = $supplierToken;
    }

    /**
     * @return null
     */
    public function getSupplierAccountId()
    {
        return $this->supplierAccountId;
    }

    /**
     * @param null $supplierAccountId
     */
    public function setSupplierAccountId($supplierAccountId)
    {
        $this->getRequestLog()->setSupplierAccountId($supplierAccountId);
        $this->supplierAccountId = $supplierAccountId;
    }

    /**
     * @return null
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * @param null $segments
     */
    public function setSegments($segments)
    {
        $this->segments = $segments;
    }

    /**
     * @return null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param null $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return null
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @param null $fileType
     */
    public function setFileType($fileType)
    {
        if (strlen($fileType) <= 10) {
            $this->fileType = $fileType;
        }
    }

    /**
     * @return null
     */
    public function getGuId()
    {
        return $this->guId;
    }

    /**
     * @param null $guId
     */
    public function setGuId($guId)
    {
        if ($this->validateGuid($guId)) {
            $this->guId = $guId;
        }
    }

    /**
     * @return ApiResponses
     */
    public function apiResponses()
    {
        return $this->apiResponses;
    }

    /**
     * @param ApiResponses $apiResponses
     */
    public function setApiResponses($apiResponses)
    {
        $this->apiResponses = $apiResponses;
    }

    /**
     * @return null
     */
    public function getDomainName()
    {
        return $this->domainName;
    }

    /**
     * @param null $domainName
     */
    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;
    }

    /**
     * @return null
     */
    public function getConentType()
    {
        return $this->conentType;
    }

    /**
     * @param null $conentType
     */
    public function setConentType($conentType)
    {
        $this->conentType = $conentType;
    }

    /**
     * @return null
     */
    public function getEngineId()
    {
        return $this->engineId;
    }

    /**
     * @param null $engineId
     */
    public function setEngineId($engineId)
    {
        if (is_numeric($engineId)) {
            $this->getRequestLog()->setEngineId($engineId);
            $this->engineId = $engineId;
        }
    }

    /**
     * @return RequestLog
     */
    public function getRequestLog()
    {
        return $this->requestLog;
    }

    /**
     * @param RequestLog $requestLog
     */
    public function setRequestLog($requestLog)
    {
        if (is_a($requestLog, 'RequestLog')) {
            $this->requestLog = $requestLog;
        }
    }

    /**
     * @return null
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param null $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param null $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return null
     */
    public function getCallBackUrl()
    {
        if (!empty($this->callBackUrl) && is_array($this->callBackUrl)) {
            $url = UrlConfig::CALLBACK_URL . "?";
            foreach ($this->callBackUrl as $key => $value) {
                $url .= $key . "=" . $value . "&";
            }

            return rtrim($url, '&');
        }

        return UrlConfig::CALLBACK_URL;
    }

    /**
     * @param null $callBackUrl
     */
    public function setCallBackUrl($callBackUrl)
    {
        $this->callBackUrl = $callBackUrl;
    }

    /**
     * @return null
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param null $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return null
     */
    public function getActiviaTm()
    {
        return $this->activiaTm;
    }

    /**
     * @param null $activiaTm
     */
    public function setActiviaTm($activiaTm)
    {
        $this->activiaTm = $activiaTm;
    }

    /**
     * @return null
     */
    public function getActiviaTmUserName()
    {
        return $this->activiaTmUserName;
    }

    /**
     * @param null $activiaTmUserName
     */
    public function setActiviaTmUserName($activiaTmUserName)
    {
        $this->activiaTmUserName = $activiaTmUserName;
    }

    /**
     * @return null
     */
    public function getActiviaTmPassword()
    {
        return $this->activiaTmPassword;
    }

    /**
     * @param null $activiaTmPassword
     */
    public function setActiviaTmPassword($activiaTmPassword)
    {
        $this->activiaTmPassword = $activiaTmPassword;
    }

    /**
     * @return null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param null $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
}
