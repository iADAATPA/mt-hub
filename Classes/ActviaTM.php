<?php

/**
 * Class ActviaTM
 * @package Classes
 */
class ActviaTM
{
    const URL_AUTH = "http://pangeanic-online.com:27979/api/v1/auth";
    const URL_TM = "http://pangeanic-online.com:27979/api/v1/tm";

    private $userName = null;
    private $password = null;
    private $accessToken = null;
    private $segment = null;
    private $translation = null;
    private $trg = null;
    private $src = null;
    private $domain = null;

    /**
     * ActviaTM constructor.
     * @param null $userName
     * @param null $password
     */
    public function __construct($userName = null, $password = null)
    {
        $this->setUserName($userName);
        $this->setPassword($password);
        $this->requestAccessToken();
    }

    /**
     * @return null
     */
    public function requestAccessToken ()
    {
        if (!empty($this->getPassword()) && !empty($this->getUserName())) {
            $customHeader[] = "Content-Type: application/json";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::URL_AUTH);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeader);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                "username" => $this->getUserName(),
                "password" => $this->getPassword()
            ]));

            $response = curl_exec($ch);
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if ($httpStatus == ApiResponses::HTTP_200_CODE) {
                $response = json_decode($response, true);
                $accessToken = empty($response["access_token"]) ? null : $response["access_token"];
                $this->setAccessToken($accessToken);

                return $accessToken;
            }
        }

        return null;
    }

    /**
     * @return null
     */
    public function addTM ()
    {
        if (!empty($this->getSrc())
            && !empty($this->getTrg())
            && !empty($this->getSegment())
            && !empty($this->getTranslation())
            && !empty($this->getAccessToken())
        ) {
            $customHeader[] = "Content-Type: application/json";
            $customHeader[] = "Authorization: JWT " . $this->getAccessToken();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::URL_TM);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeader);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'slang' => $this->getSrc(),
                'tlang' => $this->getTrg(),
                'stext' => $this->getSegment(),
                'ttext' => $this->getTranslation(),
                'domain' => $this->getDomain()
            ]));


            $response = curl_exec($ch);
        }

        return;
    }

    /**
     * @return null|string
     */
    public function getTM () {
        if (!empty($this->getSrc())
            && !empty($this->getTrg())
            && !empty($this->getSegment())
            && !empty($this->getAccessToken())
        ) {
            $ch = curl_init();
            $customHeader[] = "Authorization: JWT " . $this->getAccessToken();
            curl_setopt($ch, CURLOPT_URL, self::URL_TM . '?' . http_build_query([
                'slang' => $this->getSrc(),
                'tlang' => $this->getTrg(),
                'q' => $this->getSegment(),
                'domain' => $this->getDomain()
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeader);

            $response = curl_exec($ch);

            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if ($httpStatus == ApiResponses::HTTP_200_CODE) {
                $response = json_decode($response, true);

                if (!empty($response["results"][0]["tu"]["target_text"])) {
                    return $response["results"][0]["tu"]["target_text"];
                }
            }
        }

        return null;
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
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @param null $segment
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;
    }

    /**
     * @return null
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * @param null $translation
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;
    }

    /**
     * @return null
     */
    public function getTrg()
    {
        return $this->trg;
    }

    /**
     * @param null $trg
     */
    public function setTrg($trg)
    {
        $this->trg = $trg;
    }

    /**
     * @return null
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * @param null $src
     */
    public function setSrc($src)
    {
        $this->src = $src;
    }

    /**
     * @return null
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param null $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return null
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param null $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }
}