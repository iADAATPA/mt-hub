<?php

class UrlConfig extends Database
{
    private $id = null;
    private $methodId = null;
    private $accountId = null;
    private $urlEndPoint = null;
    private $parameters = null;
    private $header = null;
    private $response = null;
    private $type = null;
    private $request = null;
    private $authorization = null;
    private $callback = null;

    const METHOD_TRANSLATE_ID = 1;
    const METHOD_TRANSLATE_DESC = 'translate';
    const METHOD_TRANSLATE_WITH_QES_ID = 2;
    const METHOD_TRANSLATE_WITH_QES_DESC = 'translateWithQes';
    const METHOD_ATRANSLATE_ID = 3;
    const METHOD_ATRANSLATE_DESC = 'translate w/ callback';
    const METHOD_ATRANSLATE_WITH_QES_ID = 4;
    const METHOD_ATRANSLATE_WITH_QES_DESC = 'aTranslateWithQes';
    const METHOD_ASYNCPOLL_ID = 5;
    const METHOD_ASYNCPOLL_DESC = 'aRetrieveTranslation';
    const METHOD_TRANSLATE_FILE_ID = 6;
    const METHOD_TRANSLATE_FILE_DESC = 'translateFile';
    const METHOD_RETRIEVE_FILE_TRANSLATION_ID = 7;
    const METHOD_RETRIEVE_FILE_TRANSLATION_DESC = 'retrieveFileTranslation';
    const METHOD_DETECT_DOMAIN_ID = 8;
    const METHOD_DETECT_DOMAIN_DESC = 'detectDomain';
    const METHOD_DETECT_LANGUAGE_ID = 9;
    const METHOD_DETECT_LANGUAGE_DESC = 'detectLanguage';
    const METHOD_ATRANSLATE_FILE_ID = 10;
    const METHOD_ATRANSLATE_FILE_DESC = 'translateFile w/ callback';


    const TYPE_POST = "POST";
    const TYPE_GET = "GET";

    const AUTH_BASIC = 1;
    const AUTH_DIGEST = 2;

    const CALLBACK_URL = "https://iadaatpa.eu/callbacksuccess.php";

    /**
     * UrlConfig constructor.
     * @param null|int $id
     */
    public function __construct($id = null)
    {
        if ($id) {
            $this->setId($id);
            $this->set($this->get());
        }
    }

    /**
     * @return mixed|null
     */
    public function insert()
    {
        if ($this->getUrlEndPoint()) {
            $query = 'INSERT INTO
							urlconfig(
							    methodid,
								accountid,
								urlendpoint,
								parameters,
								header,
								response,
								type,
								request,
								callback,
								authorization
						)
						VALUES (
							:methodid,
                            :accountid,
                            :urlendpoint,
                            :parameters,
                            :header,
                            :response,
                            :type,
                            :request,
                            :callback,
							:authorization
						)';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':methodid', $this->getMethodId(), PDO::PARAM_INT);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':urlendpoint', $this->getUrlEndPoint(), PDO::PARAM_STR);
            $this->bindValue(':parameters', $this->getParameters(), PDO::PARAM_STR);
            $this->bindValue(':header', $this->getHeader(), PDO::PARAM_STR);
            $this->bindValue(':response', $this->getResponse(), PDO::PARAM_STR);
            $this->bindValue(':type', $this->getType(), PDO::PARAM_STR);
            $this->bindValue(':request', $this->getRequest(), PDO::PARAM_STR);
            $this->bindValue(':callback', $this->getCallback(), PDO::PARAM_STR);
            $this->bindValue(':authorization', $this->getAuthorization(), PDO::PARAM_INT);
            $this->execute();
            $id = $this->lastInsertId();
            $this->endTransaction();

            return $id;
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function get()
    {
        if (!empty($this->getId())) {
            $query = 'SELECT
                        id,
						methodid,
                        accountid,
                        urlendpoint,
                        parameters,
                        header,
                        response,
                        type,
                        request,
                        callback,
						authorization
					FROM
						urlconfig
					WHERE 
					    id = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $result = $this->result();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getAll()
    {
        if ($this->getAccountId()) {
            $query = 'SELECT
                    id,
                    methodid,
                    accountid,
                    urlendpoint,
                    parameters,
                    header,
                    response,
                    type,
                    request,
                    callback,
					authorization
                FROM
                    urlconfig
                WHERE
                    accountid = :accountid';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $result = $this->resultSet();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getAllByMethodId()
    {
        if ($this->getAccountId()) {
            $result = $this->getAll();
            $urlConfigs = [];

            if ($result && is_array($result)) {
                foreach ($result as $row) {
                    $urlConfigs[$row['methodid']] = $row;
                }
            }

            return $urlConfigs;
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function update()
    {
        if ($this->getId()) {
            $query = 'UPDATE
							urlconfig
						SET
							methodid	  = :methodid,
							accountid	  = :accountid,
			                urlendpoint   = :urlendpoint,
							parameters    = :parameters,	
							header        = :header,
							response      = :response,					
							type		  = :type,
							request       = :request,
							callback      = :callback,
							authorization = :authorization
						WHERE
							id			  = :id';

            $this->startTransaction();
            $this->query($query);
            $this->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $this->bindValue(':methodid', $this->getMethodId(), PDO::PARAM_STR);
            $this->bindValue(':accountid', $this->getAccountId(), PDO::PARAM_INT);
            $this->bindValue(':urlendpoint', $this->getUrlEndPoint(), PDO::PARAM_STR);
            $this->bindValue(':parameters', $this->getParameters(), PDO::PARAM_STR);
            $this->bindValue(':header', $this->getHeader(), PDO::PARAM_STR);
            $this->bindValue(':response', $this->getResponse(), PDO::PARAM_STR);
            $this->bindValue(':type', $this->getType(), PDO::PARAM_STR);
            $this->bindValue(':request', $this->getRequest(), PDO::PARAM_STR);
            $this->bindValue(':callback', $this->getCallback(), PDO::PARAM_STR);
            $this->bindValue(':authorization', $this->getAuthorization(), PDO::PARAM_INT);
            $result = $this->execute();
            $this->endTransaction();

            return $result;
        }

        return null;
    }

    /**
     * @param array $details
     * @return bool
     */
    public function set($details)
    {
        if (!empty($details) && is_array($details)) {
            $this->setId($details['id'] ? $details['id'] : null);
            $this->setMethodId($details['methodid'] ? $details['methodid'] : null);
            $this->setAccountId($details['accountid'] ? $details['accountid'] : null);
            $this->setUrlEndPoint($details['urlendpoint'] ? $details['urlendpoint'] : null);
            $this->setParameters($details['parameters'] ? $details['parameters'] : null);
            $this->setHeader($details['header'] ? $details['header'] : null);
            $this->setResponse($details['response'] ? $details['response'] : null);
            $this->setType($details['type'] ? $details['type'] : null);
            $this->setRequest($details['request'] ? $details['request'] : null);
            $this->setCallback($details['callback'] ? $details['callback'] : null);
            $this->setAuthorization($details['authorization'] ? $details['authorization'] : null);

            return true;
        }

        return false;
    }

    /**
     * Get api methods with their database ids
     * @return array
     */
    static public function getApiMethods()
    {
        $methods = [
            UrlConfig::METHOD_TRANSLATE_ID => UrlConfig::METHOD_TRANSLATE_DESC,
            UrlConfig::METHOD_TRANSLATE_WITH_QES_ID => UrlConfig::METHOD_TRANSLATE_WITH_QES_DESC,
            UrlConfig::METHOD_ATRANSLATE_ID => UrlConfig::METHOD_ATRANSLATE_DESC,
            UrlConfig::METHOD_ATRANSLATE_WITH_QES_ID => UrlConfig::METHOD_ATRANSLATE_WITH_QES_DESC,
            UrlConfig::METHOD_ASYNCPOLL_ID => UrlConfig::METHOD_ASYNCPOLL_DESC,
            UrlConfig::METHOD_TRANSLATE_FILE_ID => UrlConfig::METHOD_TRANSLATE_FILE_DESC,
            UrlConfig::METHOD_RETRIEVE_FILE_TRANSLATION_ID => UrlConfig::METHOD_RETRIEVE_FILE_TRANSLATION_DESC,
            UrlConfig::METHOD_DETECT_DOMAIN_ID => UrlConfig::METHOD_DETECT_DOMAIN_DESC,
            UrlConfig::METHOD_DETECT_LANGUAGE_ID => UrlConfig::METHOD_DETECT_LANGUAGE_DESC,
            UrlConfig::METHOD_ATRANSLATE_FILE_ID => UrlConfig::METHOD_ATRANSLATE_FILE_DESC
        ];

        return $methods;
    }

    /**
     * @param $json
     * @return null|string
     */
    public function validateJson($json)
    {
        $json = json_decode($json, true);

        if ($json === null) {
            return null;
        }

        return json_encode($json);
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getMethodId()
    {
        return $this->methodId;
    }

    /**
     * @param null $methodId
     */
    public function setMethodId($methodId)
    {
        $this->methodId = $methodId;
    }

    /**
     * @return null
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param null $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return null
     */
    public function getUrlEndPoint()
    {
        return $this->urlEndPoint;
    }

    /**
     * @param null $urlEndPoint
     */
    public function setUrlEndPoint($urlEndPoint)
    {
        $this->urlEndPoint = $urlEndPoint;
    }

    /**
     * @return null
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param null $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param null $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param null $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return null
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param null $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param null $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return null
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * @param null $authorization
     */
    public function setAuthorization($authorization)
    {
        $this->authorization = $authorization;
    }

    /**
     * @return null
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param null $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }
}
