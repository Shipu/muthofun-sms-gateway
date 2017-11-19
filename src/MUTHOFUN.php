<?php

namespace Shipu\MuthoFun;

class MUTHOFUN extends AbstractApi
{
    protected $config;
    protected $sms = [];
    protected $debug = false;
    protected $mobiles = [];
    protected $template = true;
    protected $autoParse = false;
    protected $sendingParameters = [];

    /**
     * MUTHOFUN constructor.
     *
     * @param $config
     */
    public function __construct( $config )
    {
        $this->config = $config;
        parent::__construct();
    }

    /**
     * Set base url
     *
     * @return string
     */
    protected function setBaseUrl()
    {
        return 'http://clients.muthofun.com:8901/';
    }

    /**
     * Set sending url
     *
     * @return string
     */
    public function setSendingUrl()
    {
        return 'esmsgw/sendsms.jsp';
    }

    /**
     * Set Message
     *
     * @param string $message
     * @param null $to
     *
     * @return $this
     */
    public function message( $message = '', $to = null )
    {
        $this->sms[] = $message;
        if ( !is_null($to) ) {
            $this->to($to);
        }

        return $this;
    }

    /**
     * Set Phone Numbers
     *
     * @param $to
     *
     * @return $this
     */
    public function to( $to )
    {
        if ( is_array($to) ) {
            $this->mobiles = array_merge($this->mobiles, $to);
        } else {
            $this->mobiles[] = $to;
        }

        return $this;
    }

    /**
     * Send Method
     *
     * @param array $array
     *
     * @return mixed
     */
    public function send( $array = [] )
    {
        return $this->makingSmsFormatAndSendingSMS($array);
    }

    /**
     * Prepare Sending parameters
     *
     * @param $sms
     * @param $mobiles
     *
     * @return $this
     */
    private function gettingParameters( $sms, $mobiles )
    {
        $this->sendingParameters = [
            'user'     => $this->config[ 'username' ],
            'password' => $this->config[ 'password' ],
            'sms'      => $sms,
            'mobiles'  => $mobiles,
            'unicode'  => '1'
        ];

        return $this;
    }

    /**
     * Formatting Given Data
     *
     * @param array $array
     *
     * @return array
     */
    protected function makingSmsFormatAndSendingSMS( $array = [] )
    {
        if($array) {
            $this->sms = array_merge($this->sms, $this->splitSmsAndNumbers($array));
        } else {
            $this->sms = $this->splitSmsAndNumbers($array);
        }

        if ( count($this->sms) == 1 ) {
            $this->sms = $this->sms[ 0 ];

            return $this->singelSMSOrTemplate();

        } else {
            return $this->makeMultiSmsMultiUser();
        }
    }

    private function singelSMSOrTemplate()
    {
        try{
            $this->mobiles = implode(',', $this->mobiles);
            return $this->makeSingleSmsToUser();
        } catch (\ErrorException $exception) {
            if($this->template) {
                $template = rawurldecode($this->sms);
                $putDataInTemplate = [];
                $sms = [];
                $mobiles = [];
                foreach ($this->mobiles as $mobile => $message) {
                    try {
                        $putData = vsprintf($template,$message);
                        $sms[] = $putData;
                        $mobiles[] = $mobile;
                        $putDataInTemplate[$mobile] = $putData;
                    } catch(\ErrorException $exception) {
                        $putData = vsprintf($template,$message[1]);
                        $sms[] = $putData;
                        $mobiles[] = $message[0];
                        $putDataInTemplate[$message[0]] = $putData;
                    }
                }
                if($sms) {
                    $this->sms = $sms;
                }

                if($mobiles) {
                    $this->mobiles = $mobiles;
                }
                return $this->makeMultiSmsMultiUser();
            }

            return false;
        }
    }

    /**
     * Formatting sms and mobiles property
     *
     * @param $array
     *
     * @return array
     */
    private function splitSmsAndNumbers( $array )
    {
        if ( $array && !isset($array[ 'message' ]) && !isset($array[ 'to' ]) ) {
            $this->sms = array_merge($this->sms, array_values($array));
            $arrayKeys = array_keys($array);

            if($arrayKeys[0] != 0) {
                $this->mobiles = array_merge($this->mobiles, $arrayKeys);
            }
        } else {
            $this->sms = array_merge($this->sms, $array);
        }

        $sms = [];
        $mobiles = [];
        foreach ( $this->sms as $key => $message ) {
            if ( is_array($message) && isset($message[ 'message' ]) && isset($message[ 'to' ]) ) {
                $sms[] = rawurlencode($message[ 'message' ]);
                $mobiles[] = $message[ 'to' ];
            } elseif ( $key === 'to' ) {
                $mobiles[] = $message;
            } else {
                $sms[] = rawurlencode($message);
            }
        }

        if ( $mobiles ) {
            $this->mobiles = array_merge($this->mobiles, $mobiles);
        }

        return $sms;
    }

    /**
     * Sending Single SMS
     *
     * @return array
     */
    protected function makeSingleSmsToUser()
    {
        $this->gettingParameters($this->sms, $this->numberPrefix . $this->mobiles);

        return $this->sendToServer();
    }

    /**
     * Sending Multiple SMS
     *
     * @return array
     */
    protected function makeMultiSmsMultiUser()
    {
        $response = [];
        $count = 1;
        foreach ( $this->sms as $key => $message ) {
            if ( isset($this->mobiles[ $key ]) ) {
                $number = $this->numberPrefix . $this->mobiles[ $key ];
                $this->gettingParameters($message, $number);
                $response[ 'res-'.$count++.'-'.$number ] = $this->sendToServer();
            }
        }

        return $response;
    }

    /**
     * Getting response from api
     *
     * @return mixed
     */
    private function sendToServer()
    {
        if($this->debug) {
            return $this->sendingParameters;
        } elseif ($this->autoParse) {
            return $this->query($this->sendingParameters)
                ->headers([ 'Accept' => 'application/xml' ])
                ->get($this->sendingUrl)->autoParse();
        }

        return $this->query($this->sendingParameters)
            ->headers([ 'Accept' => 'application/xml' ])
            ->get($this->sendingUrl);
    }

    /**
     * Set Debug
     *
     * @param bool $debug
     *
     * @return $this
     */
    public function debug( $debug = false )
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * Set Auto Parse
     *
     * @param bool $autoParse
     *
     * @return $this
     */
    public function autoParse( $autoParse = true )
    {
        $this->autoParse = $autoParse;
        return $this;
    }

    /**
     * Set Template
     *
     * @param bool $template
     *
     * @return $this
     */
    public function template( $template = true )
    {
        $this->template = $template;
        return $this;
    }

}
