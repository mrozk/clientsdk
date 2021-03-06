<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/11/15
 * Time: 2:10 PM
 */

namespace DDelivery\Adapter;


use DDelivery\Business\Business;
use DDelivery\DDeliveryException;
use DDelivery\DDeliveryUI;
use DDelivery\Server\Api;
use DDelivery\Storage\LogStorageDB;
use DDelivery\Storage\LogStorageInterface;
use DDelivery\Storage\OrderStorageDB;
use DDelivery\Storage\OrderStorageInterface;
use DDelivery\Storage\SettingStorageDB;
use DDelivery\Storage\SettingStorageInterface;
use DDelivery\Storage\TokenStorageDB;
use DDelivery\Storage\TokenStorageInterface;

class Container {

    protected $parameters = array();

    protected $shared = array();

    public function __construct(array $parameters){
        if( array_key_exists('adapter', $parameters) && ($parameters['adapter'] instanceof Adapter) ){

            $this->shared['adapter'] = $parameters['adapter'];
            $this->parameters = $parameters;
        }else{
            throw new DDeliveryException("Неверно задан адаптер");
        }
    }

    /**
     * Получить контроллер
     *
     * @return DDeliveryUI
     */
    public function getUi(){
        if(!isset($this->shared['ui'])){
            $ui = new DDeliveryUI();
            $ui->setAdapter($this->getAdapter());
            $ui->setBusiness($this->getBusiness());
            $ui->setLog($this->getLogStorage());
            $this->shared['ui'] = $ui;
        }
        return $this->shared['ui'];
    }

    /**
     *
     * Получить бизнес логику
     *
     * @return Business
     */
    public function getBusiness(){
        if(!isset($this->shared['business'])){
            $api = $this->getApi();
            $orderStorage = $this->getOrderStorage();
            $tokenStorage = $this->getTokenStorage();
            $settingStorage = $this->getSettingStorage();
            $log = $this->getLogStorage();
            $business = new Business($api, $tokenStorage, $settingStorage, $orderStorage, $log);
            $this->shared['business'] = $business;
        }
        return $this->shared['business'];
    }

    /**
     * Получить хранилище настроек
     *
     * @return LogStorageInterface
     */
    public function getLogStorage(){
        if(!isset($this->shared['log'])){
            $adapter = $this->getAdapter();
            $pdo = $adapter->getDb();
            $config = $adapter->getDbConfig();
            $setting = new LogStorageDB($pdo, $config['type'], $config['prefix']);
            $this->shared['setting'] = $setting;
        }
        return $this->shared['setting'];
    }

    /**
     * Получить хранилище настроек
     *
     * @return SettingStorageInterface
     */
    public function getSettingStorage(){
        if(!isset($this->shared['setting'])){
            $adapter = $this->getAdapter();
            $pdo = $adapter->getDb();
            $config = $adapter->getDbConfig();
            $setting = new SettingStorageDB($pdo, $config['type'], $config['prefix']);
            $this->shared['setting'] = $setting;
        }
        return $this->shared['setting'];
    }

    /**
     * Получить хранилище токенов
     *
     * @return TokenStorageInterface
     */
    public function getTokenStorage(){
        if(!isset($this->shared['token'])){
            $adapter = $this->getAdapter();
            $pdo = $adapter->getDb();
            $config = $adapter->getDbConfig();
            $token = new TokenStorageDB($pdo, $config['type'], $config['prefix']);
            $this->shared['token'] = $token;
        }
        return $this->shared['token'];
    }

    /**
     *
     * Получить хранилище заказов
     *
     * @return OrderStorageInterface
     */
    public function getOrderStorage(){
        if(!isset($this->shared['order'])){
            $adapter = $this->getAdapter();
            $pdo = $adapter->getDb();
            $config = $adapter->getDbConfig();
            $order = new OrderStorageDB($pdo, $config['type'], $config['prefix']);
            $this->shared['order'] = $order;
        }
        return $this->shared['order'];
    }


    /**
     *
     * Получить адаптер
     *
     * @return Api
     */
    public function getApi(){
        if(!isset($this->shared['api'])){
            $adapter = $this->getAdapter();
            $api = new Api($adapter->getApiKey(), $adapter->getSdkServer());
            $this->shared['api'] = $api;
        }
        return $this->shared['api'];
    }

    /**
     *
     * Получить адаптер
     *
     * @return Adapter
     */
    public function getAdapter(){
        return $this->shared['adapter'];
    }



} 