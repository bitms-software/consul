<?php


namespace Bitms\Component\Consul;


interface ConstantsInterface
{
    /**
     * name: Product Information Management
     *
     * @see https://bitms.tech/swagger/product-information-management#
     */
    public const API_PIM = 'PIM';

    /**
     * name: Document Generation Service
     *
     * @see https://bitms.tech/swagger/document-generation-service#
     */
    public const API_DGS = 'DGS';

    /**
     * name: Account Information Service
     *
     * @see https://bitms.tech/swagger/account-information-service#
     */
    public const API_AMS = 'AMS';

    /**
     * name: Account Management Service
     *
     * @see https://bitms.tech/swagger/account-information-service#
     */
    public const API_AIS = 'AIS';

    /**
     * name: Authentication and Authorization Library
     *
     * @see https://bitms.tech/swagger/authentication-authorization-library#
     */
    public const API_AAL = 'AAL';

    /**
     * name: Payment Provider Service
     *
     * @see https://bitms.tech/swagger/payment-provider-service
     */
    public const API_PPS = 'PPS';
}